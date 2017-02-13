<div class="row">
    <h4 class="resultsTitle margin-bottom-20">{{$mentors->count()}} mentors found</h4>
    <div class="col-md-12 padding-0">
        @foreach($mentors as $mentor)
            <div class="col-md-3">
                @include('mentors.single', ['mentor' => $mentor])
            </div>
        @endforeach
    </div>
</div>
@include('mentors.modals')
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.MentorsListController();
            controller.init();
        });
    </script>
@endsection