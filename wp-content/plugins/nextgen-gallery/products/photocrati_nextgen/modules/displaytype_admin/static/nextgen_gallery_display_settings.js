(function($) {
    $('.accordion').accordion({
        clearStyle: true,
        autoHeight: false,
        heightStyle: 'content'
    });

    $('label.tooltip, span.tooltip').tooltip();
})(jQuery);