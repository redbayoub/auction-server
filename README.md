# Auction Server

This is the **SERVER** portion of a web auction application for an antique items seller. The application allows users to bid on antique items displayed in the site and admin users to set up items for auction.

> You can get the **FRONT END** portion of the web application from [https://github.com/redbayoub/auction-web.git](https://github.com/redbayoub/auction-web.git).


---

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/9.x/installation#installation)

> Alternative installation is possible without local dependencies relying on [Laravel Sail](#laravel-sail-installation-method).

Clone the repository

    git clone https://github.com/redbayoub/auction-server.git

Switch to the repo folder

    cd auction-server

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Create a symbolic link from public/storage to storage/app/public [The Public Disk](https://laravel.com/docs/9.x/filesystem#the-public-disk)

    php artisan storage:link

Run the database migrations (**Set the database connection in .env before migrating**). See [Environment variables](#environment-variables).

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

## Authentication

Generate a regular user and admin user, run the database seeder

    php artisan db:seed

Regular User Credentials

-   Username: user1
-   Password: user2

Admin User Credentials

-   Username: admin1
-   Password: admin2

## Database Seeding

To seed auction Items in the database run

    php artisan db:seed --class=ItemSeeder

## Testing

To make sure everything is running as expected run

    php artisan test

---

## Laravel Sail Installation method

To install with [Laravel Sail](https://laravel.com/docs/9.x/sail), run following commands:

```
git clone https://github.com/redbayoub/auction-server.git
cd auction-server
composer install
./vendor/bin/sail up
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link
```

You can now access the server at [http://localhost](http://localhost).

---

## Environment variables

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Environment variables can be set in `.env` file

    APP_URL=http://localhost:8000

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_HOST=3306
    DB_DATABASE=auction_app
    DB_USERNAME=root
    DB_PASSWORD=

    SESSION_DRIVER=cookie

    SANCTUM_STATEFUL_DOMAINS=localhost:3000
    SESSION_DOMAIN=localhost

-   APP_URL variable refers to the url of the server application

-   DB_CONNECTION, DB_HOST, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD variables refers to your database configurations

-   SESSION_DRIVER this variable sholuld be set to `cookie` to make sure that the front end application are authenticated

-   SANCTUM_STATEFUL_DOMAINS variable refers to the SPA front end application
