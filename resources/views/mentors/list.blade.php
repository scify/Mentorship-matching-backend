<div class="row">
    <div class="col-md-12">
        @foreach($mentors as $mentor)
            <div class="col-md-3">
                @include('mentors.single', ['mentor' => $mentor])
            </div>
        @endforeach
    </div>
</div>
@include('mentors.modals')