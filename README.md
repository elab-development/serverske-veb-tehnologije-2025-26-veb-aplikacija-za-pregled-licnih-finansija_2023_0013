# Licne finansije

Veb aplikacija za pregled licnih finansija. Korisnik unosi prihode i rashode,
kategorizuje ih, postavlja mesecne budzete po kategorijama, prima notifikacije
kada predje limit i pregleda graficke izvestaje sa eksportom u PDF i Excel.

Seminarski rad iz predmeta Serverske veb tehnologije, Fakultet organizacionih
nauka.

## Tehnologije

- Laravel 13 (PHP 8.2+)
- MySQL 8
- Laravel Breeze (Blade stack)
- Tailwind CSS 3 + Alpine.js
- Chart.js (grafici)
- barryvdh/laravel-dompdf (PDF eksport)
- maatwebsite/excel (Excel eksport)
- Laravel Dusk (browser testovi)

## Sistemski preduslovi

- PHP 8.2+
- Composer 2.x
- Node 18+ i npm
- MySQL 8 (XAMPP ili lokalna instalacija)
- Chrome/Chromium (za Dusk testove)

## Instalacija

```bash
# 1. Klonirati repo i instalirati zavisnosti
composer install
npm install

# 2. Kopirati env i generisati app key
cp .env.example .env
php artisan key:generate

# 3. Konfigurisati bazu u .env
# DB_DATABASE=licne_finansije
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Kreirati bazu u MySQL-u (npr. preko phpMyAdmin)
# CREATE DATABASE licne_finansije;
# CREATE DATABASE licne_finansije_test;  -- za Dusk testove

# 5. Pokrenuti migracije i seedere
php artisan migrate:fresh --seed

# 6. Build assets
npm run build

# 7. Pokrenuti server
php artisan serve
```

Aplikacija se otvara na http://127.0.0.1:8000.
