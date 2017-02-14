window.MentorProfileController = function () {
};

window.MentorProfileController.prototype = function () {
    var

        init = function () {
            $('a[data-toggle="tab"]').click(function (e) {
                e.preventDefault();
                $(".profilePage").find('.tab-pane.active').removeClass('active');

                $(".profilePage").find('.tab-content.active').removeClass('active');
                console.log($(this).attr('data-href'));
                $(".profilePage").find('div[id="' + $(this).attr('data-href') + '"]').addClass('active');
            });
        };
    return {
        init: init
    }
}();