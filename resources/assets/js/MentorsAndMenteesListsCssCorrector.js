window.MentorsAndMenteesListsCssCorrector = function () {
};

window.MentorsAndMenteesListsCssCorrector.prototype = function () {
    var setCorrectCssClasses = function(listId) {
        var $allSingleItems = $(listId).find(".singleItem");
        var $firstSingleItem = $allSingleItems.first();
        if($firstSingleItem.length !== 0) {
            if($firstSingleItem.find(".menu-action").length > 0) {
                $allSingleItems.find(".no-slide-left").removeClass("no-slide-left");
            }
            if($firstSingleItem.find(".menu-action").length === 2) {
                $allSingleItems.addClass("twoActionButtons");
            }
        }
    };
    return {
        setCorrectCssClasses: setCorrectCssClasses
    }
}();
