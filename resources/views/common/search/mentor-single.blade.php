<a href="{{ route('showMentorProfilePage', $mentor->id) }}" class="visible" target="_blank">
    <div class="list-action-left">
        <img src="{{ asset("/assets/img/mentor_default.png") }}" class="face-radius" alt="">
    </div>
    <div class="list-content">
        <span class="title">{{ $mentor->first_name }} {{ $mentor->last_name }}</span>
        <span class="caption">{{ $mentor->job_position }}@if($mentor->job_position != "" && $mentor->company != null) - @endif
            @if($mentor->company != null){{ $mentor->company->name }}@endif</span>
    </div>
</a>
