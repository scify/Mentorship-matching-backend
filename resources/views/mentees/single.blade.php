<li class="has-action-left singleItem">

    @include('mentees.single-mentee-menu')

    <a href="{{route('showMenteeProfilePage', $menteeViewModel->mentee->id)}}"
       class="visible no-slide-left"
       target="_blank">
        <div class="list-action-left">
            <img src="{{ asset("/assets/img/mentee_default.png") }}" class="face-radius" alt="">
        </div>
        <div class="list-content">
            <span class="title">
                {{$menteeViewModel->mentee->first_name}} {{$menteeViewModel->mentee->last_name}}, {{$menteeViewModel->mentee->age}} y.o
                <small>
                    @if($menteeViewModel->mentee->educationLevel != null)
                            , {{ $menteeViewModel->mentee->educationLevel->name}}
                    @endif
                    @if($menteeViewModel->mentee->university != null)
                        , {{ $menteeViewModel->mentee->university->name}}
                    @endif
                    @if(!empty($menteeViewModel->avgRating))
                        <div class="rating-display">
                            @for($i = 0; $i < $menteeViewModel->avgRating; $i++)
                                <span class="glyphicon glyphicon-star"></span>
                            @endfor
                        </div>
                    @endif
                </small>
                <span class="caption">
                    {{--{{$menteeViewModel->mentee->email}}--}}
                    {{--@if($menteeViewModel->mentee->specialty != null)--}}
                            {{--{{$menteeViewModel->mentee->specialty->name}}--}}
                    {{--@endif--}}
                    @foreach($menteeViewModel->mentee->specialties as $specialty)
                        {{$specialty->name}}
                        @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                    | Experience: {{$menteeViewModel->mentee->specialty_experience}}
                </span>
                <span class="caption margin-top-5">
                    <span class="{{$menteeViewModel->mentee->is_employed ? 'green' : 'red'}}"> {{$menteeViewModel->mentee->is_employed ? 'Employed' : 'Unemployed'}}</span>
                    | {{ $menteeViewModel->numberOfTotalSessions }} total sessions
                    @if($menteeViewModel->mentee->created_at!= null)
                        | joined: {{$menteeViewModel->mentee->created_at->diffForHumans()}}
                    @endif
                </span>
            </span>
        </div>
    </a>
</li>
