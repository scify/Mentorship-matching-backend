<h4 class="resultsTitle margin-bottom-20">{{$mentorViewModels->count()}} mentor(s) found</h4>
@foreach($mentorViewModels as $mentorViewModel)
    @include('mentors.single', ['mentorViewModel' => $mentorViewModel])
@endforeach
