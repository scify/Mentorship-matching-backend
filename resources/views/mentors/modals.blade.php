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