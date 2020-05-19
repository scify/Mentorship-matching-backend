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
                    @if(!empty($mentorViewModel->avgRating))
                        <div class="rating-display">
                            @for($i = 0; $i < $mentorViewModel->avgRating; $i++)
                                <span class="glyphicon glyphicon-star"></span>
                            @endfor
                        </div>
                    @endif
                </small>
                <span class="caption">
                    @foreach($mentorViewModel->mentor->specialties as $specialty)
                        {{$specialty->name}}
                        @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                    @if($mentorViewModel->mentor->industries->count() > 0){{"("}}@endif
                    @foreach($mentorViewModel->mentor->industries as $industry)
                        {{$industry->name}}
                        @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                    @if($mentorViewModel->mentor->industries->count() > 0){{")"}}@endif
                </span>
                <span class="caption margin-top-5">
                    @if($mentorViewModel->mentor->status != null)
                        <span class="{{$mentorViewModel->mentor->status->status}}"> {{$mentorViewModel->mentor->status->description}}</span>
                    @endif
                    | {{ $mentorViewModel->mentor->numberOfTotalSessions }} total sessions
                    @if($mentorViewModel->mentor->created_at!= null)
                        | joined: {{$mentorViewModel->mentor->created_at->diffForHumans()}}
                    @endif
                </span>
            </span>
        </div>
    </a>
</li>
