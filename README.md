# Sistem Inventory Alat Medis & Tugas CRUD SIMRS

Proyek Tugas Praktikum Pemrograman Web dan Sistem Informasi Manajemen Rumah Sakit (SIMRS) berbasis PHP dan database MySQL / MariaDB.

---

## 👨‍🎓 Identitas Mahasiswa
* **Nama**: Syuja Ridhwan Fairuz Zahran
* **NIM**: 1202405030
* **Program Studi**: Teknik Elektromedik
* **Institusi**: Universitas Muhammadiyah PKU Surakarta

---

## 📁 Struktur Repository

Repository ini mencakup dua versi pengerjaan tugas:

```text
inventory-alat/
├── index.php                 # Halaman portal utama tugas
├── database_simrs.sql        # Export database MySQL siap import
├── crud/                     # Versi 1: Tugas CRUD Dasar (PHP Native & MySQL)
│   ├── config.php            # Koneksi database
│   ├── index.php             # Menampilkan daftar alat (Read)
│   ├── add.php               # Form tambah data alat (Create)
│   ├── edit.php              # Form perbarui data alat (Update)
│   └── delete.php            # Skrip hapus data alat (Delete)
└── inventory_alat/           # Versi 2: Sistem Inventory Lengkap (Full UI & Security)
    ├── config.php            # Koneksi database MySQL
    ├── auth_check.php        # Middleware proteksi autentikasi & pencatat log
    ├── login.php             # Halaman login modern (Tema Biru Langit & Putih Lembut)
    ├── logout.php            # Skrip logout & pembersih sesi
    ├── log_akses.php         # Monitoring riwayat siapa saja yang mengakses sistem
    ├── index.php             # Dashboard inventaris, filter lokasi, pencarian & tabel
    ├── add.php               # Form input alat baru dengan validasi
    ├── edit.php              # Form edit alat medis
    ├── delete.php            # Hapus alat medis dengan konfirmasi
    ├── header.php            # Komponen navbar & sidebar navigasi
    ├── footer.php            # Komponen footer & library script
    └── assets/               # File CSS (Tema Sky Blue), Bootstrap 5, Icons, JS
```

---

## ⚙️ Fitur-Fitur Utama

### 1. CRUD Master Data Alat Medis
* **Create**: Menambahkan peralatan medis baru (Nama Alat, Tahun Pengadaan, Merek/Pabrikan, Lokasi/Ruang RS).
* **Read**: Menampilkan seluruh data dengan kode unik otomatis (`#ALT-0001`), badge tahun, dan badge lokasi ruangan.
* **Update**: Mengubah spesifikasi teknis dan perolehan alat.
* **Delete**: Menghapus data alat dengan dialog konfirmasi keamanan browser.

### 2. Autentikasi & Keamanan Sesi
* Proteksi login berbasis session PHP.
* Dukungan password hashing MD5 dan BCRYPT.

### 3. Monitoring & Audit Trail (`log_akses`)
* Mencatat otomatis **siapa saja yang mengakses sistem**, meliputi:
  * Waktu dan tanggal akses.
  * Username dan Nama Lengkap pengguna.
  * Aktivitas yang dilakukan (*Login, Logout, Tambah Data, Ubah Data, Hapus Data, atau Percobaan Login Gagal*).
  * Alamat IP klien dan User-Agent perangkat / browser.

### 4. Tampilan Antarmuka Modern
* Skema warna **Biru Langit (Sky Blue)** dan **Putih Lembut (Soft Off-White)** yang nyaman di mata.
* Desain responsif berbasis Bootstrap 5 dan Bootstrap Icons.
* Fitur pencarian instan dan filter cepat per ruangan (ICU, UGD, OK, POLI, LAB, dll.).

---

## 🗄️ Struktur Database (`simrs`)

File skema dan data awal tersedia di [`database_simrs.sql`](database_simrs.sql):

1. **Tabel `alat`**:
   * `id`: `INT(11) AUTO_INCREMENT PRIMARY KEY`
   * `nama_alat`: `VARCHAR(100)`
   * `tahun`: `YEAR(4)`
   * `merek`: `VARCHAR(60)`
   * `lokasi`: `VARCHAR(10)`

2. **Tabel `users`**:
   * `id`: `INT AUTO_INCREMENT PRIMARY KEY`
   * `username`: `VARCHAR(50) UNIQUE`
   * `password`: `VARCHAR(255)`
   * `nama_lengkap`: `VARCHAR(100)`
   * `role`: `VARCHAR(50)`
   * `terakhir_login`: `DATETIME`

3. **Tabel `log_akses`**:
   * `id`: `INT AUTO_INCREMENT PRIMARY KEY`
   * `user_id`: `INT`
   * `username`: `VARCHAR(50)`
   * `nama_lengkap`: `VARCHAR(100)`
   * `aktivitas`: `VARCHAR(100)`
   * `ip_address`: `VARCHAR(45)`
   * `user_agent`: `TEXT`
   * `waktu`: `DATETIME`

---

## 🔑 Akun Demo Login

| Username | Password | Nama Lengkap | Role |
| :--- | :--- | :--- | :--- |
| `admin` | `admin123` | Administrator SIMRS | Super Admin |
| `syuja` | `1202405030` | Syuja Ridhwan F. Z. | Teknisi Elektromedik |
| `elektromedik` | `123456` | Staf Elektromedik RS | Teknisi Elektromedik |

---

## 🚀 Panduan Menjalankan di Komputer Lokal

1. Pastikan **XAMPP** sudah terinstal (Apache dan MySQL aktif).
2. Clone repository ini ke dalam folder `htdocs`:
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/f4ayruze/inventory-alat.git
   ```
3. Buka browser dan akses phpMyAdmin: `http://localhost/phpmyadmin/`.
4. Buat database baru bernama **`simrs`**.
5. Import file `database_simrs.sql` ke dalam database `simrs`.
6. Buka web di browser:
   * **Portal Tugas**: `http://localhost/inventory-alat/`
   * **Sistem Inventory Lengkap**: `http://localhost/inventory-alat/inventory_alat/login.php`
   * **Versi CRUD Dasar**: `http://localhost/inventory-alat/crud/`
