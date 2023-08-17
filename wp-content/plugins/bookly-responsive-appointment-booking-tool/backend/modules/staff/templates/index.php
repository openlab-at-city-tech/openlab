<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls;
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Dialogs;
use Bookly\Lib;

/** @var array $datatables */
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <?php if ( Lib\Utils\Common::isCurrentUserAdmin() ) : ?>
            <h4 class="col m-0 text-nowrap">
                <?php esc_html_e( 'Staff Members', 'bookly' ) ?>
                <small class="text-muted">(
                    <small class="bookly-js-staff-count">
                        <div class="bookly-loading bookly-loading-sm"></div>
                    </small>
                    )
                </small>
            </h4>
        <?php else : ?>
            <h4 class="col m-0">
                <?php esc_html_e( 'Profile', 'bookly' ) ?>
                <small class="bookly-js-staff-count" style="color: transparent"></small>
            </h4>
        <?php endif ?>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card">
        <div class="card-body">

            <?php if ( Lib\Utils\Common::isCurrentUserAdmin() ): ?>
                <div class="form-row justify-content-end">
                    <div class="col-12 col-sm-auto">
                        <?php Controls\Buttons::renderDefault( null, 'w-100 mb-3', __( 'Staff members order', 'bookly' ), array( 'data-toggle' => 'bookly-modal', 'data-target' => '#bookly-staff-order-modal', 'disabled' => 'disabled' ), true ) ?>
                    </div>
                    <?php Dialogs\Staff\Categories\Proxy\Pro::renderAdd() ?>
                    <div class="col-auto">
                        <?php Controls\Buttons::renderAdd( 'bookly-js-new-staff', 'w-100 mb-3', __( 'Add staff', 'bookly' ) ) ?>
                    </div>
                    <?php Dialogs\TableSettings\Dialog::renderButton( 'staff_members' ) ?>
                </div>
                <div class="form-row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input class="form-control" type="text" id="bookly-filter-search" placeholder="<?php esc_attr_e( 'Quick search staff', 'bookly' ) ?>"/>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <div class="form-group">
                            <select class="form-control bookly-js-select" id="bookly-filter-category" data-placeholder="<?php esc_attr_e( 'Categories', 'bookly' ) ?>">
                                <?php foreach ( $categories as $category ) : ?>
                                    <option value="<?php echo esc_attr( $category['id'] ) ?>"><?php echo esc_html( $category['name'] ) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select class="form-control bookly-js-select" id="bookly-filter-visibility" data-placeholder="<?php esc_attr_e( 'Visibility', 'bookly' ) ?>">
                                <option value="public"><?php esc_html_e( 'Public', 'bookly' ) ?></option>
                                <option value="private"><?php esc_html_e( 'Private', 'bookly' ) ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-4 my-auto">
                        <?php if ( Lib\Config::proActive() ): ?>
                            <div class="form-group">
                                <?php Controls\Inputs::renderCheckBox( __( 'Show archived', 'bookly' ), null, null, array( 'id' => 'bookly-filter-archived' ) ) ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            <?php endif ?>

            <table id="bookly-staff-list" class="table table-striped w-100">
                <thead>
                <tr>
                    <th width='24'></th>
                    <?php foreach ( $datatables['staff_members']['settings']['columns'] as $column => $show ) : ?>
                        <?php if ( $show ) : ?>
                            <th><?php echo esc_html( $datatables['staff_members']['titles'][ $column ] ) ?></th>
                        <?php endif ?>
                    <?php endforeach ?>
                    <th width="85"></th>
                    <th width="16"><?php Controls\Inputs::renderCheckBox( null, null, null, array( 'id' => 'bookly-check-all' ) ) ?></th>
                </tr>
                </thead>
            </table>

            <div class="text-right mt-3">
                <?php if ( Lib\Utils\Common::isCurrentUserAdmin() ): ?>
                    <?php Controls\Buttons::renderDelete( 'bookly-delete', null, null, array( 'disabled' => 'disabled' ) ) ?>
                <?php endif ?>
            </div>
        </div>
    </div>

    <?php Dialogs\Common\CascadeDelete::render() ?>
    <?php Dialogs\Common\UnsavedChanges::render() ?>
    <?php Dialogs\Staff\Categories\Proxy\Pro::renderDialog() ?>
    <?php Dialogs\Staff\Edit\Dialog::render() ?>
    <?php Dialogs\Staff\Order\Dialog::render() ?>
    <?php Dialogs\Staff\Edit\Proxy\Packages::renderStaffServicesTip() ?>
    <?php Dialogs\TableSettings\Dialog::render() ?>
</div>