# -------------------------------
# Stage 1: build dependencies
# -------------------------------
    FROM python:3.10-slim-bullseye AS builder
    ENV DEBIAN_FRONTEND=noninteractive PYTHONUNBUFFERED=1

    RUN apt-get update \
     && apt-get install -y --no-install-recommends \
          gcc build-essential \
          ca-certificates openssl \
          libglib2.0-0 libsm6 libxrender1 libxext6 \
     && rm -rf /var/lib/apt/lists/*

    WORKDIR /app
    COPY requirements.txt .
    RUN pip install --no-cache-dir -r requirements.txt

    # -------------------------------
    # Stage 2: runtime
    # -------------------------------
    FROM python:3.10-slim-bullseye
    ENV DEBIAN_FRONTEND=noninteractive PYTHONUNBUFFERED=1

    RUN apt-get update \
     && apt-get install -y --no-install-recommends \
          ca-certificates openssl \
          libglib2.0-0 libsm6 libxrender1 libxext6 \
     && rm -rf /var/lib/apt/lists/*

    WORKDIR /app
    COPY --from=builder /usr/local/lib/python3.10/site-packages /usr/local/lib/python3.10/site-packages
    COPY --from=builder /usr/local/bin /usr/local/bin
    COPY . .

    RUN mkdir -p logs && chown -R www-data:www-data /app
    USER www-data

    EXPOSE 5000
    CMD ["gunicorn", "--workers=3", "--bind=0.0.0.0:5000", "app:app"]
