window.EquivalenceSetsController = function (cards, editCardRoute, createEquivalenceSetRoute) {
    this.cards = JSON.parse(cards);
    this.editCardRoute = editCardRoute;
    this.createEquivalenceSetRoute = createEquivalenceSetRoute;
};

window.EquivalenceSetsController.prototype = function () {
    var deleteEquivalenceSetBtnHandler = function () {
            $(".deleteSetBtn").on("click", function (e) {
                e.stopPropagation();
                var setId = $(this).attr("data-equivalenceSetId");
                console.log(setId);
                $('#deleteEquivalenceSetModal').modal('toggle');
                $('#deleteEquivalenceSetModal .submitLink').attr("href", "equivalenceSet/delete?id=" + setId);
            });
        },
        editCardBtnHandler = function (cards, editCardRoute) {
            $(".editCardBtn").on("click", function (e) {
                e.stopPropagation();
                var cardId = $(this).attr("data-cardId");
                console.log(editCardRoute);
                var card = findCardById(cards, cardId);
                populateCardForm(card);
                $('#cardSimpleModal').modal('toggle');
                $('#simpleCardForm').attr("action", editCardRoute + "?cardId=" +cardId);
            });
        },
        findCardById = function(cards, cardId) {
            for(var i = 0; i < cards.length; i++) {
                if(cards[i].id == cardId)
                    return cards[i];
            }
            return null;
        },
        populateCardForm = function(card) {
            console.log(card);
            if(card.imageObj.file_path != null && card.imageObj.file_path != '') {
                $('.cardImage').removeClass("fileinput-new");
                $('.cardImage').addClass("fileinput-exists");
                $('.cardImage .fileinput-preview').append('<img src="' + card.imgPath +'" style="max-height: 170px;">');
            }

            if(card.negativeImageObj != null) {
                if (card.negativeImageObj.file_path != null && card.negativeImageObj.file_path != '') {
                    $('.cardNegativeImage').removeClass("fileinput-new");
                    $('.cardNegativeImage').addClass("fileinput-exists");
                    $('.cardNegativeImage .fileinput-preview').append('<img src="' + card.negativeImgPath + '" style="max-height: 170px;">');
                }
            }

            if(card.soundObj.file_path != null && card.soundObj.file_path != '') {
                $('.cardAudioVal').html(card.soundObj.file_path);
            }
            $("#cardSubmitBtn").html("Edit");

        },
        clearCardForm = function(instance) {
            $('.cardImage').addClass("fileinput-new");
            $('.cardImage').removeClass("fileinput-exists");
            $('.cardImage .fileinput-preview img').remove();
            $('.cardAudioVal').html("");
            $("#cardSubmitBtn").html("Create");
            $('#simpleCardForm').attr("action", instance.createEquivalenceSetRoute);
        },
        modalCloseHandler = function(instance) {
            $('#cardSimpleModal').on('hidden.bs.modal', function () {
                clearCardForm(instance);
                console.log("form cleared");
            })
        },
        init = function () {
            var instance = this;
            console.log(instance.cards);
            console.log("here");
            deleteEquivalenceSetBtnHandler();
            editCardBtnHandler(instance.cards, instance.editCardRoute);
            modalCloseHandler(instance);
        };
    return {
        init: init
    }
}();