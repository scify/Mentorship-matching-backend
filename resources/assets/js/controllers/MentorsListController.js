window.MentorsListController = function () {
};

window.MentorsListController.prototype = function () {
    var deleteMentorBtnHandler = function () {
            $(".deleteMentorBtn").on("click", function (e) {
                e.stopPropagation();
                var mentorId = $(this).attr("data-mentorId");
                $('#deleteMentorModal').modal('toggle');
                $('#deleteMentorModal').find('input[name="mentor_id"]').val(mentorId);
            });
        },

        init = function () {
            deleteMentorBtnHandler();
        };
    return {
        init: init
    }
}();