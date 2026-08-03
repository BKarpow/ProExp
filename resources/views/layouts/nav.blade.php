<nav class="navbar navbar-expand-md navbar-dark shadow-sm" style="background-color:var(--main-bg-color);">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="/build/icons/favicon.png" class=" img-fluid" /> {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item dropdown">

                        <a id="navbarDropdown2" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Терміни
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">

                            <a href="{{ route('date.todo') }}" class=" dropdown-item">
                                ЧЕК ЛИСТ
                            </a>

                            <a href="{{ route('product.create') }}" class=" dropdown-item">
                                Додати продукт
                            </a>

                            <a href="{{ route('date.index') }}" class=" dropdown-item">
                                Терміни
                            </a>

                            <a href="{{ route('group.index') }}" class="dropdown-item">
                                Відділи
                            </a>
                            <a href="{{ route('product.index') }}" class="dropdown-item">
                                Продукти
                            </a>
                            <a href="{{ route('import.csv') }}" class="dropdown-item">
                                Імпортувати терміни
                            </a>
                        </div>
                    </li>
                    <!-- /.nav-item -->
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown10" class="nav-link dropdown-toggle" href="#"
                        role="button"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false" v-pre>
                                Інвентаоизації
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{route('inventory.create')}}" class="dropdown-item">
                                    Створити
                                </a> <!-- /.dropdown-item -->
                            </div>
                            <!-- /.dropdown-menu dropdown-menu-end -->
                    </li>
                    <!-- /.nav-item dropdown -->
                    @if (auth()->user()->isAdmin())
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown1" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Адмін функції
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('admin.index') }}" class="dropdown-item">
                                    Панедь
                                </a>
                                <a href="{{ route('admin.user.index') }}" class="dropdown-item">
                                    Користувачі
                                </a> <!-- /.dropdown-item -->
                                <a href="{{ route('admin.options') }}" class="dropdown-item">
                                    Налаштування
                                </a> <!-- /.dropdown-item -->
                                <a href="{{ route('wproduct.import') }}" class="dropdown-item">
                                    Імпорт Вагових товарів
                                </a> <!-- /.dropdown-item -->
                                <a href="{{ route('shop.index') }}" class="dropdown-item">
                                Магазини
                            </a>
                            <a href="{{ route('tools') }}" class="dropdown-item">
                                Інструменти
                            </a>
                            </div>
                            <!-- /.dropdown-menu dropdown-menu-end -->
                        </li>


                        <li class="nav-item dropdown">
                            <a id="navbarDropdown1" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Менеджмент
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('shoes.group') }}" class="dropdown-item">
                                    Групи товарів
                                </a>

                                <a href="{{ route('shoes.models') }}" class="dropdown-item">
                                    Моделі
                                </a>

                                <a href="{{ route('shoes.warehouse') }}" class="dropdown-item">
                                    Склад
                                </a>
                                
                                
                                
                                
                            
                            </div>
                            <!-- /.dropdown-menu dropdown-menu-end -->
                        </li>
                    @endif



                @endauth

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Вхід</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Створити акаунт</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                           <i class="bi bi-person"></i> {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a href="{{ route('date.expired') }}" class="dropdown-item">
                                Протермін
                            </a>
                            <!-- /.btn btn-success -->
                            <a href="{{ route('options.index') }}" class="dropdown-item">
                                Налаштування
                            </a> <!-- /.dropdown-item -->
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                Вихід
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
