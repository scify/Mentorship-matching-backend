<li class="{{ (Route::current()->getName() == 'showMentorshipSessionsForAccountManager') ? 'open' : '' }}">
    <a href="{{ route('showMentorshipSessionsForAccountManager') }}"> My Mentorship Sessions </a>
</li>
@if($user->isMatcher())
    <li class="{{ (Route::current()->getName() == 'showMatchesForMatcher') ? 'open' : '' }}">
        <a href="{{ route('showMatchesForMatcher') }}">My Matches </a>
    </li>
@endif
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
<li class="{{ (Route::current()->getName() == 'showEditUserForm') ? 'open' : '' }}">
    <a href="{{ route('showEditUserForm', $user->id) }}"> My Profile </a>
</li>
