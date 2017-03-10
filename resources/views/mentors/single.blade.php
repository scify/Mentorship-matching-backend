<div class="singleItem card row">
    <div class="col-md-10" style="height:100%;">
        <div class="col-md-1 userImageContainer center-outer">
            <div class="userImage center-inner {{$mentorViewModel->mentor->status->status}}">
                <img src="{{ asset("/assets/img/mentor_default.png") }}" class="centered face-radius" alt="">
            </div>
        </div>
        <div class="col-md-8 center-outer">
            <div class="center-inner">
                <div class="centered">
                    {{$mentorViewModel->mentor->first_name}} {{$mentorViewModel->mentor->last_name}},
                    {{$mentorViewModel->mentor->job_position}}
                    @if($mentorViewModel->mentor->company != null)
                        {{ "@ " . $mentorViewModel->mentor->company->name}}
                    @endif
                    <small>, {{$mentorViewModel->mentor->age}} years old</small>

                    <div class="mentorAttrsList font-size-medium">

                        @foreach($mentorViewModel->mentor->specialties as $specialty)
                            {{$specialty->name}}
                            @if(!$loop->last)
                                ,
                            @endif
                        @endforeach
                        {{"("}}
                        @foreach($mentorViewModel->mentor->industries as $industry)
                            {{$industry->name}}
                            @if(!$loop->last)
                                ,
                            @endif
                        @endforeach
                        {{")"}}
                    </div>
                    <ul class="list-inline mentorAttrsList font-size-smaller">
                        @if($mentorViewModel->mentor->status != null)
                            <li class="padding-left-0 {{$mentorViewModel->mentor->status->status}}">{{$mentorViewModel->mentor->status->description}}</li>
                        @endif
                        |
                        <li> 3 mentor sessions</li>
                        @if($mentorViewModel->mentor->created_at!= null)
                            |
                            <li>joined: {{$mentorViewModel->mentor->created_at->diffForHumans()}}</li>
                        @endif
                        |
                        <li><a class="noPanelHeadingStyleLink"
                               href="{{route('showMentorProfilePage', $mentorViewModel->mentor->id)}}">view
                                profile</a></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-2" style="height:100%;">
        <div class="card card-clickable card-clickable-over-content" style="box-shadow: none; height:100%; text-align: right">
            <div class="clickable-button userAdminActionsBtn">
                <div class="layer bg-orange"></div>
                <div class="btn btn-floating btn-orange initial-position floating-open"><i
                            class="fa fa-cog"
                            aria-hidden="true"></i></div>
            </div>
            <div class="layered-content bg-orange col-md-3">
                <div class="overflow-content">
                    <ul class="borderless float-left" style="text-align: left">

                        <li><a href="{{route('showEditMentorForm', $mentorViewModel->mentor->id)}}"
                               class="noAfterContent btn btn-flat btn-ripple"><i class="fa fa-pencil"
                                                                                 aria-hidden="true"></i> Edit</a>
                        </li>
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
                                                                                     aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>