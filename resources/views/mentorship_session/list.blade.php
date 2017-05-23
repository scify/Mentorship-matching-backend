<div id="mentorshipSessionsList" data-fetch-session-history-url="{{ route('fetchSessionHistory') }}">
    <h4 class="resultsTitle margin-bottom-20">{{$mentorshipSessionViewModels->total()}} mentorship session(s) found. Click on a session for more info.</h4>
    <ul class="list-material has-hidden background-transparent row">
        @foreach($mentorshipSessionViewModels as $mentorshipSessionViewModel)
            @include('mentorship_session.single', ['mentorshipSessionViewModel' => $mentorshipSessionViewModel])
        @endforeach
    </ul>
    <div class="loading-bar indeterminate margin-top-10 invisible" id="mentorshipSessionsBottomLoader"></div>
    {{ $mentorshipSessionViewModels->links() }}
</div>
