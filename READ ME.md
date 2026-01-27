# 🌙 Ramadhan Glow Up - Installation Guide

Journal Ramadhan interaktif dengan sistem database lengkap untuk tracking progress harian, journaling, mood, dan water intake.

## 📋 Prerequisites

- **Laragon** sudah terinstall dan running
- **PHP** 7.4 atau lebih tinggi
- **MySQL** (sudah include di Laragon)
- Web browser modern

## 🚀 Cara Instalasi

### 1. Persiapan Folder Project

1. Buka Laragon
2. Klik kanan pada icon Laragon di system tray > **Root** untuk membuka folder `C:\laragon\www`
3. Buat folder baru dengan nama `ramadhan-glowup`
4. Copy semua file project ke folder tersebut:
   ```
   C:\laragon\www\ramadhan-glowup\
   ├── config.php
   ├── auth.php
   ├── login.php
   ├── index.php
   ├── logout.php
   ├── save_progress.php
   ├── save_journal.php
   ├── save_mood.php
   ├── save_water.php
   ├── get_user_data.php
   └── database.sql
   ```

### 2. Setup Database

1. **Start Laragon**
   - Buka Laragon
   - Klik tombol **"Start All"**
   - Tunggu hingga Apache dan MySQL berwarna hijau

2. **Buka phpMyAdmin**
   - Klik kanan icon Laragon > **MySQL** > **phpMyAdmin**
   - Atau buka browser: `http://localhost/phpmyadmin`
   - Login dengan:
     - Username: `root`
     - Password: (kosongkan)

3. **Import Database**
   - Klik tab **"SQL"** di bagian atas
   - Copy seluruh isi file `database.sql`
   - Paste ke kolom query
   - Klik tombol **"Go"** atau **"Kirim"**
   - Database `ramadhan_glowup` akan otomatis terbuat beserta semua tabelnya

   **ATAU cara alternatif:**
   - Klik **"New"** atau **"Baru"** di sidebar kiri
   - Buat database dengan nama: `ramadhan_glowup`
   - Klik database yang baru dibuat
   - Klik tab **"Import"**
   - Pilih file `database.sql`
   - Klik **"Go"**

### 3. Konfigurasi Database (Opsional)

File `config.php` sudah dikonfigurasi untuk Laragon default:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosong untuk Laragon default
define('DB_NAME', 'ramadhan_glowup');
```

Jika Anda mengubah password MySQL, edit file `config.php` sesuai kebutuhan.

### 4. Akses Website

1. Buka browser
2. Ketik di address bar: `http://ramadhan-glowup.test`
   
   *Laragon otomatis membuat virtual host berdasarkan nama folder*
   
   **Alternatif URL:**
   - `http://localhost/ramadhan-glowup`
   - `http://127.0.0.1/ramadhan-glowup`

3. Anda akan diarahkan ke halaman **Login/Register**

### 5. Register User Pertama

1. Klik tab **"Register"**
2. Isi form:
   - **Nama Lengkap**: Nama Anda
   - **Email**: email@example.com
   - **Password**: minimal 6 karakter
3. Klik tombol **"Register"**
4. Anda akan otomatis login dan diarahkan ke halaman utama

## ✨ Fitur yang Tersedia

### 📝 Daily Tasks Tracking
- Checkbox untuk tasks Fisik dan Spiritual
- Auto-save setiap kali dicentang
- Data tersimpan per user

### 📖 Journaling
- "Why" Ramadhan (3 lines)
- Kebiasaan buruk yang ingin dipuasakan (2 lines)
- Auto-save setiap 1 detik setelah mengetik

### 😊 Mood Tracker
- 4 pilihan mood: 😁 😐 😴 😟
- Tersimpan per hari

### 💧 Water Intake Tracker
- Track konsumsi air (8 gelas)
- Klik pada icon tetes air untuk update
- Auto-save langsung

## 🔧 Troubleshooting

### Error: Connection Failed
**Solusi:**
1. Pastikan Laragon sudah running (lampu hijau)
2. Cek apakah MySQL aktif di Laragon
3. Restart Laragon jika perlu

### Error: Database Not Found
**Solusi:**
1. Buka phpMyAdmin
2. Cek apakah database `ramadhan_glowup` sudah ada
3. Jika belum, jalankan lagi file `database.sql`

### Error: Cannot Access Website
**Solusi:**
1. Pastikan folder berada di `C:\laragon\www\ramadhan-glowup`
2. Coba akses via `http://localhost/ramadhan-glowup` 
3. Restart Laragon

### Error: PHP Session Issues
**Solusi:**
1. Buka `config.php`
2. Pastikan session_start() berjalan
3. Clear browser cache dan cookies

## 📊 Struktur Database

Database terdiri dari 8 tabel utama:

1. **users** - Data user (login/register)
2. **categories** - Kategori tasks (Fisik/Spiritual)
3. **daily_content** - Materi harian (Day 1-30)
4. **tasks** - Master list tasks
5. **daily_task** - Link antara content dan tasks
6. **daily_logs** - Progress user per task
7. **user_journals** - Journaling entries
8. **mood_check** - Mood tracking
9. **water_level** - Water intake tracking

## 🎨 Customization

### Menambah Hari Baru (Day 2, 3, dst)
1. Insert data ke tabel `daily_content`
2. Insert tasks ke tabel `tasks` (jika perlu)
3. Link tasks ke content via tabel `daily_task`
4. Buat halaman baru atau modifikasi index.php

### Mengubah Warna
Edit variabel CSS di `index.php`:
```css
:root {
    --primary-sage: #8A9A5B;        /* Warna utama */
    --secondary-terracotta: #E2725B; /* Warna sekunder */
    --highlight-gold: #E1AD01;       /* Warna highlight */
}
```

## 📱 Responsive Design

Website sudah responsive dan dapat diakses via:
- Desktop/Laptop
- Tablet
- Mobile phones

## 🔐 Security Notes

- Password di-hash menggunakan `password_hash()` PHP
- Session management untuk login
- Prepared statements untuk prevent SQL injection
- Input validation di semua form

## 📞 Support

Jika ada pertanyaan atau issue:
1. Cek file `database.sql` sudah diimport dengan benar
2. Cek koneksi database di `config.php`
3. Lihat Console browser (F12) untuk error JavaScript
4. Cek error log PHP di Laragon

---

**Happy Coding & Ramadhan Mubarak! 🌙✨**