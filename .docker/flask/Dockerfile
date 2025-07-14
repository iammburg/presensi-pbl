FROM python:3.10-slim

WORKDIR /app

COPY image-classifier/requirements.txt ./

RUN pip install --no-cache-dir -r requirements.txt

COPY image-classifier/ /app/

CMD ["gunicorn", "-w", "1", "-t", "600", "-b", "0.0.0.0:5000", "app:app"]
