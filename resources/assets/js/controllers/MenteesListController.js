window.MenteesListController = function () {
};

window.MenteesListController.prototype = function () {
    var deleteMenteeBtnHandler = function () {
            $(".deleteMenteeBtn").on("click", function (e) {
                e.stopPropagation();
                var menteeId = $(this).attr("data-menteeId");
                $('#deleteMenteeModal').modal('toggle');
                $('#deleteMenteeModal').find('input[name="mentee_id"]').val(menteeId);
            });
        },

        init = function () {
            deleteMenteeBtnHandler();
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