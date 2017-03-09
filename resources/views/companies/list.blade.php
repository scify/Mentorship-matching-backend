<div class="row">
    <div class="col-md-12">
        @foreach($companyViewModels as $companyViewModel)
            <div class="col-md-4">
                @include('companies.single', ['companyViewModel' => $companyViewModel])
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