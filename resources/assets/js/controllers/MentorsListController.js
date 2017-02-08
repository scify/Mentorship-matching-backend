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
            $('a[data-toggle="tab"]').click(function (e) {
                e.preventDefault();
                console.log($(this).attr('data-id'));
                $(".card_" + $(this).attr('data-id')).find('.tab-pane.active').removeClass('active');

                $(".card_" + $(this).attr('data-id')).find('.tab-content.active').removeClass('active');

                $(".card_" + $(this).attr('data-id')).find('div[id="' + $(this).attr('data-href') + '"]').addClass('active');
            });
        };
    return {
        init: init
    }
}();