<li class="{{ (Route::current()->getName() == 'showAllMentors') ? 'open' : '' }}">
    <a href="{{ route('showAllMentors') }}">Mentors </a>
</li>
<li class="{{ (Route::current()->getName() == 'showAllMentees') ? 'open' : '' }}">
    <a href="{{ route('showAllMentees') }}">Mentees </a>
</li>
<li class="{{ (Route::current()->getName() == 'showMatchesForMatcher') ? 'open' : '' }}">
    <a href="{{ route('showMatchesForMatcher', \Illuminate\Support\Facades\Auth::user()->id) }}">My Matches </a>
</li>
<li class="{{ (Route::current()->getName() == 'showAllMentorshipSessions') ? 'open' : '' }}">
    <a href="{{ route('showAllMentorshipSessions') }}"> Mentorship Sessions </a>
</li>
