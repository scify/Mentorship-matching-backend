window.AvailabilityStatusChangeViewHandler = function() {
};

window.AvailabilityStatusChangeViewHandler.prototype = (function(){
    var originalStatusId,
        dateFieldDisplayConditions,
        statusChangeHandler = function() {
            var $followUpDate = $("#status-change-follow-up-date");
            var $comment = $("#status-change-comment");
            $("select[name=status_id]").change(function() {
                $followUpDate.fadeOut("fast")
                // if we are creating a new profile don't do anything
                if(originalStatusId === "") {
                    return;
                }
                // if the value is set to the original value, don't do anything
                if($(this).val() == originalStatusId) {
                    $comment.fadeOut("fast");
                    return;
                }
                // display the follow up date field if needed
                for(var i = 0; i < dateFieldDisplayConditions.length; i++) {
                    if ($(this).val() === dateFieldDisplayConditions[i]) {
                        $followUpDate.fadeIn("fast");
                    }
                }
                $comment.fadeIn("fast");
            });

        },
        init = function() {
            var $statusIdSelect = $("select[name=status_id]");
            originalStatusId = $statusIdSelect.data("original-value");
            dateFieldDisplayConditions = $statusIdSelect.data("enable-follow-up-date").split(",");
            statusChangeHandler();
        };
    return {
        init: init
    };
})();
