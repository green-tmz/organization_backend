cp .env.local .env
cp ./deploy/docker-compose/local.docker-compose.yml docker-compose.yml
docker compose build
docker compose run --rm app composer install
docker compose run --rm app php artisan storage:link
docker compose up -d
sleep 3
docker compose exec app php artisan key:generate
docker compose exec pgsql psql -U organization -c "CREATE EXTENSION postgis;"
docker compose exec pgsql psql -U organization -c "CREATE DATABASE organization_test;"
docker compose exec app php artisan migrate --seed
