@extends('layouts.app')
@section('content')
    <div class="col-md-6 centeredVertically">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="col-md-12">Filter by role</h3>
            </div><!--.panel-heading-->
            <div class="panel-body">
                <div class="col-md-3">Role</div><!--.col-md-3-->
                <div class="col-md-6">
                    <select data-placeholder="Choose role" name="user_role" class="chosen-select">
                        @foreach($userRoles as $userRole)
                            <option value="{{$userRole->id}}">{{$userRole->title}}</option>
                        @endforeach
                    </select>
                </div><!--.col-md-9-->
            </div>
        </div>
    </div><!--.row-->
    <div class="col-md-12 centeredVertically">
        <div class="usersList">
            @include('users.list')
        </div>
    </div>
    @include('users.modals')
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            $('.chosen-select').chosen({
                width: '100%'
            });
            var controller = new window.UsersListController();
            controller.init();
        });
    </script>
@endsection
