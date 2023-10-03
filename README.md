## About

This backend template is used as an example of how to register, verify email, log in, and log out using laravel sanctum.

## How To

- composer install
- create .env file from .env.example file
- php artisan key:generate
- set email smtp config in .env
- set database config in .env
- php artisan migrate
- done

## Main Functions
- Test Connections:
    - URL : [DOMAIN]/public/
    - METHOD: GET

- GET CSRF for POSTMAN:
    - URL : [DOMAIN]/public/token
    - METHOD: GET
    - SET HEADER X-CSRF-TOKEN in POSTMAN using this token to avoid "laravel page expired"

- Register new user:
    - URL : [DOMAIN]/public/register/newregister
    - METHOD : POST
    - FORMDATA:
        - username
        - name
        - email
        - password
        - password_confirmation

- Login user:
    - URL : [DOMAIN]/public/auth/login
    - METHOD : POST
    - FORMDATA:
        - username
        - password

- Logout user:
    - URL : [DOMAIN]/public/auth/logout
    - METHOD : POST

- Test user login token:
    - URL : [DOMAIN]/public/hello
    - METHOD : ANY

## Main Folders

- routes/web.php
- app/Http/Controllers/*
- app/Http/Models/*