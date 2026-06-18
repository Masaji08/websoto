#!/bin/bash
# Railway deploy hook — jalankan setelah build
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
