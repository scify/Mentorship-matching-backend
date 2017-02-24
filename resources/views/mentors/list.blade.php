<div class="row">
    <h4 class="resultsTitle margin-bottom-20">{{$mentors->count()}} mentor(s) found</h4>

        @foreach($mentors as $mentor)
        <div class="col-md-12 padding-0">
            @include('mentors.single', ['mentor' => $mentor])
        </div>
        @endforeach

</div>
