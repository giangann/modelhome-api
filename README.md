# production: https://modelhome.vn

# installation:
1. install package:
       composer install
3. copy template env fiel:
       cp .env.example .env
5. create database, config .env file
6. migration, without seeder
       php artisan migarate
7. generate application key
       php artisan key:generate
8. create the symbolic link
       php artisan storage:link
9. start php server (port 8000)
        php artisan serve
