# laravel-menu

Dynamic Menu generation using Laravel

## Following are the step to configure Dynamic Menu


#### Step 1:copy vendor using composer

    composer require webelightdev/laravel-menu
    
    or
    
    "require": {
       
        "webelightdev/laravel-menu": "dev-master"
    }
    composer update

#### step 2: Copy providers to config/app.php

    'providers' => [
     // ...
        Webelightdev\LaravelMenu\MenuServiceProvider::class,
     // ...

    ]

#### step 3: Run  
	php artisan vendor:publish


#### step 4: Run  
	php artisan migrate

	
#### step 5: create public/uploads folder  and set permission 0777

This packager Required Auth login
if you don't have Auth login 

	php artisan make:auth
    php artisan migrate
    
## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.