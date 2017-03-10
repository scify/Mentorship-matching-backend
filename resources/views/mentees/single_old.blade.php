<div class="profileCard card_{{$mentee->id}}">
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li class="active"><a data-href="tab_1_{{$mentee->id}}" data-toggle="tab" data-id="{{$mentee->id}}" >Info</a></li>
        <li><a data-href="tab_2_{{$mentee->id}}" data-toggle="tab" data-id="{{$mentee->id}}" >Profile</a></li>
        <li><a data-href="tab_3_{{$mentee->id}}" data-toggle="tab" data-id="{{$mentee->id}}" >Details</a></li>
        <li><a data-href="tab_4_{{$mentee->id}}" data-toggle="tab" data-id="{{$mentee->id}}" >Goals</a></li>
    </ul>
    <div class="card card-user card-clickable card-clickable-over-content">
        <div class="profileCardBody">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1_{{$mentee->id}}">
                    <h4 class="userDetail"><a href="{{route('showMenteeProfilePage', $mentee->id)}}" target="_blank"> {{$mentee->first_name}} {{$mentee->last_name}}</a>
                    @if($mentee->is_employed)
                        <i class="fa fa-briefcase green" aria-hidden="true" title="{{trans('messages.employed')}}"></i>
                    @else
                        <i class="fa fa-briefcase red" aria-hidden="true" title="{{trans('messages.unemployed')}}"></i>
                    @endif
                    </h4>
                    @if($mentee->linkedin_url != null)
                        <a href="{{$mentee->linkedin_url}}" target="_blank"><i class="fa fa-linkedin-square linkedInIcon" aria-hidden="true"></i></a>
                    @endif
                    <p class="userDetail">{{$mentee->email}}</p>
                    <div class="userDetail">
                        Age: {{$mentee->year_of_birth}}
                    </div>
                    <div class="userDetail">{{$mentee->residence->name}}</div>
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

                                        <li><a href="{{route('showEditMenteeForm', $mentee->id)}}"
                                               class="btn btn-flat btn-ripple"><i class="fa fa-pencil"
                                                                                  aria-hidden="true"></i> Edit</a></li>
                                        <li>
                                            <a data-toggle="modal"
                                               data-userName="{{$mentee->first_name . $mentee->last_name}}"
                                               data-menteeId="{{$mentee->id}}"
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
                <div class="tab-pane" id="tab_2_{{$mentee->id}}">
                    @if($mentee->specialty != null)
                        <div class="menteeAttrsList"><b>Requested specialty:</b>
                            {{$mentee->specialty->name}}
                        </div>
                    @endif
                    <hr>
                    <b>Specialty Experience:</b> {{$mentee->specialty_experience}}
                </div>
                <div class="tab-pane" id="tab_3_{{$mentee->id}}">
                        <div class="panel-group accordion" id="accordion_{{$mentee->id}}">
                            <div class="panel">
                                <div class="panel-heading active">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion_{{$mentee->id}}" href="#collapse_1_{{$mentee->id}}">Contact details</a>
                                </div>
                                <div id="collapse_1_{{$mentee->id}}" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <ul class="borderless profileDetailsList">
                                            <li>
                                                <b>Address:</b> {{$mentee->address}}
                                            </li>
                                            @if($mentee->linkedin_url != null)
                                                <li>
                                                    <a href="{{$mentee->linkedin_url}}">Linkedin</a>
                                                </li>
                                            @endif
                                            @if($mentee->phone != null)
                                                <li>
                                                    <b>Phone:</b> <a href="tel:{{$mentee->phone}}">{{$mentee->phone}}</a>
                                                </li>
                                            @endif
                                            @if($mentee->cell_phone != null)
                                                <li>
                                                    <b>Cell phone:</b> <a href="tel:{{$mentee->cell_phone}}">{{$mentee->cell_phone}}</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion_{{$mentee->id}}" href="#collapse_2_{{$mentee->id}}">Job details</a>
                                </div>

                                <div id="collapse_2_{{$mentee->id}}" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ul class="borderless profileDetailsList">
                                            <li><b>Employed:</b> {{$mentee->is_employed ? 'Yes' : 'No'}}</li>
                                            @if($mentee->is_employed && $mentee->job_description != null)
                                                <li>
                                                    <b>Job description:</b> {{$mentee->job_description}}
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion_{{$mentee->id}}" href="#collapse_3_{{$mentee->id}}">Education</a>
                                </div>
                                <div id="collapse_3_{{$mentee->id}}" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ul class="borderless profileDetailsList">
                                            @if($mentee->university_name != null)
                                                <li>
                                                    <b>University:</b> {{$mentee->university_name}}
                                                </li>
                                            @endif
                                            @if($mentee->university_department_name != null)
                                                <li>
                                                    <b>Department:</b> {{$mentee->university_department_name}}
                                                </li>
                                            @endif
                                            @if($mentee->university_graduation_year != null)
                                                <li>
                                                    <b>University graduation year:</b> {{$mentee->university_graduation_year}}
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="tab-pane" id="tab_4_{{$mentee->id}}">
                    @if($mentee->expectations != null)
                        <div class="menteeAttrsList"><b>Expectations:</b>
                            {{$mentee->expectations}}
                        </div>
                    @endif
                    <hr>
                    @if($mentee->career_goals != null)
                        <div class="menteeAttrsList"><b>Career goals:</b>
                            {{$mentee->career_goals}}
                        </div>
                    @endif
                    <hr>
                    @if($mentee->reference != null)
                        <div class="menteeAttrsList"><b>Heard about JobPairs by:</b>
                            {{$mentee->reference}}
                        </div>
                    @endif
                </div>

            </div>
        </div><!--.card-body-->
        <div class="card-footer">
        </div><!--.card-footer-->
    </div><!--.card-->
</div>