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

## Instalacija

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# konfigurisi DB_DATABASE, DB_USERNAME, DB_PASSWORD u .env
php artisan migrate
npm run build
php artisan serve
```

Aplikacija se otvara na http://127.0.0.1:8000.
