window.UsersListController = function () {
};

window.UsersListController.prototype = function () {
    var deleteUserBtnHandler = function () {
            $("body").on("click", ".deleteUserBtn", function (e) {
                e.stopPropagation();
                var userId = $(this).attr("data-userId");
                $('#deleteUserModal').modal('toggle');
                $('#deleteUserModal').find('input[name="user_id"]').val(userId);
            });
        },
        searchBtnHandler = function () {
            $("#searchBtn").on("click", function (e) {
                var roleId = $('select[name=user_role]').val();
                var userName = $('input[name=userName]').val();
                console.log("search triggered for role: " + roleId + " and name: " + userName);
                getUsersByFilter(roleId, userName);
            });
        },
        clearSearchBtnHandler = function () {
            $("#clearSearchBtn").on("click", function (e) {
                getUsersByFilter(null, null);
                $('select[name=user_role]').val(0);
                $('select[name=user_role]').trigger("chosen:updated");
                $('input[name=userName]').val(null);
            });
        },
        activateUserBtnHandler = function () {
            $("body").on("click", ".activateUserBtn", function (e) {
                e.stopPropagation();
                var userId = $(this).attr("data-userId");
                $('#activateUserModal').modal('toggle');
                $('#activateUserModal').find('input[name="user_id"]').val(userId);
            });
        },
        deactivateUserBtnHandler = function () {
            $("body").on("click", ".deactivateUserBtn", function (e) {
                e.stopPropagation();
                var userId = $(this).attr("data-userId");
                console.log(userId);
                $('#deactivateUserModal').modal('toggle');
                $('#deactivateUserModal').find('input[name="user_id"]').val(userId);
            });
        },
        initializeRoleSelect = function() {
            $('.chosen-select').chosen({
                width: '100%'
            });
        },
        getUsersByFilter = function(roleId, userName) {
            var data = {
                'role_id': roleId,
                'user_name': userName
            };
            $.ajax({
                method: "GET",
                url: "byCriteria",
                cache: false,
                data: data,
                beforeSend: function () {
                    $(".loader").removeClass('hidden');
                },
                success: function (response) {
                    parseSuccessData(response);
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
        parseSuccessData = function(response) {
            var responseObj = JSON.parse(response);
            //if operation was unsuccessful
            if (responseObj.status == 2) {
                $(".loader").addClass('hidden');
                $("#errorMsg").removeClass('hidden');
                $("#errorMsg").html(responseObj.data);
                $("#usersList").html("");
            } else {
                $("#usersList").html("");
                $("#errorMsg").addClass('hidden');
                $(".loader").addClass('hidden');
                $("#usersList").html(responseObj.data);
                Pleasure.listenClickableCards();
            }
        },
        initializeHandlers = function() {
            deleteUserBtnHandler();
            activateUserBtnHandler();
            deactivateUserBtnHandler();
            searchBtnHandler();
            clearSearchBtnHandler();
        },
        init = function () {
            initializeHandlers();
            initializeRoleSelect();
        };
    return {
        init: init
    }
}();
