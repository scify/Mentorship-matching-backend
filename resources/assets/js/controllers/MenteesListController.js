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
                window.MenteesCriteria.displayOnlyNeverMatched =
                    $("input[name=only_never_matched]").parent().hasClass("checked");
                window.MenteesCriteria.completedSessionAgo = $("select[name=completed_session_ago]").val();
                getMenteesByFilter();
            });
        },
        clearSearchBtnHandler = function() {
            $("#clearSearchBtn").on("click", function() {
                $('input[name=only_never_matched]').iCheck('uncheck');
                $('select[name=completed_session_ago]').val(0);
                $('select[name=completed_session_ago]').trigger("chosen:updated");
                // clear MenteesCriteria object from all of its properties
                for(var prop in window.MenteesCriteria) {
                    if(window.MenteesCriteria.hasOwnProperty(prop)) {
                        delete window.MenteesCriteria[prop];
                    }
                }
                getMenteesByFilter();
            });
        },
        getMenteesByFilter = function() {
            $.ajax({
                method: "GET",
                url: $(".filtersContainer").data("url"),
                cache: false,
                data: window.MenteesCriteria,
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
            console.log(response);
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
        initSelectInputs = function() {
            $("select[name=completed_session_ago]").chosen({
                width: '100%'
            });
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
            initSelectInputs();
            initHandlers();
        };
    return {
        init: init
    }
}();

window.MenteesCriteria = {};
