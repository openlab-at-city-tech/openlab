/**
 * Scripts within the customizer controls window.
 **/

 (function( $, api ) {
    wp.customize.bind('ready', function() {

        /* === Radio Image Control === */
        api.controlConstructor['radio-color'] = api.Control.extend( {
            ready: function() {
                var control = this;

                $( 'input:radio', control.container ).change(
                    function() {
                        control.setting.set( $( this ).val() );
                    }
                    );
            }
        } );

        // Deep linking for counter section to about section.
        jQuery('.flawless-blog-edit').click(function(e) {
            e.preventDefault();
            var jump_to = jQuery(this).attr( 'data-jump' );
            wp.customize.section( jump_to ).focus()
        });

        $('#sub-accordion-section-flawless_blog_topbar').css( 'display', 'none' );
    });
})( jQuery, wp.customize );

(function(api) {

    const flawless_blog_section_lists = ['flash_articles','banner','popular-posts','posts-grid'];
    flawless_blog_section_lists.forEach(flawless_blog_homepage_scroll);

    function flawless_blog_homepage_scroll(item, index) {
        // Detect when the front page sections section is expanded (or closed) so we can adjust the preview accordingly.
        item = item.replace(/-/g, '_');
        wp.customize.section('flawless_blog_' + item + '_section', function(section) {
            section.expanded.bind(function(isExpanding) {
                // Value of isExpanding will = true if you're entering the section, false if you're leaving it.
                wp.customize.previewer.send(item, { expanded: isExpanding });
            });
        });
    }
})(wp.customize);