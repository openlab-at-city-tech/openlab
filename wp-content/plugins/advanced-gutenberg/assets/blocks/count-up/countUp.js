jQuery(document).ready(function ($) {
    $('.advgb-counter-number').each(function () {
        var counterValue = $(this).text();
        var isNumber = $.isNumeric(counterValue);

        if (isNumber) {
            $(this).counterUp({
                delay: 10,
                time: 1000
            })
        }
    });
});