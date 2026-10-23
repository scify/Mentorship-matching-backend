@extends('layouts.app')
@section('content')
    <div id="allMentors">
        @include('mentors.filters')
        @include('mentors.list')
        @include('mentors.modals')
    </div>
@endsection


@section('additionalFooter')
    <script type="module">
        $( document ).ready(function() {
            var controller = new window.MentorsListController();
            controller.init(@json(Route::currentRouteName()));

            @if(\Illuminate\Support\Facades\Auth::user()->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
            var availabilityStatusChangeHandler = new AvailabilityStatusChangeViewHandler();
            availabilityStatusChangeHandler.init("#allMentors");
            @endif
        });
    </script>
@endsection
