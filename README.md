# 🚀 LeadGen Application

LeadGen is a comprehensive Lead Management System built with **Laravel**. It is designed to track leads, manage branch (cabang) operations, and handle hierarchical user relationships (uplines/downlines).

## 🌟 Journey & Features

Aplikasi ini dikembangkan untuk mempermudah proses manajemen calon klien (leads) dengan sistem yang terstruktur. Beberapa fitur utama yang dibangun selama *journey* pembuatan aplikasi ini meliputi:

- **Lead Management**: Sistem pencatatan, penugasan, dan pemantauan prospek (leads) yang terpusat.
- **Sistem Hierarki User (Upline/Downline)**: Dilengkapi dengan fitur `UplineChangeRequest` yang memungkinkan restrukturisasi tim sales atau agent dengan persetujuan (approval) yang rapi.
- **Manajemen Cabang (Branch Filtering)**: Mengelompokkan data berdasarkan Cabang. Kami juga memberikan perhatian khusus pada UI/UX, seperti penyempurnaan dropdown filter panah (filter arrow) agar navigasi antar cabang lebih mulus.
- **Audit Trails**: Setiap tindakan penting dan perubahan data dicatat melalui `AuditLog` untuk memastikan transparansi dan akuntabilitas sistem.

## 🛠️ Tech Stack

- **Backend**: Laravel (PHP)
- **Frontend**: Blade Templates, Tailwind CSS, Vite
- **Database**: MySQL

## 🚀 Getting Started (Cara Install di Local)

Ikuti langkah-langkah berikut untuk menjalankan project ini di komputer lokal Anda:

### Prerequisites
- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL

### Installation

1. **Clone repository ini**
   ```bash
   git clone https://github.com/okkyaditya/leadgen.git
   cd leadgen
   ```

2. **Install dependencies PHP (Backend)**
   ```bash
   composer install
   ```

3. **Install dependencies NPM (Frontend) & Build**
   ```bash
   npm install
   npm run build
   ```

4. **Setup Environment**
   Gandakan file `.env.example` menjadi `.env` lalu generate application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Setup Database**
   Buka file `.env` dan sesuaikan kredensial database Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Migrate & Seed Database**
   ```bash
   php artisan migrate --seed
   ```

7. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   *Buka `http://localhost:8000` di browser Anda.*

## 📦 Deployment
Aplikasi ini sudah dipersiapkan dan dioptimasi untuk di-deploy pada layanan Shared Hosting standar (cPanel) maupun VPS.

---
*Built with ❤️ for better Lead Management.*
