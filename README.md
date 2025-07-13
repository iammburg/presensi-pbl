
<p align="center">
<a href="https://laravel.com"  target="_blank"><img  src="https://ik.polines.ac.id/wp-content/uploads/2023/11/logo-web.png"  width="360"  alt="Polines Logo"></a> 
<a  href="https://laravel.com"  target="_blank"><img  src="https://ik.polines.ac.id/wp-content/uploads/2024/02/laravel-logo.jpg"  width="220"  alt="Laravel Logo"></a>
</p>  

# Sistem Presensi PBL - Politeknik Negeri Semarang

Sistem Presensi PBL merupakan aplikasi manajemen kehadiran berbasis web dengan pengenalan wajah untuk lingkungan pendidikan. Dibangun untuk Project-Based Learning di Program Studi D3 Teknik Informatika & S.Tr. Teknologi Rekayasa Komputer, Politeknik Negeri Semarang.

## Fitur Utama

- **Pengenalan Wajah**: Verifikasi kehadiran dengan pengenalan wajah menggunakan FaceNet dan MTCNN
- **Manajemen Presensi**: Pencatatan dan pelacakan kehadiran secara real-time
- **Manajemen Akademik**: Pengelolaan tahun ajaran, kurikulum, kelas, jadwal, mata pelajaran, pelaporan prestasi dan pelanggaran
- **Manajemen Pengguna**: Pengelolaan akun untuk admin, guru, dan siswa
- **RBAC (Role-Based Access Control)**: Kontrol akses berdasarkan peran pengguna
- **Dashboard Informatif**: Visualisasi data presensi dan statistik
- **Integrasi Data**: Import/export data siswa dan guru melalui Excel

## Teknologi

- **Backend**: Laravel 11 dengan PHP 8.2
- **Frontend**: Bootstrap, jQuery, dan Blade Template
- **Database**: MySQL 8.0/MariaDB 10.4
- **Pengenalan Wajah**: Flask API dengan PyTorch, FaceNet, dan MTCNN
- **Docker**: Containerization untuk deployment yang mudah

## Prasyarat

- PHP 8.2 atau lebih tinggi
- Composer
- MySQL 8.0/MariaDB 10.4 atau lebih tinggi
- Python 3.8+ (untuk layanan pengenalan wajah)
- Node.js dan NPM (opsional, untuk pengembangan frontend)

## Instalasi

### Metode Standar

1. Clone repository ini:
```
git clone https://github.com/username/presensi-pbl.git
cd presensi-pbl
```

2. Install dependency PHP:
```
composer install
```

3. Copy file `.env.example` menjadi `.env`:
```
copy .env.example .env
```

4. Buat database baru dan sesuaikan konfigurasi pada file `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={your database}
DB_USERNAME={your database username}
DB_PASSWORD={your database password}
```

5. Generate application key, jalankan migrasi database, dan isi data awal:
```
php artisan key:generate
php artisan migrate
php artisan db:seed
```

6. Jalankan aplikasi:
```
php artisan serve
```

### Menggunakan Docker

1. Pastikan Docker dan Docker Compose sudah terinstal
2. Clone repository dan masuk ke direktori proyek
3. Jalankan dengan Docker Compose:
```
docker-compose up -d
```

## Konfigurasi Image Classifier (Pengenalan Wajah)

1. Masuk ke direktori `image-classifier`:
```
cd image-classifier
```

2. Install dependency Python:
```
pip install -r requirements.txt
```

3. Jalankan Flask API:
```
python app.py
```
atau gunakan Docker:
```
docker-compose -f docker-compose.prod.yml up -d
```

## Akses Aplikasi

Setelah instalasi, aplikasi dapat diakses di `http://localhost:8000` dengan kredensial berikut:
```
Username: superadmin@gmail.com
Password: adminadmin
```

## Kontributor

- Tim Pengembang kelompok 1 & 2 PBL D3 Teknik Informatika
- Dosen Pembimbing Politeknik Negeri Semarang

## Terima Kasih Kepada

- Kaprodi D3 Teknik Informatika
- Kaprodi S.Tr. Teknologi Rekayasa Komputer
- Ketua Jurusan Teknik Elektro, Politeknik Negeri Semarang
- Task Force PBL D3 Teknik Informatika & S.Tr. Teknologi Rekayasa Komputer

---

Aplikasi ini dikembangkan oleh kelompok 1 & 2 PBL prodi D3 Teknik Informatika
