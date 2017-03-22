window.ProfileController = function () {
};

window.ProfileController.prototype = function() {
    var initTabs = function() {
            $('a[data-toggle="tab"]').click(function (e) {
                e.preventDefault();
                var profilePageClassElement = $(".profilePage");
                profilePageClassElement.find('.tab-pane.active').removeClass('active');
                profilePageClassElement.find('.tab-content.active').removeClass('active');
                console.log($(this).attr('data-href'));
                profilePageClassElement.find('div[id="' + $(this).attr('data-href') + '"]').addClass('active');
            });
        },
        init = function() {
            initTabs();
        };
    return {
        init: init
    };
}();
