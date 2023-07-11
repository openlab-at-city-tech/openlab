<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use Bookly\Lib\Utils\Common;

/** @var Dialogs\Staff\Edit\Forms\StaffServices $form */
?>
<div>
    <?php if ( $form->getCategories() || $form->getUncategorizedServices() ) : ?>
        <form>
            <?php Proxy\Locations::renderLocationSwitcher( $staff_id, $location_id, 'custom_services' ) ?>
            <div id="bookly-staff-services">
                <?php if ( $form->getUncategorizedServices() ) : ?>
                    <div class="card bg-light p-3">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="bookly-check-all-entities" type="checkbox"/>
                                    <label class="custom-control-label" for="bookly-check-all-entities"><?php esc_html_e( 'All services', 'bookly' ) ?></label>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-row text-muted d-none d-lg-flex">
                                    <div class="col-lg-3 text-center">
                                        <?php esc_html_e( 'Price', 'bookly' ) ?>
                                    </div>
                                    <?php Proxy\Shared::renderStaffServiceLabels() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="bookly-js-category-services list-group pt-2 list-unstyled">
                        <?php foreach ( $form->getUncategorizedServices() as $service ) : ?>
                            <?php $sub_service = current( $service->getSubServices() ) ?>
                            <li class="p-2 mx-2" data-service-id="<?php echo esc_attr( $service->getId() ) ?>" data-service-type="<?php echo esc_attr( $service->getType() ) ?>"
                                data-sub-service="<?php echo esc_attr( empty( $sub_service ) ? null : $sub_service->getId() ) ?>">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="custom-control custom-checkbox mt-2">
                                            <input
                                                    class="custom-control-input bookly-js-service-checkbox"
                                                    id="bookly-check-service-<?php echo esc_attr( $service->getId() ) ?>"
                                                    type="checkbox"
                                                <?php checked( array_key_exists( $service->getId(), $services_data ) ) ?>
                                                    value="<?php echo esc_attr( $service->getId() ) ?>"
                                                    name="service[<?php echo esc_attr( $service->getId() ) ?>]"
                                            />
                                            <label class="custom-control-label w-100 bookly-toggle-label" for="bookly-check-service-<?php echo esc_attr( $service->getId() ) ?>">
                                                <?php echo esc_html( $service->getTitle() ) ?>
                                                <?php Proxy\Ratings::renderStaffServiceRating( $staff_id, $service->getId(), 'right' ) ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="form-row">
                                            <div class="col-3">
                                                <div class="d-lg-none"><?php esc_html_e( 'Price', 'bookly' ) ?></div>
                                                <input class="form-control text-right" type="text" <?php disabled( ! array_key_exists( $service->getId(), $services_data ) ) ?>
                                                       name="price[<?php echo esc_attr( $service->getId() ) ?>]"
                                                       value="<?php echo esc_attr( array_key_exists( $service->getId(), $services_data ) ? $services_data[ $service->getId() ]['price'] : $service->getPrice() ) ?>"
                                                />
                                            </div>

                                            <?php Proxy\Shared::renderStaffService( $staff_id, $service, $services_data, array() ) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php Proxy\Shared::renderStaffServiceTail( $staff_id, $service, $location_id ) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>

                <?php foreach ( $form->getCategories() as $category ) : ?>
                    <div class="card bg-light p-3">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input bookly-js-category-checkbox" id="bookly-category-<?php echo esc_attr( $category->getId() ) ?>" type="checkbox" data-category-id="<?php echo esc_attr( $category->getId() ) ?>"/>
                                    <label class="custom-control-label" for="bookly-category-<?php echo esc_attr( $category->getId() ) ?>"><?php echo esc_html( $category->getName() ) ?></label>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-row text-muted d-none d-lg-flex">
                                    <div class="col-lg-3 text-center">
                                        <?php esc_html_e( 'Price', 'bookly' ) ?>
                                    </div>
                                    <?php Proxy\Shared::renderStaffServiceLabels() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="bookly-js-category-services list-group pt-2 list-unstyled">
                        <?php foreach ( $category->getServices() as $service ) : ?>
                            <?php $sub_service = current( $service->getSubServices() ) ?>
                            <li class="p-2 mx-2" data-service-id="<?php echo esc_attr( $service->getId() ) ?>" data-service-type="<?php echo esc_attr( $service->getType() ) ?>"
                                data-sub-service="<?php echo esc_attr( empty( $sub_service ) ? null : $sub_service->getId() ) ?>">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="custom-control custom-checkbox mt-2">
                                            <input
                                                    class="custom-control-input bookly-js-service-checkbox"
                                                    data-category-id="<?php echo esc_attr( $category->getId() ) ?>"
                                                    id="bookly-check-service-<?php echo esc_attr( $service->getId() ) ?>"
                                                    type="checkbox"
                                                <?php checked( array_key_exists( $service->getId(), $services_data ) ) ?>
                                                    value="<?php echo esc_attr( $service->getId() ) ?>"
                                                    name="service[<?php echo esc_attr( $service->getId() ) ?>]"
                                            />
                                            <label class="custom-control-label w-100 bookly-toggle-label" for="bookly-check-service-<?php echo esc_attr( $service->getId() ) ?>">
                                                <?php echo esc_html( $service->getTitle() ) ?>
                                                <?php Proxy\Ratings::renderStaffServiceRating( $staff_id, $service->getId(), 'right' ) ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="form-row">
                                            <div class="col-3">
                                                <div class="d-lg-none"><?php esc_html_e( 'Price', 'bookly' ) ?></div>
                                                <input class="form-control text-right" type="text" <?php disabled( ! array_key_exists( $service->getId(), $services_data ) ) ?>
                                                       name="price[<?php echo esc_attr( $service->getId() ) ?>]"
                                                       value="<?php echo esc_attr( array_key_exists( $service->getId(), $services_data ) ? $services_data[ $service->getId() ]['price'] : $service->getPrice() ) ?>"
                                                />
                                            </div>

                                            <?php Proxy\Shared::renderStaffService( $staff_id, $service, $services_data, array() ) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php Proxy\Shared::renderStaffServiceTail( $staff_id, $service, $location_id ) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endforeach ?>

                <input type="hidden" name="staff_id" value="<?php echo esc_attr( $staff_id ) ?>">

                <div class="bookly-js-modal-footer">
                    <span class="bookly-js-services-error text-danger"></span>
                    <?php Buttons::renderSubmit( 'bookly-services-save' ) ?>
                    <?php Buttons::renderReset( 'bookly-services-reset' ) ?>
                </div>
            </div>
        </form>
    <?php else : ?>
        <h5 class="text-center"><?php esc_html_e( 'No services found. Please add services.', 'bookly' ) ?></h5>
        <p class="text-center">
            <a class="btn btn-xlg btn-success-outline"
               href="<?php echo Common::escAdminUrl( Bookly\Backend\Modules\Services\Page::pageSlug() ) ?>">
                <?php esc_html_e( 'Add Service', 'bookly' ) ?>
            </a>
        </p>
    <?php endif ?>
    <div style="display: none">
        <?php Dialogs\SpecialPrice\Proxy\SpecialHours::renderSpecialPricePopup( $staff_id ) ?>
    </div>
</div>