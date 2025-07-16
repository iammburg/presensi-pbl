import os, time, threading, io, logging
import numpy as np
import torch
from PIL import Image
from flask import Flask, request, jsonify
from flask_cors import CORS
from facenet_pytorch import MTCNN, InceptionResnetV1
from torchvision import transforms

app = Flask(__name__)
CORS(app)
logging.basicConfig(level=logging.INFO)
logger = app.logger

STUDENT_PHOTOS_DIR = os.environ.get("STUDENT_PHOTOS_DIR")

# STUDENT_PHOTOS_DIR = os.path.abspath(
#     os.path.join(os.path.dirname(__file__), "../storage/app/public/student-photos")
# )
device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
logger.info("🧠 Device in use: %s", device)

# 📦 Globals
db_embeddings = {}
CACHE_LOCK = threading.Lock()
REFRESH_INTERVAL = 300  # seconds
THRESHOLD = 0.55  # 💡 Lebih lunak dari sebelumnya

# 📷 Model load (sekali)
mtcnn = MTCNN(image_size=160, margin=0, keep_all=False, device=device)
resnet = InceptionResnetV1(pretrained="vggface2").eval().to(device)
logger.info("✅ Models loaded successfully")

# 🔁 Data Augmentation
augmentations = transforms.Compose(
    [
        transforms.RandomHorizontalFlip(p=0.5),
        transforms.RandomRotation(degrees=5),
    ]
)


# 📥 Embedding dari image file
def get_input_embedding(file_bytes):
    img = Image.open(io.BytesIO(file_bytes)).convert("RGB")
    face = mtcnn(img)
    if face is None:
        return None
    with torch.no_grad():
        emb = resnet(face.unsqueeze(0).to(device)).cpu().numpy()[0]
    return emb / np.linalg.norm(emb)


# 🧬 Bandingkan ke DB
def match_face(emb, threshold=THRESHOLD):
    if emb is None:
        return None, None
    best_nisn, best_sim = None, threshold
    with CACHE_LOCK:
        if not db_embeddings:
            logger.warning("❌ DB embeddings kosong!")
            return None, None
        for nisn, db_emb in db_embeddings.items():
            sim = float(np.dot(db_emb, emb))
            if sim > best_sim:
                best_sim = sim
                best_nisn = nisn
    return best_nisn, best_sim


# 📦 Refresh embeddings
def load_db_embeddings():
    if not os.path.exists(STUDENT_PHOTOS_DIR):
        logger.warning("📁 STUDENT_PHOTOS_DIR tidak ditemukan: %s", STUDENT_PHOTOS_DIR)
        return

    logger.info("🔄 Loading student embeddings...")
    temp = {}
    for fname in os.listdir(STUDENT_PHOTOS_DIR):
        if not fname.lower().endswith((".jpg", ".jpeg", ".png")):
            continue
        nisn = fname.split("_")[0]
        path = os.path.join(STUDENT_PHOTOS_DIR, fname)

        try:
            img = Image.open(path).convert("RGB")
            emb_list = []

            face = mtcnn(img)
            if face is not None:
                emb = resnet(face.unsqueeze(0).to(device)).cpu().detach().numpy()[0]
                emb_list.append(emb)

            for _ in range(2):  # 📉 Kurangi augmentasi supaya stabil
                aug_img = augmentations(img)
                face = mtcnn(aug_img)
                if face is not None:
                    emb_aug = (
                        resnet(face.unsqueeze(0).to(device)).cpu().detach().numpy()[0]
                    )
                    emb_list.append(emb_aug)

            if emb_list:
                mean_emb = np.mean(emb_list, axis=0)
                temp[nisn] = mean_emb / np.linalg.norm(mean_emb)

        except Exception as e:
            logger.warning("⚠️ Gagal proses %s: %s", fname, e)

    with CACHE_LOCK:
        db_embeddings.clear()
        db_embeddings.update(temp)
        logger.info("✅ Loaded %d embeddings", len(db_embeddings))


# ⏲️ Background refresher
def refresher():
    while True:
        try:
            load_db_embeddings()
        except Exception as e:
            logger.error("❌ Refresher error: %s", e)
        time.sleep(REFRESH_INTERVAL)


# 🚀 Start refresher saat app mulai
threading.Thread(target=refresher, daemon=True).start()
logger.info("🧵 Refresher thread started")


@app.route("/health")
def health():
    return jsonify({"status": "OK"}), 200


@app.route("/embed", methods=["POST"])
def embed():
    nisn = request.form.get("nisn")
    file = request.files.get("file")
    if not nisn or not file:
        return jsonify({"error": "nisn or file missing"}), 400

    try:
        emb = get_input_embedding(file.read())
        if emb is None:
            return jsonify({"error": "no_face"}), 422
        with CACHE_LOCK:
            db_embeddings[nisn] = emb
        logger.info("✅ Embedded for %s", nisn)
        return jsonify({"success": True, "nisn": nisn}), 200
    except Exception as e:
        logger.error("❌ Embed failed for %s: %s", nisn, e)
        return jsonify({"error": "server_error"}), 500


@app.route("/classify", methods=["POST"])
def classify():
    file = request.files.get("file")
    if not file:
        return jsonify({"error": "no_file"}), 400

    emb = get_input_embedding(file.read())
    if emb is None:
        return jsonify({"label": None, "confidence": 0.0, "method": "no_face"}), 200

    nisn, conf = match_face(emb)
    return (
        jsonify(
            {
                "label": nisn,
                "confidence": conf or 0.0,
                "method": "cosine" if nisn else "unknown",
            }
        ),
        200,
    )


if __name__ == "__main__":
    logger.info("🚀 Starting Flask App")
    load_db_embeddings()
    app.run(host="0.0.0.0", port=5000, debug=os.environ.get("FLASK_DEBUG") == "1")
