@if(isset($menteeViewModel))
    <b id="menteePresetName">{{$menteeViewModel->mentee->first_name}}  {{$menteeViewModel->mentee->last_name}}</b>
    <input type="hidden" name="mentee_profile_id" value="{{$menteeViewModel->mentee->id}}">
@else
    @if(isset($isCreatingNewSession) && !$isCreatingNewSession)
        <a class="name-anchor" data-url="{{ route('showMenteeProfilePage', ['id' => 'id']) }}" target="_blank"><b id="menteeFullName"></b></a>
    @else
        <b id="menteeFullName"></b>
    @endif
    <input type="hidden" name="mentee_profile_id">
@endif
