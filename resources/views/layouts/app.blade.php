<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
@include('layouts.head')
<title> @yield('title') {{ config('app.name', 'Laravel') }}</title>
</head>
<body >
    <div id="app" >

        <pwa></pwa>
        @include('layouts.nav')
        @if (session('status'))
        <div class="container m-1">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-4">
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                </div>
                <!-- /.col-md-4 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
        @endif

        <main class="py-4">
            @yield('content')
        </main>
        <div class="container">
             <div class="col-md-4 d-flex align-items-center">
                 <a href="/" class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1" aria-label="Контроль термінів">
                     ProExp </a> <span class="mb-3 mb-md-0 text-body-secondary">© <?=date('y')?> Розробник Богдан Карпов</span> </div> <ul class="nav col-md-4 justify-content-end list-unstyled d-flex"> <li class="ms-3"><a class="text-body-secondary" href="#" aria-label="Instagram"><svg class="bi" width="24" height="24" aria-hidden="true"><use xlink:href="#instagram"></use></svg></a></li> <li class="ms-3"><a class="text-body-secondary" href="#" aria-label="Facebook"><svg class="bi" width="24" height="24"><use xlink:href="#facebook"></use></svg></a></li> </ul>
        </div>
        @if (!Route::is('date.create'))
            <fab url="{{ route('date.create') }}"> </fab>
        @endif
    </div>
</body>
</html>
