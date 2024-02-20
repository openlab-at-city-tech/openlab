jQuery(document).ready( function()
{
    ( function( wp ) {

        //
        // Editor toolbar button
        //
        var ZotpressBibButton = function( props ) {
            return wp.element.createElement(

                wp.blockEditor.RichTextToolbarButton, {
                    icon: 'shortcode',
                    title: 'Zotpress Shortcode',
                    onClick: function()
                    {
                        console.log( 'zp: Zotpress bib shortcode' );

                        // Clear the previous search list
                        zpClearSearch();

                        // Modal with shortcode generator;
                        // grab the legacy metabox from the Page sidebar
                        jQuery("#zp-ZotpressMetaBox")
                            .addClass("zp-ShortcodeBuilder")
                            .appendTo(".interface-interface-skeleton__body")
                            .find(".zp-ZotpressMetaBox-Insert-Button").text( zpTranslate.txt_insertsc );

                        // Focus on search input
                        jQuery("#zp-ZotpressMetaBox")
                            .find("#zp-ZotpressMetaBox-Search-Input").focus();

                        //
                        // Shortcode buttons
                        //

                        jQuery(".zp-ZotpressMetaBox-Insert-Button")
                            .click( function()
                            {
                                zpInsertShortcode( jQuery(this), props );
                            }
                        );
                    },
                    isActive: props.isActive,
                }
            );
        }

        //
        // We're using Format Type to insert shortcodes
        //
        wp.richText.registerFormatType(
            'zotpress-gutenberg/bib', {
                title: 'Insert Zotpress Shortcode',
                tagName: 'span',
                className: 'zp-InText-Citation-Container',
                edit: ZotpressBibButton,
            }
        );

        //
        // Insert shortcode depending on type
        //
        function zpInsertShortcode( $scButton, props )
        {
            // Generate the shortcode
            var zpBiblioShortcode = window.zpGenerateShortcodeString( $scButton.data("sctype") );
            // console.log(zpBiblioShortcode);

            // Create the shortcode string
            let shortcodeRichText = wp.richText.create({
                // TODO: When making it like Word ...
                // ... can re-add the HTML ...
                // ... and change the HTML view display
                // to something more meaningful, like
                // "Okorafor (2021)" for in-text ...
                // html: '<span class="zp-InText-Citation-Container">'+zpBiblioShortcode+'</span>'
                html: zpBiblioShortcode,
            });

            // Insert it
            props.onChange(
                wp.richText.insert(
                    props.value,
                    shortcodeRichText, // content
                    props.value.start,
                    props.value.end,
                )
            );

            // Close the modal, put the metabox back
            jQuery("#zp-ZotpressMetaBox")
                .removeClass("zp-ShortcodeBuilder")
                .appendTo("#ZotpressMetaBox .inside")
                .find(".zp-ZotpressMetaBox-Insert-Button").text( zpTranslate.txt_insertsc );

        } // zpInsertShortcode()

        //
        // Exit button
        //
        jQuery("#zp-ShortcodeBuilder-Close").click(
            function()
            {
                jQuery("#zp-ZotpressMetaBox")
                    .removeClass("zp-ShortcodeBuilder")
                    .appendTo("#ZotpressMetaBox .inside");
            }
        );

        //
        // Clear the search list
        //
        function zpClearSearch()
        {
            jQuery(".zp-ZotpressMetaBox-Insert-Button").off("click");
            window.zpRefItems = [];
            jQuery("#zp-ZotpressMetaBox-List .item").remove();

        } // zpClearSearch()

    } )( window.wp );
});
