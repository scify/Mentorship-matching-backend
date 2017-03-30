window.MentorshipSessionsListController = function() {
};

window.MentorshipSessionsListController.prototype = function() {
    var displaySessionInfoModalHandler = function() {
            $("body").on("click", ".singleItem > .visible", function() {
                var mentorName = $(this).find("#mentorPresetName").text();
                var menteeName = $(this).find("#menteePresetName").text();
                var accountManagerId = $(this).data("accountmanagerid");
                var accountManagerName = $(this).find("#accountManagerName").text();
                var matcherId = $(this).data("matcherid");
                var matcherName = $(this).data("matcherfullname");
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
            });
        },
        init = function() {
            displaySessionInfoModalHandler();
        };
    return {
        init: init
    };
}();
