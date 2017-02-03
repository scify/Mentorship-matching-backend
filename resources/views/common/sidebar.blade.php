<div class="layer-container">
    <div class="menu-layer">
        <ul>
            @if($user->hasAccessToCreateUsers())
                <li class="{{ (Route::current()->getName() == 'showCreateUserForm') ? 'open' : '' }}">
                    <a href="{{ route('showCreateUserForm') }}"><i class="fa fa-plus-square" aria-hidden="true"></i> Create new user </a>
                </li>
                <li class="{{ (Route::current()->getName() == 'showAllUsers') ? 'open' : '' }}">
                    <a href="{{ route('showAllUsers') }}"><i class="fa fa-th-list" aria-hidden="true"></i> All Users </a>
                </li>
            @endif
        </ul>
    </div>
</div>
