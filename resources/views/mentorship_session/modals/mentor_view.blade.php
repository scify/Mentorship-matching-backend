@if(isset($mentorViewModel))
    <b id="mentorPresetName">{{$mentorViewModel->mentor->first_name}}  {{$mentorViewModel->mentor->last_name}}</b>
    <input type="hidden" name="mentor_profile_id" value="{{$mentorViewModel->mentor->id}}">
@else
    <b id="mentorFullName"></b>
    <input type="hidden" name="mentor_profile_id">
@endif
