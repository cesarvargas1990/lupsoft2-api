# LUPSOFT2 API (Lumen 5.8)

## Levantar en local

```bash
git clone <url-del-repo>
cd lupsoft2-api
cp .env.example .env
composer install
php artisan jwt:secret
php artisan migrate
php -S localhost:8000 -t public
```

## Levantar con Docker

```bash
cp .env.example .env
docker-compose build
docker-compose up -d
docker-compose exec app sh -c "composer install && php artisan migrate --force && php artisan db:seed --force"
```

API en Docker: `http://localhost:8002`

## Comando importante en despliegue (Linux/Produccion)

Si cambias nombres de clases/modelos o relaciones Eloquent, ejecuta:

```bash
composer dump-autoload -o
```

Si aplica, reinicia PHP-FPM/OPcache despues de eso.

## Probar login (curl)

```bash
curl --location 'http://localhost:8002/auth/login' \
  --header 'Content-Type: application/json' \
  --data-raw '{"email":"admin@admin.com","password":"password"}'
```

## Tests (local)

```bash
composer test
```

## Tests (local sin Docker, PHP 7.4)

```bash
/opt/homebrew/opt/php@7.4/bin/php ./vendor/bin/phpunit
```

## Lint (local)

```bash
composer lint
```

## Lint (local sin Docker, PHP 7.4)

```bash
find app bootstrap config database routes tests -name '*.php' -print0 | xargs -0 -n1 /opt/homebrew/opt/php@7.4/bin/php -l
```

## Format (local)

```bash
composer format
```

## Format (local sin Docker, PHP 7.4)

```bash
for d in app bootstrap config database routes tests; do /opt/homebrew/opt/php@7.4/bin/php vendor/bin/php-cs-fixer fix "$d"; done
```

## PHPUnit en Docker

```bash
docker-compose exec app sh -c "./vendor/bin/phpunit"
```

## Coverage en Docker

```bash
docker-compose exec app sh -c "./vendor/bin/phpunit --coverage-clover coverage.xml"
docker-compose exec app sh -c "./vendor/bin/phpunit --coverage-html coverage-report"
```

## Sonar

```bash
sonar-scanner \
  -Dsonar.projectKey=psoft2 \
  -Dsonar.sources=. \
  -Dsonar.host.url=http://localhost:9000 \
  -Dsonar.login=<token>
```
