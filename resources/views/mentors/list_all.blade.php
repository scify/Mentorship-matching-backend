@extends('layouts.app')
@section('content')
    <div id="allMentors">
        @include('mentors.filters')
        @include('mentors.list')
        @include('mentors.modals')
    </div>
@endsection


@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.MentorsListController();
            controller.init();

            @if(\Illuminate\Support\Facades\Auth::user()->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
            var availabilityStatusChangeHandler = new AvailabilityStatusChangeViewHandler();
            availabilityStatusChangeHandler.init("#allMentors");
            @endif
        });
    </script>
@endsection
