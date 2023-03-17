# JOBSERVICE
Сервис поиска работы и подбора персонала. Адаптирован к потребностям студентов и предлагает ряд функций, важных как для соискателей, так и для работодателей.

Платформа позволяет соискателям добавлять резюме и искать вакансии, используя расширенные фильтры, отслеживая прогресс по трудоустройству. 

Платформа позволяет работодателям размещать вакансии, просматривать заявки кандидатов и управлять процессом найма от начала до конца.

Помимо базовых функций возможностей, сервис предоставляет систему рекомендаций, основанную на рейтинге и предпочтениях.

## Запуск
- git clone https://github.com/wybin4/job-service.git
- `cd job-service`
- cp .env.example `.env`
- Добавьте DB_DATABASE и DB_PASSWORD в .env
- Добавьте MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD и MAIL_FROM_ADDRESS в .env
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan storage:link`
- `composer install`
- `php artisan migrate:fresh --seed`
- `php artisan serve`


