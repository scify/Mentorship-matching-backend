window.FormValidator = function () {
};

window.FormValidator.prototype = function () {
    var init = function () {
        $('.chosen-select').chosen({
            width: '100%'
        });
        $('.chosen-select').chosen();
        $.validator.setDefaults({ ignore: ":hidden:not(select)" });
        // validation of chosen on change
        if ($("select.chosen-select").length > 0) {
            $("select.chosen-select").each(function() {
                if ($(this).attr('required') !== undefined) {
                    $(this).on("change", function() {
                        $(this).valid();
                    });
                }
            });
        }

        // validation
        $('.jobPairsForm').validate({
            errorPlacement: function (error, element) {
                if (element.is("select.chosen-select")) {
                    // placement for chosen
                    element.next("div.chosen-container").append(error);
                } else {
                    // standard placement
                    var elementjQuery = $(element);
                    elementjQuery.closest(".inputer").next().html(error);
                    element.next("div").append(error);
                }
            }
        });
    };
    return {
        init: init
    }
}();
