<!-- Modals -->
<div class="modal scale fade" id="activateUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">\
            <form method="POST" action="{{route('activateUser')}}" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="user_id" value="">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                Are you sure you want to activate the selected user?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-flat btn-primary btn-success submitLink">Activate User</button>
            </div>
            </form>
        </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->
<div class="modal scale fade" id="deactivateUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{route('deactivateUser')}}" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="user_id" value="">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                Are you sure you want to deactivate the selected user?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-flat btn-primary btn-danger submitLink">Deactivate User</button>
            </div>
            </form>
        </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->
<div class="modal scale fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{route('deleteUser')}}" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="user_id" value="">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>

            <div class="modal-body">
                Are you sure you want to delete the selected user?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-flat btn-primary btn-danger submitLink">Delete User</button>
            </div>
            </form>
        </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->