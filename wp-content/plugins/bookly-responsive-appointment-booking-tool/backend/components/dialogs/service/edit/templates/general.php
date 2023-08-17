<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;
use Bookly\Lib\Entities\Service;
/**
 * @var array $service
 * @var array $categories_collection
 * @var array $simple_services
 * @var array $staff_dropdown_data
 * @var array $staff_ids
 */
$service_id = $service['id'];
?>
<div class="bookly-js-service-general-container">
    <input type="hidden" name="attachment_id" value="<?php echo esc_attr( $service['attachment_id'] ) ?>">
    <div class="row">
        <div class="col-md-auto">
            <div id="bookly-js-service-image">
                <div class="form-group">
                    <?php $img = wp_get_attachment_image_src( $service['attachment_id'], 'thumbnail' ) ?>

                    <div class="bookly-js-image bookly-thumb<?php echo esc_attr( $img ? ' bookly-thumb-with-image' : '' ) ?>"
                         style="<?php echo esc_attr( $img ? 'background-image: url(' . $img[0] . '); background-size: cover;' : '' ) ?>"
                    >
                        <i class="fas fa-fw fa-4x fa-camera mt-2 text-white w-100"></i>
                        <?php if ( current_user_can( 'upload_files' ) ) : ?>
                            <a class="far fa-fw fa-trash-alt text-danger bookly-thumb-delete bookly-js-delete"
                               href="javascript:void(0)"
                               title="<?php esc_attr_e( 'Delete', 'bookly' ) ?>"
                               <?php if ( ! $img ) : ?>style="display: none;"<?php endif ?>>
                            </a>
                            <div class="bookly-thumb-edit">
                                <label class="bookly-thumb-edit-btn">
                                    <?php esc_html_e( 'Image', 'bookly' ) ?>
                                </label>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="bookly-service-title"><?php esc_html_e( 'Title', 'bookly' ) ?></label>
                <input name="title" value="<?php echo esc_attr( $service['title'] ) ?>" id="bookly-service-title" class="form-control" type="text"/>
            </div>
        </div>
    </div>
    <?php if ( $service['type'] != Service::TYPE_PACKAGE ) : ?>
        <div class="form-group">
            <label for="bookly-service-category"><?php esc_html_e( 'Category', 'bookly' ) ?></label>
            <select id="bookly-service-category" class="form-control custom-select" name="category_id">
                <option value="0"><?php esc_html_e( 'Uncategorized', 'bookly' ) ?></option>
                <?php foreach ( $categories_collection as $category ) : ?>
                    <option value="<?php echo esc_attr( $category['id'] ) ?>" <?php selected( $category['id'], $service['category_id'] ) ?>><?php echo esc_html( $category['name'] ) ?></option>
                <?php endforeach ?>
            </select>
        </div>
    <?php endif ?>
    <?php if ( $service['type'] == Service::TYPE_SIMPLE ) : ?>
        <div class="form-group">
            <label><?php esc_html_e( 'Color', 'bookly' ) ?></label>
            <div class="bookly-color-picker">
                <input name="color" value="<?php echo esc_attr( $service['color'] ) ?>" class="bookly-js-color-picker" data-last-color="<?php echo esc_attr( $service['color'] ) ?>" type="text" />
            </div>
        </div>
    <?php endif ?>
    <?php Proxy\Pro::renderVisibility( $service ) ?>
    <?php Proxy\CustomerGroups::renderSubForm( $service ) ?>
    <?php if ( $service['type'] == Service::TYPE_COLLABORATIVE ) : ?>
        <?php Proxy\CollaborativeServices::renderSubForm( $service, $simple_services ) ?>
    <?php endif ?>
    <?php if ( $service['type'] == Service::TYPE_COMPOUND ) : ?>
        <?php Proxy\CompoundServices::renderSubForm( $service, $simple_services ) ?>
    <?php endif ?>
    <div class="form-group">
        <label for="bookly_service_price" class="bookly-js-price-label"><?php esc_html_e( 'Price', 'bookly' ) ?></label>
        <?php Proxy\CustomDuration::renderServicePriceLabel( $service_id ) ?>
        <input id="bookly_service_price" class="form-control bookly-js-question" type="number" min="0" step="1" name="price" value="<?php echo esc_attr( $service['price'] ) ?>"/>
    </div>
    <?php if ( $service['type'] == Service::TYPE_COMPOUND || $service['type'] == Service::TYPE_COLLABORATIVE ) : ?>
        <?php Proxy\DepositPayments::renderDeposit( $service ) ?>
    <?php endif ?>
    <?php if ( $service['type'] == Service::TYPE_PACKAGE ) : ?>
        <?php Proxy\Packages::renderSubForm( $service, $simple_services ) ?>
    <?php endif ?>
    <?php if ( $service['type'] == Service::TYPE_SIMPLE || $service['type'] == Service::TYPE_PACKAGE ) : ?>
        <div id="bookly-js-service-providers">
            <div class="form-group">
                <label><?php esc_html_e( 'Providers', 'bookly' ) ?></label><br/>
                <ul class="bookly-js-providers"
                    data-txt-select-all="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
                    data-txt-all-selected="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
                    data-txt-nothing-selected="<?php esc_attr_e( 'No staff selected', 'bookly' ) ?>"
                >
                    <?php foreach ( $staff_dropdown_data as $category_id => $category ) : ?>
                        <li<?php if ( ! $category_id ) : ?> data-flatten-if-single<?php endif ?>><?php echo esc_html( $category['name'] ) ?>
                            <ul>
                                <?php foreach ( $category['items'] as $staff ) : ?>
                                    <li
                                            data-input-name="staff_ids[]"
                                            data-value="<?php echo esc_attr( $staff['id'] ) ?>"
                                            data-selected="<?php echo (int) in_array( $staff['id'], $staff_ids ) ?>"
                                    >
                                        <?php echo esc_html( $staff['full_name'] ) ?>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
            <?php if ( $service['type'] == Service::TYPE_SIMPLE ) : ?>
                <?php Proxy\Pro::renderStaffPreference( $service ) ?>
            <?php endif ?>
        </div>
    <?php endif ?>
    <div class="form-group">
        <?php Proxy\Pro::renderGatewayPreference( $service ) ?>
    </div>
    <div class="form-group">
        <label for="bookly-service-info"><?php esc_html_e( 'Info', 'bookly' ) ?></label>
        <textarea class="form-control" id="bookly-service-info" name="info" rows="3" type="text"><?php echo esc_textarea( $service['info'] ) ?></textarea>
        <small class="form-text text-muted"><?php printf( __( 'This text can be inserted into notifications with %s code.', 'bookly' ), '{service_info}' ) ?></small>
    </div>
</div>
