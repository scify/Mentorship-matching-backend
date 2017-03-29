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
                $('#matchMentorModal').find('input[name="mentee_id"]').val(menteeId);
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
                $('#matchMentorModal').find('input[name="mentor_id"]').val(mentorId);
                $('#mentorFullName').html(mentorFullName);
            });
        },
        initHandlers = function() {
            matchMentorButtonHandler();
            matchMenteeButtonHandler();
        },
        init = function () {
            initHandlers();
        };
    return {
        init: init
    }
}();
