<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<x-university-layout>
    <div class="row" style="background-color:white;height:100vh;padding:40px;">
        <div class="col-md-3">
            <div class="col-title font-bold mb-2">Студенты</div>
            <a class="row btn-area" href="{{ url('/university/add-one') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Студент</div>
                    <div class="btn-text">Добавьте одного студента</div>
                </div>
            </a>
            <a class="row btn-area" href="{{ url('/university/add-many') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Студенты</div>
                    <div class="btn-text">Добавьте студентов списком</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="col-title font-bold mb-2">Профиль</div>
            <a class="row btn-area">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Профиль</div>
                    <div class="btn-text">Редактировать профиль</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="col-title font-bold mb-2">Другое</div>
            <a class="row btn-area" href="{{ url('/university/statistics') }}">
                <div class="col-md-auto btn-i">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div class="col-md-auto">
                    <div class="btn-title">Статистика</div>
                    <div class="btn-text">Просмотр</div>
                </div>
            </a>
        </div>
    </div>
</x-university-layout>
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