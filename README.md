# JOBSERVICE
Сервис по поиску работы и подбору персонала, ориентированный на студентов. Предоставлен базовый набор функций для соискателей и работодателей.

А именно:
- Создание резюме и вакансий
- Поиск и фильтрация
- Просмотр и изменение заявок

Кроме базовых функций в сервисе реализована система рекомендаций на основании оценок и предпочтений. 

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


