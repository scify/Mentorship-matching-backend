window.AvailabilityStatusChangeViewHandler = function() {
};

window.AvailabilityStatusChangeViewHandler.prototype = (function(){
    var dateFieldDisplayConditions,
        statusChangeHandler = function() {
            var $followUpDate = $("#status-change-follow-up-date");
            var $comment = $("#status-change-comment");
            $("select[name=status_id]").change(function() {
                $followUpDate.fadeOut("fast");
                $followUpDate.find("input").val("");
                var originalStatusId = $(this).data("original-value");
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
            dateFieldDisplayConditions = $("select[name=status_id]").data("enable-follow-up-date").split(",");
            statusChangeHandler();
        };
    return {
        init: init
    };
})();
