@extends('layouts.app')
@section('content')
    <div class="profilePage">
        <div class="page-header full-content parallax" style="height: 200px; overflow: hidden">
            <div class="profile-info">

                <div class="profile-text light">
                    {{$mentor->first_name}}  {{$mentor->last_name}},
                    <span class="caption userRole">{{trans('messages.mentor')}} <a href="{{route('showEditMentorForm', $mentor->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a> </span>
                    <span class="caption">{{trans('messages.profile_page')}} </span>
                </div><!--.profile-text-->
            </div><!--.profile-info-->

            <div class="row breadCrumpContainer">
                <div class="">
                    <ol class="breadcrumb">
                        <li><a href="{{route('home')}}"><i class="ion-home"></i></a></li>
                        <li><a href="{{route('showAllMentors')}}">mentors</a></li>
                        <li><a href="#" class="active">{{$mentor->first_name}}  {{$mentor->last_name}}</a></li>
                    </ol>
                </div><!--.col-->
            </div><!--.row-->

            <div class="header-tabs scrollable-tabs sticky">
                <ul class="nav nav-tabs tabs-active-text-white tabs-active-border-yellow">
                    <li class="active"><a data-href="details" data-toggle="tab" class="btn-ripple">{{trans('messages.info')}}</a></li>
                    <li><a data-href="skills" data-toggle="tab" class="btn-ripple">{{trans('messages.skills')}}</a></li>
                    <li><a data-href="#photos" data-toggle="tab" class="btn-ripple">{{trans('messages.matches')}}</a></li>
                </ul>
            </div>

        </div><!--.page-header-->

        <div class="user-profile">
            <div class="">
                <div class="tab-content without-border">
                    <div id="details" class="tab-pane active">
                        <div class="panel">
                            <div class="panel-heading">
                                <div class="panel-title"><h3>Basic Information</h3></div>
                            </div><!--.panel-heading-->
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">{{trans('messages.availability')}}</div>
                                        <div class="col-md-9">
                                            @if($mentor->is_available)
                                                {{trans('messages.available')}} <i class="fa fa-check green" aria-hidden="true"></i>
                                            @else
                                                {{trans('messages.not')}} {{trans('messages.available')}} <i class="fa fa-times red" aria-hidden="true"></i>
                                            @endif
                                        </div>
                                    </div><!--.row-->
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">{{trans('messages.age')}}</div>
                                        <div class="col-md-9">{{$mentor->age}}</div>
                                    </div><!--.row-->
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">{{trans('messages.email')}}</div>
                                        <div class="col-md-9">{{$mentor->email}}</div>
                                    </div><!--.row-->
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">{{trans('messages.phone')}}</div>
                                        <div class="col-md-9">{{$mentor->phone}}</div>
                                    </div><!--.row-->
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">{{trans('messages.cell_phone')}}</div>
                                        <div class="col-md-9">{{$mentor->phone}}</div>
                                    </div><!--.row-->
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">{{trans('messages.address')}}</div>
                                        <div class="col-md-9">{{$mentor->address}}</div>
                                    </div><!--.row-->
                                    @if($mentor->residence != null)
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.residence')}}</div>
                                            <div class="col-md-9">{{$mentor->residence->name}}</div>
                                        </div><!--.row-->
                                    @endif
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">{{trans('messages.linkedin')}}</div>
                                        @if($mentor->linkedin_url != null)
                                            <a href="{{$mentor->linkedin_url}}"><div class="col-md-9">{{$mentor->linkedin_url}}</div></a>
                                        @endif
                                    </div><!--.row-->

                                </div>
                                <div class="col-md-6">
                                    @if($mentor->company != null)
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.company')}}</div>
                                            <div class="col-md-9">{{$mentor->company->name}}</div>
                                        </div><!--.row-->
                                    @endif
                                    @if($mentor->company_sector != null)
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.company_sector')}}</div>
                                            <div class="col-md-9">{{$mentor->company_sector}}</div>
                                        </div><!--.row-->
                                    @endif
                                    @if($mentor->job_position != null)
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.job_position')}}</div>
                                            <div class="col-md-9">{{$mentor->job_position}}</div>
                                        </div><!--.row-->
                                    @endif
                                    @if($mentor->job_experience_years != null)
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.job_experience_years')}}</div>
                                            <div class="col-md-9">{{$mentor->job_experience_years}}</div>
                                        </div><!--.row-->
                                    @endif
                                    @if($mentor->university_name != null)
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.university')}}</div>
                                            <div class="col-md-9">{{$mentor->university_name}}</div>
                                        </div><!--.row-->
                                    @endif
                                    @if($mentor->university_department_name != null)
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.university_department')}}</div>
                                            <div class="col-md-9">{{$mentor->university_department_name}}</div>
                                        </div><!--.row-->
                                    @endif
                                </div>

                            </div><!--.panel-->
                        </div>

                    </div>
                    <div id="skills" class="tab-pane">
                        <div class="panel">
                            <div class="panel-heading">
                                <div class="panel-title"><h3>{{trans('messages.skills')}}</h3></div>
                            </div><!--.panel-heading-->
                            <div class="panel-body">
                                <div class="col-md-6">
                                    @if($mentor->skills != null)
                                        <div class="formRow row">
                                            <div class="col-md-9">{{$mentor->skills}}</div>
                                        </div><!--.row-->
                                    @endif
                                </div>
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