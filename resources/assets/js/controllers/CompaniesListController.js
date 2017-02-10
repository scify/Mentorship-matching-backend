window.CompaniesListController = function () {
};

window.CompaniesListController.prototype = function () {
    var deleteCompanyBtnHandler = function () {
            $(".deleteCompanyBtn").on("click", function (e) {
                e.stopPropagation();
                var companyId = $(this).attr("data-companyId");
                $('#deleteCompanyModal').modal('toggle');
                $('#deleteCompanyModal').find('input[name="company_id"]').val(companyId);
            });
        },
        hideCardFooterOnTabChange = function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var targetId = $(e.target).attr("data-href"); // activated tab
                var target = $("#" + targetId);
                if(targetId.indexOf("tab_2") >= 0) {
                    target.closest(".card-user").find(".card-footer").addClass("hidden");
                } else {
                    target.closest(".card-user").find(".card-footer").removeClass("hidden");
                }
            });
        },
        init = function () {
            deleteCompanyBtnHandler();
            $('a[data-toggle="tab"]').click(function (e) {
                e.preventDefault();
                $(".card_" + $(this).attr('data-id')).find('.tab-pane.active').removeClass('active');

                $(".card_" + $(this).attr('data-id')).find('.tab-content.active').removeClass('active');

                $(".card_" + $(this).attr('data-id')).find('div[id="' + $(this).attr('data-href') + '"]').addClass('active');
            });
            hideCardFooterOnTabChange();
        };
    return {
        init: init
    }
}();