window.MentorshipSessionsListController = function() {
};

window.MentorshipSessionsListController.prototype = function() {
    var mentorsAndMenteesListsCssCorrector,
        sessionInfoModalHandler = function() {
            $("body").on("click", ".singleItem > .visible", function() {
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
                $mentorFullName.parent().attr("href", $mentorFullName.parent().data("url").replace("id", mentorId));
                var $menteeFullName = $modal.find("#menteeFullName");
                $menteeFullName.html(menteeName);
                $menteeFullName.parent().attr("href", $menteeFullName.parent().data("url").replace("id", menteeId));
                var $accountManagerNameAnchor = $modal.find("#accountManagerName");
                $accountManagerNameAnchor.html(accountManagerName);
                $accountManagerNameAnchor.attr("href", $accountManagerNameAnchor.data("url").replace("id", accountManagerId));
                var $matcherNameAnchor = $modal.find("#matcherName");
                $matcherNameAnchor.html(matcherName);
                $matcherNameAnchor.attr("href", $matcherNameAnchor.data("url").replace("id", matcherId));
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
                    parseSuccessData(response);
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
        parseSuccessData = function(response) {
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
                var sessionStatusId = $siblingVisibleAnchor.data("sessionstatusid");
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
        init = function() {
            mentorsAndMenteesListsCssCorrector = new window.MentorsAndMenteesListsCssCorrector();
            mentorsAndMenteesListsCssCorrector.setCorrectCssClasses();
            initSelectInputs();
            sessionInfoModalHandler();
            editSessionModalHandler();
            deleteSessionModalHandler();
            submitValidationHandler();
        };
    return {
        init: init
    };
}();
