@extends('layouts.app')

@section('title')
    Моделі товарів
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Перегляд</div>

                    <div class="card-body">
                        <s-models></s-models>
                    </div> <!-- /.cart-body -->
                </div>
            </div>
        </div>
    </div>
@endsection
