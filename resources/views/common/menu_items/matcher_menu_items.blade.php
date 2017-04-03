<li class="{{ (Route::current()->getName() == 'showAllMentors') ? 'open' : '' }}">
    <a href="{{ route('showAllMentors') }}">Mentors </a>
</li>
<li class="{{ (Route::current()->getName() == 'showAllMentees') ? 'open' : '' }}">
    <a href="{{ route('showAllMentees') }}">Mentees </a>
</li>
<li class="{{ (Route::current()->getName() == 'showMatchesForMatcher') ? 'open' : '' }}">
    <a href="{{ route('showMatchesForMatcher', $user->id) }}">My Matches </a>
</li>
<li class="{{ (Route::current()->getName() == 'showAllMentorshipSessions') ? 'open' : '' }}">
    <a href="{{ route('showAllMentorshipSessions') }}"> Mentorship Sessions </a>
</li>
@if($user->isAccountManager())
    <li class="{{ (Route::current()->getName() == 'showMentorshipSessionsForAccountManager') ? 'open' : '' }}">
        <a href="{{ route('showMentorshipSessionsForAccountManager') }}"> My Mentorship Sessions </a>
    </li>
@endif