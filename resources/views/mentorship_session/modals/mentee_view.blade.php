@if(isset($menteeViewModel))
    <b>{{$menteeViewModel->mentee->first_name}}  {{$menteeViewModel->mentee->last_name}}</b>
    <input type="hidden" name="mentee_id" value="{{$menteeViewModel->mentee->id}}">
@else
    <b id="menteeFullName"></b>
    <input type="hidden" name="mentee_id">
@endif