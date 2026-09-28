# Product Manager (PHP Native & PDO)

Aplikasi web sederhana untuk manajemen produk yang dibangun menggunakan **PHP Native** dan **MySQL/PDO** tanpa bantuan framework.

## Fitur Aplikasi
- **Create**: Menambah produk baru dengan validasi server-side dan penerapan pola PRG.
- **Read**: Menampilkan daftar produk dengan Flexbox serta fitur pencarian (Search).
- **Update**: Mengubah data produk berdasarkan ID.
- **Delete**: Menghapus produk aman menggunakan POST dan proteksi token CSRF.

## Persyaratan Server
- PHP versi 8.0 atau yang lebih baru
- Database MySQL / MariaDB
- Web Server (Apache/XAMPP)

## Cara Instalasi & Menjalankan
1. Clone atau download repositori ini ke folder `htdocs` server lokal kamu (misal: `C:\xampp\htdocs\Product_Manager`).
2. Buat database baru di phpMyAdmin bernama `store_db`.
3. Import file database yang tersedia di folder `database/store_db.sql`.
4. Buka browser dan akses alamat:
   `http://localhost/Product_Manager/public/index.php`