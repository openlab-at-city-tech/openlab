<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Notices;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Controls\Buttons;
/** @var array $datatables */
?>
<div id="campaigns">
    <div class='row justify-content-between'>
        <div class='col-md-4'>
            <div class='form-group'>
                <input class='form-control' type='text' id='bookly-filter' placeholder="<?php esc_attr_e( 'Quick search', 'bookly' ) ?>"/>
            </div>
        </div>
        <div class='col-md-4 form-row justify-content-end'>
            <?php Dialogs\Mailing\Campaign\Dialog::renderNewCampaignButton() ?>
            <?php Dialogs\TableSettings\Dialog::renderButton( 'sms_mailing_campaigns', 'BooklyL10n', esc_attr( add_query_arg( 'tab', 'campaigns' ) ) ) ?>
        </div>
    </div>

    <table id="bookly-campaigns" class="table table-striped w-100">
        <thead>
        <tr>
            <?php foreach ( $datatables['sms_mailing_campaigns']['settings']['columns'] as $column => $show ) : ?>
                <?php if ( $show ) : ?>
                    <th><?php echo esc_html( $datatables['sms_mailing_campaigns']['titles'][ $column ] ) ?></th>
                <?php endif ?>
            <?php endforeach ?>
            <th width='75'></th>
            <th width='16'><?php Inputs::renderCheckBox( null, null, null, array( 'id' => 'bookly-cam-check-all' ) ) ?></th>
        </tr>
        </thead>
    </table>

    <div class='text-right my-3'>
        <?php Buttons::renderDelete() ?>
    </div>
    <?php Notices\Cron\Notice::render() ?>
    <?php Dialogs\Mailing\Campaign\Dialog::render() ?>
</div>

