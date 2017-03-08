window.SearchController = function () {
};

window.SearchController.prototype = function () {
    var $ajaxCall,
        searchBtnHandler = function () {
            $(".nav-search .search").on("click", function (e) {
                getSearchResults(null);
            });
        },
        searchInputHandler = function() {
            $("#input-search").on("keyup", function() {
                setTimeout(getSearchResults($(this).val()), 2 * 1000);
            });
        },
        getSearchResults = function(searchQuery) {
            // if there is a pending ajax call, abort it
            if($ajaxCall !== undefined && $ajaxCall !== null && $ajaxCall.state() === 'pending') {
                $ajaxCall.abort();
            }
            var data = {
                'search_query': searchQuery
            };
            $ajaxCall = $.ajax({
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
                    if(errorThrown !== 'abort') {
                        console.log(xhr.responseText);
                        $(".search-layer .loader").addClass('hidden');
                        $("#search-error-messages").removeClass('hidden');
                        //The message added to Response object in Controller can be retrieved as following.
                        $("#search-error-messages").html(errorThrown);
                    }
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
            searchInputHandler();
        },
        init = function () {
            initializeHandlers();
        };
    return {
        init: init
    }
}();
