# LUMEN prestasoft api

## Usage Local

-   `git clone https://github.com/ndiecodes/lumen-auth-example.git auth-api`
-   `cd auth-api`
-   `composer install`
-   `php artisan jwt:secret`
-   `php artisan migrate`
-   `php -S localhost:8000 -t public`


## Usage Docker

-   `docker-compose build`
-   `docker-compose up -d`

## Test curl

- ``` curl --location 'localhost:8000/auth/login' \
     --header 'Content-Type: application/json' \
     --data-raw '{"email":"admin@admin.com", "password":"password"}'
     ```