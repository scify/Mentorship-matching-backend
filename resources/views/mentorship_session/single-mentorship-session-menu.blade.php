@if($loggedInUser->userHasAccessToCRUDMentorshipSessions())
    <a href="javascript:void(0)"
       data-toggle="modal"
       class="deleteSessionBtn hidden menu-action"><i class="deleteIcon ion-android-delete"></i></a>
    <a href="javascript:void(0)" class="editSessionBtn hidden secondItem menu-action"><i class="editIcon ion-edit"></i></a>
@elseif($loggedInUser->userHasAccessToOnlyEditStatusForMentorshipSessions())
    <a href="javascript:void(0)" data-toggle="modal"
       class="editSessionBtn hidden menu-action">
        <i class="editIcon ion-edit"></i>
    </a>
@endif
