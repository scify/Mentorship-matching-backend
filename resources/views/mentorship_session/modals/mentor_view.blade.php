@if(isset($mentorViewModel))
    <b id="mentorPresetName">{{$mentorViewModel->mentor->first_name}}  {{$mentorViewModel->mentor->last_name}}</b>
    <input type="hidden" name="mentor_profile_id" value="{{$mentorViewModel->mentor->id}}">
@else
    @if(isset($isCreatingNewSession) && !$isCreatingNewSession)
    <a class="name-anchor" data-url="{{ route('showMentorProfilePage', ['id' => 'id']) }}" target="_blank"><b id="mentorFullName"></b></a>
    @else
    <b id="mentorFullName"></b>
    @endif
    <input type="hidden" name="mentor_profile_id">
@endif
