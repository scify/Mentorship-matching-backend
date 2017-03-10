<div class="profileCard card_{{$menteeViewModel->mentee->id}}">
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li class="active"><a data-href="tab_1_{{$menteeViewModel->mentee->id}}" data-toggle="tab"
                              data-id="{{$menteeViewModel->mentee->id}}" >Info</a></li>
        <li><a data-href="tab_2_{{$menteeViewModel->mentee->id}}" data-toggle="tab"
               data-id="{{$menteeViewModel->mentee->id}}" >Profile</a></li>
        <li><a data-href="tab_3_{{$menteeViewModel->mentee->id}}" data-toggle="tab"
               data-id="{{$menteeViewModel->mentee->id}}" >Details</a></li>
        <li><a data-href="tab_4_{{$menteeViewModel->mentee->id}}" data-toggle="tab"
               data-id="{{$menteeViewModel->mentee->id}}" >Goals</a></li>
    </ul>
    <div class="card card-user card-clickable card-clickable-over-content">
        <div class="profileCardBody">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1_{{$menteeViewModel->mentee->id}}">
                    <h4 class="userDetail"><a href="{{route('showMenteeProfilePage', $menteeViewModel->mentee->id)}}"
                                              target="_blank"> {{$menteeViewModel->mentee->first_name}} {{$menteeViewModel->mentee->last_name}}</a>
                        @if($menteeViewModel->mentee->is_employed)
                            <i class="fa fa-briefcase green" aria-hidden="true" title="{{trans('messages.employed')}}"></i>
                        @else
                            <i class="fa fa-briefcase red" aria-hidden="true" title="{{trans('messages.unemployed')}}"></i>
                        @endif
                    </h4>
                    @if($menteeViewModel->mentee->linkedin_url != null)
                        <a href="{{$menteeViewModel->mentee->linkedin_url}}" target="_blank"><i class="fa fa-linkedin-square linkedInIcon" aria-hidden="true"></i></a>
                    @endif
                    <p class="userDetail">{{$menteeViewModel->mentee->email}}</p>
                    <div class="userDetail">
                        Age: {{$menteeViewModel->mentee->age}}
                    </div>
                    <div class="userDetail">{{$menteeViewModel->mentee->residence->name}}</div>
                    @if($loggedInUser != null)
                        @if($loggedInUser->userHasAccessToCRUDSystemUsers())
                            <div class="clickable-button">
                                <div class="layer bg-orange"></div>
                                <a class="btn btn-floating btn-orange initial-position floating-open"><i class="fa fa-cog"
                                                                                                         aria-hidden="true"></i></a>
                            </div>

                            <div class="layered-content bg-orange">
                                <div class="overflow-content">
                                    <ul class="borderless float-left">

                                        <li><a href="{{route('showEditMenteeForm', $menteeViewModel->mentee->id)}}"
                                               class="btn btn-flat btn-ripple"><i class="fa fa-pencil"
                                                                                  aria-hidden="true"></i> Edit</a></li>
                                        <li>
                                            <a data-toggle="modal"
                                               data-userName="{{$menteeViewModel->mentee->first_name . $menteeViewModel->mentee->last_name}}"
                                               data-menteeId="{{$menteeViewModel->mentee->id}}"
                                               class="btn btn-flat btn-ripple deleteMenteeBtn">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                    <ul class="borderless float-right">
                                    </ul>
                                </div><!--.overflow-content-->
                                <div class="clickable-close-button">
                                    <a class="btn btn-floating initial-position floating-close"><i class="fa fa-times"
                                                                                                   aria-hidden="true"></i></a>
                                </div>
                            </div>
                        @endif
                    @endif

                </div>
                <div class="tab-pane" id="tab_2_{{$menteeViewModel->mentee->id}}">
                    @if($menteeViewModel->mentee->specialty != null)
                        <div class="menteeAttrsList"><b>Requested specialty:</b>
                            {{$menteeViewModel->mentee->specialty->name}}
                        </div>
                    @endif
                    <hr>
                    <b>Specialty Experience:</b> {{$menteeViewModel->mentee->specialty_experience}}
                </div>
                <div class="tab-pane" id="tab_3_{{$menteeViewModel->mentee->id}}">
                    <div class="panel-group accordion" id="accordion_{{$menteeViewModel->mentee->id}}">
                        <div class="panel">
                            <div class="panel-heading active">
                                <a class="panel-title" data-toggle="collapse" data-parent="#accordion_{{$menteeViewModel->mentee->id}}" href="#collapse_1_{{$menteeViewModel->mentee->id}}">Contact details</a>
                            </div>
                            <div id="collapse_1_{{$menteeViewModel->mentee->id}}" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <ul class="borderless profileDetailsList">
                                        <li>
                                            <b>Address:</b> {{$menteeViewModel->mentee->address}}
                                        </li>
                                        @if($menteeViewModel->mentee->linkedin_url != null)
                                            <li>
                                                <a href="{{$menteeViewModel->mentee->linkedin_url}}">Linkedin</a>
                                            </li>
                                        @endif
                                        @if($menteeViewModel->mentee->phone != null)
                                            <li>
                                                <b>Phone:</b> <a href="tel:{{$menteeViewModel->mentee->phone}}">{{$menteeViewModel->mentee->phone}}</a>
                                            </li>
                                        @endif
                                        @if($menteeViewModel->mentee->cell_phone != null)
                                            <li>
                                                <b>Cell phone:</b> <a href="tel:{{$menteeViewModel->mentee->cell_phone}}">{{$menteeViewModel->mentee->cell_phone}}</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-heading">
                                <a class="panel-title" data-toggle="collapse" data-parent="#accordion_{{$menteeViewModel->mentee->id}}" href="#collapse_2_{{$menteeViewModel->mentee->id}}">Job details</a>
                            </div>

                            <div id="collapse_2_{{$menteeViewModel->mentee->id}}" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul class="borderless profileDetailsList">
                                        <li><b>Employed:</b> {{$menteeViewModel->mentee->is_employed ? 'Yes' : 'No'}}</li>
                                        @if($menteeViewModel->mentee->is_employed && $menteeViewModel->mentee->job_description != null)
                                            <li>
                                                <b>Job description:</b> {{$menteeViewModel->mentee->job_description}}
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-heading">
                                <a class="panel-title" data-toggle="collapse" data-parent="#accordion_{{$menteeViewModel->mentee->id}}" href="#collapse_3_{{$menteeViewModel->mentee->id}}">Education</a>
                            </div>
                            <div id="collapse_3_{{$menteeViewModel->mentee->id}}" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul class="borderless profileDetailsList">
                                        @if($menteeViewModel->mentee->university_name != null)
                                            <li>
                                                <b>University:</b> {{$menteeViewModel->mentee->university_name}}
                                            </li>
                                        @endif
                                        @if($menteeViewModel->mentee->university_department_name != null)
                                            <li>
                                                <b>Department:</b> {{$menteeViewModel->mentee->university_department_name}}
                                            </li>
                                        @endif
                                        @if($menteeViewModel->mentee->university_graduation_year != null)
                                            <li>
                                                <b>University graduation year:</b> {{$menteeViewModel->mentee->university_graduation_year}}
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab_4_{{$menteeViewModel->mentee->id}}">
                    @if($menteeViewModel->mentee->expectations != null)
                        <div class="menteeAttrsList"><b>Expectations:</b>
                            {{$menteeViewModel->mentee->expectations}}
                        </div>
                    @endif
                    <hr>
                    @if($menteeViewModel->mentee->career_goals != null)
                        <div class="menteeAttrsList"><b>Career goals:</b>
                            {{$menteeViewModel->mentee->career_goals}}
                        </div>
                    @endif
                    <hr>
                    @if($menteeViewModel->mentee->reference != null)
                        <div class="menteeAttrsList"><b>Heard about JobPairs by:</b>
                            {{$menteeViewModel->mentee->reference}}
                        </div>
                    @endif
                </div>

            </div>
        </div><!--.card-body-->
        <div class="card-footer">
        </div><!--.card-footer-->
    </div><!--.card-->
</div>
