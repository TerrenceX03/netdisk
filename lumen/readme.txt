Documentation

In this demo project, basic database CRUD operations and file operations were achieved. 

For more information about installation and configuration, please check Lumen online documentation(https://lumen.laravel.com/docs/5.7)

For more information about database migration, please check Laravel documentation(https://laravel.com/docs/5.7/migrations)

--------------------------------------------------------------------------------------
Server Requirements:
PHP >= 7.1.3
OpenSSL PHP Extension
PDO PHP Extension
Mbstring PHP Extension
--------------------------------------------------------------------------------------
Installation

Download the Lumen installer using compomser(https://getcomposer.org) command:
    composer global require "laravel/lumen-installer"
(Make sure to place the ~/.composer/vendor/bin directory in your PATH so the lumen executable can be located by your system.)

Install Lumen project using the command:
    Lumen new [project name]
Don't forget to configure the local environment. Configuration options are stored in the .env file.

To use the database, we should activate Eloquent and Facades, just uncomment the line //$app->withFacades() and //$app->withEloquent() in /bootstrap/app.php 
--------------------------------------------------------------------------------------
Setup Database, Models and migrations

Create a table migration using the command:
    php artisan make:migration [table name]
The new migration will be placed in database/migrations. We should add attributes into the migration file.

Use the command below to run the migration:
    php artisan migrate

To create a model, create a new class in app/ extends class Model.
--------------------------------------------------------------------------------------
Routes and Controllers

Routes in routes/web.php are like this below:
    $router->get($uri, $callback);
    $router->post($uri, $callback);
    $router->put($uri, $callback);
    $router->patch($uri, $callback);
    $router->delete($uri, $callback);
    $router->options($uri, $callback);

The controller functions should be defined in app/Http/Controllers, which can be called in routes.










