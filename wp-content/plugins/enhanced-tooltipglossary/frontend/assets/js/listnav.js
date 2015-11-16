/*
 * Inside this closure we use $ instead of jQuery in case jQuery is reinitialized again before document.ready()
 */
(function ($) {
    "use strict";

    $(document).ready(function () {

        if (window.cmtt_listnav_data !== undefined && window.cmtt_listnav_data.listnav && window.cmtt_listnav_data.list_id) {
            $(document).ready(function ($) {
                $("#" + window.cmtt_listnav_data.list_id).listnav(window.cmtt_listnav_data.listnav);
            });
        }

    });
}(jQuery));