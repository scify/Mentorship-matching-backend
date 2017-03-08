window.UserProfileController = function () {
};

window.UserProfileController.prototype = function () {
    var initTabs = function() {
            $('a[data-toggle="tab"]').click(function (e) {
                e.preventDefault();
                var profilePageClassElement = $(".profilePage");
                profilePageClassElement.find('.tab-pane.active').removeClass('active');
                profilePageClassElement.find('.tab-content.active').removeClass('active');
                profilePageClassElement.find('div[id="' + $(this).attr('data-href') + '"]').addClass('active');
            });
        },
        capacityEditBtnHandler = function() {
            $("#capacityEditBtn").click(function(){
                $("#editCapacityContainer").css("display", "block");
                $("#capacityUpdateDiv").slideDown("slow");
            });
        },
        capacityEditSubmitHandler = function() {
            $("#capacityEditSubmitBtn").click(function(){
                var newCapacity = $('input[name=capacity]').val();
                var userId = $(this).attr("data-userId");
                console.log("userId: " + userId);
                console.log("newCapacity: " + newCapacity);
                if(newCapacity < 0) {
                    parseErrorData("Capacity cannot be less than 0.");
                    return;
                }
                if(userId != undefined && newCapacity != undefined)
                    editAccountManagerCapacity(newCapacity, userId);
            });
        },
        editAccountManagerCapacity = function(newCapacity, userId) {
            var data = {
                'user_id': userId,
                'capacity': newCapacity
            };
            $.ajax({
                method: "GET",
                url: "editUserCapacity",
                cache: false,
                data: data,
                beforeSend: function () {
                    $(".loader").removeClass('hidden');
                },
                success: function (response) {
                    parseSuccessData(response, newCapacity);
                },
                error: function (xhr, status, errorThrown) {
                    console.log(xhr.responseText);
                    parseErrorData(errorThrown);
                }
            });
        },
        parseSuccessData = function(response, newCapacity) {
            var responseObj = JSON.parse(response);
            console.log(responseObj);
            //if operation was unsuccessful
            if (responseObj.status == 2) {
                $(".loader").addClass('hidden');
                $("#errorMsg").removeClass('hidden');
                $("#errorMsg").html(responseObj.data);
            } else {
                $("#editCapacityContainer").slideUp("slow");
                $("#errorMsg").addClass('hidden');
                $(".loader").addClass('hidden');
                //update capacity field
                $("#accountManagerCapacity").html(newCapacity);
                //show success div
                $("#successMsg").removeClass("hidden");
                $("#successMsg").css("display", "block");
                //slide up whole div
                setTimeout(function(){
                    $("#capacityUpdateDiv").slideUp("slow");

                    $("#successMsg").slideUp('slow');
                    $("#capacityUpdateDiv").css("display", "block");
                }, 2000);

            }
        },
        parseErrorData = function(errorThrown) {
            $(".loader").addClass('hidden');
            $("#errorMsg").removeClass('hidden');
            //The message added to Response object in Controller can be retrieved as following.
            $("#errorMsg").html(errorThrown);
            setTimeout(function(){$("#errorMsg").addClass('hidden'); }, 2000);
        },
        init = function () {
            initTabs();
            capacityEditBtnHandler();
            capacityEditSubmitHandler();
        };
    return {
        init: init
    }
}();