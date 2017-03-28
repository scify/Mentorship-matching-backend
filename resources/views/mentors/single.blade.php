<li class="has-action-left singleItem {{$actionButtonsNum == 2 ? 'twoActionButtons' : ''}}">
    @if($matchingMode)
        <a href="javascript: void(0);" class="hidden">
            <div class="matchMentorBtn"
                 data-toggle="modal"
                 data-userName="{{$mentorViewModel->mentor->first_name . $mentorViewModel->mentor->last_name}}"
                 data-menteeId="{{$mentorViewModel->mentor->id}}">
                <i class="matchingIcon ion-arrow-swap"></i>
            </div>
        </a>
    @else
        @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
            <a href="javascript: void(0);" target="_blank"
               data-toggle="modal"
               data-userName="{{$mentorViewModel->mentor->first_name . $mentorViewModel->mentor->last_name}}"
               data-menteeId="{{$mentorViewModel->mentor->id}}"
               class="deleteMentorBtn hidden"><i class="deleteIcon ion-android-delete"></i></a>
            <a href="{{route('showEditMentorForm', $mentorViewModel->mentor->id)}}" class="hidden secondItem"><i class="editIcon ion-edit"></i></a>
        @elseif($loggedInUser->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
            <a href="javascript:void(0)" data-toggle="modal"
               data-mentorId="{{$mentorViewModel->mentor->id}}" data-original-status="{{$mentorViewModel->mentor->status_id}}"
               class="editMentorStatusBtn hidden">
                <i class="editIcon ion-edit"></i>
            </a>
        @endif
    @endif

    <a href="{{route('showMentorProfilePage', $mentorViewModel->mentor->id)}}"
       class="visible
       @if($matchingMode)
           @if(!$loggedInUser->isMatcher())
                {{ 'no-slide-left'}}
           @endif
       @else
           @if(!$loggedInUser->userHasAccessToCRUDMentorsAndMentees() || !$loggedInUser->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
                {{ 'no-slide-left'}}
           @endif
       @endif"
       target="_blank">
        <div class="list-action-left">
            <img src="{{ asset("/assets/img/mentor_default.png") }}" class="face-radius" alt="">
        </div>
        <div class="list-content">
            <span class="title">
                {{$mentorViewModel->mentor->first_name}} {{$mentorViewModel->mentor->last_name}}, {{$mentorViewModel->mentor->age}} y.o,
                <small>
                    {{$mentorViewModel->mentor->job_position}}
                    @if($mentorViewModel->mentor->company != null)
                        {{ "@ " . $mentorViewModel->mentor->company->name}}
                    @endif
                </small>
                <span class="caption">
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
                </span>
                <span class="caption margin-top-5">
                    @if($mentorViewModel->mentor->status != null)
                        <span class="{{$mentorViewModel->mentor->status->status}}"> {{$mentorViewModel->mentor->status->description}}</span>
                    @endif
                    | 3 mentor sessions
                    @if($mentorViewModel->mentor->created_at!= null)
                        | joined: {{$mentorViewModel->mentor->created_at->diffForHumans()}}
                    @endif
                </span>
            </span>
        </div>
    </a>
</li>
