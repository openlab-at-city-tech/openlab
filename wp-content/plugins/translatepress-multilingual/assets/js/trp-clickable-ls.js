jQuery('.trp_language_switcher_shortcode .trp-ls-shortcode-current-language').click(function () {
    jQuery( '.trp_language_switcher_shortcode .trp-ls-shortcode-current-language' ).addClass('trp-ls-clicked');
    jQuery( '.trp_language_switcher_shortcode .trp-ls-shortcode-language' ).addClass('trp-ls-clicked');
});

jQuery('.trp_language_switcher_shortcode .trp-ls-shortcode-language').click(function () {
    jQuery( '.trp_language_switcher_shortcode .trp-ls-shortcode-current-language' ).removeClass('trp-ls-clicked');
    jQuery( '.trp_language_switcher_shortcode .trp-ls-shortcode-language' ).removeClass('trp-ls-clicked');
});

jQuery(document).keyup(function(e) {
    if (e.key === "Escape") {
        jQuery( '.trp_language_switcher_shortcode .trp-ls-shortcode-current-language' ).removeClass('trp-ls-clicked');
        jQuery( '.trp_language_switcher_shortcode .trp-ls-shortcode-language' ).removeClass('trp-ls-clicked');
    }
});

jQuery(document).on("click", function(event){
    if(!jQuery(event.target).closest(".trp_language_switcher_shortcode .trp-ls-shortcode-current-language").length){
        jQuery( '.trp_language_switcher_shortcode .trp-ls-shortcode-current-language' ).removeClass('trp-ls-clicked');
        jQuery( '.trp_language_switcher_shortcode .trp-ls-shortcode-language' ).removeClass('trp-ls-clicked');
    }
});