/**
 * Targets plugins that load colorbox
 * Fixes accessibility issue where some versions of colorbox load empty buttons on document load
 * This script is enqueued to specific plugin dependencies, to make sure it's called after said dependencies, no matter the dependency priority
 */

if (window.OpenLab === undefined) {
    var OpenLab = {};
}

OpenLab.colorboxFixes = (function ($) {
    return{
        emptyButtons: function () {

            if ($('#cboxContent').length === 0) {
                return false;
            }

            $('#cboxContent').find('button').each(function () {

                OpenLab.colorboxFixes.fillEmptyButton($(this));

            });
        },
        fillEmptyButton: function (thisButton) {

            var legacyLabel = thisButton.html();

            if (legacyLabel.length > 0) {
                return false;
            }

            var label = "Colorbox Button";
            var id = thisButton.attr('id');

            if (id) {

                label = id.replace(/cbox/, 'Colorbox ');

            }

            thisButton.html('<span class="sr-only">' + label + '</span>');

        }
    };
})(jQuery, OpenLab);

(function ($) {

    $(window).load(function () {
        OpenLab.colorboxFixes.emptyButtons();
    });

})(jQuery);