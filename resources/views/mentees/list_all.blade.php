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
                <div class="panel-body filtersContainer noInputStyles" data-url="{{ route('filterMentees') }}">
                    {{--<div class="row">--}}
                        {{--<div class="col-md-3">Role</div><!--.col-md-3-->--}}
                        {{--<div class="col-md-6">--}}
                            {{--<select data-placeholder="Choose role" name="user_role" class="chosen-select">--}}
                                {{--<option><!-- Empty option allows the placeholder to take effect. --><option>--}}
                                {{--@foreach($userRoles as $userRole)--}}
                                    {{--<option value="{{$userRole->id}}">{{$userRole->title}}</option>--}}
                                {{--@endforeach--}}
                            {{--</select>--}}
                        {{--</div><!--.col-md-9-->--}}
                    {{--</div>--}}
                    <div class="row">
                        <div class="col-md-3">Mentees that completed sessions</div><!--.col-md-3-->
                        <div class="col-md-6">
                            <select data-placeholder="Choose time passed from last completed session" name="completed_session_ago" class="chosen-select">
                                <option><!-- Empty option allows the placeholder to take effect. --><option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}">{{$i}} @if($i == 1) month @else months @endif ago</option>
                                @endfor
                            </select>
                        </div><!--.col-md-9-->
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="icheckbox">
                                <label>
                                    <input type="checkbox" name="only-never-matched">
                                    Display only never matched mentees
                                </label>
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
        </div><!--.row-->
    </div>
    <div class="row">

    <div class="col-md-12 centeredVertically">
        <div class="loading-bar indeterminate margin-top-10 hidden loader"></div>
        <div id="errorMsg" class="alert alert-danger stickyAlert margin-top-20 margin-bottom-20 margin-left-100 hidden" role="alert"></div>
        <div id="menteesList">
            @include('mentees.list')
        </div>
    </div>
    </div>
    @include('mentees.modals')

@endsection

@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.MenteesListController();
            controller.init();
        });
    </script>
@endsection
