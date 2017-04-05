<div class="modal scale fade matchMentorItem" id="mentorshipSessionShowModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Mentorship session information</h4>
            </div>
            <div class="modal-body">
                <div class="header-tabs sticky fixed">
                    <ul class="nav nav-tabs tabs-active-text-white tabs-active-border-yellow">
                        <li class="active"><a data-href="info" data-toggle="tab" class="btn-ripple">{{trans('messages.info')}}</a></li>
                        <li><a data-href="" data-toggle="tab" class="btn-ripple">{{trans('messages.specialties')}} & {{trans('messages.skills.capitalF')}}</a></li>
                    </ul>
                </div>
                <div class="">
                    <div class="tab-content without-border">
                        <div id="info" class="tab-pane active">
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
                                <div class="col-md-2 col-xs-2 text-center">
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
                            <div class="row">
                                <!-- Account manager -->
                                <div class="col-md-3 col-xs-3 text-right">
                                    <span class="caption">{{trans('messages.account_manager')}}:</span>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <span class="caption"><a target="_blank" data-url="{{ route('showUserProfile', ['id' => 'id']) }}" id="accountManagerName"></a></span>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Matcher -->
                                <div class="col-md-3 col-xs-3 text-right">
                                    <span class="caption">{{trans('messages.matcher')}}:</span>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <span class="caption"><a target="_blank" data-url="{{ route('showUserProfile', ['id' => 'id']) }}" id="matcherName"></a></span>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Status -->
                                <div class="col-md-3 col-xs-3 text-right">
                                    <span class="caption">Session Status:</span>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <span class="caption"><span id="sessionStatus"></span></span>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Created at -->
                                <div class="col-md-3 col-xs-3 text-right">
                                    <span class="caption">Session Initialized:</span>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <span class="caption"><span id="createdAt"></span></span>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Updated at -->
                                <div class="col-md-3 col-xs-3 text-right">
                                    <span class="caption">Lastly Updated:</span>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <span class="caption"><span id="updatedAt"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->
