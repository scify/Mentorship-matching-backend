window.TabsHandler = function () {
};

window.TabsHandler.prototype = function() {
    var initTabs = function($parentElement) {
            $($parentElement.find('a[data-toggle="tab"]')).click(function (e) {
                e.preventDefault();
                $parentElement.find('.tab-pane.active').removeClass('active');
                $parentElement.find('.tab-content.active').removeClass('active');
                $parentElement.find('div[id="' + $(this).attr('data-href') + '"]').addClass('active');
            });
        },
        init = function(parentElementName) {
            initTabs($(parentElementName));
        };
    return {
        init: init
    };
}();
