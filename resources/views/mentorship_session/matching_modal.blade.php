<div class="modal scale fade" id="matchMentorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{route('showAllMentees')}}" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="mentor_id" value="">
                <div class="modal-header">
                    <h4 class="modal-title">Select an account manager from the drop-down list and begin the session</h4>
                </div>

                <div class="modal-body">
                    <div class="row margin-top-50">
                        <div class="col-md-5 text-align-right">
                            <div class="row">
                                <div class="col-md-9">
                                    <b>{{$mentorViewModel->mentor->first_name}}  {{$mentorViewModel->mentor->last_name}}</b>
                                    <h6>Mentor</h6>
                                </div>
                                <div class="col-md-3">
                                    <img class="matchingImg face-radius" src="{{ asset("/assets/img/mentor_default.png") }}" alt="Mentor profile image">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <i class="matchingIcon ion-arrow-swap"></i>
                        </div>
                        <div class="col-md-5 text-align-left">
                            <div class="row">
                                <div class="col-md-3">
                                    <img class="matchingImg face-radius" src="{{ asset("/assets/img/mentee_default.png") }}" alt="Mentor profile image">
                                </div>
                                <div class="col-md-9">
                                    <b id="menteeFullName"></b>
                                    <h6>Mentee</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row accountManagerSelector">
                        <!-- Account manager -->
                        <div class="col-md-3 margin-top-5">
                            <div class="selectorTitle">{{trans('messages.account_manager')}}</div>
                        </div>
                        <div class="col-md-9 text-align-left">
                            <select data-placeholder="Select an account manager for the session" name="account_manager_id" class="chosen-select">
                                <option><!-- Empty option allows the placeholder to take effect. --><option>
                                @foreach($accountManagers as $accountManager)
                                    <option value="{{$accountManager->id}}" {{old('account_manager_id') == $accountManager->id ? 'selected' : ''}}>
                                        {{$accountManager->first_name}} {{$accountManager->last_name}} - capacity: {{$accountManager->capacity}} (remaining: {{$accountManager->remainingCapacity}})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-flat btn-primary btn-success submitLink">Begin Session</button>
                </div>
            </form>
        </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->