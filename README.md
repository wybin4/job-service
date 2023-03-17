# JOBSERVICE
Сервис поиска работы и подбора персонала, ориентированный на студентов. Предлагает ряд функций, важных как для студентов-соискателей, так и для работодателей.

Платформа позволяет соискателям добавлять резюме, искать вакансии, используя расширенные фильтры, отслеживать прогресс по трудоустройству. 

Платформа позволяет работодателям размещать вакансии, просматривать заявки кандидатов и управлять процессом найма от начала до конца.

Помимо базовых функций, сервис предоставляет систему рекомендаций, основанную на рейтинге и предпочтениях.

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

## Детали
В системе есть четыре личных кабинета - администратора, представителя университета, работодателя и студента.

Функция самостоятельной регистрации любым из действующих лиц не предусмотрена сообразно поставленной задаче - верифицировать действующие лица и их доступ к системе вне сервиса.

Личный кабинет администратора предоставляет следующие функции:
- Регистрация представителей учебных заведений
- Регистрация работодателей
- Редактирование справочники сфер деятельности, профессий и так далее
- Просмотр статистики по сервису в целом и по университетам

В личном кабинете представителя университета были реализованы следующие функции:
- Регистрация студента
- Регистрация студентов списком в xslx-формате
- Просмотр статистики по зарегестрированным студентам

В личном кабинете работодателя были реализованы следующие функции:
- Добавление, редактирвание и архивация вакансий
- Подбор резюме по вакансиям
- Поиск и фильтрация резюме
- Отправка приглашений на собеседования и отслеживание их статуса
- Выставление оценок нанятым студентам

В личном кабинете студента были реализованы следующие функции:
- Добавление, редактирвание и архивация резюме
- Подбор вакансий по резюме
- Поиск и фильтрация вакансий
- Отправка откликов и отслеживание их статуса
- Выставление оценок работодателям во время работы у них

## Материалы
- [UseCase](https://drive.google.com/file/d/1wW9Du9CE2hkdw6p7I7h_5B0-WB8PWS26/view?usp=sharing), несколько уровней детализации
- [Диаграмма классов](https://drive.google.com/file/d/1FJNAPDURpdWECHmWyTDBPteH6_oAPt7W/view?usp=sharing)
- SADT, DFD, ERD
- Часть дизайна в [Figma](https://www.figma.com/file/hJZ0JDXeuobYIYmFKmvt2t/%D0%B2%D0%B5%D0%B1?node-id=0-1&t=RXojOQLTWAAHdUk4-0)
