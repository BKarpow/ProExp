@extends('layouts.app')

@section('title') Створити продукту  @endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-header">Новий продукт</div>

                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                <detect-prices></detect-prices>
                                    <form action="{{route('product.create')}}" method="POST">
                                        @csrf
                                        <div class="mb-2">


                                                @error('barcode')
                                                    <div class="mt-1 alert alert-danger">
                                                        {{ $message }}
                                                    </div>
                                                    <!-- /.mt-1 alert alert-danger -->
                                                @enderror
                                        </div>
                                        <!-- /.form-group -->


                                    </form>
                                </div>
                                <!-- /.col-md-6 -->
                                <div class="col-md-6">

                                </div>
                                <!-- /.col-md-6 -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.container -->

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
