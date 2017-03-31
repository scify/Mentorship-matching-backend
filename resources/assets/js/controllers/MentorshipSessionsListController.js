window.MentorshipSessionsListController = function() {
};

window.MentorshipSessionsListController.prototype = function() {
    var mentorsAndMenteesListsCssCorrector,
        sessionInfoModalHandler = function() {
            $("body").on("click", ".singleItem > .visible", function() {
                var mentorName = $(this).find("#mentorPresetName").text();
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
                $modal.modal("toggle");
                $modal.find("#mentorFullName").html(mentorName);
                $modal.find("#menteeFullName").html(menteeName);
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
            submitValidationHandler();
        };
    return {
        init: init
    };
}();
