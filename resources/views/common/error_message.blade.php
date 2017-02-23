@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
    <div class="alert alert-danger" style="float: left; position: initial;">
        <strong>Not found!</strong> {{$message}} <a href="{{url('dashboard')}}" class="alert-link">Return home</a>
    </div>
        </div>
    </div>
@endsection