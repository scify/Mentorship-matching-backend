<li class="has-action-left singleItem">
    @if(\Illuminate\Support\Facades\Auth::user()->userHasAccessToCRUDMentorsAndMentees())
        <a href="#"
           data-toggle="modal"
           data-userName="{{$menteeViewModel->mentee->first_name . $menteeViewModel->mentee->last_name}}"
           data-menteeId="{{$menteeViewModel->mentee->id}}"
           class="deleteMenteeBtn hidden"><i class="deleteIcon ion-android-delete"></i></a>
        <a href="{{route('showEditMenteeForm', $menteeViewModel->mentee->id)}}" class="hidden secondItem"><i class="editIcon ion-edit"></i></a>
    @endif
    <a href="{{route('showMenteeProfilePage', $menteeViewModel->mentee->id)}}" class="visible {{Illuminate\Support\Facades\Auth::user()->userHasAccessToCRUDMentorsAndMentees() ? '':'no-slide-left'}}">
        <div class="list-action-left">
            <img src="{{ asset("/assets/img/mentee_default.png") }}" class="face-radius" alt="">
        </div>
        <div class="list-content">
            <span class="title">
                {{$menteeViewModel->mentee->first_name}} {{$menteeViewModel->mentee->last_name}}
                <small>
                @if($menteeViewModel->mentee->educationLevel != null)
                        , {{ $menteeViewModel->mentee->educationLevel->name}}
                @endif
                @if($menteeViewModel->mentee->university != null)
                    , {{ $menteeViewModel->mentee->university->name}}
                @endif
                , {{$menteeViewModel->mentee->age}} years old</small>
                <span class="caption">
                    {{$menteeViewModel->mentee->email}}
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