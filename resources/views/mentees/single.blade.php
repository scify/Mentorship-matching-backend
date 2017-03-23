<li class="has-action-left singleItem">
    <a href="#"
       data-toggle="modal"
       data-userName="{{$menteeViewModel->mentee->first_name . $menteeViewModel->mentee->last_name}}"
       data-menteeId="{{$menteeViewModel->mentee->id}}"
       class="deleteMenteeBtn hidden"><i class="deleteIcon ion-android-delete"></i></a>
    <a href="{{route('showEditMenteeForm', $menteeViewModel->mentee->id)}}" class="hidden secondItem"><i class="editIcon ion-edit"></i></a>
    <a href="{{route('showMenteeProfilePage', $menteeViewModel->mentee->id)}}" class="visible">
        <div class="list-action-left">
            <img src="{{ asset("/assets/img/mentee_default.png") }}" class="face-radius" alt="">
        </div>
        <div class="list-content">
            <span class="title">
                {{$menteeViewModel->mentee->first_name}} {{$menteeViewModel->mentee->last_name}},
                {{$menteeViewModel->mentee->university_name}}
                @if($menteeViewModel->mentee->company != null)
                    {{ "@ " . $menteeViewModel->mentee->company->name}}
                @endif
                <small>, {{$menteeViewModel->mentee->age}} years old</small>
                <span class="caption">
                    @if($menteeViewModel->mentee->specialty != null)
                            {{$menteeViewModel->mentee->specialty->name}}
                    @endif
                    | Experience: {{$menteeViewModel->mentee->specialty_experience}}
                </span>
                <span class="caption margin-top-5">
                    @if($menteeViewModel->mentee->status != null)
                        <span class="{{$menteeViewModel->mentee->status->status}}"> {{$menteeViewModel->mentee->status->description}}</span>
                    @endif
                    | 3 mentee sessions
                    @if($menteeViewModel->mentee->created_at!= null)
                        | joined: {{$menteeViewModel->mentee->created_at->diffForHumans()}}
                    @endif
                </span>
            </span>
        </div>
    </a>
</li>