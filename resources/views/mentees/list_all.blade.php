@extends('layouts.app')
@section('content')
    <div id="allMentees">
        @include('mentees.filters')
        @include('mentees.list')
        @include('mentees.modals')
    </div>
@endsection

@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.MenteesListController();
            controller.init();

            @if(\Illuminate\Support\Facades\Auth::user()->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
            var availabilityStatusChangeHandler = new AvailabilityStatusChangeViewHandler();
            availabilityStatusChangeHandler.init("#allMentees");
            @endif
        });
    </script>
@endsection
