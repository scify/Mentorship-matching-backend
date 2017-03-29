window.MatchingController = function () {
};

window.MatchingController.prototype = function () {
    var matchMentorButtonHandler = function () {
            $("body").on("click", ".matchMentorBtn", function (e) {
                e.stopPropagation();
                var menteeId = $(this).attr("data-menteeId");
                var menteeFullName = $(this).attr("data-userName");
                console.log(menteeId);
                $('#matchMentorModal').modal('toggle');
                $('#matchMentorModal').find('input[name="mentee_profile_id"]').val(menteeId);
                $('#menteeFullName').html(menteeFullName);
            });
        },
        matchMenteeButtonHandler = function () {
            $("body").on("click", ".matchMenteeBtn", function (e) {
                e.stopPropagation();
                var mentorId = $(this).attr("data-mentorId");
                var mentorFullName = $(this).attr("data-userName");
                console.log(mentorId);
                $('#matchMentorModal').modal('toggle');
                $('#matchMentorModal').find('input[name="mentor_profile_id"]').val(mentorId);
                $('#mentorFullName').html(mentorFullName);
            });
        },
        submitValidationHandler = function() {
            $("#matchMentorModal form").submit(function() {
                // if an account manager is not set, display error message and do not submit
                if($(this).find("select[name=account_manager_id]").val() === "") {
                    toastr.error("Please select an account manager from the list.");
                    return false;
                }
            });
        },
        initHandlers = function() {
            matchMentorButtonHandler();
            matchMenteeButtonHandler();
            submitValidationHandler();
        },
        init = function () {
            initHandlers();
        };
    return {
        init: init
    }
}();
