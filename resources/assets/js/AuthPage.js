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
        showForgotPasswordPaneIfContainsErrors = function() {
            if($("#pane-forgot-password .has-error").length > 0)
                $('.show-pane-forgot-password').trigger("click");
        },
        initializeHandlers = function() {
            showForgotPasswordPaneHandler();
            showLoginPaneHandler();
            showForgotPasswordPaneIfContainsErrors();
        },
        init = function() {
            initializeHandlers();
        };
    return {
        init: init
    }
}();