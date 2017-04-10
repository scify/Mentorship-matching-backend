window.MentorshipSessionsListController = function() {
};

window.MentorshipSessionsListController.prototype = function() {
    var mentorsAndMenteesListsCssCorrector,
        mentorshipSessionsCriteria = {},
        sessionStatusId,
        sessionInfoModalHandler = function(parentDiv) {
            $("body").on("click", parentDiv + " #mentorshipSessionsList .singleItem > .visible", function() {
                var mentorId = $(this).data("mentorid");
                var mentorName = $(this).find("#mentorPresetName").text();
                var menteeId = $(this).data("menteeid");
                var menteeName = $(this).find("#menteePresetName").text();
                var accountManagerId = $(this).data("accountmanagerid");
                var accountManagerName = $(this).find("#accountManagerName").text();
                var matcherId = $(this).data("matcherid");
                var matcherName = $(this).data("matcherfullname");
                var $sessionStatus = $(this).find("#sessionStatus");
                var sessionStatus = $sessionStatus.text();
                var sessionStatusClass = $sessionStatus.attr("class");
                var createdAt = $(this).find("#createdAt").text();
                var updatedAt = $(this).find("#updatedAt").text();
                var $modal = $("#mentorshipSessionShowModal");
                fetchSessionHistory.call(this);
                $modal.find("#info").addClass("active");
                $modal.find("#history").removeClass("active");
                $modal.find(".nav-tabs > li").removeClass("active");
                $modal.find(".nav-tabs > li").first().addClass("active");
                $modal.modal("toggle");
                var $mentorFullName = $modal.find("#mentorFullName");
                $mentorFullName.html(mentorName);
                var mentorUrlAttr = $mentorFullName.parent().attr("href", $mentorFullName.parent().data("url"));
                console.log(mentorUrlAttr);
                if(mentorUrlAttr.length !== 0) {
                    $mentorFullName.parent().attr("href", $mentorFullName.parent().data("url").replace("id", mentorId));
                }

                var $menteeFullName = $modal.find("#menteeFullName");
                $menteeFullName.html(menteeName);
                var menteeUrlAttr = $menteeFullName.parent().attr("href", $menteeFullName.parent().data("url"));
                if(menteeUrlAttr.length !== 0) {
                    $menteeFullName.parent().attr("href", $menteeFullName.parent().data("url").replace("id", menteeId));
                }
                var $accountManagerNameAnchor = $modal.find("#accountManagerName");
                $accountManagerNameAnchor.html(accountManagerName);
                var accountManagerUrlAttr = $accountManagerNameAnchor.attr("href", $accountManagerNameAnchor.data("url"));
                if(accountManagerUrlAttr.length !== 0) {
                    $accountManagerNameAnchor.attr("href", $accountManagerNameAnchor.data("url").replace("id", accountManagerId));
                }
                var $matcherNameAnchor = $modal.find("#matcherName");
                $matcherNameAnchor.html(matcherName);
                var matcherUrlAttr = $matcherNameAnchor.attr("href", $matcherNameAnchor.data("url"));
                if(matcherUrlAttr.length !== 0) {
                    $matcherNameAnchor.attr("href", $matcherNameAnchor.data("url").replace("id", matcherId));
                }
                $modal.find("#sessionStatus").addClass(sessionStatusClass).html(sessionStatus);
                $modal.find("#createdAt").html(createdAt);
                $modal.find("#updatedAt").html(updatedAt);
            });
        },
        fetchSessionHistory = function() {
            var request = { "mentorship_session_id" : $(this).data("sessionid") };
            console.log($(this).parents("#mentorshipSessionsList").data("fetch-session-history-url"));
            $.ajax({
                method: "GET",
                url: $(this).parents("#mentorshipSessionsList").data("fetch-session-history-url"),
                cache: false,
                data: request,
                beforeSend: function () {
                    $("#mentorshipSessionShowModal #history .timeline").html("");
                    $("#mentorshipSessionShowModal #history #errorMsg").html("");
                    $("#mentorshipSessionShowModal #history #errorMsg").addClass('hidden');
                    $('#mentorshipSessionShowModal #history .panel-body').append('<div class="refresh-container"><div class="loading-bar indeterminate"></div></div>');
                },
                success: function(response) {
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                    parseSuccessHistoryData(response);
                },
                error: function(xhr, status, errorThrown) {
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                    $("#mentorshipSessionShowModal #history #errorMsg").removeClass('hidden');
                    //The message added to Response object in Controller can be retrieved as following.
                    $("#mentorshipSessionShowModal #history #errorMsg").html(errorThrown);
                }
            });
        },
        parseSuccessHistoryData = function(response) {
            var responseObj = JSON.parse(response);
            //if operation was unsuccessful
            if (responseObj.status == 2) {
                $("#mentorshipSessionShowModal #history .timeline").html("");
                $("#mentorshipSessionShowModal #history #errorMsg").removeClass('hidden');
                //The message added to Response object in Controller can be retrieved as following.
                $("#mentorshipSessionShowModal #history #errorMsg").html(responseObj.data);
            } else {
                $("#mentorshipSessionShowModal #history #errorMsg").html("");
                $("#mentorshipSessionShowModal #history #errorMsg").addClass('hidden');
                $("#mentorshipSessionShowModal #history .timeline").html(responseObj.data);
            }
        },
        editSessionModalHandler = function() {
            $("body").on("click", ".editSessionBtn", function() {
                var $siblingVisibleAnchor = $(this).siblings("a.visible");
                var sessionId = $siblingVisibleAnchor.data("sessionid");
                var mentorName = $siblingVisibleAnchor.find("#mentorPresetName").text();
                var menteeName = $siblingVisibleAnchor.find("#menteePresetName").text();
                var accountManagerId = $siblingVisibleAnchor.data("accountmanagerid");
                sessionStatusId = $siblingVisibleAnchor.data("sessionstatusid");
                var $modal = $("#matchMentorModal");
                $modal.modal("toggle");
                $modal.find("input[name=mentorship_session_id]").val(sessionId);
                $modal.find("#mentorFullName").html(mentorName);
                $modal.find("#menteeFullName").html(menteeName);
                if($modal.find("select[name=account_manager_id]").length > 0) {
                    $modal.find("select[name=account_manager_id]").val(accountManagerId).trigger("chosen:updated");
                }
                $modal.find("select[name=status_id]").val(sessionStatusId).trigger("chosen:updated");
            });
        },
        deleteSessionModalHandler = function() {
            $("body").on("click", ".deleteSessionBtn", function () {
                var $siblingVisibleAnchor = $(this).siblings("a.visible");
                var sessionId = $siblingVisibleAnchor.data("sessionid");
                var $modal = $("#deleteMentorshipSessionModal");
                $modal.modal("toggle");
                $modal.find("input[name=mentorship_session_id]").val(sessionId);
            });
        },
        statusChangeHandler = function () {
            var $comment = $(".sessionStatusChangeComment");
            $("select[name=status_id]").change(function() {
                // if the value is set to the original value, don't do anything
                if($(this).val() == sessionStatusId) {
                    $comment.fadeOut("fast");
                    $comment.find("textarea").val("");
                    return;
                }
                $comment.fadeIn("fast");
            });
        },
        searchBtnHandler = function (parentDiv) {
            $("#searchBtn").on("click", function () {
                mentorshipSessionsCriteria.mentorName = $('input[name=mentorName]').val();
                mentorshipSessionsCriteria.menteeName = $('input[name=menteeName]').val();
                mentorshipSessionsCriteria.statusId = $('select[name=statusId]').val();
                var dateRange = $('input[name=sessionStartedDatesRange]').val();
                if (dateRange !== "") {
                    var dates = dateRange.split(" - ");
                    if(typeof dates === 'object' && dates.length === 2) {
                        mentorshipSessionsCriteria.startedDateRange = dateRange;
                    } else {
                        toastr.error("Invalid dates inserted. Please fix them before you try searching.");
                        return;
                    }
                }
                dateRange = $('input[name=sessionCompletedDatesRange]').val();
                if (dateRange !== "") {
                    var dates = dateRange.split(" - ");
                    if(typeof dates === 'object' && dates.length === 2) {
                        mentorshipSessionsCriteria.completedDateRange = dateRange;
                    } else {
                        toastr.error("Invalid dates inserted. Please fix them before you try searching.");
                        return;
                    }
                }
                mentorshipSessionsCriteria.accountManagerId = $('select[name=accountManagerId]').val();
                mentorshipSessionsCriteria.matcherId = $('select[name=matcherId]').val();
                getMentorshipSessionsByFilter(parentDiv);
            });
        },
        clearSearchBtnHandler = function (parentDiv) {
            $("#clearSearchBtn").on("click", function () {
                $('input[name=mentorName]').val("");
                $('input[name=menteeName]').val("");
                $('select[name=statusId]').val(0).trigger("chosen:updated");
                $('input[name=sessionStartedDatesRange]').val("");
                $('input[name=sessionCompletedDatesRange]').val("");
                $('select[name=accountManagerId]').val(0).trigger("chosen:updated");
                $('select[name=matcherId]').val(0).trigger("chosen:updated");
                // clear mentorshipSessionsCriteria object from all of its properties
                for(var prop in mentorshipSessionsCriteria) {
                    if(mentorshipSessionsCriteria.hasOwnProperty(prop)) {
                        delete mentorshipSessionsCriteria[prop];
                    }
                }
                getMentorshipSessionsByFilter(parentDiv);
            });
        },
        getMentorshipSessionsByFilter = function (parentDiv) {
            $.ajax({
                method: "GET",
                url: $(".filtersContainer").data("url"),
                cache: false,
                data: mentorshipSessionsCriteria,
                beforeSend: function () {
                    $('.panel-body').first().append('<div class="refresh-container"><div class="loading-bar indeterminate"></div></div>');
                },
                success: function (response) {
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                    parseSuccessSessionsData(response);
                    mentorsAndMenteesListsCssCorrector.setCorrectCssClasses(parentDiv + "#mentorshipSessionsList");
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
        parseSuccessSessionsData = function(response) {
            var responseObj = JSON.parse(response);
            //if operation was unsuccessful
            if (responseObj.status == 2) {
                $("#errorMsg").removeClass('hidden');
                $("#errorMsg").html(responseObj.data);
                $("#mentorshipSessionsList").html("");
            } else {
                $("#mentorshipSessionsList").html("");
                $("#errorMsg").addClass('hidden');
                $("#mentorshipSessionsList").html(responseObj.data);
                Pleasure.listenClickableCards();
            }
        },
        submitValidationHandler = function() {
            $("#matchMentorModal form").submit(function() {
                var numberOfValidationErrors = 0;
                // if an account manager exists and is not set, display error message and do not submit
                if($(this).find("select[name=account_manager_id]").length > 0 && $(this).find("select[name=account_manager_id]").val() === "") {
                    toastr.error("Please select an account manager from the list.");
                    numberOfValidationErrors++;
                }
                // if a session status is not set, display error message and do not submit
                if($(this).find("select[name=status_id]").val() === "") {
                    toastr.error("Please select a session status from the list.");
                    numberOfValidationErrors++;
                }
                if(numberOfValidationErrors > 0) {
                    return false;
                }
            });
        },
        initSelectInputs = function() {
            $(".chosen-select").chosen({
                width: '100%'
            });
        },
        init = function(parentDiv) {
            mentorsAndMenteesListsCssCorrector = new window.MentorsAndMenteesListsCssCorrector();
            mentorsAndMenteesListsCssCorrector.setCorrectCssClasses(parentDiv + " #mentorshipSessionsList");
            initSelectInputs();
            sessionInfoModalHandler(parentDiv);
            editSessionModalHandler();
            deleteSessionModalHandler();
            submitValidationHandler();
            statusChangeHandler();
            searchBtnHandler(parentDiv);
            clearSearchBtnHandler(parentDiv);
        };
    return {
        init: init
    };
}();
