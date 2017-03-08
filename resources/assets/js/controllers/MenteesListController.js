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
        searchBtnHandler = function () {
            $("#searchBtn").on("click", function (e) {
                getMenteesByFilter();
            });
        },
        getMenteesByFilter = function() {
            var data = {};
            $.ajax({
                method: "GET",
                url: "byCriteria",
                cache: false,
                data: data,
                beforeSend: function () {
                    $('.panel-body').append('<div class="refresh-container"><div class="loading-bar indeterminate"></div></div>');
                },
                success: function (response) {
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                    parseSuccessData(response);
                },
                error: function (xhr, status, errorThrown) {
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                    console.log(xhr.responseText);
                    $("#errorMsg").removeClass('hidden');
                    //The message added to Response object in Controller can be retrieved as following.
                    $("#errorMsg").html(errorThrown);
                }
            });
        },
        initHandlers = function() {
            deleteMenteeBtnHandler();
            $('a[data-toggle="tab"]').click(function (e) {
                e.preventDefault();
                console.log($(this).attr('data-id'));
                $(".card_" + $(this).attr('data-id')).find('.tab-pane.active').removeClass('active');

                $(".card_" + $(this).attr('data-id')).find('.tab-content.active').removeClass('active');

                $(".card_" + $(this).attr('data-id')).find('div[id="' + $(this).attr('data-href') + '"]').addClass('active');
            });
            searchBtnHandler();
        },
        init = function () {
            initHandlers();
        };
    return {
        init: init
    }
}();
