<div class="row">
    <h4 class="resultsTitle margin-bottom-20">{{sizeof($users)}} users found</h4>
    <div class="col-md-12 padding-0">
        @foreach($users as $user)
            <div class="col-md-3">
                @include('users.single', ['user' => $user, 'accountManagersActiveSessions' => $accountManagersActiveSessions])
            </div>
        @endforeach
    </div>
</div>
@include('users.modals')
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.UsersListController();
            controller.init();
        });
    </script>
@endsection
