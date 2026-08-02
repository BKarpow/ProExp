@extends('layouts.app')

@section('title')
    Групи товарів
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Перегляд</div>

                    <div class="card-body">
                        <s-group></s-group>
                    </div> <!-- /.cart-body -->
                </div>
            </div>
        </div>
    </div>
@endsection
