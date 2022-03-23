'use strict';
(function($) {
    
    $(document).ready( function() {
        /**
         * Initialize Quill Editor
         */
        var quillEditor = new Quill('#ol-rich-editor', {
            modules: {
                toolbar: [
                    [ 'bold', 'italic', 'underline', 'link' ],
                    [ { 'list': 'ordered'}, { 'list': 'bullet' } ]
                ]
            },
            theme: 'snow'
        });

        /**
         * Get content of the Quill editor and put it's content
         * in the comment text field.
         * 
         */
        quillEditor.on( 'text-change', function( delta, oldDelta, source ) {
            if( quillEditor.getText().trim() ) {
                let contentHtml = quillEditor.root.innerHTML;
                $('textarea#comment').val(contentHtml);
                $('#response-notice').remove();
            } else {
                $('textarea#comment').val('');
            }
        });

    } );

})(jQuery);
