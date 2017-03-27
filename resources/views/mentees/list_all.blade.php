@extends('layouts.app')
@section('content')

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
                <div class="col-md-3 filterName">Mentees' name</div><!--.col-md-3-->
                <div class="col-md-6">
                    <div class="inputer">
                        <div class="input-wrapper">
                            <input name="mentee_name" class="form-control" placeholder="Mentee name" type="text" style="width: 100%;">
                        </div>
                    </div>
                </div><!--.col-md-6-->
            </div>
            <div class="row">
                <div class="col-md-3 filterName">Mentees' age</div><!--.col-md-3-->
                <div class="col-md-6">
                    <input id="age" name="age" placeholder="age" type="text">
                </div><!--.col-md-6-->
            </div>
            <div class="row">
                <div class="col-md-3 filterName">Mentees' skills (comma separated)</div><!--.col-md-3-->
                <div class="col-md-6">
                    <div class="inputer">
                        <div class="input-wrapper">
                            <input name="mentee_skills" class="form-control" placeholder="Mentees' skills" type="text" style="width: 100%;">
                        </div>
                    </div>
                </div><!--.col-md-6-->
            </div>
            <div class="row">
                <div class="col-md-3 filterName">Mentees' education level</div><!--.col-md-3-->
                <div class="col-md-3">
                    <select data-placeholder="Choose an education level" name="education_level" class="chosen-select">
                        <option><!-- Empty option allows the placeholder to take effect. --><option>
                    @foreach($educationLevels as $educationLevel)
                        <option value="{{ $educationLevel->id }}">{{ $educationLevel->name }}</option>
                        @endforeach
                    </select>
                </div><!--.col-md-3-->
                <div class="col-md-3 filterName">Mentees' university</div><!--.col-md-3-->
                <div class="col-md-3">
                    <select data-placeholder="Choose a university" name="university" class="chosen-select">
                        <option><!-- Empty option allows the placeholder to take effect. --><option>
                    @foreach($universities as $university)
                        <option value="{{ $university->id }}">{{ $university->name }}</option>
                        @endforeach
                    </select>
                </div><!--.col-md-3-->
            </div>
            <div class="row">
                <div class="col-md-3 filterName">Mentees that signed up</div><!--.col-md-3-->
                <div class="col-md-3">
                    <select data-placeholder="Choose time passed from sign up" name="signed_up_ago" class="chosen-select">
                        <option><!-- Empty option allows the placeholder to take effect. --><option>
                        @for($i = 1; $i <= 13; $i++)
                            <option value="{{$i}}">
                                @if($i < 13)
                                    {{$i}} @if($i == 1) month @else months @endif ago
                                @else
                                    more that a year ago
                                @endif
                            </option>
                        @endfor
                    </select>
                </div><!--.col-md-6-->
                <div class="col-md-3 filterName">Mentees that completed sessions</div><!--.col-md-3-->
                <div class="col-md-3">
                    <select data-placeholder="Choose time passed from completed session" name="completed_session_ago" class="chosen-select">
                        <option><!-- Empty option allows the placeholder to take effect. --><option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{$i}}">{{$i}} @if($i == 1) month @else months @endif ago</option>
                        @endfor
                    </select>
                </div><!--.col-md-6-->
            </div>
            <div class="row">
                <div class="col-md-3 filterName">
                    <label for="only-unemployed-mentees">Mentees that are unemployed</label>
                </div>
                <div class="col-md-3">
                    <div class="icheckbox">
                        <input type="checkbox" name="only_unemployed_mentees" id="only-unemployed-mentees">
                    </div>
                </div>
                <div class="col-md-3 filterName">
                    <label for="only-active-sessions">Mentees with active sessions</label>
                </div>
                <div class="col-md-3">
                    <div class="icheckbox">
                        <input type="checkbox" name="only_active_sessions" id="only-active-sessions">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 filterName">
                    <label for="only-never-matched">Mentees with no sessions</label>
                </div>
                <div class="col-md-3">
                    <div class="icheckbox">
                        <input type="checkbox" name="only_never_matched" id="only-never-matched">
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

    <div class="loading-bar indeterminate margin-top-10 hidden loader"></div>
    <div id="errorMsg" class="alert alert-danger stickyAlert margin-top-20 margin-bottom-20 margin-left-100 hidden" role="alert"></div>
    <div id="menteesList">
        @include('mentees.list')
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
