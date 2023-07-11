<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Controls\Buttons;
/** @var array $datatable */
?>
<div class='row mb-2'>
    <div class='col'>
        <strong><?php esc_html_e( 'Current mailing list', 'bookly' ) ?>:</strong> <span id="bookly-js-mailing-list-name"></span>
    </div>
</div>
<div class="row justify-content-between">
    <div class="col-md-4">
        <div class="form-group">
            <input class="form-control" type="text" id="bookly-filter" placeholder="<?php esc_attr_e( 'Quick search', 'bookly' ) ?>"/>
        </div>
    </div>
    <div class="col-md-8 form-row justify-content-end">
        <?php Dialogs\Mailing\AddRecipients\Dialog::renderAddRecipientsButton() ?>
        <div class='col-auto'>
            <?php Buttons::render( 'bookly-js-show-mailing-list', 'btn-default', __( 'Back to lists', 'bookly' ), array(), '<span class="d-none d-lg-inline"> {caption}</span>', '<i class="fas fa-fw fa-list"></i>' ) ?>
        </div>
        <?php Dialogs\TableSettings\Dialog::renderButton( 'sms_mailing_recipients_list', 'BooklyL10n', esc_attr( add_query_arg( 'tab', 'mailing_lists' ) ) ) ?>
    </div>
</div>

<table id="bookly-recipients-list" class="table table-striped w-100">
    <thead>
    <tr>
        <?php foreach ( $datatable['settings']['columns'] as $column => $show ) : ?>
            <?php if ( $show ) : ?>
                <th><?php echo esc_html( $datatable['titles'][ $column ] ) ?></th>
            <?php endif ?>
        <?php endforeach ?>
        <th width='16'><?php Inputs::renderCheckBox( null, null, null, array( 'id' => 'bookly-mr-check-all' ) ) ?></th>
    </tr>
    </thead>
</table>

<div class='text-right my-3'>
    <?php Buttons::renderDelete() ?>
</div>
<?php Dialogs\Mailing\AddRecipients\Dialog::render() ?>