window.UsersListController = function () {
};

window.UsersListController.prototype = function () {
    var deleteUserBtnHandler = function () {
            $(".deleteUserBtn").on("click", function (e) {
                e.stopPropagation();
                var userId = $(this).attr("data-userId");
                $('#deleteUserModal').modal('toggle');
                $('#deleteUserModal').find('input[name="user_id"]').val(userId);
            });
        },
        activateUserBtnHandler = function () {
            $(".activateUserBtn").on("click", function (e) {
                e.stopPropagation();
                var userId = $(this).attr("data-userId");
                $('#activateUserModal').modal('toggle');
                $('#activateUserModal').find('input[name="user_id"]').val(userId);
            });
        },
        deactivateUserBtnHandler = function () {
            $(".deactivateUserBtn").on("click", function (e) {
                e.stopPropagation();
                var userId = $(this).attr("data-userId");
                console.log(userId);
                $('#deactivateUserModal').modal('toggle');
                $('#deactivateUserModal').find('input[name="user_id"]').val(userId);
            });
        },
        initializeRoleSelect = function(instance) {
            var roleSelect = $('.chosen-select').chosen({
                width: '100%'
            });

            roleSelect.change(function(){
                var roleId = $(this).val();
                if(roleId != undefined || roleId != "")
                    getUsersByRole(roleId, instance);
            });
        },
        getUsersByRole = function (roleId, instance) {
            var data = {
                'role_id': roleId
            };
            $.ajax({
                method: "GET",
                url: "byRole",
                cache: false,
                data: data,
                beforeSend: function () {

                    $(".loader").removeClass('hidden');
                },
                success: function (response) {
                    var responseObj = JSON.parse(response);
                    console.log(responseObj);
                    //if operation was unsuccessful
                    if(responseObj.status == 2) {
                        $(".loader").addClass('hidden');
                        $("#errorMsg").removeClass('hidden');
                        $("#errorMsg").html(responseObj.data);
                    } else {
                        $("#usersList").html("");
                        $("#errorMsg").addClass('hidden');
                        $(".loader").addClass('hidden');
                        $("#usersList").html(responseObj.data);
                        Pleasure.listenClickableCards();
                        //run the handlers initialization function again
                        instance.initializeHandlers();

                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log(xhr.responseText);
                    $(".loader").addClass('hidden');
                    $("#errorMsg").removeClass('hidden');
                    //The message added to Response object in Controller can be retrieved as following.
                    $("#errorMsg").html(errorThrown);
                }
            });
        },
        initializeHandlers = function() {
            deleteUserBtnHandler();
            activateUserBtnHandler();
            deactivateUserBtnHandler();
        },
        init = function () {
            console.log("on init");
            var instance = this;
            initializeHandlers();
            initializeRoleSelect(instance);
        };
    return {
        init: init,
        initializeHandlers: initializeHandlers
    }
}();