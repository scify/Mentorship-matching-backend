/**
 * Created by snik on 4/12/17.
 */
window.UniversityHandler = function() {

};

window.UniversityHandler.prototype = (function(){
    var universitySelectionHandler = function() {
        $("select[name=university_id]").change(function(){
            var $universityNameDiv = $(".universityName");
            if(parseInt($(this).val()) === parseInt($(this).data("show-name-on-id"))) {
                $universityNameDiv.fadeIn("fast");
            } else {
                $universityNameDiv.find("input").val("");
                $universityNameDiv.fadeOut("fast");
            }
        });
    },
    initHandler = function() {
        universitySelectionHandler();
    };
    return {
        initHandler: initHandler
    }
})();
