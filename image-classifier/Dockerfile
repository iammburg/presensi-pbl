FROM python:3.11-slim-buster

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      build-essential \
      zlib1g-dev libjpeg-dev libpng-dev \
      libtiff-dev libwebp-dev \
      libopenjp2-7-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /python-docker

COPY requirements.txt .
RUN pip install --upgrade pip
RUN pip install -r requirements.txt

COPY . .

EXPOSE 5000
ENTRYPOINT ["python"]
CMD ["src/app.py"]
