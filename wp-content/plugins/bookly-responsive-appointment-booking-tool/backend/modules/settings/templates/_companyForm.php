<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs as ControlsInputs;
use Bookly\Backend\Components\Settings\Inputs;
use Bookly\Backend\Components\Settings\Selects;
use Bookly\Lib\Utils\Common;
$sizes = array(
    array( '1', __( '1 - 9 employees', 'bookly' ) ),
    array( '10', __( '10 - 19 employees', 'bookly' ) ),
    array( '20', __( '20 - 49 employees', 'bookly' ) ),
    array( '50', __( '50 - 249 employees', 'bookly' ) ),
    array( '250', __( '250 or more employees', 'bookly' ) ),
);
if (! get_option( 'bookly_co_size' ) ) {
    array_unshift( $sizes, array( '', __( 'Select company size', 'bookly' ) ) );
}
?>
<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'company' ) ) ?>">
    <div class="card-body">
        <div class="row form-group">
            <div class="col-auto">
                <div id="bookly-js-company-logo">
                    <input type="hidden" name="bookly_co_logo_attachment_id"
                           data-default="<?php form_option( 'bookly_co_logo_attachment_id' ) ?>"
                           value="<?php form_option( 'bookly_co_logo_attachment_id' ) ?>"
                    />
                    <?php $img = wp_get_attachment_image_src( get_option( 'bookly_co_logo_attachment_id' ), 'thumbnail' ) ?>
                    <div class="bookly-thumb bookly-js-image<?php echo esc_attr( $img ? ' bookly-thumb-with-image' : '' ) ?>"
                         data-style="<?php echo esc_attr( $img ? 'background-image: url(' . $img[0] . '); background-size: cover;' : '' ) ?>"
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
                                <label class="bookly-thumb-edit-btn bookly-js-edit">
                                    <?php esc_html_e( 'Image', 'bookly' ) ?>
                                </label>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="col">
                <?php Inputs::renderText( 'bookly_co_name', __( 'Company name', 'bookly' ) ) ?>
            </div>
        </div>

        <?php Inputs::renderTextArea( 'bookly_co_address', __( 'Address', 'bookly' ), '', 5 ) ?>
        <?php Inputs::renderText( 'bookly_co_phone', __( 'Phone', 'bookly' ) ) ?>
        <?php Inputs::renderText( 'bookly_co_website', __( 'Website', 'bookly' ) ) ?>
        <?php Selects::renderSingleWithCategories( 'bookly_co_industry', __( 'Industry', 'bookly' ), null, get_option( 'bookly_co_industry' ) ? Common::getIndustries() : array_merge( array( __( 'Select industry', 'bookly' ) ), Common::getIndustries() ) ) ?>
        <?php Selects::renderSingle( 'bookly_co_size', __( 'Company size', 'bookly' ), null, $sizes ) ?>
        <?php Inputs::renderText( 'bookly_co_email', __( 'Email', 'bookly' ) ) ?>
    </div>

    <div class="card-footer bg-transparent d-flex justify-content-end">
        <?php ControlsInputs::renderCsrf() ?>
        <?php Buttons::renderSubmit() ?>
        <?php Buttons::renderReset( 'bookly-company-reset', 'ml-2' ) ?>
    </div>
</form>