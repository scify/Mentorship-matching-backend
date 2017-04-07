<div class="modal scale fade matchMentorItem" id="matchMentorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="@if(!isset($isCreatingNewSession) || $isCreatingNewSession)
                {{route('matchMentorWithMentee')}}
                @elseif(isset($isCreatingNewSession) && !$isCreatingNewSession) {{route('updateMentorshipSession')}} @endif" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                @if(isset($isCreatingNewSession) && !$isCreatingNewSession)
                <input type="hidden" name="mentorship_session_id">
                @endif
                <div class="modal-header">
                    @if(!isset($isCreatingNewSession) || $isCreatingNewSession)
                    <h4 class="modal-title">Select an account manager from the drop-down list and begin the session</h4>
                    @elseif(isset($isCreatingNewSession) && !$isCreatingNewSession)
                    <h4 class="modal-title">Edit existing session</h4>
                    @endif
                </div>

                <div class="modal-body">
                    <div class="row margin-top-50">
                        <div class="col-md-5 col-xs-5 text-align-right">
                            <div class="row">
                                <div class="col-md-9 col-xs-9">
                                    @include('mentorship_session.modals.mentor_view')
                                    <h6>Mentor</h6>
                                </div>
                                <div class="col-md-3 col-xs-3">
                                    <img class="matchingImg face-radius" src="{{ asset("/assets/img/mentor_default.png") }}" alt="Mentor profile image">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <i class="matchingIcon ion-arrow-swap"></i>
                        </div>
                        <div class="col-md-5 col-xs-5 text-align-left">
                            <div class="row">
                                <div class="col-md-3 col-xs-3">
                                    <img class="matchingImg face-radius" src="{{ asset("/assets/img/mentee_default.png") }}" alt="Mentor profile image">
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    @include('mentorship_session.modals.mentee_view')
                                    <h6>Mentee</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($loggedInUser->isMatcher() || $loggedInUser->userHasAccessToCRUDMentorshipSessions())
                    <div class="row accountManagerSelector" @if($loggedInUser->userHasAccessToCRUDMentorshipSessions()
                        && isset($isCreatingNewSession) && !$isCreatingNewSession) style="margin-bottom: 0;" @endif>
                        <!-- Account manager -->
                        <div class="col-md-3 col-xs-3 margin-top-5">
                            <div class="selectorTitle">{{trans('messages.account_manager')}}</div>
                        </div>
                        <div class="col-md-9 col-xs-9 text-align-left">
                            <select data-placeholder="Select an account manager for the session" name="account_manager_id" class="chosen-select">
                                <option><!-- Empty option allows the placeholder to take effect. --><option>
                                @foreach($accountManagers as $accountManager)
                                    {{--showing only account managers that have a positive remaining capacity--}}
                                    @if($accountManager->remainingCapacity > 0)
                                        <option value="{{$accountManager->id}}" {{old('account_manager_id') == $accountManager->id ? 'selected' : ''}}>
                                            {{$accountManager->first_name}} {{$accountManager->last_name}} - capacity: {{$accountManager->capacity}} (remaining: {{$accountManager->remainingCapacity}})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    @if(isset($isCreatingNewSession) && !$isCreatingNewSession)
                    <div class="row sessionStatusSelector" @if($loggedInUser->userHasAccessToCRUDMentorshipSessions())
                        style="margin-top: 10px;" @endif>
                        <!-- Session Status -->
                        <div class="col-md-3 col-xs-3 margin-top-5">
                            <div class="selectorTitle">{{trans('messages.status.capitalF')}}</div>
                        </div>
                        <div class="col-md-9 col-xs-9 text-align-left">
                            <select data-placeholder="Select a status for the session" name="status_id" class="chosen-select">
                                <option><!-- Empty option allows the placeholder to take effect. --><option>
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}" {{old('status_id') == $status->id ? 'selected' : ''}}>
                                        {{$status->description}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row sessionStatusChangeComment" @if($loggedInUser->userHasAccessToCRUDMentorshipSessions())
                        style="margin-top: 10px; display: none;" @endif>
                        <!-- Session Status Change Comment -->
                        <div class="col-md-3 col-xs-3 margin-top-5">
                            <div class="selectorTitle">{{trans('messages.session_status_change_reason')}}</div>
                        </div>
                        <div class="col-md-9 col-xs-9 text-align-left">
                            <div class="inputer">
                                <div class="input-wrapper">
                                    <textarea class="form-control js-auto-size" rows="2" name="comment"
                                              placeholder="Insert mentorship session status change comment"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Cancel</button>
                    @if(isset($isCreatingNewSession) && !$isCreatingNewSession)
                    <button type="submit" class="btn btn-flat btn-primary btn-success submitLink">Update Session</button>
                    @else
                    <button type="submit" class="btn btn-flat btn-primary btn-success submitLink">Begin Session</button>
                    @endif
                </div>
            </form>
        </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->
