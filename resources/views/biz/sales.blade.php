@extends('layouts.app2')

@section('title')
    Склад взуття
@endsection

@section('content')

    <s-sales :user-id="{{ Auth::id() }}"></s-sales>
@endsection
