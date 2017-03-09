<div class="row">
    <h4 class="resultsTitle margin-bottom-20">{{$mentorViewModels->count()}} mentor(s) found</h4>

        @foreach($mentorViewModels as $mentorViewModel)
        <div class="col-md-12 padding-0">
            @include('mentors.single', ['mentorViewModel' => $mentorViewModel])
        </div>
        @endforeach

</div>
