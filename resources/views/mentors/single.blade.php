<li class="has-action-left singleItem">

    @include('mentors.single-mentor-menu')

    <a href="{{route('showMentorProfilePage', $mentorViewModel->mentor->id)}}"
       class="visible no-slide-left" target="_blank">
        <div class="list-action-left">
            <img src="{{ asset("/assets/img/mentor_default.png") }}" class="face-radius" alt="">
        </div>
        <div class="list-content">
            <span class="title">
                {{$mentorViewModel->mentor->first_name}} {{$mentorViewModel->mentor->last_name}}, {{$mentorViewModel->mentor->age}} y.o,
                <small>
                    {{$mentorViewModel->mentor->job_position}}
                    @if($mentorViewModel->mentor->company != null)
                        {{ "@ " . $mentorViewModel->mentor->company->name}}
                    @endif
                </small>
                <span class="caption">
                    @foreach($mentorViewModel->mentor->specialties as $specialty)
                        {{$specialty->name}}
                        @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                    {{"("}}
                    @foreach($mentorViewModel->mentor->industries as $industry)
                        {{$industry->name}}
                        @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                    {{")"}}
                </span>
                <span class="caption margin-top-5">
                    @if($mentorViewModel->mentor->status != null)
                        <span class="{{$mentorViewModel->mentor->status->status}}"> {{$mentorViewModel->mentor->status->description}}</span>
                    @endif
                    | 3 mentor sessions
                    @if($mentorViewModel->mentor->created_at!= null)
                        | joined: {{$mentorViewModel->mentor->created_at->diffForHumans()}}
                    @endif
                </span>
            </span>
        </div>
    </a>
</li>
