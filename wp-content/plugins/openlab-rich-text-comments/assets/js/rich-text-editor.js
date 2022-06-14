'use strict';
(function($) {
    
    $(document).ready( function() {
        /**
         * Initialize Quill Editor
         */
        var quillEditor = new Quill('#ol-rich-editor', {
            modules: {
                toolbar: {
                    container: [ 'bold', 'italic', 'underline', 'link', 'image', { 'list': 'ordered'}, { 'list': 'bullet' } ],
                    handlers: {
                        image: imageHandler
                    }
                }
            },
            theme: 'snow'
        });

        /**
         * Change link input placeholder
         */
        var tooltip = quillEditor.theme.tooltip;
        var input = tooltip.root.querySelector('input[data-link]');
        input.dataset.link = 'https://example.com';

        /**
         * Implement custom handler for the image button.
         * Show tooltip for entering URL, instead of uploading an image.
         */
        function imageHandler() {
            const tooltip = quillEditor.theme.tooltip;
            const originalSave = tooltip.save;
            const originalHide = tooltip.hide;

            // Called on save
            tooltip.save = function() {
                const range = quillEditor.getSelection(true);
                const value = tooltip.textbox.value;
                if (value) {
                    quillEditor.insertText(range.index, value, true);
                }
            };

            // Called on hide and save.
            tooltip.hide = function () {
                tooltip.save = originalSave;
                tooltip.hide = originalHide;
                tooltip.hide();
            };

            tooltip.edit('image');
            tooltip.textbox.placeholder = 'Enter media URL';
        }

        /**
         * Get content of the Quill editor and put it's content
         * in the comment text field.
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

        /**
         * Add aria-label to the buttons in the toolbar;
         * Add aria-pressed on the buttons in the toolbar;
         * Add aria-hidden on the SVG icons in the buttons;
         * Add role and aria-multiline attribute to the editor textarea;
         */
        $('button.ql-bold').attr( 'aria-label', 'Toggle bold text' );
        $('button.ql-italic').attr( 'aria-label', 'Toggle italic text' );
        $('button.ql-underline').attr( 'aria-label', 'Toggle underline text' );
        $('button.ql-link').attr( 'aria-label', 'Toggle link modal' );
        $('button.ql-list[value="ordered"]').attr('aria-label', 'Toggle ordered list');
        $('button.ql-list[value="bullet"]').attr('aria-label', 'Toggle bulleted list');
        $('button.ql-image').attr( 'aria-label', 'Toggle multimedia modal' );
        $('.ql-formats button').attr('aria-pressed', false );
        $('.ql-formats button > svg').attr('aria-hidden', true );
        $('#ol-rich-editor .ql-editor').attr( {
            'role': 'textbox',
            'aria-multiline': true
        } );

        /**
         * Toggle aria-pressed attribute on clicking the buttons
         * in the editor's toolbar.
         */
        $(document).on( 'click', '.ql-formats button', function(e) {
            $(this).attr( 'aria-pressed', $(this).hasClass('ql-active') );
        });

        /**
         * Validate if the comment textarea is empty and show an error message.
         */
        $(document).on( 'click', 'form#commentform input#submit', function(e) {
            $('#response-notice').remove();
            
            let commentElement = $('textarea#comment');
            let isRequired = ( typeof commentElement.attr('required') != 'undefined' && commentElement.attr('required') !== false ) ? true : false;
            let commentText = commentElement.val();

            if( ! commentText && isRequired ) {
                e.preventDefault();
                $('form#commentform').append('<div id="response-notice"><p>The comment field is required.</p></div>');
                return;
            }
        });

    } );

})(jQuery);
