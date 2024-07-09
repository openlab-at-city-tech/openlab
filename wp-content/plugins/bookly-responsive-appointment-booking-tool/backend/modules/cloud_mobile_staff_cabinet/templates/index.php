<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Cloud;
use Bookly\Backend\Components\Controls;
use Bookly\Backend\Components\Dialogs\MobileStaffCabinet\AccessEdit;
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Staff Cabinet Mobile App', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row pb-3">
                <div class="col">
                </div>
                <div class="col-auto">
                    <?php Cloud\Account\Panel::render() ?>
                </div>
            </div>
            <div class='row justify-content-between'>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <h4 class="mt-2"><?php esc_html_e( 'Access tokens', 'bookly' ) ?></h4>
                    </div>
                </div>
                <div class='col-md-4 form-row justify-content-end'>
                    <?php AccessEdit\Dialog::renderNewToken() ?>
                    <?php Dialogs\TableSettings\Dialog::renderButton( 'cloud_mobile_staff_cabinet' ) ?>
                </div>
            </div>
            <table id="bookly-keys-list" class="table table-striped w-100">
                <thead>
                <tr>
                    <?php foreach ( $datatables['cloud_mobile_staff_cabinet']['settings']['columns'] as $column => $show ) : ?>
                        <?php if ( $show ) : ?>
                            <th><?php echo esc_html( $datatables['cloud_mobile_staff_cabinet']['titles'][ $column ] ) ?></th>
                        <?php endif ?>
                    <?php endforeach ?>
                    <th width="85"></th>
                    <th width="16"><?php Controls\Inputs::renderCheckBox( null, null, null, array( 'id' => 'bookly-check-all' ) ) ?></th>
                </tr>
                </thead>
            </table>

            <div class='text-right mt-3'>
                <?php Controls\Buttons::renderDelete( 'bookly-js-revoke', null, __( 'Revoke', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
    <?php AccessEdit\Dialog::render() ?>
    <?php Dialogs\TableSettings\Dialog::render() ?>
</div>