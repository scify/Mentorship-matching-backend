<div class="row">
    <h4 class="resultsTitle margin-bottom-20">{{$menteeViewModels->count()}} mentee(s) found</h4>
    <div class="col-md-12 padding-0">
        @foreach($menteeViewModels as $menteeViewModel)
            <div class="col-md-3">
                @include('mentees.single', ['menteeViewModel' => $menteeViewModel])
            </div>
        @endforeach
    </div>
</div>
