@extends('layouts.app')

@section('title')
    Склад взуття
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Склад взуття</div>

                    <div class="card-body">
                        <s-warehouse></s-warehouse>
                    </div> <!-- /.cart-body -->
                </div>
            </div>
        </div>
    </div>
@endsection
