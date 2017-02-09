<div class="layer-container">
    <div class="menu-layer">
        <ul>
            @if($user->isAdmin())
                @include('common.admin_menu_items')
            @endif
        </ul>
    </div>
</div>
