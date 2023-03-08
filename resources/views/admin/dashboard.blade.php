<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<x-admin-layout>
    <div class="row" style="background-color:white;height:100vh;padding:40px;">
        <div class="col-md-3">
            <div class="col-title font-bold mb-2">Пользователи</div>
            <a class="row btn-area" href="{{ url('/admin/add-one-university') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Образовательное учереждение</div>
                    <div class="btn-text">Добавьте ВУЗ</div>
                </div>
            </a>
            <a class="row btn-area" href="{{ url('/admin/add-one-employer') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Работодатель</div>
                    <div class="btn-text">Добавьте компанию</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="col-title font-bold mb-2">Справочники</div>
            <a class="row btn-area" href="{{ url('/admin/skills') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Навыки</div>
                    <div class="btn-text">Действия над навыками</div>
                </div>
            </a>
            <a class="row btn-area" href="{{ url('/admin/spheres') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-maximize"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Сферы деятельности</div>
                    <div class="btn-text">Действия над сферами</div>
                </div>
            </a>
            <a class="row btn-area" href="{{ url('/admin/subspheres') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Области деятельности</div>
                    <div class="btn-text">Действия над областями</div>
                </div>
            </a>
            <a class="row btn-area" href="{{ url('/admin/professions') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Профессии</div>
                    <div class="btn-text">Действия над профессиями</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="col-title font-bold mb-2">Другое</div>
            <a class="row btn-area" href="{{ url('/admin/statistics') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-signal"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Статистика</div>
                    <div class="btn-text">Просмотр</div>
                </div>
            </a>
            <a class="row btn-area" href="{{ url('/admin/university-statistics') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Рейтинг учебных заведений</div>
                    <div class="btn-text">Просмотр</div>
                </div>
            </a>
        </div>
    </div>
</x-admin-layout>
<style>
    html {
        overflow-x: hidden;
    }

    .btn-area {
        cursor: pointer;
        padding: 15px 5px;
        margin-bottom: 5px;
        border-radius: 20px;
    }

    .btn-area:hover {
        background-color: var(--dot-color);
        transition: 0.8s;
    }

    .btn-i {
        padding: 9px;
        color: var(--link-hover-color);
        background-color: var(--dot-color);
        border-radius: 20px;
        margin-left: 10px;
        width: 40px;
        height: 40px;
        display: table-cell;
        text-align: center;
    }

    .btn-title {
        font-weight: 600;
    }

    .btn-text {
        font-size: 14px;
        color: grey;
        margin-top: -5px;
    }
</style>

</html>