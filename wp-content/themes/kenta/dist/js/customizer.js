(() => {
    var __webpack_exports__ = {};
    if (wp.customize) {
        wp.customize.bind("ready", (function() {
            wp.customize.previewer.bind("kenta-theme-changed", (function(theme) {
                jQuery(document.documentElement).attr("data-kenta-theme", theme);
                jQuery(document.documentElement).attr("data-lotta-theme", theme);
            }));
            jQuery(document.documentElement).attr("data-kenta-theme", window.KentaCustomizer.theme);
            jQuery(document.documentElement).attr("data-lotta-theme", window.KentaCustomizer.theme);
            jQuery(window.KentaCustomizer.call_to_actions.join(",")).click((function(ev) {
                ev.preventDefault();
                var $btn = jQuery(this);
                $btn.attr("disabled", "disabled");
                $btn.html('<span class="loader"></span><span>Processing</span>');
                jQuery.ajax({
                    url: $btn.attr("href"),
                    complete: function complete() {
                        window.location.reload();
                    }
                });
            }));
        }));
    }
})();