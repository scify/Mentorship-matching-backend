const {Pleasure} = require("../../pleasure-admin-panel/js/pleasure");
require('toastr');

window.MenteesListController = function () {
};

window.MenteesListController.prototype = function () {
    var mentorsAndMenteesListsCssCorrector,
        menteesCriteria = {},
        pageNum = 1,
        deleteMenteeBtnHandler = function () {
            $("body").on("click", ".deleteMenteeBtn", function (e) {
                e.stopPropagation();
                var menteeId = $(this).attr("data-menteeId");
                $('#deleteMenteeModal').modal('toggle');
                $('#deleteMenteeModal').find('input[name="mentee_id"]').val(menteeId);
            });
        },
        editMenteeStatusBtnHandler = function () {
            $("body").on("click", ".editMenteeStatusBtn", function (e) {
                e.stopPropagation();
                var menteeId = $(this).attr("data-menteeId");
                var originalStatusId = $(this).attr("data-original-status");
                var $editMenteeStatusModal = $('#editMenteeStatusModal');
                $editMenteeStatusModal.modal('toggle');
                $editMenteeStatusModal.find('input[name="mentee_id"]').val(menteeId);
                $editMenteeStatusModal.find('select[name="status_id"]').attr("data-original-value", originalStatusId);
                $editMenteeStatusModal.find('select[name="status_id"]').val(originalStatusId).trigger("chosen:updated");
            });
        },
        paginateMenteesBtnHandler = function () {
            $("body").on("click", "#menteesList .pagination a", function (e) {
                e.preventDefault();
                pageNum = $(this).attr("href").replace('#?page=', '');
                if(!$(this).parent().hasClass("active")) {
                    $("#menteesFilters").find("#searchBtn").trigger("click");
                }
            });
        },
        searchBtnHandler = function (currentRouteName) {
            menteesCriteria.currentRouteName = currentRouteName;
            var $menteesFilterDiv = $("#menteesFilters");
            if(typeof $menteesFilterDiv.attr("data-only-available") !== 'undefined' &&
                $menteesFilterDiv.data("only-available") === true) {
                menteesCriteria.displayOnlyAvailable = true;
            }
            $("#searchBtn").on("click", function () {
                menteesCriteria.menteeName = $("input[name=mentee_name]").val();
                menteesCriteria.ageRange = $("input[name=age]").val();
                menteesCriteria.educationLevel = $("select[name=education_level]").val();
                menteesCriteria.university = $("select[name=university]").val();
                menteesCriteria.skills = $("input[name=mentee_skills]").val();
                menteesCriteria.signedUpAgo = $("select[name=signed_up_ago]").val();
                menteesCriteria.completedSessionAgo = $("select[name=completed_session_ago]").val();
                menteesCriteria.specialty = $("select[name=specialty]").val();
                menteesCriteria.averageRating = $("select[name=average_rating]").val();
                menteesCriteria.displayOnlyUnemployed = $("input[name=only_unemployed_mentees]").parent().hasClass("checked");
                menteesCriteria.displayOnlyActiveSession = $("input[name=only_active_sessions]").parent().hasClass("checked");
                menteesCriteria.displayOnlyNeverMatched =
                    $("input[name=only_never_matched]").parent().hasClass("checked");
                menteesCriteria.displayOnlyExternallySubscribed =
                    $('input[name=only_externally_subscribed]').parent().hasClass("checked");
                menteesCriteria.displayOnlyAvailableWithCancelledSessions =
                    $('input[name=available_with_cancelled_session]').parent().hasClass("checked");
                console.log(menteesCriteria);
                getMenteesByFilter.call(this);
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
                $('select[name=specialty]').val(0).trigger("chosen:updated");
                $('select[name=average_rating]').val(0).trigger("chosen:updated");
                $('input[name=only_unemployed_mentees]').iCheck('uncheck');
                $('input[name=only_active_sessions]').iCheck('uncheck');
                $('input[name=only_never_matched]').iCheck('uncheck');
                $('input[name=only_externally_subscribed]').iCheck('uncheck');
                $('input[name=available_with_cancelled_session]').iCheck('uncheck');
                // clear MenteesCriteria object from all of its properties
                for(var prop in menteesCriteria) {
                    // remove all properties except of the one containing the current route's name,
                    // needed to display correct buttons next to the card and
                    // the one used to display only available mentees
                    if(menteesCriteria.hasOwnProperty(prop) && prop !== "currentRouteName" && prop !== "displayOnlyAvailable") {
                        delete menteesCriteria[prop];
                    }
                }
                getMenteesByFilter.call(this);
            });
        },
        getMenteesByFilter = function() {
            // button pressed that triggered this function
            var self = this;
            menteesCriteria.page = pageNum;
            $.ajax({
                method: "GET",
                url: $(".filtersContainer").data("url"),
                cache: false,
                data: menteesCriteria,
                beforeSend: function () {
                    $(self).parents('.panel-body').first().append('<div class="refresh-container"><div class="loading-bar indeterminate"></div></div>');
                    $("#menteesBottomLoader").removeClass("invisible");
                },
                success: function (response) {
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                    parseSuccessData(response);
                    mentorsAndMenteesListsCssCorrector.setCorrectCssClasses("#menteesList");
                    $("#menteesBottomLoader").addClass("invisible");
                },
                error: function (xhr, status, errorThrown) {
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                    console.log(xhr.responseText);
                    $("#errorMsg").removeClass('hidden');
                    //The message added to Response object in Controller can be retrieved as following.
                    $("#errorMsg").html(errorThrown);
                    $("#menteesBottomLoader").addClass("invisible");
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
        initHandlers = function(currentRouteName) {
            deleteMenteeBtnHandler();
            editMenteeStatusBtnHandler();
            searchBtnHandler(currentRouteName);
            clearSearchBtnHandler();
        },
        init = function (currentRouteName) {
            mentorsAndMenteesListsCssCorrector = new window.MentorsAndMenteesListsCssCorrector();
            mentorsAndMenteesListsCssCorrector.setCorrectCssClasses("#menteesList");
            initSelectInputs();
            initAgeRangeSlider();
            initHandlers(currentRouteName);
            paginateMenteesBtnHandler();
        };
    return {
        init: init
    }
}();
