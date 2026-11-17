# Sistem Inventory Alat Medis – SIMRS

**Mata Kuliah:** Pemrograman Web  
**Institusi:** Universitas Muhammadiyah PKU Surakarta  

---

## Fitur

| Modul | Deskripsi |
|---|---|
| **Sign Up** | Registrasi akun baru dengan validasi & hashing BCrypt |
| **Sign In** | Login dengan rate-limit 5×/5 mnt, pesan error generik |
| **Dashboard** | Statistik real-time, filter lokasi, pencarian, pagination |
| **Tambah Alat** | Form lengkap + modal Tambah Cepat |
| **Edit Alat** | Prepared statement, CSRF token |
| **Hapus Alat** | POST-only + CSRF, hanya Super Admin |
| **Log Akses** | Audit trail lengkap, hanya Super Admin |
| **CRUD Dasar** | Versi sederhana PHP (tugas), wajib login |

---

## Keamanan yang Diterapkan

- **BCrypt** – semua password di-hash dengan `password_hash(..., PASSWORD_BCRYPT)`
- **Prepared Statement** – semua query database menggunakan `mysqli_prepare()`
- **CSRF Token** – setiap form POST dilindungi token acak 64-hex
- **RBAC** – role *Super Admin* dan *Teknisi Elektromedik* dengan hak berbeda
- **Rate Limiting** – max 5 percobaan login gagal per 5 menit per IP
- **Session Security** – `session_regenerate_id()`, HttpOnly, SameSite=Lax, timeout 2 jam
- **XSS Prevention** – seluruh output menggunakan `htmlspecialchars(ENT_QUOTES, 'UTF-8')`
- **Audit Log** – setiap aktivitas (login, tambah, edit, hapus) dicatat ke `log_akses`

---

## Struktur Database (`simrs`)

```sql
alat        – id, nama_alat, tahun, merek, lokasi
users       – id, nama_lengkap, username, email, password, role, created_at
log_akses   – id, user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu
```

Import file: `database_simrs.sql` (UTF-8, tanpa BOM)

---

## Role & Hak Akses

| Role | Dashboard | Tambah | Edit | Hapus | Log Akses |
|---|---|---|---|---|---|
| Super Admin | ✅ | ✅ | ✅ | ✅ | ✅ |
| Teknisi Elektromedik | ✅ | ✅ | ✅ | ❌ | ❌ |

> Daftar akun baru melalui halaman **Sign Up**. Semua akun baru mendapat role *Teknisi Elektromedik* secara default.

---

## Cara Menjalankan (Lokal)

1. Pastikan XAMPP berjalan (Apache + MySQL)
2. Import `database_simrs.sql` ke phpMyAdmin (database: `simrs`)
3. Salin folder `inventory_alat/` dan `crud/` ke `C:/xampp/htdocs/`
4. Akses via: `http://localhost/inventory-alat/`

---

## Repository

GitHub: [f4ayruze/inventory-alat](https://github.com/f4ayruze/inventory-alat)
