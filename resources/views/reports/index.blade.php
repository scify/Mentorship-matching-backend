@extends('layouts.app')
@section('content')
    @include('reports.platform_reports')
    @include('reports.exports')
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
        });
    </script>
@endsection
