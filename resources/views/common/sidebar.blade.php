<div class="layer-container">
    <div class="menu-layer">
        <ul>
            <li data-open-after="{{Route::current()->getName() == 'dashboard' ? 'true' : ''}}">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            @if($user->isAdmin())
                @include('common.menu_items.admin_menu_items')
            @elseif($user->isMatcher())
                @include('common.menu_items.matcher_menu_items')
            @endif
            <li>
                <a class="" href="{{ url('/logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                      style="display: none;">{{ csrf_field() }} </form>
            </li>
        </ul>
    </div>
    @include('common.sidebar-search')
    @include('common.sidebar-user-notifications')



</div>
