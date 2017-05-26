/**
 * Created by snik on 4/12/17.
 */
window.ResidenceHandler = function() {

};

window.ResidenceHandler.prototype = (function(){
    var residenceSelectionHandler = function() {
        $("select[name=residence_id]").change(function(){
            var $residenceNameDiv = $(".residenceName");
            if(parseInt($(this).val()) === parseInt($(this).data("show-name-on-id"))) {
                $residenceNameDiv.fadeIn("fast");
            } else {
                $residenceNameDiv.find("input").val("");
                $residenceNameDiv.fadeOut("fast");
            }
        });
    },
    initHandler = function() {
        residenceSelectionHandler();
    };
    return {
        initHandler: initHandler
    }
})();
