<div class="col-md-12 singleAccordion">
    <div class="panel-group accordion" id="accordion">
        <div class="panel">
            <div class="panel-heading card-heading heading-full card card-user card-clickable card-clickable-over-content">
                <a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse_"{{$mentor->id}}>{{$mentor->first_name}} {{$mentor->last_name}},
                    <small>{{intval(date("Y")) - intval($mentor->year_of_birth)}} years old</small>
                </a>
                <div class="clickable-button userAdminActionsBtn">
                    <div class="layer bg-orange"></div>
                    <div class="btn btn-floating btn-orange initial-position floating-open"><i class="fa fa-cog"
                                                                                             aria-hidden="true"></i></div>
                </div>

                <div class="layered-content bg-orange">
                    <div class="overflow-content">
                        <ul class="borderless float-left">

                            <li><a href="{{route('showEditMentorForm', $mentor->id)}}"
                                   class="noAfterContent btn btn-flat btn-ripple"><i class="fa fa-pencil"
                                                                      aria-hidden="true"></i> Edit</a></li>
                            <li>
                                <div data-toggle="modal"
                                   data-userName="{{$mentor->first_name . $mentor->last_name}}"
                                   data-mentorId="{{$mentor->id}}"
                                   class="btn btn-flat btn-ripple deleteMentorBtn">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                </div>
                            </li>
                        </ul>
                        <ul class="borderless float-right">
                        </ul>
                    </div><!--.overflow-content-->
                    <div class="clickable-close-button">
                        <div class="btn btn-floating initial-position floating-close"><i class="fa fa-times"
                                                                                       aria-hidden="true"></i></div>
                    </div>
                </div>
                <div class="padding-left-20">
                    <div class="mentorAttrsList"><b>Specialties:</b>
                        @foreach($mentor->specialties as $specialty)
                            {{$specialty->name}}
                            @if(!$loop->last)
                                ,
                            @endif
                        @endforeach
                        <b class="margin-left-10">Industries:</b>
                        @foreach($mentor->industries as $industry)
                            {{$industry->name}}
                            @if(!$loop->last)
                                ,
                            @endif
                        @endforeach
                        @if($mentor->company != null)
                            <b class="margin-left-10">Company:</b>
                            {{$mentor->company->name}}
                        @endif
                    </div>
                    <div class="mentorAttrsList">
                    </div>
                </div>
            </div>
            <div id="collapse_"{{$mentor->id}} class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
        </div>
    </div>
</div>