window.SearchController = function () {
};

window.SearchController.prototype = function () {
    var searchBtnHandler = function () {
            $(".nav-search .search").on("click", function (e) {
                getSearchResults(null);
            });
        },
        getSearchResults = function(searchQuery) {
            var data = {
                'search_query': searchQuery
            };
            $.ajax({
                method: "GET",
                url: $("#common-search").data("search-url"),
                cache: false,
                data: data,
                beforeSend: function () {
                    $(".search-layer .loader").removeClass('hidden');
                },
                success: function (response) {
                    parseSuccessData(response);
                },
                error: function (xhr, status, errorThrown) {
                    console.log(xhr.responseText);
                    $(".search-layer .loader").addClass('hidden');
                    $("#search-error-messages").removeClass('hidden');
                    //The message added to Response object in Controller can be retrieved as following.
                    $("#search-error-messages").html(errorThrown);
                }
            });
        },
        parseSuccessData = function(response) {
            var responseObj = JSON.parse(response);
            //if operation was unsuccessful
            if (responseObj.status == 2) {
                $(".search-layer .loader").addClass('hidden');
                $("#search-error-messages").removeClass('hidden');
                $("#search-error-messages").html(responseObj.data);
                $("#search-results").html("");
            } else {
                $("#search-results").html("");
                $("#search-error-messages").addClass('hidden');
                $(".search-layer .loader").addClass('hidden');
                $("#search-results").html(responseObj.data);
            }
        },
        initializeHandlers = function() {
            searchBtnHandler();
        },
        init = function () {
            initializeHandlers();
        };
    return {
        init: init
    }
}();
