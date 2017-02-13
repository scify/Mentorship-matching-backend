<div class="row">
    <h4 class="resultsTitle margin-bottom-20">{{$users->count()}} users found</h4>
    <div class="col-md-12 padding-0">
        @foreach($users as $user)
            <div class="col-md-3">
                @include('users.single', ['user' => $user])
            </div>
        @endforeach
    </div>
</div>
@include('users.modals')