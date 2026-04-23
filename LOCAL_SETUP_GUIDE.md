# 📖 Panduan Lengkap Instalasi & Eksekusi EduForum (Local Development)

Panduan ini dibuat secara rinci agar Anda (atau rekan setim Anda) tidak kebingungan saat pertama kali melakukan *setup* project EduForum dari nol, atau saat ingin me-*running* server lokal setiap harinya.

Project EduForum cukup kompleks karena menggunakan arsitektur **Real-time WebSockets (Reverb)**, **Background Jobs (Queues)**, **Google Auth & API**, serta layanan **Email**.

---

## ⚙️ TAHAP 1: Persyaratan Sistem (Prerequisites)
Pastikan komputer/laptop Anda sudah ter-install aplikasi berikut:
- **PHP** (Minimal versi 8.2 atau lebih baru)
- **Composer** (Package manager untuk PHP)
- **Node.js & npm** (Minimal v16+)
- **MySQL / XAMPP** (Untuk Database)
- **Cloudflared CLI** (Untuk mengakses URL lokal menjadi Publik secara aman/HTTPS).

---

## 🛠️ TAHAP 2: Setup Awal (Hanya Dilakukan 1x Saat Pertama Clone)

Jika Anda baru saja mendapatkan *source code* ini dari GitHub atau zip file, ikuti langkah berikut secara berurutan:

### 1. Buka Terminal di Folder Root Project (`EduForum/`)
Jalankan komando ini untuk mengunduh semua sistem inti Laravel dan dependensi _frontend_:
```bash
composer install
npm install
```

### 2. Atur File Konfigurasi Latar (.env)
Aplikasi membutuhkan file konfigurasi rahasia.
1. Copy file `.env.example` -> lalu *Rename* (ubah nama) hasil copyannya menjadi `.env` (pastikan ada titik di depannya).
2. Jalankan perintah ini untuk membuat kunci keamanan aplikasi:
   ```bash
   php artisan key:generate
   ```

### 3. Isi Detail Penting di File `.env`
Buka file `.env` di text editor (seperti VSCode) dan perhatikan baris-baris ini:

a. **Database Configuration**
Pastikan aplikasi XAMPP/MySQL Anda sudah hidup, dan buatlah database kosong bernama `eduforum`. Lalu samakan di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eduforum
DB_USERNAME=root
DB_PASSWORD=
```

b. **Email SMTP (Untuk Notifikasi)**
Jika Anda menggunakan akun Gmail, isi bagian ini (Username diisi email Anda, password diisi "Google App Password" khusus):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=nama_anda@gmail.com
MAIL_PASSWORD=app_password_anda_di_sini
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@eduforum.app
MAIL_FROM_NAME="EduForum"
```

c. **Google Auth & Calendar Integration**
Isi kode Rahasia dari Google Cloud Console Anda:
```env
GOOGLE_CLIENT_ID=isi_client_id_anda
GOOGLE_CLIENT_SECRET=isi_client_secret_anda
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```
*(Catatan Penting Google Redirect: Jika Anda menggunakan Cloudflare Tunnel untuk tes API lintas perangkat, ubah `http://localhost:8000` menjadi link Cloudflare Anda, contoh: `https://abcd.trycloudflare.com/auth/google/callback`. Anda juga harus mengubah link ini di Dashboard Google Cloud Console).*

d. **Reverb (Chat Real-time)**
Jalankan perintah ini di terminal agar Laravel Reverb mengisi kodenya secara otomatis:
```bash
php artisan reverb:install
```
*(Apabila ditanya untuk memodifikasi konfigurasi `broadcasting`, ketik `Yes/Y`).*

### 4. Eksekusi Database Migrations & Seeding
Jalankan perintah ini untuk membangun tabel database lengkap dari awal beserta data dummy yang diperlukan.
```bash
php artisan migrate:fresh --seed
```

### 5. Kompilasi Aset Desain (Tailwind & Alpine)
```bash
npm run build
```

---

## 🚀 TAHAP 3: Menjalankan Project Sehari-hari (Wajib Dilakukan!)

Karena sistem EduForum bersifat asinkronus (memiliki banyak tugas *di luar* server web biasa), Anda diwajibkan membuka **4 buah Jendela Terminal (CMD/Powershell)** secara terpisah secara bersamaan setiap kali ingin *mempraktikkan* kodingan Anda. 

Buka 4 tab terminal, dan arahkan semuanya ke folder proyek. Lalu jalankan perintah berikut, 1 perintah di tiap-tiap jendela.

### 🔴 Terminal 1: Menghidupkan Website
```bash
php artisan serve
```
*Tugas: Menjalankan kerangka web Laravel di `http://127.0.0.1:8000`.*

### 🟢 Terminal 2: Mesin WebSocket (Reverb)
```bash
php artisan reverb:start
```
*Tugas: Menangkap pesan 'Socket' agar UI Chat bisa hidup dan langsung muncul di layar mahasiswa/dosen seketika (real-time) tanpa perlu refresh browser.*

### 🔵 Terminal 3: Layanan Antrian (Queue Worker)
```bash
php artisan queue:listen
```
*Tugas: Mengirim Email dan mengirim notifikasi ke Google Calendar API. Jika Terminal 3 mati/tidak dijalankan, aplikasi akan terlihat sukses, tapi Email TIDAK AKAN PERNAH SESAMPAINYA!*

### 🟡 Terminal 4: Jembatan Eksternal (Cloudflare Tunnel)
```bash
cloudflared tunnel --url http://127.0.0.1:8000
```
*Tugas: Memberikan Anda alamat URL publik berlapis `https` secara gratis seperti `https://xxx-yyy.trycloudflare.com`.*
- *Mengapa PENTING? Karena fitur Autentikasi Google dan API Eksternal seringkali menolak permintaan yang berasal dari IP Lokal yang tidak diregistrasi/tidak ber-HTTPS.*
- *Setelah Terminal 4 jalan, amati URL akhirnya, dan buka URL tersebut di Browser Anda untuk menggunakan EduForum (jangan pakai localhost:8000).* 
- *Jika Anda pakai akses URL Cloudflare, pastikan kembali file `.env` variabel `APP_URL` Anda di-update dengan link cloudflare tersebut, agar Link pada 'tombol kirim Email' diarahkan secara benar.*

---

## 💡 TAHAP 4: Troubeshooting & Bantuan Kritis

*   **Pesan Chat tidak muncul sama sekali secara *Real-time*?**
    *   Pastikan Terminal 2 (`reverb:start`) berjalan dan tidak *Error*.
    *   Pastikan file `.env` memiliki baris `BROADCAST_CONNECTION=reverb`. Jika Anda edit `.env`, Anda **wajib** merestart Terminal 2 dan 3.

*   **Email peringatan dosen tidak terkirim?**
    *   Pastikan konfigurasi SMTP di `.env` (tahap 2.b) sudah diisi.
    *   Pastikan Terminal 3 (`queue:listen`) berjalan. Coba lihat terminal 3, bila ada pesan Error merah, artinya *crednetials* email/google Auth Anda tidak valid.

*   **Layout desain atau efek kaca UI rusak (Tailwind)?**
    *   Setiap kali Anda selesai menulis kodingan UI/view baru, ingat untuk selalu menjalankan perintah ini di terminal:
    ```bash
    npm run build
    ```
    *(Atau biar praktis bisa menggunakan perintah `npm run dev` di tab terminal ke-5, agar desain terupdate otomatis setiap kali File di-Save).*

---
**SELESAI.** Anda kini sudah memegang kendali penuh atas keseluruhan infrastruktur EduForum. Selamat mengembangkan aplikasi!
