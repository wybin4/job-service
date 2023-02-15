<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>
    <script src="{{asset('/js/sex_by_russian_name.js')}}"></script>
</head>
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('employer.dashboard') }}">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-600" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('employer.dashboard')" :active="request()->routeIs('employer.dashboard')">
                        {{ __('Главная') }}
                    </x-nav-link>
                    <x-nav-link :href="route('employer.resume-feed')" :active="request()->routeIs('employer.resume-feed')">
                        {{ __('Поиск') }}
                    </x-nav-link>
                    @if(Auth::User()->all_vacancy->count())
                    <x-nav-link :href="route('employer.vacancies-list')" :active="request()->routeIs('employer.vacancies-list')">
                        {{ __('Мои вакансии') }}
                    </x-nav-link>
                    @endif
                    <x-nav-link :href="route('employer.student-interaction')" :active="request()->routeIs('employer.student-interaction')">
                        {{ __('Взаимодействия') }}
                    </x-nav-link>
                </div>
            </div>
            <div class="flex justify-end">
                <div class="hidden sm:flex sm:items-center" style="margin-right: 50px;">
                    <x-notif-dropdown align="right">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                @if(count(Auth::User()->unreadNotifications))
                                <div class="notif-dot"></div>
                                @endif
                                <i class="fa-regular fa-bell"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="notif-head font-bold">Уведомления</div>
                            @if(count(Auth::User()->unreadNotifications))
                            @foreach (Auth::User()->unreadNotifications as $notification)
                            @php $student = App\Models\Student::find($notification->data['student_id']); @endphp
                            @if($notification->data['type'] == 5)
                            @php
                            $vacancy = App\Models\Vacancy::find($notification->data['vacancy_id'])->profession->profession_name;
                            $type = ' отправил Вам отклик на вакансию "'. $vacancy .'"';
                            $href = "/employer/vacancy-responses/".$notification->data['vacancy_id'];
                            @endphp
                            @elseif($notification->data['type'] == 1 || $notification->data['type'] == 2)
                            @php
                            $vacancy = App\Models\Vacancy::find($notification->data['vacancy_id'])->profession->profession_name;
                            $type = ' ответил на Ваш оффер по вакансии "'.$vacancy .'"';
                            $href = "/employer/vacancy-offers/".$notification->data['vacancy_id'];
                            @endphp
                            @elseif($notification->data['type'] == 7)
                            @php
                            $vacancy = App\Models\Vacancy::find($notification->data['vacancy_id'])->profession->profession_name;
                            $type = ' отказался от работы по вакансии "'.$vacancy .'"';
                            $interaction = App\Models\Interaction::where('vacancy_id', $notification->data['vacancy_id'])
                            ->where('student_id', $student->id)->first();
                            if($interaction->type == 0)
                            {
                            $href = "/employer/vacancy-responses/".$notification->data['vacancy_id'];
                            }
                            else
                            {
                            $href = "/employer/vacancy-offers/".$notification->data['vacancy_id'];
                            }
                            @endphp
                            @endif
                            <x-dropdown-link class="mark-as-read" href="{{$href}}" id="{{$notification->id}}">
                                @if ($student->image)
                                <span><img class="nav-pic" src="{{asset('/storage/images/'.$student->image)}}"><span class="notif-text"><strong class="notif-strong">{{$student->student_fio}}</strong>{{$type}}</span></span>
                                @else
                                <span><span class="nav-future-pic">{{mb_substr($student->student_fio, 0, 1)}}</span><span class="notif-text"><strong class="notif-strong">{{$student->student_fio}}</strong>{{$type}}</span></span>
                                @endif
                                <div class="notif-date">{{$notification->created_at}}</div>
                            </x-dropdown-link>
                            @endforeach
                            @else
                            <div class="notif-head notif-no">Нет уведомлений</div>
                            @endif
                        </x-slot>

                    </x-notif-dropdown>
                </div>
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ Auth::guard('employer')->user()->name }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('employer.create-vacancy')">
                                {{ __('Добавить вакансию') }}
                            </x-dropdown-link>
                            @if(Auth::User()->all_vacancy->count())
                            <x-dropdown-link :href="route('employer.vacancies-list')">
                                {{ __('Мои вакансии') }}
                            </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('employer.alter-profile')">
                                {{ __('Профиль') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('employer.logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('employer.logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Выйти') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('employer.dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::guard('employer')->user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::guard('employer')->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('employer.logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('employer.logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Выйти') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        <style>
            .nav-future-pic {
                font-size: 16px;
                display: inline-flex;
                flex-direction: column;
                justify-content: center;
                text-align: center;
                vertical-align: middle;
                background-color: var(--future-pic-color);
                border-radius: 100px;
                color: var(--future-pic-text-color);
            }

            .nav-pic,
            .nav-future-pic {
                width: 40px;
                height: 40px;
            }

            .nav-pic {
                display: inline-block
            }

            .notif-text {
                display: table;
                margin-left: 47px;
                margin-top: -37px;
                margin-bottom: 5px;
            }


            .notif-head {
                margin-left: 20px;
                margin-top: 10px;
                margin-bottom: 5px;
            }

            .notif-no {
                margin-bottom: 20px;
            }

            .notif-date {
                color: grey;
                margin-left: 47px;
                margin-top: -4px;
                margin-bottom: 5px;
            }
        </style>
    </div>
    <script>
        $(window).on('load', function() {
            $(".mark-as-read").on('click', function(e) {
                console.log($(this).attr('id'))
                let id = $(this).attr('id');
                e.preventDefault();
                $.ajax({
                    url: '{{ route("employer.mark-as-read") }}',
                    type: "POST",
                    data: {
                        'id': id,
                        'employer_id': '{{Auth::user()->id}}'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });
                window.location.href = $(this).attr('href');
            })
        });
    </script>
</nav>

<script>
    moment.locale('ru');

    $(".notif-date").each(function(index) {
        $(this).text(moment.duration(moment().diff($(this).text())).humanize() + " назад");
    });

    $(".notif-text").each(function() {
        let text = $(this).html();
        const name = $(this).find(".notif-strong").text().split(" ");
        const sex_by_russian_name = new SexByRussianName(name[0], name[1], name[2]);
        if (!sex_by_russian_name.get_gender()) {
            text = text.replace("ответил", "ответила")
            text = text.replace("отправил", "отправила")
            text = text.replace("отказался", "отказалась")
        }
        text = text.replace($(this).find(".notif-strong").text(), name[0] + " " + name[1])
        $(this).html(text);
    })
</script>