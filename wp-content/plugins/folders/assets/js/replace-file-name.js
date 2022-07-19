(function (factory) {
    "use strict";
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    }
    else if(typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'));
    }
    else {
        factory(jQuery);
    }
}(function ($, undefined) {
    $(document).ready(function(){
        var appendString = "<div class='folders-undo-notification' id='name-change-success'><div class='folders-undo-body' style='padding: 0'><a href='javascript:;' class='close-undo-box'><span></span></a><div class='folders-undo-header' style='padding: 0'></div></div></div>";
        $("body").append(appendString);
        $(document).on("click", ".folder-replace-checkbox", function(){
            if($(this).is(":checked")) {
                $(this).closest("td").find("a.update-name-with-title").addClass("show");
            } else {
                $(this).closest("td").find("a.update-name-with-title").removeClass("show");
            }
        });
        $(document).on("click", ".close-undo-box", function(e){
            e.preventDefault();
            $("#name-change-success").removeClass("active");
        });
    });
}));