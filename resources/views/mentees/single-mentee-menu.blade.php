@if($matchingMode)
    <a href="javascript: void(0);" class="hidden">
        <div class="matchMentorBtn"
             data-toggle="modal"
             data-userName="{{$menteeViewModel->mentee->first_name . ' ' . $menteeViewModel->mentee->last_name}}"
             data-menteeId="{{$menteeViewModel->mentee->id}}">
            <i class="matchingIcon ion-arrow-swap"></i>
        </div>
    </a>
@else
    @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
    <a href="javascript: void(0);" target="_blank"
       data-toggle="modal"
       data-userName="{{$menteeViewModel->mentee->first_name . $menteeViewModel->mentee->last_name}}"
       data-menteeId="{{$menteeViewModel->mentee->id}}"
       class="deleteMentorBtn hidden menu-action"><i class="deleteIcon ion-android-delete"></i></a>
    <a href="{{route('showEditMenteeForm', $menteeViewModel->mentee->id)}}" class="hidden secondItem menu-action"><i class="editIcon ion-edit"></i></a>
    @elseif($loggedInUser->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
        <a href="javascript:void(0)" data-toggle="modal"
           data-mentorId="{{$menteeViewModel->mentee->id}}" data-original-status="{{$menteeViewModel->mentee->status_id}}"
           class="editMentorStatusBtn hidden menu-action">
            <i class="editIcon ion-edit"></i>
        </a>
    @endif
@endif