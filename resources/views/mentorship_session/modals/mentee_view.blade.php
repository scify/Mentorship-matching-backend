@if(isset($menteeViewModel))
    <b id="menteePresetName">{{$menteeViewModel->mentee->first_name}}  {{$menteeViewModel->mentee->last_name}}</b>
    <input type="hidden" name="mentee_profile_id" value="{{$menteeViewModel->mentee->id}}">
@else
    <b id="menteeFullName"></b>
    <input type="hidden" name="mentee_profile_id">
@endif
