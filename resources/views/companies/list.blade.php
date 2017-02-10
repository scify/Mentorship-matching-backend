<div class="row">
    <div class="col-md-12">
        @foreach($companies as $company)
            <div class="col-md-3">
                @include('companies.single', ['company' => $company])
            </div>
        @endforeach
    </div>
</div>
@include('companies.modals')
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.CompaniesListController();
            controller.init();
        });
    </script>
@endsection