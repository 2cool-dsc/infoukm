
# InfoUKM - Portal Informasi Kegiatan Mahasiswa

InfoUKM adalah website portal resmi untuk menampilkan berbagai kegiatan dan event dari Unit Kegiatan Mahasiswa (UKM) di universitas. 
Website ini menyediakan fitur pencarian event secara real-time, kategori event (upcoming & ongoing), serta halaman detail event.

## 🚀 Fitur Utama

- **Pencarian Event Real-time (AJAX)**  
  Pengguna dapat mencari event atau UKM tanpa reload halaman.

- **Kategori Event**
  - *Upcoming Events*: Event yang akan datang.
  - *Ongoing Events*: Event yang sedang berlangsung.

- **Halaman Detail Event**  
  Informasi lengkap mengenai sebuah event, termasuk banner, waktu, lokasi, dan penyelenggara.

- **Responsive Design**  
  Tampilan yang menyesuaikan dengan berbagai ukuran layar (desktop, tablet, mobile).

- **Animasi Halus**  
  Elemen muncul dengan transisi dari bawah ke atas + efek fade-in.

- **API WAHA (broadcast message)**  
  Jika UKM menambahkan Event, otomatis akan terkirim sebuat format chat event melalui whatsapp ke suatu grup yang di inginkan  

## 🛠️ Teknologi yang Digunakan

- **Frontend:** HTML5, CSS3 (Vanilla CSS), JavaScript (Vanilla JS)
- **Backend:** PHP (Native)
- **Database:** MySQL
- **Server:** Apache (XAMPP/Laragon saat development)

## 📂 Struktur Folder

```
InfoUKM/
│── uploads/              # Folder untuk menyimpan file banner dan logo UKM
│── styles/               # Folder untuk menyimpan file css
│── koneksi.php           # Koneksi database
│── index.php             # Halaman utama (upcoming & ongoing events)
│── search_event.php      # Endpoint pencarian AJAX
│── event_detail.php      # Halaman detail event
│── login.php             # Halaman login admin/UKM
│── dashboard_admin.php   # Halaman dashboard untuk admin
│── dashboard_ukm.php     # Halaman dashboard untuk UKM
│── tambah_akun.php       # Halaman untuk menambah akun UKM
│── tambah_event.php      # Halaman untuk menambah event UKM
│── edit_akun.php         # Halaman untuk meng-edit akun UKM
│── edit_event.php        # Halaman untuk meng-edit event UKM
│── login_proses.php      # Menyimpan Alur proses login
│── logout.php            # Menyimpan Alur penghapusan Session
│── README.md             # Dokumentasi proyek
```

## ⚙️ Instalasi di Lokal

1. **Clone repository**
   ```bash
   git clone https://github.com/2cool-dsc/infoukm.git
   cd infoukm
   ```

2. **Import Database**
   - Buat database di MySQL, misalnya `infoukm`.
   - Import file SQL yang ada di file `infoukm.sql`.

3. **Konfigurasi Koneksi**
   Edit file `koneksi.php`:
   ```php
   $conn = new mysqli("localhost", "root", "", "infoukm");
   ```

4. **Jalankan Server Lokal**
   - Gunakan XAMPP atau Laragon.
   - Pastikan Apache & MySQL aktif.
   - Akses melalui browser: `http://localhost/infoukm`.

## 🌐 Deployment

- Untuk hosting gratis dengan database MySQL, dapat menggunakan:
  - [InfinityFree](https://www.infinityfree.net/)

> **Catatan:** API WAHA (WhatsApp API) yang digunakan pada beberapa fitur tidak dapat dijalankan di hosting gratis biasa karena memerlukan server yang aktif 24/7.

## 📜 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---
Dibuat dengan oleh Team 2cool
