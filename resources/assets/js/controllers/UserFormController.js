window.UserFormController = function () {
    this.ACCOUNT_MANAGER_USER_ROLE_ID = 3;
};

window.UserFormController.prototype = function () {
    var roleSelectorHandler = function(instance) {
        $("#roleSelector").chosen().change(function(){
            // selectedIds is an array with the selected role ids (as strings)
            var selectedIds = $(this).val();
            if(selectedIds.indexOf(String(instance.ACCOUNT_MANAGER_USER_ROLE_ID)) != -1) {
                // if the user selected the account manager role, show account manager details div
                $("#accountManagerDetailsContainer").removeClass("hidden");
            } else {
                // if the user un-selected the account manager role, hide account manager details div
                $("#accountManagerDetailsContainer").addClass("hidden");
            }
        });
    },
    userIconClickHandler = function() {
        $('.userIconRadio').on('ifChanged', function(event){
            $(".userIconLabel").addClass("greyscale");
            $(this).parent().next().removeClass("greyscale");
            $(this).parent().next().addClass("SDGASDFGSEGHSEHGHGSDH");
        });
    },
    init = function () {
        var instance = this;
        $('.chosen-select').chosen({
            width: '100%'
        });
        roleSelectorHandler(instance);
        userIconClickHandler();
    };
    return {
        init: init
    }
}();
