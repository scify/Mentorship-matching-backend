<div class="layer-container">
    <div class="menu-layer">
        <ul>
            @if($user->userHasAccessToCRUDMentorsAndMentees())
                <li>
                    <a href="javascript:;"><i class="fa fa-list-alt"></i> Mentors </a>
                    <ul class="child-menu">
                        <li data-open-after="{{Route::current()->getName() == 'showAllMentors' ? 'true' : ''}}">
                            <a href="{{ route('showAllMentors') }}"><i class="fa fa-th-list" aria-hidden="true"></i> All Mentors </a>
                        </li>
                        <li data-open-after="{{Route::current()->getName() == 'showCreateMentorForm' ? 'true' : ''}}">
                            <a href="{{ route('showCreateMentorForm') }}"><i class="fa fa-plus-square" aria-hidden="true"></i> Create new Mentor </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;"><i class="fa fa-list-alt"></i> Mentees </a>
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
                    <a href="javascript:;"><i class="fa fa-list-alt"></i> System users </a>
                    <ul class="child-menu">
                        <li data-open-after="{{Route::current()->getName() == 'showAllUsers' ? 'true' : ''}}">
                            <a href="{{ route('showAllUsers') }}"><i class="fa fa-th-list" aria-hidden="true"></i> All Users </a>
                        </li>
                        <li data-open-after="{{Route::current()->getName() == 'showCreateUserForm' ? 'true' : ''}}">
                            <a href="{{ route('showCreateUserForm') }}"><i class="fa fa-plus-square" aria-hidden="true"></i> Create new user </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</div>
