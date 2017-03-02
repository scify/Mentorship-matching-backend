@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>FILTERS</h4>
                </div>
            </div><!--.panel-heading-->
            <div class="panel-body">
                <div class="col-md-3">Role</div><!--.col-md-3-->
                <div class="col-md-6">
                    <select data-placeholder="Choose role" name="user_role" class="chosen-select">
                        <option><!-- Empty option allows the placeholder to take effect. --><option>
                        {{--@foreach($userRoles as $userRole)--}}
                            {{--<option value="{{$userRole->id}}">{{$userRole->title}}</option>--}}
                        {{--@endforeach--}}
                    </select>
                </div><!--.col-md-9-->
            </div>
        </div>
    </div><!--.row-->
    </div>
    <div class="row">

    <div class="col-md-12 centeredVertically">
        <div class="loading-bar indeterminate margin-top-10 hidden loader"></div>
        <div id="errorMsg" class="alert alert-danger stickyAlert margin-top-20 margin-bottom-20 margin-left-100 hidden" role="alert"></div>
        <div id="usersList">
            @include('mentees.list')
        </div>
    </div>
    </div>

@endsection

