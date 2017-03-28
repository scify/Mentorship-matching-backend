window.MenteesListController = function () {
};

window.MenteesListController.prototype = function () {
    var menteesCriteria = {},
        deleteMenteeBtnHandler = function () {
            $("body").on("click", ".deleteMenteeBtn", function (e) {
                e.stopPropagation();
                var menteeId = $(this).attr("data-menteeId");
                $('#deleteMenteeModal').modal('toggle');
                $('#deleteMenteeModal').find('input[name="mentee_id"]').val(menteeId);
            });
        },
        searchBtnHandler = function () {
            $("#searchBtn").on("click", function () {
                menteesCriteria.menteeName = $("input[name=mentee_name]").val();
                menteesCriteria.ageRange = $("input[name=age]").val();
                menteesCriteria.educationLevel = $("select[name=education_level]").val();
                menteesCriteria.university = $("select[name=university]").val();
                menteesCriteria.skills = $("input[name=mentee_skills]").val();
                menteesCriteria.signedUpAgo = $("select[name=signed_up_ago]").val();
                menteesCriteria.completedSessionAgo = $("select[name=completed_session_ago]").val();
                menteesCriteria.displayOnlyUnemployed = $("input[name=only_unemployed_mentees]").parent().hasClass("checked");
                menteesCriteria.displayOnlyActiveSession = $("input[name=only_active_sessions]").parent().hasClass("checked");
                menteesCriteria.displayOnlyNeverMatched =
                    $("input[name=only_never_matched]").parent().hasClass("checked");
                getMenteesByFilter();
            });
        },
        clearSearchBtnHandler = function() {
            $("#clearSearchBtn").on("click", function() {
                $('input[name=mentee_name]').val("");
                $('#age').data("ionRangeSlider").reset();
                $('select[name=education_level]').val(0).trigger("chosen:updated");
                $('select[name=university]').val(0).trigger("chosen:updated");
                $('input[name=mentee_skills]').val("");
                $('select[name=signed_up_ago]').val(0).trigger("chosen:updated");
                $('select[name=completed_session_ago]').val(0).trigger("chosen:updated");
                $('input[name=only_unemployed_mentees]').iCheck('uncheck');
                $('input[name=only_active_sessions]').iCheck('uncheck');
                $('input[name=only_never_matched]').iCheck('uncheck');
                // clear MenteesCriteria object from all of its properties
                for(var prop in menteesCriteria) {
                    if(menteesCriteria.hasOwnProperty(prop)) {
                        delete menteesCriteria[prop];
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
                data: menteesCriteria,
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
        initSelectInputs = function() {
            $(".chosen-select").chosen({
                width: '100%'
            });
        },
        initAgeRangeSlider = function() {
            $('#age').ionRangeSlider({
                min: 18,
                max: 75,
                from: 18,
                to: 75,
                type: 'double',
                step: 1,
                postfix: ' years old',
                grid: true
            });
        },
        initHandlers = function() {
            deleteMenteeBtnHandler();
            searchBtnHandler();
            clearSearchBtnHandler();
        },
        init = function () {
            initSelectInputs();
            initAgeRangeSlider();
            initHandlers();
        };
    return {
        init: init
    }
}();
