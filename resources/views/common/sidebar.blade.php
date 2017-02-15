<div class="layer-container">
    <div class="menu-layer">
        <ul>
            @if($user->isAdmin())
                @include('common.admin_menu_items')
            @endif
            <li>
                <a class="" href="{{ url('/logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                      style="display: none;">{{ csrf_field() }} </form>
            </li>
        </ul>
    </div>
</div>
