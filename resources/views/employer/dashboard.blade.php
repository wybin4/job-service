<!DOCTYPE html>
<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="//api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>
</head>
<div id="location-popup"></div>
<div id="choose-location-popup">
    <div class="modal" id="choose-location-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="text-xl text-center">Выбрать город</h2>
                </div>
                <div class="modal-body" style="max-height:150px">
                    <p id="edit-errors"></p>
                    <div class="select-div">
                        <input id="select" class="chosen-value" type="text" value="" autocomplete="off">
                        <ul class="value-list" id="value-list-1">
                            <script>
                                let requestUrl = 'https://raw.githubusercontent.com/pensnarik/russian-cities/master/russian-cities.json';
                                let xhr = new XMLHttpRequest();

                                xhr.open('GET', requestUrl, true);
                                xhr.responseType = 'json';
                                xhr.send()

                                xhr.onload = function() {
                                    let cities = xhr.response;
                                    let list = '';
                                    for (let i = 0; i < cities.length; i++) {
                                        list += '<li class="li-1" value="' + i + 1 + '">' + cities[i].name + ", " + cities[i].subject + '</li>\n';
                                    }
                                    $("#value-list-1").html(list);
                                }
                            </script>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <span type="button" class="span-like-button" id="btn-select-location" data-bs-dismiss="modal">Добавить</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="blurable-content">
    <x-employer-layout>
        <div style="background-color:white !important">
            <input type="hidden" id="is-there-a-location" value=" {{Auth::guard('employer')->user()->location}}" />
            <div class="row first-div">
                <div class="col-md-7">
                    <h1 class="big-text">Начните искать сотрудников</h1>
                    <h1 class="big-text">прямо <span class="big-indigo-text">сейчас</span></h1>
                    <span class="big-indigo-underline"></span>
                    <p id="popular"><span class="text-muted font-bold examples-p">Популярные запросы:</span>
                        <span>
                            @php $i = 1; @endphp
                            @foreach($popular_professions as $pp)
                            @if ($i == count($popular_professions))
                            <a href="/employer/resume-feed?profession_name={{$pp->profession_name}}" class="text-gray-500">{{$pp->profession_name}}</a>
                            @else
                            <a href="/employer/resume-feed?profession_name={{$pp->profession_name}}" class="text-gray-500">{{$pp->profession_name}}, </a>
                            @endif
                            @php $i++; @endphp
                            @endforeach
                        </span>
                    </p>
                </div>
                <div class="col-md-5">
                    <img id="employer_main_img" src="{{ asset('/storage/app_images/employer_main_img.png') }}" alt="employer main image">
                </div>
            </div>
            <div class="text-center how-to">
                <h1 class="medium-text">Как работать с сервисом</h1>
                <p class="text-muted mt-2">Всего за несколько простых шагов вы сможете подобрать сотрудников!</p>
                <img id="steps_img" src="{{ asset('/storage/app_images/steps.png') }}" alt="steps img">
                <div class="row info-row">
                    <div class="col-md-auto info-col">
                        <div class="little-header-text">Разместите вакансию</div>
                        <div class="text-muted">Постарайтесь указать актуальную информацию о компании и требованиях к соискателю</div>
                    </div>
                    <div class="col-md-auto info-col">
                        <div class="little-header-text">Просмотрите доступные резюме</div>
                        <div class="text-muted">В нашем сервисе есть возможность подобрать резюме по вакансии</div>
                    </div>
                    <div class="col-md-auto info-col">
                        <div class="little-header-text">Предложите соискателям пройти собеседование</div>
                        <div class="text-muted">И соискатели получат письмо на почту с вашим предложением</div>
                    </div>
                </div>
                @if (!Auth::User()->active_vacancy)
                <button class="button start-btn"><a href="/employer/create-vacancy">Начать</a></button>
                @else
                <button class="button start-btn"><a href="/employer/resume-feed">Начать</a></button>
                @endif

            </div>
            <div class="text-center find-by-sphere">
                <h1 class="medium-text">Поиск по отрасли</h1>
                <p class="text-muted mt-2">Найдите сотрудников, подходящих именно вам</p>
            </div>
            <div class="carousel">
                @php $i = 0; @endphp
                @foreach($spheres as $sphere)
                <div class="sphere-box">
                    <div class="row carousel-row">
                        <div class="col-md-auto">
                            <img class="carousel-img" src="{{ asset('/storage/app_images/'.$sphere->sphere_of_activity_name.'.png') }}" />
                        </div>
                        <div class="col-md-auto">
                            <div class="sphere-title font-bold" id="sphere-title-{{$i}}"><a href="/employer/resume-feed?sphere={{$sphere->id}}">{{$sphere->sphere_of_activity_name}}</a></div>
                            <script>
                                textLength = $("#sphere-title-{{$i}}").text().length;
                                if (textLength > 15) {
                                    $("#sphere-title-{{$i}}").css('font-size', Math.sqrt(3100 / textLength) + 'px');
                                }
                            </script>
                            @if(count($spheres_with_count->where('sphere_of_activity_name', $sphere->sphere_of_activity_name)))
                            @php $arr = $spheres_with_count->where('sphere_of_activity_name', $sphere->sphere_of_activity_name); @endphp
                            @foreach ($arr as $a)
                            <div class="vacancies-count text-muted">{{$a->sphere_of_activities_count}} резюме</div>
                            @endforeach
                            @else
                            <div class="vacancies-count text-muted">Нет резюме</div>
                            @endif
                        </div>
                    </div>
                </div>
                @php $i++; @endphp
                @endforeach
            </div>
            <div style="margin-bottom:210px;"></div>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('.carousel').slick({
                        infinite: true,
                        slidesToShow: 5,
                        slidesToScroll: 3,
                        centerMode: true,
                        dots: true,
                        arrows: false,
                        dotsClass: 'dots-style',
                    });
                });
            </script>
        </div>
    </x-employer-layout>
</div>
<script>
    window.onload = function() {
        if ($("#is-there-a-location").val() == " ") {
            const popup = `<div class="modal" id="location-modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="text-xl text-center">Ваш город - ${ymaps.geolocation.city}?</h2>
      </div>
      <div class="modal-footer">
        <span type="button" class="span-like-button" id="btn-add-location">Нет, выбрать другой</span>
        <span type="button" class="span-like-button" id="btn-close-location" data-bs-dismiss="modal">Да, всё верно</span>
      </div>
    </div>
  </div>
</div>`;
            $('#location-popup').append(popup);
            $('#location-modal').show();
            //запрещаем скролл
            $('html, body').css({
                overflow: 'hidden',
                height: '100%'
            });
            //добавляем блюр
            $('#blurable-content').addClass("blur");
            $('#btn-close-skill').click(function() {
                $('#skill-modal').remove();
                // восстанавливаем скролл
                $('html, body').css({
                    overflow: 'auto',
                    height: 'auto'
                });
                //убираем блюр
                $('#blurable-content').removeClass("blur")
            })
            $('#btn-close-location').click(function() {
                $('#location-modal').remove();
                // восстанавливаем скролл
                $('html, body').css({
                    overflow: 'auto',
                    height: 'auto'
                });
                //убираем блюр
                $('#blurable-content').removeClass("blur");
                const location = ymaps.geolocation.city + ", " + ymaps.geolocation.region;
                $.ajax({
                    url: '{{ route("employer.add-location") }}',
                    type: "POST",
                    data: {
                        'location': location
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        console.log("Добавили местоположение!")
                    },
                    error: function(msg) {
                        console.log("Не получилось добавить местоположение")
                    }
                });
            })
            $("#btn-add-location").click(function() {
                $('#location-popup').empty();
                $('#choose-location-modal').show();

                //// dummy code for selector-1

                let inputField1 = document.getElementById('select');
                let dropdown1 = document.getElementById("value-list-1");
                let dropdownArray1 = [...dropdown1.querySelectorAll('li')];

                function closeDropdown(dropdown) {
                    dropdown.classList.remove('open');
                }
                closeDropdown(dropdown1);

                inputField1.addEventListener('input', () => {
                    let valueArray1 = [];
                    dropdownArray1.forEach(item => {
                        valueArray1.push(item.textContent);
                    });
                    dropdown1.classList.add('open');
                    let inputValue = inputField1.value.toLowerCase();
                    let valueSubstring;
                    if (inputValue.length > 0) {
                        for (let j = 0; j < valueArray1.length; j++) {
                            if (!(inputValue.substring(0, inputValue.length) === valueArray1[j].substring(0, inputValue.length).toLowerCase())) {
                                dropdownArray1[j].classList.add('closed');
                            } else {
                                dropdownArray1[j].classList.remove('closed');
                            }
                        }
                    } else {
                        for (let i = 0; i < dropdownArray1.length; i++) {
                            dropdownArray1[i].classList.remove('closed');
                        }
                    }
                });
                dropdownArray1.forEach(item => {
                    item.addEventListener('click', (evt) => {
                        inputField1.value = item.textContent;
                        dropdownArray1.forEach(dropdown1 => {
                            dropdown1.classList.add('closed');
                        });
                    });
                })

                inputField1.addEventListener('focus', () => {
                    dropdown1.classList.remove('open');
                    inputField1.placeholder = 'Поиск';
                    dropdown1.classList.add('open');
                    dropdownArray1.forEach(dropdown1 => {
                        dropdown1.classList.remove('closed');
                    });
                });

                inputField1.addEventListener('blur', () => {
                    dropdown1.classList.remove('open');
                });
            })
            $('#btn-select-location').click(function() {
                const location = $(".chosen-value").val();

                xhr.open('GET', requestUrl, true);
                xhr.responseType = 'json';
                xhr.send()

                xhr.onload = function() {
                    let cities = xhr.response;
                    cities = cities.map(city => city.name + ", " + city.subject)
                    if (cities.includes(location)) {
                        $('#choose-location-modal').remove();
                        // восстанавливаем скролл
                        $('html, body').css({
                            overflow: 'auto',
                            height: 'auto'
                        });
                        //убираем блюр
                        $('#blurable-content').removeClass("blur");
                        $.ajax({
                            url: '{{ route("employer.add-location") }}',
                            type: "POST",
                            data: {
                                'location': location
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                console.log("Добавили местоположение!")
                            },
                            error: function(msg) {
                                console.log("Не получилось добавить местоположение")
                            }
                        });
                    } else {
                        $('#edit-errors').append('<div class="alert alert-danger">Выберите город из списка</div>');
                    }
                }
            })
        }
    }
</script>
<style>
    .blur {
        transition: all 0.2s ease-in-out;
        filter: blur(3px);
    }

    .select-div,
    .chosen-value,
    .value-list {
        width: 400px !important;
    }

    #employer_main_img {
        width: 500px;
        animation-name: up-down;
        animation-duration: 4s;
        animation-iteration-count: infinite;
        animation-direction: alternate;
        position: absolute;
    }

    @keyframes up-down {
        from {
            top: 100px;
        }

        50% {
            top: 110px;
        }

        to {
            top: 100px;
        }
    }

    html {
        overflow-x: hidden;
    }

    .first-div {
        background-color: #f2f6fd;
        border-radius: 10% 30% 50% 70%;
        padding-bottom: 140px;
        height: 600px;
    }

    @import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');

    .big-text {
        font-size: 52px;
        margin-left: 210px;
        font-family: 'Montserrat';
        font-weight: 600;
    }



    .big-text:first-child {
        margin-top: 130px;
    }

    .big-text:nth-child(2) {
        margin-bottom: 20px;
    }

    .big-indigo-text {
        color: var(--text-selection-color);
        z-index: 20;
        position: relative;
    }

    .big-indigo-underline {
        width: 200px;
        height: 30px;
        background-color: var(--text-underline-color);
        position: absolute;
        top: 355px;
        left: 400px;
        z-index: 0;
    }

    #popular {
        width: 600px;
        margin-left: 210px;

    }

    /**слайдер */
    .carousel-row {
        margin-top: 15px;
        margin-left: 1px;
    }

    .carousel-img {
        width: 50px;
        height: 50px;
    }

    .sphere-title {
        font-size: 16px;
        text-align: left;
        margin-top: 5px;
    }

    .sphere-title:hover {
        color: var(--link-hover-color);
        cursor: pointer;
        transition: 0.5s;
    }

    .vacancies-count {
        font-size: 14px;
        text-align: left;
    }

    .dots-style {
        text-align: center;
        display: flex;
        justify-content: center;
        list-style: none;
        margin-top: 20px;
    }

    .dots-style button {
        background: var(--dot-color);
        border: none;
        border-radius: 100%;
        font-size: 0;
        height: 12px;
        width: 12px;
        margin: 5px;
        outline: none;
    }

    .dots-style li[class="slick-active"] button {
        background: var(--dot-active-color);
        height: 15px;
        width: 15px;
    }

    .sphere-box {
        width: 270px;
        border-radius: 12px;
        height: 93px;
        background-color: white;
        color: black;
        border: solid 1px #e7e8ea;
    }

    .sphere-box:hover {
        margin-top: 6px;
        transition: 0.4s;
        border: solid 1px var(--hover-border-color);
    }

    .wrapper {
        width: 100%;
        padding-top: 20px;
        text-align: center;
    }


    .carousel {
        width: 90%;
        margin: 40px auto;
        background-color: white;
    }

    .slick-slide {
        margin: 10px;
    }

    .slick-slide img {
        width: 100%;
        border: 2px solid #fff;
    }

    .wrapper .slick-dots li button:before {
        font-size: 20px;
        color: white;
    }

    .find-by-sphere {
        margin-top: 210px;
    }

    .how-to {
        margin-top: 100px;
    }

    #steps_img {
        width: 1100px;
        margin: 40px auto;
    }

    .info-col {
        width: 380px;

    }

    .info-col:nth-child(2) {
        margin-left: 60px;
    }

    .info-col:nth-child(3) {
        margin-left: 90px;
    }

    .info-row {
        margin-left: 120px;
    }

    .start-btn {
        margin-top: 60px;
    }
</style>

</html>