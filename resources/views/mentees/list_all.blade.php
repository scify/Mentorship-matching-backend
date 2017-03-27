@extends('layouts.app')
@section('content')
    @include('mentees.filters')
    @include('mentees.list')
    @include('mentees.modals')

@endsection

@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.MenteesListController();
            controller.init();
        });
    </script>
@endsection
