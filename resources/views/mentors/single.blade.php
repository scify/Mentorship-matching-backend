<div class="col-md-12 singleAccordion">
    <div class="panel-group accordion" id="accordion">
        <div class="panel">
            <div class="panel-heading singleMentorItem  card  card-clickable card-clickable-over-content">
                <a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse_"{{$mentorViewModel->mentor->id}}>{{$mentorViewModel->mentor->first_name}} {{$mentorViewModel->mentor->last_name}},
                    <small>{{$mentorViewModel->mentor->age}} years old</small>
                </a>
                <div class="clickable-button userAdminActionsBtn">
                    <div class="layer bg-orange"></div>
                    <div class="btn btn-floating btn-orange initial-position floating-open"><i class="fa fa-cog"
                                                                                             aria-hidden="true"></i></div>
                </div>

                <div class="layered-content bg-orange">
                    <div class="overflow-content">
                        <ul class="borderless float-left">

                            <li><a href="{{route('showEditMentorForm', $mentorViewModel->mentor->id)}}"
                                   class="noAfterContent btn btn-flat btn-ripple"><i class="fa fa-pencil"
                                                                      aria-hidden="true"></i> Edit</a></li>
                            <li>
                                <div data-toggle="modal"
                                   data-userName="{{$mentorViewModel->mentor->first_name . $mentorViewModel->mentor->last_name}}"
                                   data-mentorId="{{$mentorViewModel->mentor->id}}"
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
                        @foreach($mentorViewModel->mentor->specialties as $specialty)
                            {{$specialty->name}}
                            @if(!$loop->last)
                                ,
                            @endif
                        @endforeach
                        <b class="margin-left-10">Industries:</b>
                        @foreach($mentorViewModel->mentor->industries as $industry)
                            {{$industry->name}}
                            @if(!$loop->last)
                                ,
                            @endif
                        @endforeach
                        @if($mentorViewModel->mentor->company != null)
                            <b class="margin-left-10">Company:</b>
                            {{$mentorViewModel->mentor->company->name}}
                        @endif
                    </div>
                    <div class="mentorAttrsList">
                    </div>
                </div>
            </div>
            <div id="collapse_"{{$mentorViewModel->mentor->id}} class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="col-md-2">
                        <ul class="borderless profileDetailsList">
                            @if($mentorViewModel->mentor->university_name != null)
                                <li>
                                    <b>University:</b> {{$mentorViewModel->mentor->university_name}}
                                </li>
                            @endif
                            @if($mentorViewModel->mentor->university_department_name != null)
                                <li>
                                    <b>Department:</b> {{$mentorViewModel->mentor->university_department_name}}
                                </li>
                            @endif
                            @if($mentorViewModel->mentor->company_sector != null)
                                <li>
                                    <b>Company sector:</b> {{$mentorViewModel->mentor->company_sector}}
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-md-2">
                        <ul class="borderless profileDetailsList">
                            @if($mentorViewModel->mentor->job_position != null)
                                <li>
                                    <b>Job position:</b> {{$mentorViewModel->mentor->job_position}}
                                </li>
                            @endif
                            @if($mentorViewModel->mentor->job_experience_years != null)
                                <li>
                                    <b>Experience years:</b> {{$mentorViewModel->mentor->job_experience_years}}
                                </li>
                            @endif
                            @if($mentorViewModel->mentor->company != null)
                                <li>
                                    <b>Company:</b> {{$mentorViewModel->mentor->company->name}}
                                </li>
                            @endif
                            @if($mentorViewModel->mentor->company_sector != null)
                                <li>
                                    <b>Company sector:</b> {{$mentorViewModel->mentor->company_sector}}
                                </li>
                            @endif
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>