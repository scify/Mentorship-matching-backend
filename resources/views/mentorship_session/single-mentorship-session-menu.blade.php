@if($loggedInUser->userHasAccessToCRUDMentorshipSessions())
    <a href="javascript: void(0);"
       data-toggle="modal"
       {{--data-userName="{{$mentorViewModel->mentor->first_name . $mentorViewModel->mentor->last_name}}"--}}
       {{--data-menteeId="{{$mentorViewModel->mentor->id}}"--}}
       class="deleteSessionBtn hidden menu-action"><i class="deleteIcon ion-android-delete"></i></a>
    <a href="javascript:void(0)" class="editSessionBtn hidden secondItem menu-action"><i class="editIcon ion-edit"></i></a>
@elseif($loggedInUser->userHasAccessToOnlyEditStatusForMentorshipSessions())
    <a href="javascript:void(0)" data-toggle="modal"
       {{--data-mentorId="{{$mentorViewModel->mentor->id}}" data-original-status="{{$mentorViewModel->mentor->status_id}}"--}}
       class="editSessionBtn hidden menu-action">
        <i class="editIcon ion-edit"></i>
    </a>
@endif
