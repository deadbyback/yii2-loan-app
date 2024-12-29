#!/bin/sh
set -e

until nc -z -v -w30 postgres 5432
do
  echo "Waiting for PostgreSQL..."
  sleep 1
done
echo "PostgreSQL is up and running"

composer install

php init --env=Development --overwrite=n

php yii migrate --interactive=0

php yii rbac/init

php yii swagger/generate

crond -b

docker-php-entrypoint php-fpm