@if($user->userHasAccessToCRUDMentorsAndMentees())
    <li>
        <a href="javascript:;"><i class="fa fa-university"></i> Mentors </a>
        <ul class="child-menu">
            <li data-open-after="{{Route::current()->getName() == 'showAllMentors' ? 'true' : ''}}">
                <a href="{{ route('showAllMentors') }}"><i class="fa fa-th-list" aria-hidden="true"></i> All Mentors </a>
            </li>
            <li data-open-after="{{Route::current()->getName() == 'showCreateMentorForm' ? 'true' : ''}}">
                <a href="{{ route('showCreateMentorForm') }}"><i class="fa fa-user-plus" aria-hidden="true"></i> Create new Mentor </a>
            </li>
        </ul>
    </li>
@endif
@if($user->userHasAccessToCRUDCompanies())
    <li>
        <a href="javascript:;"><i class="fa fa-building-o"></i> Companies </a>
        <ul class="child-menu">
            <li data-open-after="{{Route::current()->getName() == 'showAllCompanies' ? 'true' : ''}}">
                <a href="{{ route('showAllCompanies') }}"><i class="fa fa-th-list" aria-hidden="true"></i> All Companies </a>
            </li>
            <li data-open-after="{{Route::current()->getName() == 'showCreateCompanyForm' ? 'true' : ''}}">
                <a href="{{ route('showCreateCompanyForm') }}"><i class="fa fa-user-plus" aria-hidden="true"></i> Create new Company </a>
            </li>
        </ul>
    </li>
@endif
@if($user->userHasAccessToCRUDMentorsAndMentees())
    <li>
        <a href="javascript:;"><i class="fa fa-graduation-cap"></i> Mentees </a>
        <ul class="child-menu">
            <li data-open-after="{{Route::current()->getName() == 'showAllMentees' ? 'true' : ''}}">
                <a href="{{ route('showAllMentees') }}"><i class="fa fa-th-list" aria-hidden="true"></i> All Mentees </a>
            </li>
            <li data-open-after="{{Route::current()->getName() == 'showCreateMenteeForm' ? 'true' : ''}}">
                <a href="{{ route('showCreateMenteeForm') }}"><i class="fa fa-plus-square" aria-hidden="true"></i> Create new Mentee </a>
            </li>
        </ul>
    </li>
@endif
@if($user->userHasAccessToCRUDSystemUser())
    <li>
        <a href="javascript:;"><i class="ion-android-list"></i> System users </a>
        <ul class="child-menu">
            <li data-open-after="{{Route::current()->getName() == 'showAllUsers' ? 'true' : ''}}">
                <a href="{{ route('showAllUsers') }}"><i class="fa fa-users" aria-hidden="true"></i> All Users </a>
            </li>
            <li data-open-after="{{Route::current()->getName() == 'showCreateUserForm' ? 'true' : ''}}">
                <a href="{{ route('showCreateUserForm') }}"><i class="fa fa-user-plus" aria-hidden="true"></i> Create new user </a>
            </li>
        </ul>
    </li>
@endif