<li class="{{ (Route::current()->getName() == 'showMatchesForMatcher') ? 'open' : '' }}">
    <a href="{{ route('showMatchesForMatcher) }}">My Matches </a>
</li>
@if($user->isAccountManager())
    <li class="{{ (Route::current()->getName() == 'showMentorshipSessionsForAccountManager') ? 'open' : '' }}">
        <a href="{{ route('showMentorshipSessionsForAccountManager') }}"> My Mentorship Sessions </a>
    </li>
@endif
<li class="{{ (Route::current()->getName() == 'showAllMentorshipSessions') ? 'open' : '' }}">
    <a href="{{ route('showAllMentorshipSessions') }}">All Mentorship Sessions </a>
</li>
<li class="{{ (Route::current()->getName() == 'showAllMentors') ? 'open' : '' }}">
    <a href="{{ route('showAllMentors') }}">Mentors </a>
</li>
<li class="{{ (Route::current()->getName() == 'showAllMentees') ? 'open' : '' }}">
    <a href="{{ route('showAllMentees') }}">Mentees </a>
</li>