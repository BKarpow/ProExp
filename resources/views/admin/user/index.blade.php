@extends('layouts.app')

@section('title') Всі користувачі сайту @endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Користувачі</div>

                    <div class="card-body">

                        @if ($data->count() == 0)
                            <div class="alert alert-info">
                                <strong>Ще немає користувачів</strong>
                            </div>
                            <!-- /.alert -->
                        @else
                            <div class="table-responsive">
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th>Інфо</th>
                                            <th>Дата створення</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                            <tr >
                                                <th >

                                                    <div class="my-2">
                                                        <a href="{{route('date.userExps', ['user' => $item])}}" class="btn btn-primary">
                                                         {{ $item->name }} </a> <!-- /.btn btn-primary -->
                                                        <div class="my-1">
                                                            <a href="{{ route('admin.user.set.manager', ['user_id' => $item->id]) }}"
                                                                class="btn btn-danger">
                                                                ЗРОБИТИ МЕНЕДЖЕРОМ
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="mt-1">
                                                        {{$item->phone}}
                                                    </div>
                                                    <!-- /.mt-1 -->

                                                    <div class="mt-1">
                                                        <ul class="list-group">
                                                            <li class="list-group-item">
                                                                Візитів: {{ $item->meta->visit ?? 0 }}
                                                            </li>
                                                            <!-- /.list-group-item -->
                                                            <li class="list-group-item">
                                                                Остання IP: {{ $item->meta->last_ip ?? '' }}
                                                            </li>
                                                            <!-- /.list-group-item -->
                                                            <li class="list-group-item">
                                                                Остання UA: {{ $item->meta->last_user_agent ?? '' }}
                                                            </li>
                                                            <!-- /.list-group-item -->
                                                            @if (isset($item->meta->created_at))
                                                                <li class="list-group-item">
                                                                    {{$item->meta->updated_at->format('d.m.Y H:i')}}
                                                                </li>
                                                                <!-- /.list-group-item -->
                                                            @endif
                                                            <li class="list-group-item">
                                                                Термінів: {{ $item->exps()->count() }}
                                                            </li>
                                                            <!-- /.list-group-item -->
                                                        </ul>
                                                        <!-- /.list-group -->
                                                    </div>
                                                    <!-- /.mt-1 -->

                                                </th>
                                                <td >{{$item->created_at->format('d.m.Y')}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-2 flex hustify-content-center">
                                    {{$data->links()}}
                                </div>
                                <!-- /.mt-2 flex hustify-content-center -->
                            </div>
                            <!-- /.table-responsive -->
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
