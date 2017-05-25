/**
 * Created by snik on 4/12/17.
 */
window.ReferenceHandler = function() {

};

window.ReferenceHandler.prototype = (function(){
    var referenceSelectionHandler = function() {
        $("select[name=reference_id]").change(function(){
            var $referenceTextDiv = $(".referenceText");
            if(parseInt($(this).val()) === parseInt($(this).data("show-name-on-id"))) {
                $referenceTextDiv.fadeIn("fast");
            } else {
                $referenceTextDiv.find("input").val("");
                $referenceTextDiv.fadeOut("fast");
            }
        });
    },
    initHandler = function() {
        referenceSelectionHandler();
    };
    return {
        initHandler: initHandler
    }
})();
