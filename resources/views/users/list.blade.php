<div class="row">
    <div class="col-md-12">
        @foreach($users as $user)
            <div class="col-md-3">
                @include('users.single', ['user' => $user])
            </div>
        @endforeach
    </div>
</div>
@include('users.modals')