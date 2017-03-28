@if(isset($mentorViewModel))
    <b>{{$mentorViewModel->mentor->first_name}}  {{$mentorViewModel->mentor->last_name}}</b>
    <input type="hidden" name="mentor_id" value="{{$mentorViewModel->mentor->id}}">
@else
    <b id="mentorFullName"></b>
    <input type="hidden" name="mentor_id">
@endif