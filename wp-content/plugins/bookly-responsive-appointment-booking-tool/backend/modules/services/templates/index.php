<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls;
use Bookly\Backend\Components\Support;
use Bookly\Backend\Modules\Services\Proxy;
use Bookly\Backend\Components\Dialogs;

/**
 * @var array $categories
 * @var array $datatable
 */
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Services', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-row justify-content-end">
                <div class="col-12 col-sm-auto">
                    <?php Controls\Buttons::renderDefault( null, 'w-100 mb-3', __( 'Services order', 'bookly' ), array( 'data-toggle' => 'bookly-modal', 'data-target' => '#bookly-service-order-modal', 'disabled' => 'disabled' ), true ) ?>
                </div>
                <div class="col-12 col-sm-auto">
                    <?php Controls\Buttons::renderDefault( null, 'w-100 mb-3', __( 'Categories', 'bookly' ), array( 'data-toggle' => 'bookly-modal', 'data-target' => '#bookly-service-categories-modal', 'disabled' => 'disabled' ), true ) ?>
                </div>
                <div class="col-auto">
                    <?php Controls\Buttons::renderAdd( null, 'w-100 mb-3', __( 'Add service', 'bookly' ), array( 'data-toggle' => 'bookly-modal', 'data-target' => '#bookly-create-service-modal' ) ) ?>
                </div>
                <?php Dialogs\TableSettings\Dialog::renderButton( 'services' ) ?>
            </div>
            <div class="form-row">
                <div class="col-md-4">
                    <div class="form-group">
                        <input class="form-control" type="text" id="bookly-filter-search" placeholder="<?php esc_attr_e( 'Quick search services', 'bookly' ) ?>"/>
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
            </div>

            <table id="services-list" class="table table-striped w-100">
                <thead>
                <tr>
                    <?php if ( Proxy\Shared::prepareServiceTypes( array() ) ) : ?>
                        <th width="24"></th>
                    <?php endif ?>
                    <th width="24"></th>
                    <?php foreach ( $datatable['settings']['columns'] as $column => $show ) : ?>
                        <?php if ( $show ) : ?>
                            <th><?php echo esc_html( $datatable['titles'][ $column ] ) ?></th>
                        <?php endif ?>
                    <?php endforeach ?>
                    <th width="75"></th>
                    <th width="16"><?php Controls\Inputs::renderCheckBox( null, null, null, array( 'id' => 'bookly-check-all' ) ) ?></th>
                </tr>
                </thead>
            </table>

            <div class="text-right mt-3">
                <?php Controls\Buttons::renderDelete( 'bookly-delete', null, null, array( 'disabled' => 'disabled' ) ) ?>
            </div>
        </div>
    </div>
    <?php Dialogs\Common\CascadeDelete::render() ?>
    <?php Dialogs\Service\Create\Dialog::render() ?>
    <?php Dialogs\Service\Edit\Dialog::render() ?>
    <?php Dialogs\Service\Categories\Dialog::render() ?>
    <?php Dialogs\Service\Order\Dialog::render() ?>
    <?php Dialogs\TableSettings\Dialog::render() ?>
    <div id="bookly-update-service-settings" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php esc_attr_e( 'Update service setting', 'bookly' ) ?></h5>
                    <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p><?php esc_html_e( 'You are about to change a service setting which is also configured separately for each staff member. Do you want to update it in staff settings too?', 'bookly' ) ?></p>
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="bookly-remember-my-choice" type="checkbox"/>
                        <label class="custom-control-label" for="bookly-remember-my-choice"><?php esc_html_e( 'Remember my choice', 'bookly' ) ?></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-default bookly-no" data-dismiss="bookly-modal" aria-hidden="true">
                        <?php esc_html_e( 'No, update just here in services', 'bookly' ) ?>
                    </button>
                    <button type="submit" class="btn btn-success bookly-yes"><?php esc_html_e( 'Yes', 'bookly' ) ?></button>
                </div>
            </div>
        </div>
    </div>
</div>