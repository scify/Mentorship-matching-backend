@if(\Illuminate\Support\Facades\Auth::user()->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
<div class="modal scale fade" id="editMentorStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{trans('messages.change_mentor_status')}}</h4>
            </div>
            <form method="POST" action="{{ route("changeMentorStatus") }}" enctype="multipart/form-data">
                <div class="modal-body jobPairsForm noInputStyles">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="mentor_id">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Mentor status -->
                            <div class="margin-bottom-5 selecterTitle">{{trans('messages.mentor_status')}}</div>
                            <select data-placeholder="select" name="status_id" class="chosen-select"
                                    data-original-value=""
                                    data-enable-follow-up-date="2,4">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->description}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="status-change-comment" style="display: none;">
                            <!-- Status change comment -->
                            <div class="{{ $errors->first('status_history_comment')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input type="text" class="form-control" name="status_history_comment"
                                               value="{{ old('status_history_comment')}}">
                                        <label for="status_history_comment">{{trans('messages.status_history_comment')}}</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('status_history_comment') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="status-change-follow-up-date" style="display: none;">
                        <div class="col-md-6">
                            <!-- Mentor follow up date -->
                            <div class="{{ $errors->first('follow_up_date')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input type="text" class="form-control bootstrap-daterangepicker-basic"
                                               name="follow_up_date" value="{{ old('follow_up_date')}}">
                                        <label for="follow_up_date">{{trans('messages.follow_up_date')}}</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('follow_up_date') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-flat btn-primary submitLink">Change Status</button>
                </div>
            </form>
        </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->
@endif
@if(\Illuminate\Support\Facades\Auth::user()->userHasAccessToCRUDMentorsAndMentees())
<div class="modal scale fade" id="deleteMentorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{route('deleteMentor')}}" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="mentor_id" value="">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete the selected mentor?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-flat btn-primary btn-danger submitLink">Delete Mentor</button>
                </div>
            </form>
        </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->
@endif
@if(\Illuminate\Support\Facades\Auth::user()->isMatcher())
<div class="modal scale fade" id="matchMentorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{route('showAllMentees')}}" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="mentor_id" value="">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>

                <div class="modal-body">
                    Begin mentorship session?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-flat btn-primary btn-success submitLink disabled">Begin Session</button>
                </div>
            </form>
        </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->
@endif

