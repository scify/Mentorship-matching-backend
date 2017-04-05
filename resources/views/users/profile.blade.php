@extends('layouts.app')
@section('content')
    <div class="profilePage">
        <div class="page-header full-content parallax">
            <div class="profile-info">
                <div class="profile-photo">
                    <img src="{{$user->icon != null? asset($user->icon->path) : asset('/assets/img/boy.png')}}" alt="User profile image">
                </div><!--.profile-photo-->
                <div class="profile-text light">
                    {{$user->first_name}}  {{$user->last_name}}
                    <span class="caption userRole">
                        @if($loggedInUser->userHasAccessToCRUDSystemUsers() || $loggedInUser->id == $user->id)
                            <a class="margin-left-10" href="{{route('showEditUserForm', $user->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                        @endif
                    </span>
                    <span class="caption">
                        @foreach($user->roles as $role)
                            <b>{{$role->title}}</b>
                            @if(!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </span>
                </div><!--.profile-text-->
            </div><!--.profile-info-->

            <ol class="breadcrumb">
                <li><a href="{{route('home')}}"><i class="ion-home"></i></a></li>
                <li><a href="{{route('showAllUsers')}}">users</a></li>
                <li><a href="#" class="active">{{$user->first_name}}  {{$user->last_name}}</a></li>
            </ol>

            <div class="header-tabs scrollable-tabs sticky">
                <ul class="nav nav-tabs tabs-active-text-white tabs-active-border-yellow">
                    <li class="active"><a data-href="details" data-toggle="tab" class="btn-ripple">{{trans('messages.info')}}</a></li>
                    @if($user->isMatcher())
                        <li><a data-href="matches" data-toggle="tab" class="btn-ripple">{{trans('messages.matches')}}</a></li>
                    @endif
                    @if($user->isAccountManager())
                        <li><a data-href="mentorship_sessions" data-toggle="tab" class="btn-ripple">{{trans('messages.mentorship_sessions')}}</a></li>
                    @endif
                </ul>
            </div>

        </div><!--.page-header-->

        <div class="user-profile">
            <div class="tab-content without-border">
                <div id="details" class="tab-pane active">
                    <div class="col-md-6">
                        <div class="panel">
                            <div class="panel-heading">
                                <div class="panel-title"><h3>Basic Information</h3></div>
                            </div><!--.panel-heading-->
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">{{trans('messages.email')}}</div>
                                        <div class="col-md-9">{{$user->email}}</div>
                                    </div><!--.row-->
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">{{trans('messages.roles.capitalF')}}</div>
                                        <div class="col-md-9">
                                            @foreach($user->roles as $role)
                                                <b>{{$role->title}}</b>
                                                @if(!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @if($user->created_at != null)
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.joined.capitalF')}}</div>
                                            <div class="col-md-9">{{$user->created_at->format('d / m / Y')}}</div>
                                        </div><!--.row-->
                                    @endif
                                    @if($user->isAccountManager())
                                        <div id="accountManagerDetailsContainer" class="margin-top-40">
                                            <div class="formRow row">
                                                <div class="col-md-9 formElementName">ACCOUNT MANAGER DETAILS</div>
                                                <div class="col-md-3">
                                                </div>
                                            </div>
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.company')}}</div>
                                                <div class="col-md-9">
                                                    {{isset($user->company) ? $user->company->name : 'No company'}}
                                                </div>
                                            </div>
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.capacity.capitalF')}}</div>
                                                <div class="col-md-9">
                                                    Can monitor up to <span id="accountManagerCapacity">{{$user->capacity->capacity}}</span> pairs.
                                                    @if($user->id == $loggedInUser->id)
                                                        <a id="capacityEditBtn" class="margin-left-10" href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($user->id == $loggedInUser->id)
                                                <div id="capacityUpdateDiv" class="display-none">
                                                    <div class="col-md-12 padding-0">
                                                        <div id="editCapacityContainer">
                                                            <div class="col-md-3 padding-0">
                                                                <div class="inputer floating-label margin-top-0">
                                                                    <div class="input-wrapper">
                                                                        <input name="capacity" type="number" class="form-control" value="{{$user->capacity->capacity != null ? $user->capacity->capacity : ""}}">
                                                                        <label for="capacity" class="control-label">Set new Capacity</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 padding-0">
                                                                <button id="capacityEditSubmitBtn" data-userId="{{$user->id}}" class="btn btn-primary btn-ripple margin-left-20">
                                                                    Save
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="loading-bar indeterminate margin-top-30 loader hidden"></div>
                                                        </div>
                                                    </div>
                                                    <div id="errorMsg" class="position-initial alert alert-danger margin-top-10 margin-bottom-10 hidden" role="alert">Error</div>
                                                    <div id="successMsg" class="position-initial alert alert-success margin-top-10 margin-bottom-10 hidden" role="alert">Saved</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div><!--.panel-->
                    </div>
                </div>
                @if($user->isMatcher())
                    <div id="matches" class="tab-pane">
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><h3>{{trans('messages.matches')}}</h3></div>
                                </div><!--.panel-heading-->
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        You haven't performed any matches yet.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if($user->isAccountManager())
                    <div id="mentorship_sessions" class="tab-pane">
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><h3>{{trans('messages.mentorship_sessions')}}</h3></div>
                                </div><!--.panel-heading-->
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        No Mentorship sessions yet.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.TabsHandler();
            controller.init(".profilePage");
            var userProfileController = new window.UserProfileController();
            userProfileController.init();
        });
    </script>
@endsection
