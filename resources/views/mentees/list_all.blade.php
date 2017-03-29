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

            @if(\Illuminate\Support\Facades\Auth::user()->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
            var availabilityStatusChangeHandler = new AvailabilityStatusChangeViewHandler();
            availabilityStatusChangeHandler.init();
            @endif
        });
    </script>
@endsection
