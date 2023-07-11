<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Ace;
/**
 * @var string $doc_slug
 */
?>
<div class="bookly-modal bookly-fade" id="bookly-editable-modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <?php Ace\Editor::render( $doc_slug ) ?>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderSubmit( 'bookly-ace-save', null, __( 'Apply', 'bookly' ) ) ?>
                <?php Buttons::renderCancel( __( 'Close', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</div>