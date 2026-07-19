<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.head')
    <title> @yield('title') {{ config('app.name', 'Laravel') }}</title>
</head>
<body >
<div id="app" >

    <pwa></pwa>
{{--    @include('layouts.nav')--}}
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

    <main >
        @yield('content')
    </main>
    @if (!Route::is('date.create'))
        <fab url="{{ route('date.create') }}"> </fab>
    @endif
</div>
</body>
</html>

