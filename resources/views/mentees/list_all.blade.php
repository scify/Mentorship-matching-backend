@extends('layouts.app')
@section('content')
    @include('mentees.filters')
    @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
        @include('mentees.list', ['actionButtonsNum' => 2, 'matchingMode' => false])
    @else
        @include('mentees.list', ['actionButtonsNum' => 1, 'matchingMode' => false])
    @endif
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
