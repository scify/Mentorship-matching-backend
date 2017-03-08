<a href="javascript:void(0)" class="visible">
    <div class="list-action-left">
        <img src="{{ asset("/assets/img/mentor_default.png") }}" class="face-radius" alt="">
    </div>
    <div class="list-content">
        <span class="title">{{ $mentor->first_name }} {{ $mentor->last_name }}</span>
        <span class="caption">{{ $mentor->job_position }} - {{ $mentor->company_name }}</span>
    </div>
</a>
