window.MentorProfileController = function() {
};

window.MentorProfileController.prototype = (function(){
    var originalStatusId,
        mentorStatusChangeHandler = function() {
            var $followUpDate = $("#status-change-follow-up-date");
            var $comment = $("#status-change-comment");
            $("select[name=status_id]").change(function() {
                $followUpDate.fadeOut("fast")
                // if we are creating a mentor don't do anything
                if(originalStatusId === "") {
                    return;
                }
                // if the value is set to the original value, don't do anything
                if($(this).val() == originalStatusId) {
                    $comment.fadeOut("fast");
                    return;
                }
                // if value is set to 2 or 4 (not available or black-listed)
                // display the follow up date field
                if(parseInt($(this).val()) % 2 === 0) {
                    $followUpDate.fadeIn("fast");
                }
                $comment.fadeIn("fast");
            });

        },
        init = function() {
            originalStatusId = $("select[name=status_id]").data("original-value");
            mentorStatusChangeHandler();
        };
    return {
        init: init
    };
})();
