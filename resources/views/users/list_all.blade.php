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
                <div class="panel-body filtersContainer noInputStyles">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-2 filterName">Role</div><!--.col-md-3-->
                                <div class="col-md-6">
                                    <select data-placeholder="Choose role" name="user_role" class="chosen-select">
                                        <option><!-- Empty option allows the placeholder to take effect. --><option>
                                        @foreach($userRoles as $userRole)
                                            <option value="{{$userRole->id}}">{{$userRole->title}}</option>
                                        @endforeach
                                    </select>
                                </div><!--.col-md-6-->
                            </div>
                            <div class="row">
                                <div class="col-md-2 filterName">Name</div><!--.col-md-3-->
                                <div class="col-md-6">
                                    <div class="inputer">
                                        <div class="input-wrapper">
                                            <input name="userName" class="form-control" placeholder="User name" type="text" id="userName">
                                        </div>
                                    </div>
                                </div><!--.col-md-6-->
                            </div>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button id="searchBtn" class="searchBtn btn btn-primary btn-ripple margin-right-10">
                                    {{trans('messages.search')}} <i class="fa fa-search" aria-hidden="true"></i>
                                </button>

                                <button id="clearSearchBtn" class="searchBtn btn btn-flat-primary btn-ripple margin-right-10">
                                    {{trans('messages.clear_filters')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--.row-->
    <div class="col-md-12 centeredVertically">
        <div class="loading-bar indeterminate margin-top-10 hidden loader"></div>

        <div id="errorMsg" class="alert alert-danger stickyAlert margin-top-20 margin-bottom-20 margin-left-100 hidden" role="alert"></div>

        <div id="usersList">
            @include('users.list')
        </div>
    </div>

@endsection

