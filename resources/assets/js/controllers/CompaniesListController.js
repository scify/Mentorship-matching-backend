window.CompaniesListController = function () {
};

window.CompaniesListController.prototype = function () {
    var companiesCriteria = {},
        deleteCompanyBtnHandler = function () {
            $(".deleteCompanyBtn").on("click", function (e) {
                e.stopPropagation();
                var companyId = $(this).attr("data-companyId");
                $('#deleteCompanyModal').modal('toggle');
                $('#deleteCompanyModal').find('input[name="company_id"]').val(companyId);
            });
        },
        hideCardFooterOnTabChange = function () {
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
        parseSuccessData = function(response) {
            var responseObj = JSON.parse(response);
            //if operation was unsuccessful
            if (responseObj.status == 2) {
                $(".loader").addClass('hidden');
                $("#errorMsg").removeClass('hidden');
                $("#errorMsg").html(responseObj.data);
                $("#companiesList").html("");
            } else {
                $("#companiesList").html("");
                $("#errorMsg").addClass('hidden');
                $(".loader").addClass('hidden');
                $("#companiesList").html(responseObj.data);
                Pleasure.listenClickableCards();
            }
        },
        getCompaniesByFilter = function () {
            $.ajax({
                method: "GET",
                url: $(".filtersContainer").data("url"),
                cache: false,
                data: companiesCriteria,
                beforeSend: function () {
                    $('.panel-body').first().append('<div class="refresh-container"><div class="loading-bar indeterminate"></div></div>');
                },
                success: function (response) {
                    parseSuccessData(response);
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                },
                error: function (xhr, status, errorThrown) {
                    $('.refresh-container').fadeOut(500, function() {
                        $('.refresh-container').remove();
                    });
                    console.log(xhr.responseText);
                    $("#errorMsg").removeClass('hidden');
                    //The message added to Response object in Controller can be retrieved as following.
                    $("#errorMsg").html(errorThrown);
                }
            });
        },
        searchBtnHandler = function () {
            $("#searchBtn").on("click", function () {
                companiesCriteria.companyId = $('select[name=company_id]').val();
                getCompaniesByFilter();
            });
        },
        clearSearchBtnHandler = function () {
            $("#clearSearchBtn").on("click", function () {
                $('select[name=company_id]').val(0).trigger("chosen:updated");
                // clear companiesCriteria object from all of its properties
                for(var prop in companiesCriteria) {
                    if(companiesCriteria.hasOwnProperty(prop)) {
                        delete companiesCriteria[prop];
                    }
                }
                getCompaniesByFilter();
            });
        },
        initHandlers = function () {
            searchBtnHandler();
            clearSearchBtnHandler();
        },
        initSelectInputs = function() {
            $(".chosen-select").chosen({
                width: '100%'
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
            initHandlers();
            initSelectInputs();
        };
    return {
        init: init
    }
}();
