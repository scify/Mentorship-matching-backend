<a href="{{ route('showMenteeProfilePage', $mentee->id) }}" class="visible" target="_blank">
    <div class="list-action-left">
        <img src="{{ asset("/assets/img/mentee_default.png") }}" class="face-radius" alt="">
    </div>
    <div class="list-content">
        <span class="title">{{ $mentee->first_name }} {{ $mentee->last_name }}</span>
        <span class="caption">{{ $mentee->job_position }}@if($mentee->job_position != "" && $mentee->university != null) - @endif
            @if($mentee->university != null){{ $mentee->university->name }}@endif</span>
    </div>
</a>
