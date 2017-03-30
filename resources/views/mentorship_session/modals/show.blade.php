<div class="modal scale fade matchMentorItem" id="mentorshipSessionShowModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Mentorship session information</h4>
            </div>
            <div class="modal-body">
                <div class="row margin-top-50">
                    <div class="col-md-5 text-align-right">
                        <div class="row">
                            <div class="col-md-9">
                                @include('mentorship_session.modals.mentor_view')
                                <h6>Mentor</h6>
                            </div>
                            <div class="col-md-3">
                                <img class="matchingImg face-radius" src="{{ asset("/assets/img/mentor_default.png") }}" alt="Mentor profile image">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <i class="matchingIcon ion-arrow-swap"></i>
                    </div>
                    <div class="col-md-5 text-align-left">
                        <div class="row">
                            <div class="col-md-3">
                                <img class="matchingImg face-radius" src="{{ asset("/assets/img/mentee_default.png") }}" alt="Mentor profile image">
                            </div>
                            <div class="col-md-9">
                                @include('mentorship_session.modals.mentee_view')
                                <h6>Mentee</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- Account manager -->
                    <div class="col-md-12">
                        <span class="caption">{{trans('messages.account_manager')}}: <a target="_blank" data-url="{{ route('showUserProfile', ['id' => 'id']) }}" id="accountManagerName"></a></span>
                    </div>
                </div>
                <div class="row">
                    <!-- Matcher -->
                    <div class="col-md-12">
                        <span class="caption">{{trans('messages.matcher')}}: <a target="_blank" data-url="{{ route('showUserProfile', ['id' => 'id']) }}" id="matcherName"></a></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->
