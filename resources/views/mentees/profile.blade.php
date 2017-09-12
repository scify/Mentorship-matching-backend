@extends('layouts.app')
@section('content')
    <div class="profilePage">
        <div class="page-header full-content parallax" style="height: 200px; overflow: hidden">
            <div class="profile-info">
                <div class="profile-photo">
                    <img src="{{ asset("/assets/img/mentee_default.png") }}" alt="Mentee profile image">
                </div><!--.profile-photo-->
                <div class="profile-text light">
                    {{$menteeViewModel->mentee->first_name}}  {{$menteeViewModel->mentee->last_name}},
                    <span class="caption userRole">{{trans('messages.mentee')}}
                        @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees() || $loggedInUser->userHasAccessToEditMentorsAndMentees())
                            <a class="margin-left-10" href="{{route('showEditMenteeForm', $menteeViewModel->mentee->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                        @endif
                    </span>
                    <span class="caption {{$menteeViewModel->mentee->is_employed ? 'green' : 'red'}}"> {{$menteeViewModel->mentee->is_employed ? 'Employed' : 'Unemployed'}}
                        @if(!empty($menteeViewModel->avgRating))
                            <div id="profile-rating-display" class="rating-display">
                                @for($i = 0; $i < $menteeViewModel->avgRating; $i++)
                                    <span class="glyphicon glyphicon-star"></span>
                                @endfor
                            </div>
                        @endif
                    </span>
                </div><!--.profile-text-->
            </div><!--.profile-info-->

            <div class="row">
                <ol class="breadcrumb">
                    <li><a href="{{route('home')}}"><i class="ion-home"></i></a></li>
                    <li><a href="{{route('showAllMentees')}}">mentees</a></li>
                    <li><a href="#" class="active">{{$menteeViewModel->mentee->first_name}}  {{$menteeViewModel->mentee->last_name}}</a></li>
                </ol>
            </div><!--.row-->

            <div class="header-tabs scrollable-tabs sticky">
                <ul class="nav nav-tabs tabs-active-text-white tabs-active-border-yellow">
                    <li class="active"><a data-href="details" data-toggle="tab" class="btn-ripple">{{trans('messages.profile_information')}}</a></li>
                    <li><a data-href="availability" data-toggle="tab" class="btn-ripple">{{trans('messages.availability')}}</a></li>
                    <li class="match-tab-label @if($menteeViewModel->mentee->status_id != 1) disabled-tab @endif">
                        <a data-href="matching" @if($menteeViewModel->mentee->status_id != 1) onclick="event.stopPropagation();" @else data-toggle="tab" @endif class="btn-ripple">
                            {{trans('messages.match')}}
                        </a>
                    </li>
                    @if($loggedInUser->isAccountManager() || $loggedInUser->isAdmin())
                        <li><a data-href="current_session" data-toggle="tab" class="btn-ripple">{{trans('messages.current_session')}}</a></li>
                        <li><a data-href="mentorship_sessions" data-toggle="tab" class="btn-ripple">{{trans('messages.mentorship_sessions_history')}}</a></li>
                    @endif
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
                                            <div class="col-md-9">{{$menteeViewModel->mentee->year_of_birth}}  <span class="margin-left-5"> ({{$menteeViewModel->mentee->age}} {{trans('messages.years_old')}})</span></div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.email')}}</div>
                                            <div class="col-md-9">{{$menteeViewModel->mentee->email}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.phone')}}</div>
                                            <div class="col-md-9">{{$menteeViewModel->mentee->phone}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.cell_phone')}}</div>
                                            <div class="col-md-9">{{$menteeViewModel->mentee->phone}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.address')}}</div>
                                            <div class="col-md-9">{{$menteeViewModel->mentee->address}}</div>
                                        </div><!--.row-->
                                        @if($menteeViewModel->mentee->residence != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.residence')}}</div>
                                                <div class="col-md-9">{{$menteeViewModel->mentee->residence->name}}
                                                    @if(!empty($menteeViewModel->mentee->residence_name)) ({{ $menteeViewModel->mentee->residence_name }}) @endif
                                                </div>
                                            </div><!--.row-->
                                        @endif
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.linkedin')}}</div>
                                            @if($menteeViewModel->mentee->linkedin_url != null)
                                                <a target="_blank" href="{{$menteeViewModel->mentee->linkedin_url}}"><div class="col-md-9">{{$menteeViewModel->mentee->linkedin_url}}</div></a>
                                            @endif
                                        </div><!--.row-->
                                        @if($menteeViewModel->mentee->created_at != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.joined.capitalF')}}</div>
                                                <div class="col-md-9">{{$menteeViewModel->mentee->created_at->format('d / m / Y')}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($menteeViewModel->mentee->creator != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.created_by')}}</div>
                                                <div class="col-md-9"><a href="{{route('showUserProfile', $menteeViewModel->mentee->creator->id)}}">{{$menteeViewModel->mentee->creator->first_name}} {{$menteeViewModel->mentee->creator->last_name}}</a></div>
                                            </div><!--.row-->
                                        @else
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.created_by')}}</div>
                                                <div class="col-md-9">Public form</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($menteeViewModel->mentee->reference_id != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.heard_about')}}</div>
                                                <div class="col-md-9">{{$menteeViewModel->mentee->reference->name}}
                                                    @if(!empty($menteeViewModel->mentee->reference_text)) ({{ $menteeViewModel->mentee->reference_text }}) @endif
                                                </div>
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
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.employed')}}</div>
                                            <div class="col-md-9">
                                                @if($menteeViewModel->mentee->is_employed)
                                                    {{trans('messages.yes')}} <i class="fa fa-check green" aria-hidden="true"></i>
                                                @else
                                                    {{trans('messages.no')}} <i class="fa fa-times red" aria-hidden="true"></i>
                                                @endif
                                            </div>
                                        </div><!--.row-->
                                        @if($menteeViewModel->mentee->job_description != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.job_description')}}</div>
                                                <div class="col-md-9">{{$menteeViewModel->mentee->job_description}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($menteeViewModel->mentee->education_level_id != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.education_level')}}</div>
                                                <div class="col-md-9">{{$menteeViewModel->mentee->educationLevel->name}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($menteeViewModel->mentee->university_id != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.university')}}</div>
                                                <div class="col-md-9">{{$menteeViewModel->mentee->university->name}}
                                                    @if(!empty($menteeViewModel->mentee->university_name)) ({{ $menteeViewModel->mentee->university_name }}) @endif</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($menteeViewModel->mentee->university_department_name != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.university_department')}}</div>
                                                <div class="col-md-9">{{$menteeViewModel->mentee->university_department_name}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($menteeViewModel->mentee->university_graduation_year != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.university_graduation_year')}}</div>
                                                <div class="col-md-9">{{$menteeViewModel->mentee->university_graduation_year}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if(!empty($menteeViewModel->mentee->cv_file_name))
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.cv')}}</div>
                                                <div class="col-md-9">
                                                    <a href="{{ url('/') . '/uploads/cv_files/' . $menteeViewModel->mentee->cv_file_name}}"
                                                       class="btn btn-primary btn-orange btn-ripple cv-btn" target="_blank">
                                                        Download CV
                                                    </a>
                                                </div>
                                            </div><!--.row-->
                                        @endif
                                    </div>
                                </div><!--.panel-->
                            </div>
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><h3>{{trans('messages.specialties')}}</h3></div>
                                </div><!--.panel-heading-->
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.specialties')}}</div>
                                            <div class="col-md-9">
                                                @if($menteeViewModel->mentee->specialties != null)
                                                    <div class="formRow row">
                                                        @if(!$menteeViewModel->mentee->specialties->isEmpty())
                                                            @foreach($menteeViewModel->mentee->specialties as $specialty)
                                                                {{$specialty->name}}
                                                                @if(!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.specialty_experience')}}</div>
                                            <div class="col-md-9">{{$menteeViewModel->mentee->specialty_experience}}</div>
                                        </div><!--.row-->
                                        <div class="formRow row">
                                            <div class="col-md-3 formElementName">{{trans('messages.skills.capitalF')}}</div>
                                            <div class="col-md-9">{{$menteeViewModel->mentee->skills}}</div>
                                        </div><!--.row-->
                                    </div>
                                </div><!--.panel-->
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><h3>{{trans('messages.goals')}} & {{trans('messages.expectations')}}</h3></div>
                                </div><!--.panel-heading-->
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        @if($menteeViewModel->mentee->career_goals != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.career_goals')}}</div>
                                                <div class="col-md-9">{{$menteeViewModel->mentee->career_goals}}</div>
                                            </div><!--.row-->
                                        @endif
                                        @if($menteeViewModel->mentee->expectations != null)
                                            <div class="formRow row">
                                                <div class="col-md-3 formElementName">{{trans('messages.expectations')}}</div>
                                                <div class="col-md-9">{{$menteeViewModel->mentee->expectations}}</div>
                                            </div><!--.row-->
                                        @endif
                                    </div>
                                </div><!--.panel-->
                            </div>
                        </div>
                    </div>
                    <div id="availability" class="tab-pane">
                        @if($menteeViewModel->mentee->statusHistory != null)
                            <div class="col-md-12">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <div class="panel-title"><h3>Mentee status history</h3></div>
                                    </div><!--.panel-heading-->
                                    <div class="panel-body">
                                        <div class="col-md-12">
                                            <div class="timeline">
                                                @foreach($menteeViewModel->mentee->statusHistory as $historyItem)
                                                    <div class="frame">
                                                        <div class="timeline-badge background-{{$historyItem->status->status}}">
                                                            <i class="fa fa-bell "></i>
                                                        </div><!--.timeline-badge-->
                                                        <span class="timeline-date">{{$historyItem->created_at->format('d / m / Y')}}</span>
                                                        <div class="timeline-bubble">
                                                            <h4 class="{{$historyItem->status->status}}">{{$historyItem->status->description}}</h4>
                                                            @if(!empty($historyItem->comment))
                                                                <p>Comment: {{$historyItem->comment}}</p>
                                                            @endif
                                                            @if($historyItem->follow_up_date != null)
                                                                <p>Follow up date: {{ \Carbon\Carbon::parse($historyItem->follow_up_date)->format('d / m / Y')}}</p>
                                                            @endif
                                                        </div><!--.timeline-bubble-->
                                                    </div><!--.frame-->
                                                @endforeach
                                            </div><!--.timeline-->
                                        </div>
                                    </div><!--.panel-->
                                </div>
                            </div>
                        @endif
                    </div>
                    <div id="matching" class="tab-pane">
                        @include('mentors.filters')
                        @include('mentors.list', ['mentorViewModels' => $availableMentorViewModels])
                    </div>
                    @if($loggedInUser->isAccountManager() || $loggedInUser->isAdmin())
                        <div id="current_session" class="tab-pane">
                            @if(!empty($currentSessionViewModel))
                                @include('mentorship_session.list', ['mentorshipSessionViewModels' => $currentSessionViewModel])
                            @else
                                <h4 class="noSessionsMessage">No active mentorship session found.</h4>
                            @endif
                        </div>
                        <div id="mentorship_sessions" class="tab-pane">
                            @if($mentorshipSessionViewModels->count() > 0)
                                @include('mentorship_session.list')
                            @else
                                <h4 class="noSessionsMessage">No mentorship sessions to show.</h4>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($loggedInUser->userHasAccessToCRUDMentorshipSessions())
        @include('mentorship_session.modals.matching_modal_create', ['menteeViewModel' => $menteeViewModel, 'isCreatingNewSession' => true])
        @include('mentorship_session.modals.matching_modal_edit', ['menteeViewModel' => $menteeViewModel, 'isCreatingNewSession' => false])
        @include('mentorship_session.modals.delete')
    @elseif($loggedInUser->userHasAccessToOnlyEditStatusForMentorshipSessions())
        @include('mentorship_session.modals.matching_modal_edit', ['menteeViewModel' => $menteeViewModel, 'isCreatingNewSession' => false])
    @endif
    @include('mentorship_session.modals.show', ['isCreatingNewSession' => false])
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var tabsHandler = new window.TabsHandler();
            tabsHandler.init(".profilePage");
            var mentorsListController = new window.MentorsListController();
            mentorsListController.init("{{Route::currentRouteName()}}");
            var matchingController = new window.MatchingController();
            matchingController.init();
            var mentorshipSessionsListController = new window.MentorshipSessionsListController();
            mentorshipSessionsListController.init("#current_session");
            mentorshipSessionsListController.init("#mentorship_sessions");
            tabsHandler.init("#mentorshipSessionShowModal");

            // set tha "available" status as pre selected
            $("#availabilitySelect").val(1);
            $("#availabilitySelect").trigger("chosen:updated");
        });
    </script>
@endsection
