jQuery( function () {
    function trp_place_tp_button() {

        // check if gutenberg's editor root element is present.
        var editorEl = document.getElementById( 'editor' )
        if ( !editorEl ){ // do nothing if there's no gutenberg root element on page.
            return
        }

        var unsubscribe = wp.data.subscribe( function () {
            if ( !document.getElementById( "trp-link-id" ) ){
                // Support the changes in UI in WordPress 6.5
                var toolbarLeftEl = editorEl.querySelector('.editor-document-tools__left')
                if ( !toolbarLeftEl ) { // Fallback to the legacy toolbar class for compatibility with older versions
                    toolbarLeftEl = editorEl.querySelector('.edit-post-header-toolbar__left')
                }

                if ( toolbarLeftEl instanceof HTMLElement ){
                    toolbarLeftEl.insertAdjacentHTML( "afterend", trp_url_tp_editor[ 0 ] )
                }
            }
        } )
    }

    /**
     * There is no good way to trigger a function when block was loaded for the first time or when adding a new block
     * so this workaround was needed. Inline JS that fixes width is not working in Gutenberg Editor.
     * https://github.com/WordPress/gutenberg/issues/8379
     */
    function trp_gutenberg_blocks_loaded(){
        if ( !trp_localized['dont_adjust_width']){
            var blockLoadedInterval = setInterval( function () {
                var trp_ls_shortcodes = document.querySelectorAll( ".trp_language_switcher_shortcode .trp-language-switcher:not(.trp-set-width)" )
                if ( trp_ls_shortcodes.length > 0 ){
                    trp_adjust_shortcode_width( trp_ls_shortcodes )
                }

            }, 500 );
        }
    }


    function trp_adjust_shortcode_width(trp_ls_shortcodes){
        for( var i = 0; i < trp_ls_shortcodes.length; i++ ) {
            var trp_el = trp_ls_shortcodes[i];
            trp_ls_shortcodes[i].classList.add("trp-set-width")
            var trp_shortcode_language_item                                          = trp_el.querySelector( ".trp-ls-shortcode-language" )
            // set width
            var trp_ls_shortcode_width                                               = trp_shortcode_language_item.offsetWidth + 16;
            trp_shortcode_language_item.style.width                                  = trp_ls_shortcode_width + "px";
            trp_el.querySelector( ".trp-ls-shortcode-current-language" ).style.width = trp_ls_shortcode_width + "px";

            // We\'re putting this on display: none after we have its width.
            trp_shortcode_language_item.style.display = "none";
        }
    }

    trp_place_tp_button()
    trp_gutenberg_blocks_loaded()

} )
