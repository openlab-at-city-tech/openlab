'use strict';
(function($) {

    const Embed = Quill.import('blots/embed');

    class MentionBlot extends Embed {
        static create(data) {
            const node = super.create(data.value);
            node.setAttribute('data-value', data.value);
    
            const denotationChar = document.createElement('span');
            denotationChar.className = 'ql-mention-denotation-char';
            denotationChar.innerHTML = data.denotationChar;
            node.appendChild(denotationChar);
    
            node.innerHTML += data.value;
            return node;
        }
    
        static value(domNode) {
            return domNode.getAttribute('data-value');
        }
      
        deleteAt() {
            return false;
        }
    }

    MentionBlot.blotName = 'Mention';
    MentionBlot.tagName = 'span';
    MentionBlot.className = 'mention';

    Quill.register(MentionBlot, true);
    
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

        /**
         * Prepend at-mention tag when replying to a private comment.
         */
        $( document ).on( 'touchstart click', '.comment-reply-link', function() {
            var privateCheckbox = $( '#ol-private-comment' );
            let username = $(this).attr('data-username');

            setTimeout( function() {
                if( privateCheckbox.prop( 'checked') ) {
                    quillEditor.insertEmbed(
                        0,
                        'Mention',
                        {
                            denotationChar: '@',
                            index: 0,
                            id: 'id',
                            value: username + ' ',
                        },
                        'api',
                    );
                    quillEditor.setSelection(username.length);
                }
            }, 100)
        });

        /**
         * Empty editor's content on canceling the comment reply or
         * clicking the reply link again.
         */
        $( document ).on( 'touchstart click', '#cancel-comment-reply-link, .comment-reply-link', function() {
            quillEditor.setText('');
        });

    } );

})(jQuery);
