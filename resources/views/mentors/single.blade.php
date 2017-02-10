<div class="profileCard card_{{$mentor->id}}">
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li class="active"><a data-href="tab_1_{{$mentor->id}}" data-toggle="tab" data-id="{{$mentor->id}}" >{{trans('messages.info')}}</a></li>
        <li><a data-href="tab_2_{{$mentor->id}}" data-toggle="tab" data-id="{{$mentor->id}}" >{{trans('messages.profile')}}</a></li>
        <li><a data-href="tab_3_{{$mentor->id}}" data-toggle="tab" data-id="{{$mentor->id}}" >{{trans('messages.details')}}</a></li>
    </ul>
    <div class="card card-user card-clickable card-clickable-over-content">
        <div class="profileCardBody">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1_{{$mentor->id}}">
                    <h4 class="userDetail">{{$mentor->first_name}} {{$mentor->last_name}},
                        <small>{{$mentor->job_position}}</small>
                        <small><i title="{{$mentor->is_available ? 'Available' : 'Not available'}}" class="fa fa-user {{$mentor->is_available ? 'green' : 'red'}}" aria-hidden="true"></i></small>

                    </h4>
                    @if($mentor->linkedin_url != null)
                        <a href="{{$mentor->linkedin_url}}" target="_blank"><i class="fa fa-linkedin-square linkedInIcon" aria-hidden="true"></i></a>
                    @endif
                    <p class="userDetail">{{$mentor->email}}</p>
                    <div class="userDetail">
                        Age: {{$mentor->age}}
                    </div>
                    <div class="userDetail">{{$mentor->residence->name}}</div>
                    @if($loggedInUser != null)
                        @if($loggedInUser->userHasAccessToCRUDSystemUser())
                            <div class="clickable-button">
                                <div class="layer bg-orange"></div>
                                <a class="btn btn-floating btn-orange initial-position floating-open"><i class="fa fa-cog"
                                                                                                         aria-hidden="true"></i></a>
                            </div>

                            <div class="layered-content bg-orange">
                                <div class="overflow-content">
                                    <ul class="borderless float-left">

                                        <li><a href="{{route('showEditMentorForm', $mentor->id)}}"
                                               class="btn btn-flat btn-ripple"><i class="fa fa-pencil"
                                                                                  aria-hidden="true"></i> Edit</a></li>
                                        <li>
                                            <a data-toggle="modal"
                                               data-userName="{{$mentor->first_name . $mentor->last_name}}"
                                               data-mentorId="{{$mentor->id}}"
                                               class="btn btn-flat btn-ripple deleteMentorBtn">
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
                <div class="tab-pane" id="tab_2_{{$mentor->id}}">
                    @if($mentor->specialties != null)
                        <div class="mentorAttrsList"><b>Specialties:</b>
                            @foreach($mentor->specialties as $specialty)
                                {{$specialty->name}}
                                @if(!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <hr>
                    @if($mentor->industries != null)
                            <div class="mentorAttrsList"><b>Industries:</b>
                            @foreach($mentor->industries as $industry)
                                {{$industry->name}}
                                @if(!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <hr>
                        <b>Skills:</b> {{$mentor->skills}}
                </div>
                <div class="tab-pane" id="tab_3_{{$mentor->id}}">
                        <div class="panel-group accordion" id="accordion_{{$mentor->id}}">
                            <div class="panel">
                                <div class="panel-heading active">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion_{{$mentor->id}}" href="#collapse_1_{{$mentor->id}}">Contact details</a>
                                </div>
                                <div id="collapse_1_{{$mentor->id}}" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <ul class="borderless profileDetailsList">
                                            <li>
                                                <b>Address:</b> {{$mentor->address}}
                                            </li>
                                            @if($mentor->linkedin_url != null)
                                                <li>
                                                    <a href="{{$mentor->linkedin_url}}">Linkedin</a>
                                                </li>
                                            @endif
                                            @if($mentor->phone != null)
                                                <li>
                                                    <b>Phone:</b> <a href="tel:{{$mentor->phone}}">{{$mentor->phone}}</a>
                                                </li>
                                            @endif
                                            @if($mentor->cell_phone != null)
                                                <li>
                                                    <b>Cell phone:</b> <a href="tel:{{$mentor->cell_phone}}">{{$mentor->cell_phone}}</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion_{{$mentor->id}}" href="#collapse_2_{{$mentor->id}}">Job details</a>
                                </div>
                                <div id="collapse_2_{{$mentor->id}}" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ul class="borderless profileDetailsList">
                                            @if($mentor->job_position != null)
                                                <li>
                                                    <b>Job position:</b> {{$mentor->job_position}}
                                                </li>
                                            @endif
                                                @if($mentor->job_experience_years != null)
                                                    <li>
                                                        <b>Experience years:</b> {{$mentor->job_experience_years}}
                                                    </li>
                                                @endif
                                            @if($mentor->company != null)
                                                <li>
                                                    <b>Company:</b> {{$mentor->company->name}}
                                                </li>
                                            @endif
                                            @if($mentor->company_sector != null)
                                                <li>
                                                    <b>Company sector:</b> {{$mentor->company_sector}}
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading">
                                    <a class="panel-title" data-toggle="collapse" data-parent="#accordion_{{$mentor->id}}" href="#collapse_3_{{$mentor->id}}">Education</a>
                                </div>
                                <div id="collapse_3_{{$mentor->id}}" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ul class="borderless profileDetailsList">
                                            @if($mentor->university_name != null)
                                                <li>
                                                    <b>University:</b> {{$mentor->university_name}}
                                                </li>
                                            @endif
                                            @if($mentor->university_department_name != null)
                                                <li>
                                                    <b>Department:</b> {{$mentor->university_department_name}}
                                                </li>
                                            @endif
                                            @if($mentor->company_sector != null)
                                                <li>
                                                    <b>Company sector:</b> {{$mentor->company_sector}}
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

            </div>
        </div><!--.card-body-->
        <div class="card-footer">
        </div><!--.card-footer-->
    </div><!--.card-->
</div>