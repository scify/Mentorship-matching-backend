@if($user->userHasAccessToCRUDMentorsAndMentees())
    <li>
        <a href="javascript:;">Mentors </a>
        <ul class="child-menu">
            <li data-open-after="{{Route::current()->getName() == 'showAllMentors' ? 'true' : ''}}">
                <a href="{{ route('showAllMentors') }}"> All Mentors </a>
            </li>
            <li data-open-after="{{Route::current()->getName() == 'showCreateMentorForm' ? 'true' : ''}}">
                <a href="{{ route('showCreateMentorForm') }}"> Create new Mentor </a>
            </li>
        </ul>
    </li>
@endif
@if($user->userHasAccessToCRUDCompanies())
    <li>
        <a href="javascript:;"> Companies </a>
        <ul class="child-menu">
            <li data-open-after="{{Route::current()->getName() == 'showAllCompanies' ? 'true' : ''}}">
                <a href="{{ route('showAllCompanies') }}"> All Companies </a>
            </li>
            <li data-open-after="{{Route::current()->getName() == 'showCreateCompanyForm' ? 'true' : ''}}">
                <a href="{{ route('showCreateCompanyForm') }}"> Create new Company </a>
            </li>
        </ul>
    </li>
@endif
@if($user->userHasAccessToCRUDMentorsAndMentees())
    <li>
        <a href="javascript:;"> Mentees </a>
        <ul class="child-menu">
            <li data-open-after="{{Route::current()->getName() == 'showAllMentees' ? 'true' : ''}}">
                <a href="{{ route('showAllMentees') }}"> All Mentees </a>
            </li>
            <li data-open-after="{{Route::current()->getName() == 'showCreateMenteeForm' ? 'true' : ''}}">
                <a href="{{ route('showCreateMenteeForm') }}"> Create new Mentee </a>
            </li>
        </ul>
    </li>
@endif
@if($user->userHasAccessToCRUDSystemUsers())
    <li>
        <a href="javascript:;"> System users </a>
        <ul class="child-menu">
            <li data-open-after="{{Route::current()->getName() == 'showAllUsers' ? 'true' : ''}}">
                <a href="{{ route('showAllUsers') }}"> All Users </a>
            </li>
            <li data-open-after="{{Route::current()->getName() == 'showCreateUserForm' ? 'true' : ''}}">
                <a href="{{ route('showCreateUserForm') }}"> Create new user </a>
            </li>
        </ul>
    </li>
@endif
<li class="{{ (Route::current()->getName() == 'showAllReports') ? 'open' : '' }}">
    <a href="{{ route('showAllReports') }}"> Reports </a>
</li>