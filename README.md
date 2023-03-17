## how to run the code
- git clone https://github.com/wybin4/job-service.git
- cp .env.example `.env`
- open .env and update DB_DATABASE (database details)
- run : `composer install`
- run : `php artisan migrate:fresh --seed`
- run : `php artisan serve`

