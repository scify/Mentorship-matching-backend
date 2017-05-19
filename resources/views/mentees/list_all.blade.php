@extends('layouts.app')
@section('content')
    <div id="allMentees">
        @include('mentees.filters')
        @include('mentees.list', ['menteesCount' => $menteeViewModels->total()])
        @include('mentees.modals')
    </div>
    {{ $menteeViewModels->links() }}
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
