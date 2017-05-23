<div id="mentorsList">
    <h4 class="resultsTitle margin-bottom-20">{{$mentorsCount}} mentor(s) found. Click on a mentor to see their profile.</h4>
    <ul class="list-material has-hidden background-transparent">
        @foreach($mentorViewModels as $mentorViewModel)
            @include('mentors.single', ['mentorViewModel' => $mentorViewModel])
        @endforeach
    </ul>
    <div class="loading-bar indeterminate margin-top-10 invisible" id="mentorsBottomLoader"></div>
    {{ $mentorViewModels->links() }}
</div>

