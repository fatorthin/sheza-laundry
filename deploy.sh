#!/bin/bash

# 1. Pindah ke direktori proyek
cd /DATA/AppData/sheza-laundry

# 2. Tarik kode terbaru dari GitHub branch 'main'
git pull origin main

# 3. Update paket PHP (jika ada library baru di composer)
docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader

# 4. Jalankan migrasi database otomatis
docker compose exec -T app php artisan migrate --force

# 5. Bersihkan cache Laravel agar perubahan langsung terbaca
docker compose exec -T app php artisan optimize:clear
