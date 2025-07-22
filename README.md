<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Drone Service API

API backend untuk website jasa drone (Laravel + Cloudinary).

## Base URL

```
http://localhost:8000/api/
```

---

## Endpoints

### 1. Paket Layanan

-   **GET** `/packages` — List semua paket
-   **POST** `/packages` — Tambah paket baru
-   **GET** `/packages/{id}` — Detail paket
-   **PUT** `/packages/{id}` — Update paket
-   **DELETE** `/packages/{id}` — Hapus paket

#### Contoh POST /packages

```json
{
    "name": "Premium",
    "description": "Foto + video menggunakan kamera + drone",
    "price": 2000000
}
```

---

### 2. Pesanan

-   **GET** `/orders` — List pesanan
-   **POST** `/orders` — Tambah pesanan
-   **GET** `/orders/{id}` — Detail pesanan
-   **PUT** `/orders/{id}` — Update pesanan
-   **DELETE** `/orders/{id}` — Hapus pesanan

#### Contoh POST /orders

```json
{
    "package_id": 1,
    "name": "Budi",
    "contact": "08123456789",
    "date": "2024-07-10 10:00:00",
    "location": "Jakarta",
    "notes": "Butuh dokumentasi aerial",
    "total_price": 2000000
}
```

---

### 3. Portofolio

-   **GET** `/portfolios` — List portofolio
-   **POST** `/portfolios` — Tambah portofolio
-   **GET** `/portfolios/{id}` — Detail portofolio
-   **PUT** `/portfolios/{id}` — Update portofolio
-   **DELETE** `/portfolios/{id}` — Hapus portofolio

#### Contoh POST /portfolios

```json
{
    "title": "Proyek Wedding Aerial",
    "description": "Dokumentasi pernikahan dari udara."
}
```

---

### 4. Upload Gambar Portofolio (Cloudinary)

-   **POST** `/portfolio-images`
-   **Body:** form-data
    -   `portfolio_id`: (ID portofolio)
    -   `image`: (type: File, gambar max 5MB)

#### Contoh Response

```json
{
  "id": 1,
  "portfolio_id": 1,
  "image_url": "https://res.cloudinary.com/xxx/image/upload/v...jpg",
  ...
}
```

---

### 5. Upload Video Portofolio (Cloudinary)

-   **POST** `/portfolio-videos`
-   **Body:** form-data
    -   `portfolio_id`: (ID portofolio)
    -   `video`: (type: File, video max 50MB)

#### Contoh Response

```json
{
  "id": 1,
  "portfolio_id": 1,
  "video_url": "https://res.cloudinary.com/xxx/video/upload/v...mp4",
  ...
}
```

---

## Catatan

-   Semua response dalam format JSON.
-   Untuk upload file, gunakan Postman/Insomnia tab **form-data**.
-   Endpoint lain (update, delete) mengikuti standar RESTful Laravel.

---

## Integrasi Frontend

Frontend (misal: Vue) dapat langsung konsumsi endpoint di atas untuk operasi CRUD dan upload file.

## Deploy ke Railway (Docker)

1. Pastikan file `Dockerfile` sudah ada di root project.
2. Push project ke repository GitHub.
3. Di Railway, buat project baru dan hubungkan ke repository ini.
4. Railway akan otomatis mendeteksi Dockerfile dan membuild container.
5. Set environment variable database (misal: DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD) di Railway sesuai kebutuhan.
6. Jalankan migrasi database di Railway console:
    ```
    php artisan migrate --force
    ```
7. Aplikasi Laravel akan berjalan di port 8000 (default Railway).

Login admin:

-   Email: admin@flywithus.com
-   Password: admin123
