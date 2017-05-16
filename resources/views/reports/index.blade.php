@extends('layouts.app')
@section('content')
    @include('reports.platform_reports')
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
        });
    </script>
@endsection
