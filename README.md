## how to run the code
- git clone https://github.com/ajdivotf/job-service.git
- cd laravel-9-multi-auth-system
- cp .env.example `.env`
- open .env and update DB_DATABASE (database details)
- run : `composer install`
- run : `php artisan key:generate`
- run : `php artisan migrate:fresh --seed`
- run : `php artisan serve`

