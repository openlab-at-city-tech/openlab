document.addEventListener("DOMContentLoaded", function(event) {
    function trpClearWooCartFragments(){

        // clear WooCommerce cart fragments when switching language
        var trp_language_switcher_urls = document.querySelectorAll(".trp-language-switcher-container a:not(.trp-ls-disabled-language)");

        for (i = 0; i < trp_language_switcher_urls.length; i++) {
            trp_language_switcher_urls[i].addEventListener("click", function(){
                if ( typeof wc_cart_fragments_params !== 'undefined' && typeof wc_cart_fragments_params.fragment_name !== 'undefined' ) {
                    window.sessionStorage.removeItem(wc_cart_fragments_params.fragment_name);
                }
            });
        }
    }

    trpClearWooCartFragments();
});
