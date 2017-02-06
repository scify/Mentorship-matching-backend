window.UsersListController = function () {
};

window.UsersListController.prototype = function () {
    var deleteUserBtnHandler = function () {
            console.log("here");
            $(".deleteUserBtn").on("click", function (e) {
                e.stopPropagation();
                var userId = $(this).attr("data-userId");
                $('#deleteUserModal').modal('toggle');
                $('#deleteUserModal').find('input[name="user_id"]').val(userId);
            });
        },
        activateUserBtnHandler = function () {
            console.log("here");
            $(".activateUserBtn").on("click", function (e) {
                e.stopPropagation();
                var userId = $(this).attr("data-userId");
                $('#activateUserModal').modal('toggle');
                $('#activateUserModal').find('input[name="user_id"]').val(userId);
            });
        },
        deactivateUserBtnHandler = function () {
            console.log("here");
            $(".deactivateUserBtn").on("click", function (e) {
                e.stopPropagation();
                var userId = $(this).attr("data-userId");
                $('#deactivateUserModal').modal('toggle');
                $('#deactivateUserModal').find('input[name="user_id"]').val(userId);
            });
        },
        init = function () {
            deleteUserBtnHandler();
            activateUserBtnHandler();
            deactivateUserBtnHandler();
        };
    return {
        init: init
    }
}();