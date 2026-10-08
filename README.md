# 🏫 Sistem Absensi SMA N 1 Pangkalan Koto Baru

### Aplikasi Absensi Sekolah Berbasis Web

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

Sistem absensi digital untuk **SMA N 1 Pangkalan Koto Baru**: pencatatan kehadiran siswa oleh guru, rekap otomatis, dan laporan untuk kepala sekolah. Dibangun dengan PHP native + MySQL — ringan, cepat, jalan di shared hosting mana pun.

---

## ✨ Fitur Utama

### 👨‍💼 Admin
- Dashboard & manajemen data master (siswa, guru, kelas, jadwal)
- Kelola akun pengguna (admin, guru, siswa, kepsek, wakel)
- Rekap absensi keseluruhan

### 👩‍🏫 Guru
- Input absensi harian per kelas
- Lihat jadwal mengajar
- Rekap absensi kelas yang diajar

### 🎒 Siswa
- Lihat riwayat absensi sendiri
- Pengajuan izin
- Lihat jadwal pelajaran

### 🏛️ Kepala Sekolah
- Laporan & rekap absensi seluruh sekolah
- Pantau kehadiran guru dan siswa

---

## 🖼️ Preview

> Ganti file di bawah dengan screenshot asli aplikasimu.

| Login | Dashboard |
|-------|-----------|
| ![Login](docs/screenshot-login.png) | ![Dashboard](docs/screenshot-dashboard.png) |

---

## 🚀 Cara Instalasi

### Syarat
- PHP 7.4+ dengan ekstensi `mysqli`
- MySQL 5.7+ / MariaDB
- Web server (Apache/Nginx, XAMPP/Laragon untuk lokal)

### 1. Clone repository
```bash
git clone https://github.com/Pdrsmth/Sistem-Absensi-SMA-N-1-Pangkalan-Koto-Baru.git
cd Sistem-Absensi-SMA-N-1-Pangkalan-Koto-Baru
```

### 2. Buat database & import
```sql
CREATE DATABASE db_imas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- lalu import via phpMyAdmin / mysql client:
-- db/db_imas.sql
```

### 3. Konfigurasi koneksi
Edit `config/db.php` sesuaikan dengan environment kamu:
```php
$con = new mysqli("localhost", "root", "", "db_imas");
```

### 4. Jalankan
Taruh folder project di document root web server (`htdocs/` / `www/`), lalu buka:
```
http://localhost/Sistem-Absensi-SMA-N-1-Pangkalan-Koto-Baru
```

Login sesuai akun yang ada di database (lihat tabel `user`).

---

## 📁 Struktur Folder

```
Sistem-Absensi-SMA-N-1-Pangkalan-Koto-Baru/
├── admin/          # Panel admin (dashboard, master data, rekap)
├── guru/           # Panel guru (input absen, jadwal, rekap)
├── siswa/          # Panel siswa (riwayat absen, izin, jadwal)
├── kepsek/         # Panel kepala sekolah (laporan)
├── config/         # Koneksi database (db.php)
├── db/             # Dump database (db_imas.sql)
├── assets/         # CSS, JS, fonts, images
├── index.php       # Halaman login
└── user.php        # Manajemen user
```

Setiap role punya folder sendiri berisi `index.php` (layout), `logout.php`, dan `modul/` (fitur per halaman) — pola yang simpel dan mudah diikuti untuk aplikasi sekolah.

---

## 🛠️ Teknologi

- **Backend**: PHP native (MySQLi)
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript, Bootstrap

---

## 🔒 Catatan Keamanan

- Kredensial di `config/db.php` masih hardcoded untuk development lokal — **jangan** pakai nilai production di file yang di-commit. Pindahkan ke `.env` / file config terpisah untuk deploy.
- Disarankan: hash password dengan `password_hash()` dan gunakan prepared statement di semua query.

---

## 🤝 Kontribusi

Lihat [CONTRIBUTING.md](CONTRIBUTING.md).

## 📄 Lisensi

Proyek ini berlisensi [MIT](LICENSE).
