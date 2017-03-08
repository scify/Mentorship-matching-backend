<a href="{{ route('showUserProfile', ['id' => $user->id]) }}" class="visible" target="_blank">
    <div class="list-action-left">
        <img src="{{$user->icon != null? asset($user->icon->path) : asset('/assets/img/boy.png')}}" class="face-radius" alt="">
    </div>
    <div class="list-content">
        <span class="title">{{ $user->first_name }} {{ $user->last_name }}</span>
        <span class="caption">
            @foreach($user->roles as $role)
                {{ $role->title }}
                @if(!$loop->last)
                    ,
                @endif
            @endforeach
        </span>
    </div>
</a>
