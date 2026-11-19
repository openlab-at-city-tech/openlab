<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

$labels = array(
    'deactivation-share-title'             => __( 'Plugin Usage Feedback', 'cminds' ),
    'deactivation-share-reason'            => __( 'Please tell us how can we make this plugin better for you?', 'cminds' ),
    'deactivation-share-agree'             => __( 'Please share my WordPress system information to help analyze my feedback*', 'cminds' ),
    'deactivation-want-contact'            => __( 'I will be happy to receive support to my email', 'cminds' ),
    'deactivation-want-contact-pt-2'       => __( 'regarding the issues I am reporting. (Please include a detailed description)', 'cminds' ),
    'deactivation-default-contact'         => get_bloginfo( 'admin_email' ),
    'deactivation-share-disclaimer'        => __( '(*) Your feedback will only be used to improve this plugin', 'cminds' ),
    'deactivation-modal-button-deactivate' => __( 'Deactivate', 'cminds' ),
    'deactivation-modal-button-confirm'    => __( 'Yes - Deactivate', 'cminds' ),
    'deactivation-modal-button-submit'     => __( 'Submit & Deactivate', 'cminds' ),
    'deactivation-modal-button-cancel'     => _x( 'Cancel', 'the text of the cancel button of the plugin deactivation dialog box.', 'cminds' ),
);

$confirmation_message = apply_filters( 'cm_uninstall_confirmation_message', '' );
?>
<script type="text/javascript">
    ( function ( $ ) {
        if ( $( '.cm-modal' ).length ) {
            $( '.cm-modal' ).remove();
        }
        var modalHtml =
            '<div class="cm-modal<?php echo empty( $confirmation_message ) ? ' no-confirmation-message' : ''; ?>">'
            + '	<div class="cm-modal-dialog">'
            + '		<div class="cm-modal-body">'
            + '			<h2><?php printf( $labels[ 'deactivation-share-title' ] ); ?></h2>'
            + '			<div class="cm-modal-panel" data-panel-id="confirm"><p><?php echo $confirmation_message; ?></p></div>'
            + '			<div class="cm-modal-panel active" data-panel-id="reasons">'
            + '			<h3><strong><?php printf( $labels[ 'deactivation-share-reason' ] ); ?></strong></h3><textarea name="cm-deactivation-reason" rows="5" style="width:94%"></textarea>'
            + '			<input name="plugin-slug" type="hidden" />'
            + '			<p><input name="cminds-data-agree" type="checkbox" checked="checked" /> <label><?php printf( $labels[ 'deactivation-share-agree' ] ); ?></label></p>'
            + '			<p><input name="cminds-want-contact" type="checkbox" /> <label><?php printf( $labels[ 'deactivation-want-contact' ] ); ?> <input disabled="disabled" value="<?php printf( $labels[ 'deactivation-default-contact' ] ); ?>" name="cminds-contact-email" type="e-mail" /> <?php printf( $labels[ 'deactivation-want-contact-pt-2' ] ); ?></label></p>'
            + '			<p><?php printf( $labels[ 'deactivation-share-disclaimer' ] ); ?></p>'
            + '			</div>'
            + '		</div>'
            + '		<div class="cm-modal-footer">'
            + '			<a href="#" class="button button-primary button-close"><?php printf( $labels[ 'deactivation-modal-button-cancel' ] ); ?></a>'
            + '			<a href="#" class="button button-secondary button-deactivate"></a>'
            + '		</div>'
            + '	</div>'
            + '</div>',
            $modal = $( modalHtml ),
            $deactivateLink = $( '#the-list .deactivate > i.cm-slug' ).prev();

        $modal.appendTo( $( 'body' ) );

        registerEventHandlers();

        function registerEventHandlers() {
            var $currentLink;

            $deactivateLink.click( function ( evt ) {

                if ( evt.which === 1 ) {
                    evt.preventDefault();
                }
                var slug = jQuery( this ).next().data( 'slug' );
                $slug = $modal.find( 'input[type="hidden"][name="plugin-slug"]' );
                $slug.val( slug );
                $currentLink = $( this );
                cmShowModal();
            } );

            var $want_contact = $modal.find( 'input[type="checkbox"][name="cminds-want-contact"]' ),
                $contact_email = $modal.find( 'input[type="e-mail"][name="cminds-contact-email"]' );

            $want_contact.on( 'click', function () {
                if ( $( this ).is( ':checked' ) ) {
                    $contact_email.attr( 'disabled', false );
                } else {
                    $contact_email.attr( 'disabled', true );
                }
            } );

            $modal.on( 'click', '.button', function ( evt ) {
                evt.preventDefault();

                if ( $( this ).hasClass( 'disabled' ) ) {
                    return;
                }

                var _parent = $( this ).parents( '.cm-modal:first' );
                var _this = $( this );

                if ( _this.hasClass( 'allow-deactivate' ) ) {
                    var $input = $modal.find( 'textarea, input[type="text"]' ),
                        $data_agree = $modal.find( 'input[type="checkbox"][name="cminds-data-agree"]' ),
                        $want_contact = $modal.find( 'input[type="checkbox"][name="cminds-want-contact"]' ),
                        $contact_email = $modal.find( 'input[type="e-mail"][name="cminds-contact-email"]' ),
                        $slug = $modal.find( 'input[type="hidden"][name="plugin-slug"]' );

                    if ( 0 === $input.val().length ) {
                        // If no selected reason, just deactivate the plugin.
                        window.location.href = $currentLink.attr( 'href' );
                        return;
                    }

                    $.ajax( {
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            'action': 'cm-submit-uninstall-reason',
                            'plugin_slug': $slug.val(),
                            'deactivation_reason': ( 0 !== $input.length ) ? $input.val().trim() : '',
                            'data_agree': $data_agree.is( ':checked' ),
                            'want_contact': $want_contact.is( ':checked' ),
                            'contact_email': ( 0 !== $contact_email.length ) ? $contact_email.val().trim() : ''
                        },
                        beforeSend: function () {
                            _parent.find( '.button' ).addClass( 'disabled' );
                            _parent.find( '.button-secondary' ).text( 'Processing...' );
                        },
                        complete: function () {
                            // Do not show the dialog box, deactivate the plugin.
                            window.location.href = $currentLink.attr( 'href' );
                        }
                    } );
                } else if ( _this.hasClass( 'button-deactivate' ) ) {
                    // Change the Deactivate button's text and show the reasons panel.
                    _parent.find( '.button-deactivate' ).addClass( 'allow-deactivate' );
                    cmShowPanel( 'reasons' );
                }
            } );

            // If the user has clicked outside the window, cancel it.
            $modal.on( 'click', function ( evt ) {
                var $target = $( evt.target );

                // If the user has clicked anywhere in the modal dialog, just return.
                if ( $target.hasClass( 'cm-modal-body' ) || $target.hasClass( 'cm-modal-footer' ) ) {
                    return;
                }

                // If the user has not clicked the close button and the clicked element is inside the modal dialog, just return.
                if ( !$target.hasClass( 'button-close' ) && ( $target.parents( '.cm-modal-body' ).length > 0 || $target.parents( '.cm-modal-footer' ).length > 0 ) ) {
                    return;
                }

                cmCloseModal();
            } );
        }

        function cmShowModal() {
            cmResetModal();

            // Display the dialog box.
            $modal.addClass( 'active' );

            $( 'body' ).addClass( 'has-cm-modal' );
        }

        function cmCloseModal() {
            $modal.removeClass( 'active' );

            $( 'body' ).removeClass( 'has-cm-modal' );
        }

        function cmResetModal() {
            $modal.find( '.button' ).removeClass( 'disabled' );

            // Uncheck all radio buttons.
            $modal.find( 'input[type="radio"]' ).prop( 'checked', false );

            // Remove all input fields ( textfield, textarea ).
            $modal.find( '.reason-input' ).remove();

            var $deactivateButton = $modal.find( '.button-deactivate' );

            /*
             * If the modal dialog has no confirmation message, that is, it has only one panel, then ensure
             * that clicking the deactivate button will actually deactivate the plugin.
             */
            if ( $modal.hasClass( 'no-confirmation-message' ) ) {
                $deactivateButton.addClass( 'allow-deactivate' );

                cmShowPanel( 'reasons' );
            } else {
                $deactivateButton.removeClass( 'allow-deactivate' );

                cmShowPanel( 'confirm' );
            }
        }

        function cmShowPanel( panelType ) {
            $modal.find( '.cm-modal-panel' ).removeClass( 'active ' );
            $modal.find( '[data-panel-id="' + panelType + '"]' ).addClass( 'active' );

            cmUpdateButtonLabels();
        }

        function cmUpdateButtonLabels() {
            var $deactivateButton = $modal.find( '.button-deactivate' );

            // Reset the deactivate button's text.
            if ( 'confirm' === cmGetCurrentPanel() ) {
                $deactivateButton.text( '<?php printf( $labels[ 'deactivation-modal-button-confirm' ] ); ?>' );
            } else {
                $deactivateButton.text( '<?php printf( $labels[ 'deactivation-modal-button-deactivate' ] ); ?>' );
            }
        }

        function cmGetCurrentPanel() {
            return $modal.find( '.cm-modal-panel.active' ).attr( 'data-panel-id' );
        }
    } )( jQuery );
</script>
<style>
    .cm-modal{position:fixed;overflow:auto;height:100%;width:100%;top:0;z-index:100000;display:none;background:rgba(0,0,0,0.6)}.cm-modal .cm-modal-dialog{background:transparent;position:absolute;left:50%;margin-left:-298px;padding-bottom:30px;top:-100%;z-index:100001;width:596px}@media (max-width: 650px){.cm-modal .cm-modal-dialog{margin-left:-50%;box-sizing:border-box;padding-left:10px;padding-right:10px;width:100%}.cm-modal .cm-modal-dialog .cm-modal-panel>h3>strong{font-size:1.3em}.cm-modal .cm-modal-dialog li.reason{margin-bottom:10px}.cm-modal .cm-modal-dialog li.reason .reason-input{margin-left:29px}.cm-modal .cm-modal-dialog li.reason label{display:table}.cm-modal .cm-modal-dialog li.reason label>span{display:table-cell;font-size:1.3em}}.cm-modal.active{display:block}.cm-modal.active:before{display:block}.cm-modal.active .cm-modal-dialog{top:10%}.cm-modal .cm-modal-body,.cm-modal .cm-modal-footer{border:2px solid #9fb6f2;background:#e2e9fb;padding:20px;}.cm-modal .cm-modal-body{border-bottom:0}.cm-modal .cm-modal-body h2{font-size:20px}.cm-modal .cm-modal-body>div{margin-top:10px}.cm-modal .cm-modal-body>div h2{font-weight:bold;font-size:20px;margin-top:0}.cm-modal .cm-modal-footer{border-top:#eeeeee solid 1px;text-align:right}.cm-modal .cm-modal-footer>.button{margin:0 7px}.cm-modal .cm-modal-footer>.button:first-child{margin:0}.cm-modal .cm-modal-panel:not(.active){display:none}.cm-modal .reason-input{margin:3px 0 3px 22px}.cm-modal .reason-input input,.cm-modal .reason-input textarea{width:100%}body.has-cm-modal{overflow:hidden}#the-list .deactivate>.cm-slug{display:none}
</style>