jQuery(document).ready(function($){
    $(".advgb-accordion-wrapper").each(function() {
        $(this).accordion({
            header: "> div > .advgb-accordion-header",
            heightStyle: "content",
            collapsible: true,
            active: $(this).data("collapsed") ? false : 0,
        });
    });
});
