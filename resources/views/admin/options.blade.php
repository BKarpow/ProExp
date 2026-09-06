@extends('layouts.app')

@section('title') Налаштування сайту @endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Налаштування</div>

                    <div class="card-body">
                        <div class="mb-2 row">
                            <div class="btn-group">
                                <a href="{{route('admin.runMigrate')}}" class="btn btn-primary">
                                    Запустити міграції
                                </a> <!-- /.btn btn-primary -->
                                <a href="{{route('admin.mail.test')}}" class="btn btn-primary">
                                    Тестовий лист адміну
                                </a> <!-- /.btn-primary -->
                                <a href="{{route('admin.cache.clear')}}" class="btn btn-warning">
                                    Очистити кеш конфігурацій
                                </a> <!-- /.btn btn-warning -->
                                <a href="{{route('admin.mail.telegram.test')}}" class="btn btn-success">
                                    Відправити в telegram те
                                </a> <!-- /.btn btn-success -->

                                <a href="{{route('shoes.warehouse.clear')}}" class="btn btn-success">
                                    Очистити склад взуття
                                </a> <!-- /.btn btn-success -->
                            </div>
                            <!-- /.btn-group -->

                        </div>
                        <!-- /.mb-2 row -->
                        <div class="mb-2 row">
                            <h3>Іпортувати список продуктів !</h3>
                            <form action="{{route('import.product.csv')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="file" class="form-label">Оберіть CSV файл</label>
                                    <input type="file" name="file" id="file" class="form-control" required>

                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">Завантажити та імпортувати</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.mb-2 row -->

                        <div class="mb-2 row">
                            <h3>Іпортувати список моделі взуття !</h3>
                            <form action="{{route('import.models.csv')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="file1" class="form-label">Оберіть CSV файл</label>
                                        <input type="file" name="file1" id="file1" class="form-control" required>

                                </div>
                                @error('file1')
                                    <div class="alert alert-warning">
                                        <strong>
                                            {{ $message }}
                                        </strong>
                                    </div>
                                @enderror
                                <div class="mb-3">
                                    <select name="groupId" class="form-control">
                                        @foreach ($groups as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">Завантажити та імпортувати</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.mb-2 row -->


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
