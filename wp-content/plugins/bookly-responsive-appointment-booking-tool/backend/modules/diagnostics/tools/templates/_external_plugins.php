<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/** @var array $plugins */
?>
<ul class="list-group" id="bookly-js-booking-forms">
    <?php foreach ( $plugins as $slug => $data ) : ?>
        <?php $active = is_plugin_active( $data['basename'] ) ? 'delete' : ( file_exists( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $data['basename'] ) ? 'activate' : 'install' ) ?>
        <li class="list-group-item list-group-item-action" data-plugin="<?php echo esc_attr( $slug ) ?>">
            <div class="row align-items-center">
                <div class="col">
                    <?php echo esc_html( $data['name'] ) ?>
                </div>
                <div class="col-auto" style="min-width:140px;">
                    <button class="btn btn-danger ladda-button w-100" data-plugin="<?php echo esc_attr( $slug ) ?>" data-action="delete" data-spinner-size="40" data-style="zoom-in" data-spinner-color="#666666"<?php if ( $active !== 'delete' ) : ?> style="display: none;"<?php endif ?>><span class="ladda-label">Delete</span></button>
                    <button class="btn btn-success ladda-button w-100" data-plugin="<?php echo esc_attr( $slug ) ?>" data-action="activate" data-spinner-size="40" data-style="zoom-in" data-spinner-color="#666666"<?php if ( $active !== 'activate' ) : ?> style="display: none;"<?php endif ?>><span class="ladda-label">Activate</span></button>
                    <button class="btn btn-default ladda-button w-100" data-plugin="<?php echo esc_attr( $slug ) ?>" data-action="install" data-spinner-size="40" data-style="zoom-in" data-spinner-color="#666666"<?php if ( $active !== 'install' ) : ?> style="display: none;"<?php endif ?>><span class="ladda-label">Install</span></button>
                </div>
            </div>
        </li>
    <?php endforeach ?>
</ul>
