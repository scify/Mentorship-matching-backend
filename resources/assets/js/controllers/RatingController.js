window.RatingController = function() {
};

window.RatingController.prototype = (function() {
    var hoverRatingItemsHandler = function() {
            $(".rating-item").on("mouseover", function() {
                var elementId = $(this).attr("id");
                var elementIdNumber = parseInt(elementId.replace("star", ""));
                // make all previous stars and self yellow, and make gray all the others
                for (var i = 0; i < 5; i++) {
                    var $star = $("#star" + (i + 1));
                    if (i < elementIdNumber)
                        $star.css("color", "#ff6");
                    else {
                        if (!$star.hasClass("glyphicon-star"))
                            $star.css("color", "");
                    }
                }
            });
        },
        unhoverRatingItemsHandler = function() {
            $("#rating-container").on("mouseleave", function() {
                $(".rating-item").each(function() {
                    if (!$(this).hasClass("glyphicon-star"))
                        $(this).css("color", "");
                });
            });
        },
        selectRatingItemHandler = function() {
            $(".rating-item").on("click", function() {
                var elementId = $(this).attr("id");
                var elementIdNumber = parseInt(elementId.replace("star", ""));
                $("input[name=rating]").val(elementIdNumber);
                // make all previous stars and self yellow and full, and make gray and empty all the others
                for (var i = 0; i < 5; i++) {
                    var $star = $("#star" + (i + 1));
                    if (i < elementIdNumber) {
                        if (!$star.hasClass("glyphicon-star"))
                            $star.removeClass("glyphicon-star-empty").addClass("glyphicon-star");
                        $star.css("color", "#ff6");
                    } else {
                        if (!$star.hasClass("glyphicon-star-empty"))
                            $star.removeClass("glyphicon-star").addClass("glyphicon-star-empty");
                        $star.css("color", "");
                    }
                }
            });
        },
        initHandler = function() {
            hoverRatingItemsHandler();
            unhoverRatingItemsHandler();
            selectRatingItemHandler();
        },
        init = function() {
            initHandler();
        };
    return {
        init: init
    }
})();
