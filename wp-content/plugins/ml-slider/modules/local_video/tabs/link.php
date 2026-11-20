<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$url    = get_post_meta( $this->slide->ID, 'ml-slider_url', true );
$target = (bool) get_post_meta( $this->slide->ID, 'ml-slider_new_window', true ) ? true : false;
?>
<div class="row mb-2 adjust-tooltip--1">
    <label>
        <?php 
        esc_html_e( 'Video Link URL', 'ml-slider' ); 
        
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->info_tooltip( __(
            'When visitors click on your video slide, they will be taken to this URL. This may affect how user interacts with the video controls.', 
            'ml-slider'
        ) );
        ?>
    </label>
</div>
<div class="row has-right-checkbox mb-0">
    <div>
        <input class="url" type="text" aria-label="<?php esc_attr_e('Video Link URL', 'ml-slider'); ?>" name="attachment[<?php
        echo esc_attr( $this->slide->ID ); ?>][url]" placeholder="<?php 
        echo esc_attr( 'URL' ); ?>" value="<?php 
        echo esc_attr( $url ); ?>" />
    </div>
    <div class="input-label">
        <label>
            <?php 
            esc_html_e( 'New window', 'ml-slider' );
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->info_tooltip( __(
                'Open link in a new window', 
                'ml-slider'
            ) );
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->switch_button( 
                'attachment[' . esc_attr( $this->slide->ID ) . '][new_window]', 
                (bool) $target,
                array(),
                'mr-0 ml-2'
            );
            ?>
        </label>
    </div>
</div>
