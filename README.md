# Sales Tracker

Aplikasi manajemen leads dan tim sales berbasis web, dibangun dengan Laravel + React (Inertia.js).

## Tech Stack

- **Backend**: Laravel 11 (PHP ^8.2)
- **Frontend**: React 18 + Inertia.js + Tailwind CSS
- **Build Tool**: Vite
- **Database**: MySQL (production), SQLite (local dev)
- **Auth & Roles**: Laravel Breeze + Spatie Permission

## Fitur Utama

- Manajemen leads dengan status, produk, dan tipe lead
- Hierarki user: Admin → Manager → Supervisor → Support → Mitra
- Upline change request dengan approval
- Filter & grouping data per cabang
- Audit log untuk setiap aksi penting
- Export data leads

## Roles

| Role | Akses |
|---|---|
| Admin | Full access |
| Manager | Manajemen user & leads per cabang |
| Supervisor | Supervisi support & mitra |
| Support | Input & kelola leads |
| Mitra | Input leads sendiri |

## Instalasi Lokal

```bash
git clone https://github.com/okkyaditya/leadgen.git
cd leadgen

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

# Setup DB di .env, lalu:
php artisan migrate --seed

php artisan serve
```

Buka `http://localhost:8000`. Default admin: `admin@leadstracker.com` / `password`.

## Dummy Data

Untuk generate data dummy (100 user + 200 leads):

```bash
php artisan db:seed --class=DummyUserSeeder --force
php artisan db:seed --class=DummyLeadSeeder --force
```
