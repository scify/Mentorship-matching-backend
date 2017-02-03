window.UsersListController = function () {
};

window.UsersListController.prototype = function () {
    var deleteUserBtnHandler = function () {
            console.log("here");
            $(".deleteUserBtn").on("click", function (e) {
                e.stopPropagation();
                var userId = $(this).attr("data-userId");
                console.log(userId);
                $('#deleteUserModal').modal('toggle');
                $('#deleteUserModal .submitLink').attr("href", "user/delete?id=" + userId);
            });
        },
        init = function () {
            var instance = this;

            deleteUserBtnHandler();
        };
    return {
        init: init
    }
}();