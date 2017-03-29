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
                </small>
                <span class="caption">
                    {{--{{$menteeViewModel->mentee->email}}--}}
                    @if($menteeViewModel->mentee->specialty != null)
                            {{$menteeViewModel->mentee->specialty->name}}
                    @endif
                    | Experience: {{$menteeViewModel->mentee->specialty_experience}}
                </span>
                <span class="caption margin-top-5">
                    <span class="{{$menteeViewModel->mentee->is_employed ? 'green' : 'red'}}"> {{$menteeViewModel->mentee->is_employed ? 'Employed' : 'Unemployed'}}</span>
                    | 3 mentee sessions
                    @if($menteeViewModel->mentee->created_at!= null)
                        | joined: {{$menteeViewModel->mentee->created_at->diffForHumans()}}
                    @endif
                </span>
            </span>
        </div>
    </a>
</li>
