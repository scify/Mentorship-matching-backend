<input type="hidden" name="_token" value="{{ csrf_token() }}" data-delete-history-url="{{ route("deleteSessionHistoryItem") }}"
       data-auth-user-id="{{ $loggedInUser->id }}" data-user-is-admin="{{ $loggedInUser->isAdmin() }}">
@foreach($history as $historyItem)
    <div class="frame" data-user-created-history="{{ $historyItem->user_id }}">
        <div class="timeline-badge background-{{$historyItem->status->title}}">
            <i class="fa fa-bell "></i>
        </div><!--.timeline-badge-->
        <span class="timeline-date">@if($historyItem->created_at != null){{$historyItem->created_at->format('d / m / Y')}}@endif</span>
        <div class="timeline-bubble" data-history-id="{{ $historyItem->id }}">
            @if($loop->last && ($historyItem->user_id === $loggedInUser->id || $loggedInUser->isAdmin()))
            <button type="button" class="close delete-from-timeline" aria-label="Delete"><span aria-hidden="true">&times;</span></button>
            @endif
            <h4 class="{{$historyItem->status->title}}">{{$historyItem->status->description}}</h4>
            @if(!empty($historyItem->comment))<p>{{$historyItem->comment}}</p>@endif
        </div><!--.timeline-bubble-->
    </div><!--.frame-->
@endforeach
