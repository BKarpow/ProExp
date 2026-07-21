@extends('layouts.app')

@section('title') Додавання терміну  @endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-11">

                <div class="card">
                    <div class="card-header">Новий термін</div>

                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{route('date.create')}}" method="POST">
                                        @csrf


                        {{-- <div class="my-2 p-1">
                            <label> Група товарів за замовчуванням id: {{$configGroup}}
                                <select required name='group' class="form-select" aria-label="Вибір магазину за замовчуванням">
                                    <option disabled @if ($configGroup == 0) selected @endif >Обрати магазин ...</option>
                                    @foreach ($groups as $shop)

                                        <option value="{{$shop->id}}" @if ($configGroup == $shop->id) selected @endif>{{$shop->name}}</option>
                                    @endforeach
                                </select>
                            </label>

                        </div> --}}
                        <!-- /.my-2 p-1 -->
                                        
                                        <div class="mb-2">
                                            <create-date></create-date>

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
