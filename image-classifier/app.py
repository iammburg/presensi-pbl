import os
import time
import threading
import io
import numpy as np
import torch
from PIL import Image
from flask import Flask, request, jsonify
from flask_cors import CORS
from facenet_pytorch import MTCNN, InceptionResnetV1
from torchvision import transforms
import logging

# Setup logging
torch.backends.cudnn.benchmark = True
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)

# Directory penyimpanan foto siswa
STUDENT_PHOTOS_DIR = os.path.abspath(
    os.path.join(os.path.dirname(__file__), "../storage/app/public/student-photos")
)

# Device configuration
device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
logger.info(f"Using device: {device}")

# Konfigurasi MTCNN (lebih longgar untuk deteksi wajah)
mtcnn = MTCNN(
    image_size=160,
    margin=10,
    min_face_size=40,
    thresholds=[0.5, 0.6, 0.7],  # stage1, stage2, stage3
    factor=0.709,
    keep_all=False,
    device=device,
    post_process=True,
)

# Pre-trained Resnet untuk embedding
resnet = InceptionResnetV1(pretrained="vggface2", classify=False).eval().to(device)

# Global cache untuk embeddings dan sinkronisasi
db_embeddings = {}
CACHE_LOCK = threading.Lock()
REFRESH_INTERVAL = 300  # detik (5 menit)
SIMILARITY_THRESHOLD = 0.65

# Augmentasi untuk variasi embedding
augmentations = transforms.Compose(
    [
        transforms.RandomHorizontalFlip(p=0.5),
        transforms.RandomRotation(degrees=5),
        transforms.ColorJitter(brightness=0.1, contrast=0.1, saturation=0.1),
        transforms.RandomAffine(degrees=0, translate=(0.05, 0.05)),
    ]
)


def load_db_embeddings():
    """Load dan cache embeddings siswa dengan augmentasi"""
    global db_embeddings
    temp = {}
    start_time = time.time()
    processed_count = 0

    for fname in os.listdir(STUDENT_PHOTOS_DIR):
        if not fname.lower().endswith((".jpg", ".jpeg", ".png")):
            continue

        nisn = fname.split("_")[0]
        path = os.path.join(STUDENT_PHOTOS_DIR, fname)
        emb_list = []

        try:
            img = Image.open(path).convert("RGB")

            # Proses gambar asli
            face = mtcnn(img)
            if face is not None:
                with torch.no_grad():
                    emb_tensor = resnet(face.unsqueeze(0).to(device))
                emb = emb_tensor.squeeze(0).cpu().numpy()
                emb_list.append(emb)

            # Proses augmentasi
            for _ in range(4):
                aug_img = augmentations(img)
                face = mtcnn(aug_img)
                if face is not None:
                    with torch.no_grad():
                        emb_tensor = resnet(face.unsqueeze(0).to(device))
                    emb_aug = emb_tensor.squeeze(0).cpu().numpy()
                    emb_list.append(emb_aug)

            if emb_list:
                mean_emb = np.mean(emb_list, axis=0)
                norm_emb = mean_emb / np.linalg.norm(mean_emb)
                temp[nisn] = norm_emb
                processed_count += 1

        except Exception as e:
            logger.warning(f"Failed to process {fname}: {e}")

    with CACHE_LOCK:
        db_embeddings = temp

    elapsed = time.time() - start_time
    logger.info(f"Loaded {processed_count} student embeddings in {elapsed:.2f}s")


def refresher():
    """Thread untuk refresh cache embeddings berkala"""
    while True:
        try:
            load_db_embeddings()
        except Exception as e:
            logger.error(f"Refresher error: {e}")
        time.sleep(REFRESH_INTERVAL)


# Mulai thread refresher
threading.Thread(target=refresher, daemon=True).start()


def get_input_embedding(file_bytes):
    """Ekstrak embedding dari gambar input"""
    try:
        img = Image.open(io.BytesIO(file_bytes)).convert("RGB")
        face = mtcnn(img)
        if face is None:
            return None

        with torch.no_grad():
            emb_tensor = resnet(face.unsqueeze(0).to(device))
        emb = emb_tensor.squeeze(0).cpu().numpy()
        return emb / np.linalg.norm(emb)

    except Exception as e:
        logger.error(f"Embedding extraction failed: {e}")
        return None


def match_face(emb):
    """Match embedding terhadap database dengan threshold"""
    if emb is None:
        return None, None

    best_nisn = None
    best_sim = SIMILARITY_THRESHOLD

    with CACHE_LOCK:
        for nisn, db_emb in db_embeddings.items():
            sim = float(np.dot(db_emb, emb))
            if sim > best_sim:
                best_sim = sim
                best_nisn = nisn

    return best_nisn, best_sim


@app.route("/health")
def health():
    return jsonify({"status": "OK"}), 200


@app.route("/embed", methods=["POST"])
def embed_single():
    """Tambahkan/refresh embedding siswa baru"""
    nisn = request.form.get("nisn")
    file = request.files.get("file")

    if not nisn or not file:
        return jsonify({"error": "Missing nisn or file"}), 400

    try:
        emb = get_input_embedding(file.read())
        if emb is None:
            return jsonify({"error": "no_face"}), 422

        with CACHE_LOCK:
            db_embeddings[nisn] = emb
        return jsonify({"success": True, "nisn": nisn}), 200

    except Exception as e:
        logger.error(f"Embedding update failed for {nisn}: {e}")
        return jsonify({"error": "server_error"}), 500


@app.route("/classify", methods=["POST"])
def classify():
    """Endpoint utama klasifikasi wajah"""
    if "file" not in request.files:
        return jsonify({"error": "No file provided"}), 400

    try:
        start_time = time.time()
        blob = request.files["file"].read()

        emb = get_input_embedding(blob)
        if emb is None:
            return jsonify({"label": None, "confidence": 0.0, "method": "no_face"}), 200

        nisn, conf = match_face(emb)
        elapsed = time.time() - start_time
        logger.info(
            f"Classification took {elapsed:.3f}s - NISN: {nisn}, Conf: {conf:.4f}"
        )

        return (
            jsonify(
                {
                    "label": nisn,
                    "confidence": float(conf) if conf else 0.0,
                    "method": "cosine" if nisn else "unknown",
                }
            ),
            200,
        )

    except Exception as e:
        logger.error(f"Classification error: {e}")
        return jsonify({"error": "processing_error"}), 500


if __name__ == "__main__":
    # Initial load
    load_db_embeddings()

    # Run server
    app.run(host="0.0.0.0", port=5000)
