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
<li class="{{ (Route::current()->getName() == 'showMentorshipSessionsForAccountManager') ? 'open' : '' }}">
    <a href="{{ route('showMentorshipSessionsForAccountManager') }}"> My Mentorship Sessions </a>
</li>
@if($user->isMatcher())
    <li class="{{ (Route::current()->getName() == 'showMatchesForMatcher') ? 'open' : '' }}">
        <a href="{{ route('showMatchesForMatcher', $user->id) }}">My Matches </a>
    </li>
@endif
