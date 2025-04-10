# LUMEN prestasoft api

## Usage Local

-   `git clone https://github.com/ndiecodes/lumen-auth-example.git auth-api`
-   `cd auth-api`
-   `composer install`
-   `php artisan jwt:secret`
-   `php artisan migrate`
-   `php -S localhost:8000 -t public`


## Usage Docker
-   `cp .env.example .env`
-   `docker-compose build`
-   `docker-compose up -d`
-   `docker-compose exec app sh -c "composer install && php artisan migrate --force && true && php artisan db:seed"` -- first run
-   `docker-compose run -d app ` -- run 


## Test curl

- ``` curl --location 'localhost:8000/auth/login' --header 'Content-Type: application/json' --data-raw '{"email":"admin@admin.com", "password":"password"}' ```

## Run Test Unit

- ``` docker-compose exec app sh -c "./vendor/bin/phpunit" ```


## Run Coverage

- ``` docker-compose exec app sh -c "./vendor/bin/phpunit --coverage-clover coverage.xml" ```

## Run Coverage html

``` docker-compose exec app sh -c "./vendor/bin/phpunit --coverage-html coverage-report" ```



## Sonar

``` sonar-scanner \
  -Dsonar.projectKey=psoft2 \
  -Dsonar.sources=. \
  -Dsonar.host.url=http://localhost:9000 \
  -Dsonar.login=sqp_cbb58fffd1e50726a877df78dd122245e708823e ```