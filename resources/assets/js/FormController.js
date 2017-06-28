window.FormController = function () {
};

window.FormController.prototype = function () {
    var init = function () {
        var isSpecialtiesInsertionEnabled = $("[name='specialties[][id]']").data("enable-specialties-insertion");

        if (isSpecialtiesInsertionEnabled) {
            // on mentors' specialties input field, a user is free to insert new specialties,
            // so the message when there is not the specialty typed found is changed to something more fitting
            $("[name='specialties[][id]']").chosen({
                width: '100%',
                no_results_text: 'Press \'Enter\' button to select'
            });

            // when a mentor types a new specialty, insert the typed value to the bottom of list and select it
            $("[name='specialties[][id]']").siblings(".chosen-container").find(".search-field input").on("keyup", function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    var inputVal = $(this).val();
                    var $specialtiesSelect = $("[name='specialties[][id]']");
                    $specialtiesSelect.append("<option value='new-specialty-" + inputVal + "'>" + inputVal + "</option>");
                    var newChosenValue = $specialtiesSelect.val();
                    newChosenValue.push('new-specialty-' + inputVal);
                    $specialtiesSelect.val(newChosenValue);
                    $specialtiesSelect.trigger("chosen:updated");
                }
            });
        }

        $('.chosen-select').chosen({
            width: '100%'
        });
        //$.validator.setDefaults({ ignore: ":hidden:not(select)" });
        // validation of chosen on change
        // if ($("select.chosen-select").length > 0) {
        //     $("select.chosen-select").each(function() {
        //         if ($(this).attr('required') !== undefined) {
        //             $(this).on("change", function() {
        //                 $(this).valid();
        //             });
        //         }
        //     });
        // }

        $(".select2-company").select2({
            maximumSelectionLength: 2,
            tags: true,
            createTag: function (params) {
                var term = $.trim(params.term);

                if (term === '') {
                    return null;
                }

                return {
                    id: 'new-company-' + term,
                    text: term,
                    newTag: true
                }
            }
        });

        // validation
        // $('.jobPairsForm').validate({
        //     errorPlacement: function (error, element) {
        //         if (element.is("select.chosen-select")) {
        //             // placement for chosen
        //             element.next("div.chosen-container").append(error);
        //         } else {
        //             // standard placement
        //             var elementjQuery = $(element);
        //             elementjQuery.closest(".inputer").next().html(error);
        //             element.next("div").append(error);
        //         }
        //     }
        // });
        //
        // var inputFields = $( ".form-control" );
        // inputFields.focus(function() {
        //     var inputName = $(this).closest(".formRow").find(".formElementName");
        //     inputName.addClass("focused");
        // });
        // inputFields.focusout(function() {
        //     var inputName = $(this).closest(".formRow").find(".formElementName");
        //     inputName.removeClass("focused");
        // });
    };
    return {
        init: init
    }
}();
