docker run --rm \
-u "$(id -u):$(id -g)" \
-v "$(pwd):/var/www/html" \
-w /var/www/html \
laravelsail/php83-composer:latest \
composer install --ignore-platform-reqs


alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'


sail up-d
php artisan octane:stop
sail artisan octane:start --watch --port=80 --admin-port=2020
su - laravel -s /bin/bash


pgsql
docker exec -it postgres-container bash
su postgres
psql

docker exec -it frank1-pgsql-1 psql -U postgres

sail down -v
sail build --no-cache
sail up -d
change .env

#abura
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=***
DB_USERNAME=****
DB_PASSWORD=******

sail artisan optimize:clear

inertia

install npm
apt install vite
npm run build

db

sail artisan migrate
sail artisan db:seed
sail artisan db:seed UserSeeder
