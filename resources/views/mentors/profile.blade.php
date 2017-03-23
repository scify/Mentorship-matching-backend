@extends('layouts.app')
@section('content')
    <div class="profilePage">
        <div class="page-header full-content parallax">
            <div class="profile-info">
                <div class="profile-photo">
                    <img src="{{ asset("/assets/img/mentor_default.png") }}" alt="Mentor profile image">
                </div><!--.profile-photo-->
                <div class="profile-text light">
                    {{$mentorViewModel->mentor->first_name}}  {{$mentorViewModel->mentor->last_name}},
                    <span class="caption userRole">{{trans('messages.mentor')}}
                        @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
                            <a class="margin-left-10" href="{{route('showEditMentorForm', $mentorViewModel->mentor->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                        @endif
                    </span>
                    <span class="caption">{{trans('messages.profile_page')}} </span>
                </div><!--.profile-text-->
            </div><!--.profile-info-->

            <div class="row">
                    <ol class="breadcrumb">
                        <li><a href="{{route('home')}}"><i class="ion-home"></i></a></li>
                        <li><a href="{{route('showAllMentors')}}">mentors</a></li>
                        <li><a href="#" class="active">{{$mentorViewModel->mentor->first_name}}  {{$mentorViewModel->mentor->last_name}}</a></li>
                    </ol>
            </div><!--.row-->

            <div class="header-tabs scrollable-tabs sticky">
                <ul class="nav nav-tabs tabs-active-text-white tabs-active-border-yellow">
                    <li class="active"><a data-href="details" data-toggle="tab" class="btn-ripple">{{trans('messages.info')}}</a></li>
                    <li><a data-href="skills" data-toggle="tab" class="btn-ripple">{{trans('messages.specialties')}} & {{trans('messages.skills.capitalF')}}</a></li>
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
                                            <div class="col-md-3 formElementName">{{trans('messages.year_of_birth')}}</div>
                                            <div class="col-md-9">{{$mentorViewModel->mentor->year_of_birth}}  <span class="margin-left-5"> ({{$mentorViewModel->mentor->age}} {{trans('messages.years_old')}})</span></div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.email')}}</div>
                                            <div class="col-md-9">{{$mentorViewModel->mentor->email}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.phone')}}</div>
                                            <div class="col-md-9">{{$mentorViewModel->mentor->phone}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.cell_phone')}}</div>
                                            <div class="col-md-9">{{$mentorViewModel->mentor->phone}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.address')}}</div>
                                            <div class="col-md-9">{{$mentorViewModel->mentor->address}}</div>
                                        </div><!--.row-->
                                        @if($mentorViewModel->mentor->residence != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.residence')}}</div>
                                                <div class="col-md-9">{{$mentorViewModel->mentor->residence->name}}</div>
                                            </div><!--.row-->
                                        @endif
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.linkedin')}}</div>
                                            @if($mentorViewModel->mentor->linkedin_url != null)
                                                <a href="{{$mentorViewModel->mentor->linkedin_url}}"><div class="col-md-9">{{$mentorViewModel->mentor->linkedin_url}}</div></a>
                                            @endif
                                        </div><!--.row-->
                                        @if($mentorViewModel->mentor->created_at != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.joined.capitalF')}}</div>
                                                <div class="col-md-9">{{$mentorViewModel->mentor->created_at->format('d / m / Y')}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($mentorViewModel->mentor->creator != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.created_by')}}</div>
                                                <div class="col-md-9"><a href="{{route('showUserProfile', $mentorViewModel->mentor->creator->id)}}">{{$mentorViewModel->mentor->creator->first_name}} {{$mentorViewModel->mentor->creator->last_name}}</a></div>
                                            </div><!--.row-->
                                        @else
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.created_by')}}</div>
                                                <div class="col-md-9">Public form</div>
                                            </div><!--.row-->
                                        @endif
                                    </div>

                                </div><!--.panel-->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><h3>Employment & education</h3></div>
                                </div><!--.panel-heading-->
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        @if($mentorViewModel->mentor->company != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.company')}}</div>
                                                <div class="col-md-9">{{$mentorViewModel->mentor->company->name}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($mentorViewModel->mentor->company_sector != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.company_sector')}}</div>
                                                <div class="col-md-9">{{$mentorViewModel->mentor->company_sector}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($mentorViewModel->mentor->job_position != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.job_position')}}</div>
                                                <div class="col-md-9">{{$mentorViewModel->mentor->job_position}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($mentorViewModel->mentor->job_experience_years != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.job_experience_years')}}</div>
                                                <div class="col-md-9">{{$mentorViewModel->mentor->job_experience_years}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($mentorViewModel->mentor->university_id != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.university')}}</div>
                                                <div class="col-md-9">{{$mentorViewModel->mentor->university->name}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($mentorViewModel->mentor->university_department_name != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.university_department')}}</div>
                                                <div class="col-md-9">{{$mentorViewModel->mentor->university_department_name}}</div>
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
                                    <div class="panel-title"><h3>{{trans('messages.skills.capitalF')}}</h3></div>
                                </div><!--.panel-heading-->
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        @if($mentorViewModel->mentor->skills != null)
                                            <div class="formRow row">
                                                <div class="col-md-9">{{$mentorViewModel->mentor->skills}}</div>
                                            </div><!--.row-->
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><h3>{{trans('messages.specialties')}}</h3></div>
                                </div><!--.panel-heading-->
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        @if($mentorViewModel->mentor->specialties != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.specialties')}}</div>
                                                <div class="col-md-9">
                                                @foreach($mentorViewModel->mentor->specialties as $specialty)
                                                    {{$specialty->name}}
                                                    @if(!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if($mentorViewModel->mentor->industries != null)
                                                <div class="formRow row">
                                                    <div class="col-md-3 formElementName">{{trans('messages.industries')}}</div>
                                                    <div class="col-md-9">
                                                @foreach($mentorViewModel->mentor->industries as $industry)
                                                    {{$industry->name}}
                                                    @if(!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                                    </div>
                                            </div>
                                        @endif
                                    </div>
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
            var controller = new window.ProfileController();
            controller.init();
        });
    </script>
@endsection