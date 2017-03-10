window.MenteesListController = function () {
};

window.MenteesListController.prototype = function () {
    var deleteMenteeBtnHandler = function () {
            $("body").on("click", ".deleteMenteeBtn", function (e) {
                e.stopPropagation();
                var menteeId = $(this).attr("data-menteeId");
                $('#deleteMenteeModal').modal('toggle');
                $('#deleteMenteeModal').find('input[name="mentee_id"]').val(menteeId);
            });
        },
        searchBtnHandler = function () {
            $("#searchBtn").on("click", function (e) {
                var filterCriteria = window.MenteesCriteria;
                filterCriteria.displayOnlyNeverMatched = $("input[name=only-never-matched]").parent().hasClass("checked");
                getMenteesByFilter(filterCriteria);
            });
        },
        clearSearchBtnHandler = function() {
            $("#clearSearchBtn").on("click", function() {
                $('input[name=only-never-matched]').iCheck('uncheck');
                delete window.MenteesCriteria.displayOnlyNeverMatched;
                getMenteesByFilter(window.MenteesCriteria);
            });
        },
        getMenteesByFilter = function(filterCriteria) {
            $.ajax({
                method: "GET",
                url: $(".filtersContainer").data("url"),
                cache: false,
                data: filterCriteria,
                beforeSend: function () {
                    $('.panel-body').first().append('<div class="refresh-container"><div class="loading-bar indeterminate"></div></div>');
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
        parseSuccessData = function(response) {
            var responseObj = JSON.parse(response);
            //if operation was unsuccessful
            if (responseObj.status == 2) {
                $("#errorMsg").removeClass('hidden');
                $("#errorMsg").html(responseObj.data);
                $("#menteesList").html("");
            } else {
                $("#usersList").html("");
                $("#errorMsg").addClass('hidden');
                $("#menteesList").html(responseObj.data);
                Pleasure.listenClickableCards();
            }
        },
        initHandlers = function() {
            deleteMenteeBtnHandler();
            $("body").on("click", "a[data-toggle='tab']", function (e) {
                e.preventDefault();
                console.log($(this).attr('data-id'));
                $(".card_" + $(this).attr('data-id')).find('.tab-pane.active').removeClass('active');

                $(".card_" + $(this).attr('data-id')).find('.tab-content.active').removeClass('active');

                $(".card_" + $(this).attr('data-id')).find('div[id="' + $(this).attr('data-href') + '"]').addClass('active');
            });
            searchBtnHandler();
            clearSearchBtnHandler();
        },
        init = function () {
            initHandlers();
        };
    return {
        init: init
    }
}();

window.MenteesCriteria = {};
