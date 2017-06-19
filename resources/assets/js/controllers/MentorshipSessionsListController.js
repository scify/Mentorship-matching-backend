window.MentorshipSessionsListController = function() {
};

window.MentorshipSessionsListController.prototype = function() {
    var mentorsAndMenteesListsCssCorrector,
        mentorshipSessionsCriteria = {},
        pageNum = 1,
        sessionStatusId,
        isFirstInit = true, // checks whether the init function runs for the first time or not
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
                var sessionComment = $(this).data("generalcomment");
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
                if($mentorFullName.parent("a").length === 1) {
                    $mentorFullName.parent().attr("href", $mentorFullName.parent().data("url").replace("id", mentorId));
                }
                var $menteeFullName = $modal.find("#menteeFullName");
                $menteeFullName.html(menteeName);
                if($menteeFullName.parent("a").length === 1) {
                    $menteeFullName.parent().attr("href", $menteeFullName.parent().data("url").replace("id", menteeId));
                }
                var $accountManagerNameAnchor = $modal.find("#accountManagerName");
                $accountManagerNameAnchor.html(accountManagerName);
                if($accountManagerNameAnchor.length !== 0) {
                    $accountManagerNameAnchor.attr("href", $accountManagerNameAnchor.data("url").replace("id", accountManagerId));
                }
                var $matcherNameAnchor = $modal.find("#matcherName");
                $matcherNameAnchor.html(matcherName);
                if($matcherNameAnchor.length !== 0) {
                    $matcherNameAnchor.attr("href", $matcherNameAnchor.data("url").replace("id", matcherId));
                }
                $modal.find("#sessionStatus").addClass(sessionStatusClass).html(sessionStatus);
                $modal.find("#sessionComment").html(sessionComment);
                $modal.find("#createdAt").html(createdAt);
                $modal.find("#updatedAt").html(updatedAt);
            });
        },
        fetchSessionHistory = function() {
            var request = { "mentorship_session_id" : $(this).data("sessionid") };
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
        paginateMentorshipSessionsBtnHandler = function (parentDiv) {
            $("body").on("click", parentDiv + " #mentorshipSessionsList .pagination a", function (e) {
                e.preventDefault();
                pageNum = $(this).attr("href").replace('#?page=', '');
                if(!$(this).parent().hasClass("active")) {
                    $("#mentorshipSessionsFilters").find("#searchBtn").trigger("click");
                }
            });
        },
        manuallyUpdateStatusHandler = function ($modal) {
            $("body").on("click", ".manuallyUpdateSession", function() {
                $modal.find(".sendInvitationMailsContainer").addClass("hidden");
                $modal.find(".updateSessionBtnContainer").removeClass("hidden");
                $modal.find(".sessionStatusSelector").removeClass("hidden");
                $modal.find(".sessionStatusChangeComment").removeClass("hidden");
                $modal.find(".generalCommentWrapper").removeClass("hidden");
                $modal.find(".actionRequiredWrapper").addClass("hidden");
            });
        },
        editSessionModalHandler = function(parentDiv) {
            $("body").on("click", parentDiv + " .editSessionBtn", function() {

                var $siblingVisibleAnchor = $(this).siblings("a.visible");
                var sessionId = $siblingVisibleAnchor.data("sessionid");
                var actionRequired = $siblingVisibleAnchor.data("actionrequired");
                var mentorId = $siblingVisibleAnchor.data("mentorid");
                var mentorName = $siblingVisibleAnchor.find("#mentorPresetName").text();
                var menteeId = $siblingVisibleAnchor.data("menteeid");
                var menteeName = $siblingVisibleAnchor.find("#menteePresetName").text();
                var accountManagerId = $siblingVisibleAnchor.data("accountmanagerid");
                var generalComment = $siblingVisibleAnchor.data("generalcomment");
                sessionStatusId = $siblingVisibleAnchor.data("sessionstatusid");
                var $modal = $("#matchMentorModalEdit");
                manuallyUpdateStatusHandler($modal);
                $modal.find(".actionRequiredWrapper").addClass("hidden");
                $modal.find(".updateSessionBtnContainer").addClass("hidden");
                $modal.find(".sendInvitationMailsContainer").addClass("hidden");
                $modal.find(".sessionStatusSelector").addClass("hidden");
                $modal.find(".sessionStatusChangeComment").addClass("hidden");
                $modal.find(".generalCommentWrapper").addClass("hidden");

                $modal.modal("toggle");
                $modal.find(".sessionStatusChangeComment").css("display", "none");
                $modal.find("input[name=mentorship_session_id]").val(sessionId);
                if(actionRequired !== undefined && actionRequired !== "") {
                    $modal.find(".actionRequiredWrapper").removeClass("hidden");
                    $modal.find("#actionRequired").html(actionRequired);
                }
                console.log("sessionStatusId: " + sessionStatusId);
                if(sessionStatusId === 1 || sessionStatusId === "1") {
                    $modal.find(".sendInvitationMailsContainer").removeClass("hidden");
                    var _href = $modal.find(".confirmAvailabilityBtn").attr("href");
                    $modal.find(".confirmAvailabilityBtn").attr("href", _href + "?session_id=" + parseInt(sessionId));
                } else {
                    $modal.find(".generalCommentWrapper").removeClass("hidden");
                    $modal.find(".sessionStatusSelector").removeClass("hidden");
                    $modal.find(".sessionStatusChangeComment").removeClass("hidden");
                    $modal.find(".updateSessionBtnContainer").removeClass("hidden");
                }

                var $mentorFullName = $modal.find("#mentorFullName");
                $mentorFullName.html(mentorName);
                if($mentorFullName.parent("a").length === 1) {
                    $mentorFullName.parent().attr("href", $mentorFullName.parent().data("url").replace("id", mentorId));
                }
                var $menteeFullName = $modal.find("#menteeFullName");
                $menteeFullName.html(menteeName);
                if($menteeFullName.parent("a").length === 1) {
                    $menteeFullName.parent().attr("href", $menteeFullName.parent().data("url").replace("id", menteeId));
                }
                $modal.find("textarea[name=general_comment]").text(generalComment);
                if($modal.find("select[name=account_manager_id]").length > 0) {
                    $modal.find("select[name=account_manager_id]").val(accountManagerId).trigger("chosen:updated");
                }
                $modal.find("select[name=status_id]").val(sessionStatusId).trigger("chosen:updated");
                displayCorrectlyStatusesAccordingToPreselectedStatus($modal.find("select[name=status_id]"));
            });
        },
        deleteSessionModalHandler = function(parentDiv) {
            $("body").on("click", parentDiv + " .deleteSessionBtn", function () {
                var $siblingVisibleAnchor = $(this).siblings("a.visible");
                var sessionId = $siblingVisibleAnchor.data("sessionid");
                var $modal = $("#deleteMentorshipSessionModal");
                $modal.modal("toggle");
                $modal.find("input[name=mentorship_session_id]").val(sessionId);
            });
        },
        displayCorrectlyStatusesAccordingToPreselectedStatus = function($select) {
            $select.find("option").attr("style", "");
            // if status is pending, introduction_sent, mentee_available or mentor_available
            // (NOTE: it means that the id is smaller than the id of started status which is 4)
            // omit statuses after started and up to completed_followup
            if(parseInt(sessionStatusId) < 4) {
                for(var i = 6; i <= 11; i++) {
                    $select.find("option[value=" + i + "]").css("display", "none");
                }
            } else { // else, omit statuses between pending and mentor_available
                for(var i = 1; i <= 4; i++) {
                    $select.find("option[value=" + i + "]").css("display", "none");
                }
            }
            $select.trigger("chosen:updated");
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
        deleteSessionHistory = function(historyId) {
            var $tokenElement = $("#history input[name=_token]");
            var $self = $(this);
            $.ajax({
                method: 'POST',
                url: $tokenElement.data("delete-history-url"),
                data: {'_token': $tokenElement.val(), 'sessionHistoryId': historyId},
                success: function(response) {
                    var responseObj = JSON.parse(response);
                    if (responseObj.status == 2) {
                        toastr.error(responseObj.data);
                        $("#history .frame:visible .delete-from-timeline").remove();
                        $self.fadeIn("slow");
                    } else {
                        toastr.success(responseObj.data);
                    }
                },
                error: function(xhr, status, errorThrown) {
                    console.log(xhr.responseText);
                    toastr.error(errorThrown);
                }
            });
        },
        deleteHistoryHandler = function () {
            $("#mentorshipSessionShowModal").on("click", ".delete-from-timeline", function() {
                var historyId = $(this).parent().data("history-id");
                deleteSessionHistory.call($(this).parents(".frame"), historyId);
                var $allVisibleTimelineFrames = $("#history .frame:visible");
                // if there are 2 or more visible frames and the second one doesn't have the
                // delete-from-timeline button and the user logged in is either the one that made the item's history
                // or is an admin, add it to that element
                if($allVisibleTimelineFrames.length > 1 &&
                    ($($allVisibleTimelineFrames.get(1)).data("user-created-history") ===
                    $("#history input[name=_token]").data("auth-user-id") ||
                    parseInt($("#history input[name=_token]").data("user-is-admin")) === 1) &&
                    $($allVisibleTimelineFrames.get(1)).find(".delete-from-timeline").length === 0)
                {
                    $($allVisibleTimelineFrames.get(1)).find(".timeline-bubble").prepend(
                        this.outerHTML
                    );
                }
                $(this).parents(".frame").fadeOut("slow");
            });
        },
        searchBtnHandler = function (parentDiv) {
            $("#searchBtn").on("click", function () {
                mentorshipSessionsCriteria.mentorName = $('input[name=mentorName]').val();
                mentorshipSessionsCriteria.menteeName = $('input[name=menteeName]').val();
                mentorshipSessionsCriteria.startStatusId = $('select[name=startStatusId]').val();
                mentorshipSessionsCriteria.endStatusId = $('select[name=endStatusId]').val();
                var dateRange = $('input[name=sessionStartedDatesRange]').val();
                if (dateRange !== "" && dateRange != undefined) {
                    var dates = dateRange.split(" - ");
                    if(typeof dates === 'object' && dates.length === 2) {
                        mentorshipSessionsCriteria.startedDateRange = dateRange;
                    } else {
                        toastr.error("Invalid dates inserted. Please fix them before you try searching.");
                        return;
                    }
                }
                dateRange = $('input[name=sessionCompletedDatesRange]').val();
                if (dateRange !== "" && dateRange !== undefined) {
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
                $('select[name=startStatusId]').val(0).trigger("chosen:updated");
                $('select[name=endStatusId]').val(0).trigger("chosen:updated");
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
            mentorshipSessionsCriteria.page = pageNum;
            $.ajax({
                method: "GET",
                url: $(".filtersContainer").data("url"),
                cache: false,
                data: mentorshipSessionsCriteria,
                beforeSend: function () {
                    $('.panel-body').first().append('<div class="refresh-container"><div class="loading-bar indeterminate"></div></div>');
                    $(parentDiv + " #mentorshipSessionsBottomLoader").removeClass("invisible");
                },
                success: function (response) {
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                    parseSuccessSessionsData(response, parentDiv);
                    mentorsAndMenteesListsCssCorrector.setCorrectCssClasses(parentDiv + " #mentorshipSessionsList");
                    $(parentDiv + " #mentorshipSessionsBottomLoader").addClass("invisible");
                },
                error: function (xhr, status, errorThrown) {
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                    console.log(xhr.responseText);
                    $("#errorMsg").removeClass('hidden');
                    //The message added to Response object in Controller can be retrieved as following.
                    $("#errorMsg").html(errorThrown);
                    $(parentDiv + " #mentorshipSessionsBottomLoader").addClass("invisible");
                }
            });
        },
        parseSuccessSessionsData = function(response, parentDiv) {
            var responseObj = JSON.parse(response);
            console.log(parentDiv);
            //if operation was unsuccessful
            if (responseObj.status == 2) {
                $(parentDiv).find("#errorMsg").removeClass('hidden');
                $(parentDiv).find("#errorMsg").html(responseObj.data);
                $(parentDiv).find("#mentorshipSessionsList").html("");
            } else {
                $(parentDiv).find("#mentorshipSessionsList").html("");
                $(parentDiv).find("#errorMsg").addClass('hidden');
                $(parentDiv).find("#mentorshipSessionsList").html(responseObj.data);
                Pleasure.listenClickableCards();
            }
        },
        displayPermittedValuesForEndSessionStatus = function(selectedStartStatusId) {
            $("select[name=endStatusId] option").each(function() {
                var currentOptionValue = parseInt($(this).val());
                // display previously hidden options
                $(this).css("display", "");
                // if value is not permitted, hide option
                if (currentOptionValue < selectedStartStatusId) {
                    $(this).css("display", "none");
                }
            });
        },
        startSessionStatusRangeHandler = function() {
            $("select[name=startStatusId]").change(function() {
                var selectedStartStatusId = parseInt($("select[name=startStatusId]").val());
                var preselectedEndStatusId = parseInt($("select[name=endStatusId]").val());
                displayPermittedValuesForEndSessionStatus(selectedStartStatusId);
                // trigger update
                console.log(preselectedEndStatusId);
                if (preselectedEndStatusId < selectedStartStatusId || isNaN(preselectedEndStatusId)) {
                    $("select[name=endStatusId]").val(selectedStartStatusId);
                }
                $("select[name=endStatusId]").trigger("chosen:updated");
            });
        },
        displayPermittedValuesForStartSessionStatus = function(selectedEndStatusId) {
            $("select[name=startStatusId] option").each(function() {
                var currentOptionValue = parseInt($(this).val());
                // display previously hidden options
                $(this).css("display", "");
                // if value is not permitted, hide option
                if (currentOptionValue > selectedEndStatusId) {
                    $(this).css("display", "none");
                }
            });
        },
        endSessionStatusRangeHandler = function() {
            $("select[name=endStatusId]").change(function() {
                var selectedEndStatusId = parseInt($("select[name=endStatusId]").val());
                var preselectedStartStatusId = parseInt($("select[name=startStatusId]").val());
                displayPermittedValuesForStartSessionStatus(selectedEndStatusId);
                // trigger update
                if (preselectedStartStatusId > selectedEndStatusId || isNaN(preselectedStartStatusId)) {
                    $("select[name=startStatusId]").val(selectedEndStatusId);
                }
                $("select[name=startStatusId]").trigger("chosen:updated");
            });
        },
        submitValidationHandler = function() {
            $("#matchMentorModal form, #matchMentorModalEdit form").submit(function() {
                var numberOfValidationErrors = 0;
                // if an account manager exists and is not set, display error message and do not submit
                if ($(this).find("select[name=account_manager_id]").length > 0 && $(this).find("select[name=account_manager_id]").val() === "") {
                    toastr.error("Please select an account manager from the list.");
                    numberOfValidationErrors++;
                }
                // if a session status is not set, display error message and do not submit
                if ($(this).find("select[name=status_id]").val() === "") {
                    toastr.error("Please select a session status from the list.");
                    numberOfValidationErrors++;
                }
                if (numberOfValidationErrors > 0) {
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
        console.log(parentDiv);
            mentorsAndMenteesListsCssCorrector = new window.MentorsAndMenteesListsCssCorrector();
            mentorsAndMenteesListsCssCorrector.setCorrectCssClasses(parentDiv + " #mentorshipSessionsList");
            sessionInfoModalHandler(parentDiv);
            editSessionModalHandler(parentDiv);
            deleteSessionModalHandler(parentDiv);
            searchBtnHandler(parentDiv);
            clearSearchBtnHandler(parentDiv);
            // run only once this function
            if(isFirstInit) {
                initNonParentSpecificHandlers();
                isFirstInit = false;
            }
            paginateMentorshipSessionsBtnHandler(parentDiv);
        },
        initNonParentSpecificHandlers = function() {
            initSelectInputs();
            submitValidationHandler();
            statusChangeHandler();
            deleteHistoryHandler();
            startSessionStatusRangeHandler();
            endSessionStatusRangeHandler();
        };
    return {
        init: init
    };
}();
