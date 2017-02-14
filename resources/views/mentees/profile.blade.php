@extends('layouts.app')
@section('content')
    <div class="profilePage">
        <div class="page-header full-content parallax" style="height: 200px; overflow: hidden">
            <div class="profile-info">

                <div class="profile-text light">
                    {{$mentee->first_name}}  {{$mentee->last_name}},
                    <span class="caption userRole">{{trans('messages.mentee')}}
                        @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
                            <a class="margin-left-10" href="{{route('showEditMenteeForm', $mentee->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                        @endif
                    </span>
                    <span class="caption">{{trans('messages.profile_page')}} </span>
                </div><!--.profile-text-->
            </div><!--.profile-info-->

            <div class="row breadCrumpContainer">
                <div class="">
                    <ol class="breadcrumb">
                        <li><a href="{{route('home')}}"><i class="ion-home"></i></a></li>
                        <li><a href="{{route('showAllMentees')}}">mentees</a></li>
                        <li><a href="#" class="active">{{$mentee->first_name}}  {{$mentee->last_name}}</a></li>
                    </ol>
                </div><!--.col-->
            </div><!--.row-->

            <div class="header-tabs scrollable-tabs sticky">
                <ul class="nav nav-tabs tabs-active-text-white tabs-active-border-yellow">
                    <li class="active"><a data-href="details" data-toggle="tab" class="btn-ripple">{{trans('messages.info')}}</a></li>
                    <li><a data-href="skills" data-toggle="tab" class="btn-ripple">{{trans('messages.specialty')}} & {{trans('messages.expectations')}}</a></li>
                    <li><a data-href="#photos" data-toggle="tab" class="btn-ripple">{{trans('messages.mentorship_sessions')}}</a></li>
                </ul>
            </div>

        </div><!--.page-header-->

        <div class="user-profile">
            <div class="">
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
                                            <div class="col-md-3 formElementName">{{trans('messages.age')}}</div>
                                            <div class="col-md-9">{{$mentee->age}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.email')}}</div>
                                            <div class="col-md-9">{{$mentee->email}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.phone')}}</div>
                                            <div class="col-md-9">{{$mentee->phone}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.cell_phone')}}</div>
                                            <div class="col-md-9">{{$mentee->phone}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.address')}}</div>
                                            <div class="col-md-9">{{$mentee->address}}</div>
                                        </div><!--.row-->
                                        @if($mentee->residence != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.residence')}}</div>
                                                <div class="col-md-9">{{$mentee->residence->name}}</div>
                                            </div><!--.row-->
                                        @endif
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.linkedin')}}</div>
                                            @if($mentee->linkedin_url != null)
                                                <a href="{{$mentee->linkedin_url}}"><div class="col-md-9">{{$mentee->linkedin_url}}</div></a>
                                            @endif
                                        </div><!--.row-->

                                    </div>

                                </div><!--.panel-->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><h3>Education & employment</h3></div>
                                </div><!--.panel-heading-->
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.employed')}}</div>
                                            <div class="col-md-9">
                                                @if($mentee->is_employed)
                                                    {{trans('messages.yes')}} <i class="fa fa-check green" aria-hidden="true"></i>
                                                @else
                                                    {{trans('messages.no')}} <i class="fa fa-times red" aria-hidden="true"></i>
                                                @endif
                                            </div>
                                        </div><!--.row-->
                                        @if($mentee->job_description != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.job_description')}}</div>
                                                <div class="col-md-9">{{$mentee->job_description}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($mentee->university_name != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.university')}}</div>
                                                <div class="col-md-9">{{$mentee->university_name}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($mentee->university_department_name != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.university_department')}}</div>
                                                <div class="col-md-9">{{$mentee->university_department_name}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($mentee->university_graduation_year != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.university_graduation_year')}}</div>
                                                <div class="col-md-9">{{$mentee->university_graduation_year}}</div>
                                            </div><!--.row-->
                                        @endif
                                    </div>
                                </div><!--.panel-->
                            </div>
                        </div>
                    </div>
                    <div id="skills" class="tab-pane">
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><h3>{{trans('messages.specialty')}}</h3></div>
                                </div><!--.panel-heading-->
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.specialty')}}</div>
                                            <div class="col-md-9">{{$mentee->specialty->name}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.specialty_experience')}}</div>
                                            <div class="col-md-9">{{$mentee->specialty_experience}}</div>
                                        </div><!--.row-->
                                    </div>
                                </div><!--.panel-->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><h3>{{trans('messages.goals')}} & {{trans('messages.expectations')}}</h3></div>
                                </div><!--.panel-heading-->
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        @if($mentee->career_goals != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.career_goals')}}</div>
                                                <div class="col-md-9">{{$mentee->career_goals}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($mentee->university_name != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.expectations')}}</div>
                                                <div class="col-md-9">{{$mentee->expectations}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($loggedInUser != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.heard_about')}}</div>
                                                <div class="col-md-9">{{$mentee->reference}}</div>
                                            </div><!--.row-->
                                        @endif
                                    </div>
                                </div><!--.panel-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.MentorProfileController();
            controller.init();
        });
    </script>
@endsection