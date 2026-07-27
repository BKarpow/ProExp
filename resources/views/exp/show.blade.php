@extends('layouts.app')

@section('title')
    {{ $t->productName() }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Перегляд</div>

                    <div class="card-body">
<div class="mb-1">
                                                        <a href="{{ route('product.edit', ['product'=>$t->product_id]) }}"
                                                            class="btn btn-dark btn-sm">
                                                            Змінити продукт
                                                        </a> <!-- /.btn btn-dark -->
                                                    </div>
                        <div class="my-2 p-1">
                            @can('update', $t)
                                <a href="{{ route('date.edit', ['dateProduct' => $t]) }}" class="btn btn-success btn-lg">
                                    Змінити цей термін
                                </a>
                                <delete-btn name-item="Термін" url-delete="{{ route('date.delete', ['dateProduct' => $t]) }}"></delete-btn>
                            @endcan
                            @cannot('update', $t)
                                <span class="badge bg-secondary">Тільки для читання, створено користувачем
                                    {{ $t->user->name }}</span>
                            @endcannot
                            <div class="my-1">
                                <delete-btn name-item="всі терміни цього продукту" url-delete="{{ route('date.delete.from.group', ['product' => $t->product->id]) }}"></delete-btn>
                            </div>
                        </div>
                        <!-- /.my-2 p-1 -->
                        <div class="mb-2 p-1">
                            <a href="https://www.google.com/search?q={{ $t->product->barcode }}&tbm=isch"
                                class="btn btn-primary">
                                Шукати продукт в Google Images </a>
                            <!-- /.btn btn-success -->
                        </div>
                        <!-- /.mb-2 p-1 -->
                        <h1>{{ $t->productName() }}</h1>
                        <div class="my-1">
                            <small>Оригінальна назва: {{$t->product->name}}</small>
                        </div>
                        <!-- /.my-1 -->
                        <div class="container">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-md-4">
                                    <img src="{{$t->product->mainImg()}}" class="img-fluid" alt="{{ $t->product->name }}">
                                </div>
                                <!-- /.col-md-4 -->
                            </div>
                            <!-- /.row -->
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="{{route('date.delImg', ['dateProduct'=>$t ])}}" class="btn btn-danger">
                                        Це не зображення цього продукту!
                                    </a> <!-- /.btn btn-primary -->
                                </div>
                                <!-- /.col-md-4 -->
                            </div>
                            <!-- /.row -->
                            <div class="row mt-2 justify-content-center align-items-center">

                                @if($pathSvg)
                                    <div class="d-flex justify-content-center align-items-center" style="height: 300px; border: 1px solid #ccc;">
                                        <img src="{{$pathSvg}}" class="img-fluid" alt="">
                                    </div>
                                    <!-- /.col-md-4 -->
                                @endif
                                <div class="mt-1">
                                    <h3 class="text-center">{{$t->product->barcode}}</h3>
                                    <!-- /.text-center -->
                                </div>
                            </div>
                            <!-- /.mt-2 -->
                        </div>
                        <!-- /.container -->

                        <h2>Залишилося днів - {{ $t->days_remaining }}</h2>
                        <div class="container mt-2">
                            <div class="row">
                                <div class="col-md-4">
                                    <h3>Виготовлено: {{ $t->start->format('d.m.Y') }}</h3>
                                    <h3>Вжити до: {{ $t->end->format('d.m.Y') }}</h3>
                                    <h4>Створено: {{ $t->created_at->format('d.m.Y') }}</h4>
                                    <h4>Оновлено: {{ $t->updated_at->format('d.m.Y') }}</h4>
                                    <h4>Додав користувач: {{ $t->user->name }}</h4>
                                </div>
                                <!-- /.col-md-4 -->
                                <div class="col-md-4">

                                </div>
                                <!-- /.col-md-4 -->
                                <div class="col-md-4">
                                    <h3>Кількість: {{ $t->count }}</h3>
                                </div>
                                <!-- /.col-md-4 -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.container mt-2 -->
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <h2>Всі терміни цього продукту</h2>
                                </div>
                                <!-- /.col-md-2 -->
                                <div class="col-md-10">
                                    <table class="table table-dark">
                                        <thead>
                                            <tr>
                                                <th>Назва</th>
                                                <th>Початок</th>
                                                <th>Кінець</th>
                                                <th>Кількість</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $p)
                                                <tr>
                                                    <td>{{ $p->productName() }}</td>
                                                    <td>{{ $p->start }}</td>
                                                    <td>
                                                        <div><strong>
                                                                {{ $p->days_remaining }} днів.
                                                            </strong></div>
                                                        {{ $p->end }}

                                                    </td>
                                                    <td>{{ $p->count }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.col-md-10 -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.container mt-2 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
