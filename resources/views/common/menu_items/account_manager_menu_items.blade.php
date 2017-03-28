@if($user->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
    <li data-open-after="{{Route::current()->getName() == 'showAllMentors' ? 'true' : ''}}">
        <a href="{{ route('showAllMentors') }}"> All Mentors </a>
    </li>
@endif
@if($user->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
    <li data-open-after="{{Route::current()->getName() == 'showAllMentees' ? 'true' : ''}}">
        <a href="{{ route('showAllMentees') }}"> All Mentees </a>
    </li>
@endif
