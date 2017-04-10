@foreach($history as $historyItem)
    <div class="frame">
        <div class="timeline-badge background-{{$historyItem->status->title}}">
            <i class="fa fa-bell "></i>
        </div><!--.timeline-badge-->
        <span class="timeline-date">@if($historyItem->created_at != null){{$historyItem->created_at->format('d / m / Y')}}@endif</span>
        <div class="timeline-bubble">
            <h4 class="{{$historyItem->status->title}}">{{$historyItem->status->description}}</h4>
            <p>Comment: {{$historyItem->comment}}</p>
        </div><!--.timeline-bubble-->
    </div><!--.frame-->
@endforeach
