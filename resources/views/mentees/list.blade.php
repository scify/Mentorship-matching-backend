<div class="row">
    <h4 class="resultsTitle margin-bottom-20">{{$mentees->count()}} mentee(s) found</h4>
    <div class="col-md-12 padding-0">
        @foreach($mentees as $mentee)
            <div class="col-md-3">
                @include('mentees.single', ['mentee' => $mentee])
            </div>
        @endforeach
    </div>
</div>
