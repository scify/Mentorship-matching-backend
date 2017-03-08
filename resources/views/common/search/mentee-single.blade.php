<a href="javascript:void(0)" class="visible">
    <div class="list-action-left">
        <img src="{{ asset("/assets/img/mentee_default.png") }}" class="face-radius" alt="">
    </div>
    <div class="list-content">
        <span class="title">{{ $mentee->first_name }} {{ $mentee->last_name }}</span>
        <span class="caption">{{ $mentee->job_position }} - {{ $mentee->company_name }}</span>
    </div>
</a>
