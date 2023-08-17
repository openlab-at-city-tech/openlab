<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/** @var array $shortcodes */
?>
<div class="input-group">
    <select id="bookly_shortcode" class="form-control custom-select">
        <?php foreach ( $shortcodes as $shortcode ) : ?>
            <option value="<?php echo esc_attr( $shortcode['code'] ) ?>"><?php echo esc_html( $shortcode['name'] ) ?></option>
        <?php endforeach ?>
    </select>
    <div class="input-group-append">
        <button class="btn btn-outline-secondary ladda-button" id="bookly-find-shortcode-and-open" data-spinner-size="40" data-style="zoom-in" data-spinner-color="rgb(62, 66, 74)"><span class="ladda-label"><i class="fas fa-external-link-alt fa-sm"></i></span></button>
    </div>
</div>