<div id="menteesList">
    <h4 class="resultsTitle margin-bottom-20">{{ $menteeViewModels->total() }} mentee(s) found. Click on a mentee to see their profile.</h4>
    <ul class="list-material has-hidden background-transparent">
        @foreach($menteeViewModels as $menteeViewModel)
            @include('mentees.single', ['menteeViewModel' => $menteeViewModel])
        @endforeach
    </ul>
    <div class="loading-bar indeterminate margin-top-10 invisible" id="menteesBottomLoader"></div>
    {{ $menteeViewModels->links() }}
</div>
