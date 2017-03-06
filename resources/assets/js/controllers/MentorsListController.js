window.MentorsListController = function () {
};

window.MentorsListController.prototype = function () {

    var deleteMentorBtnHandler = function () {
            $("body").on("click", ".deleteMentorBtn", function (e) {
                e.stopPropagation();
                var mentorId = $(this).attr("data-mentorId");
                $('#deleteMentorModal').modal('toggle');
                $('#deleteMentorModal').find('input[name="mentor_id"]').val(mentorId);
            });
        },
        initializeHandlers = function() {
            deleteMentorBtnHandler();
            cardTabClickHandler();
            searchBtnHandler();
            clearSearchBtnHandler();
        },
        searchBtnHandler = function () {
            $("#searchBtn").on("click", function (e) {
                var specialtyId = $('select[name=specialty]').val();
                var mentorName = $('input[name=mentorName]').val();
                console.log("search triggered for specialty: " + specialtyId + " and name: " + mentorName);
                getMentorsByFilter(specialtyId, mentorName);
            });
        },
        clearSearchBtnHandler = function () {
            $("#clearSearchBtn").on("click", function (e) {
                getMentorsByFilter(null, null);
                $('select[name=specialty]').val(0);
                $('select[name=specialty]').trigger("chosen:updated");
                $('input[name=mentorName]').val(null);
            });
        },
        getMentorsByFilter = function (specialtyId, mentorName) {
            var data = {
                'specialty_id': specialtyId,
                'mentor_name': mentorName
            };
            $.ajax({
                method: "GET",
                url: "byCriteria",
                cache: false,
                data: data,
                beforeSend: function () {
                    $(".loader").removeClass('hidden');
                },
                success: function (response) {
                    parseSuccessData(response);
                },
                error: function (xhr, status, errorThrown) {
                    console.log(xhr.responseText);
                    $(".loader").addClass('hidden');
                    $("#errorMsg").removeClass('hidden');
                    //The message added to Response object in Controller can be retrieved as following.
                    $("#errorMsg").html(errorThrown);
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
                $("#usersList").html("");
            } else {
                $("#usersList").html("");
                $("#errorMsg").addClass('hidden');
                $(".loader").addClass('hidden');
                $("#usersList").html(responseObj.data);
                Pleasure.listenClickableCards();
            }
        },
        initializeSpecialtiesSelect = function() {
            $('#specialtiesSelect').chosen({
                width: '100%'
            });
        },
        cardTabClickHandler = function() {
            $('a[data-toggle="tab"]').click(function (e) {
                e.preventDefault();
                console.log($(this).attr('data-id'));
                var objectId = $(".card_" + $(this).attr('data-id'));
                objectId.find('.tab-pane.active').removeClass('active');
                objectId.find('.tab-content.active').removeClass('active');
                objectId.find('div[id="' + $(this).attr('data-href') + '"]').addClass('active');
            });
        },
        init = function () {
            initializeHandlers();
            initializeSpecialtiesSelect();
        };
    return {
        init: init
    }
}();
