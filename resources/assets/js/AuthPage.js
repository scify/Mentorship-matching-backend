window.AuthPage = function () {
};

window.AuthPage.prototype = function () {
    var showForgotPasswordPaneHandler = function() {
            $('.show-pane-forgot-password').click(function() {
                $('.panel-body').hide();
                $('#pane-forgot-password').fadeIn(1000);
                $('.login-screen').addClass('forgot-password');
            });
        },
        showLoginPaneHandler = function() {
            $('.show-pane-login').click(function() {
                $('.panel-body').hide();
                $('#pane-login').fadeIn(1000);
                $('.login-screen').removeClass('forgot-password');
            });
        },
        initializeHandlers = function() {
            showForgotPasswordPaneHandler();
            showLoginPaneHandler();
        },
        init = function() {
            initializeHandlers();
        };
    return {
        init: init
    }
}();