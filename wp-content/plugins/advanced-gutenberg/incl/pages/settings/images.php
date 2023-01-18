<?php
defined( 'ABSPATH' ) || die;

wp_enqueue_media(); // We require this for "Default thumbnail" setting

$settings           = get_option( 'advgb_settings' );
$default_thumb      = plugins_url(
                        'assets/blocks/recent-posts/recent-post-default.png',
                        ADVANCED_GUTENBERG_PLUGIN
                    );
$rp_default_thumb   = isset( $settings['rp_default_thumb'] )
                    ? $settings['rp_default_thumb']
                    : [ 'url' => $default_thumb, 'id' => 0 ];

$gallery_lightbox_caption   = $this->getOptionSetting( 'advgb_settings', 'gallery_lightbox_caption', 'text', '1' );
$gallery_lightbox_checked   = $this->getOptionSetting( 'advgb_settings', 'gallery_lightbox', 'checkbox', 1 );

?>
<form method="post">
    <?php wp_nonce_field( 'advgb_settings_images_nonce', 'advgb_settings_images_nonce_field' ) ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php _e( 'Open galleries in lightbox', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="checkbox" name="gallery_lightbox"
                           id="gallery_lightbox"
                           value="1"
                        <?php esc_attr_e( $gallery_lightbox_checked ) ?>
                    />
                    <?php
                    _e(
                        'Open gallery images as a lightbox style popup',
                        'advanced-gutenberg'
                    )
                    ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Image caption', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <select name="gallery_lightbox_caption" id="gallery_lightbox_caption">
                        <option value="0"<?php echo ( $gallery_lightbox_caption === '0' || $gallery_lightbox_caption === 0 ) ? ' selected' : '' ?>>
                            <?php esc_html_e( 'Disabled', 'advanced-gutenberg' ); ?>
                        </option>
                        <option value="1"<?php echo ( $gallery_lightbox_caption === '1' || $gallery_lightbox_caption === 1 ) ? ' selected' : '' ?>>
                            <?php esc_html_e('Bottom', 'advanced-gutenberg'); ?>
                        </option>
                        <option value="2"<?php echo ( $gallery_lightbox_caption === '2' || $gallery_lightbox_caption === 2 ) ? ' selected' : '' ?>>
                            <?php esc_html_e( 'Overlay', 'advanced-gutenberg' ); ?>
                        </option>
                    </select>
                </label>
                <p class="description">
                    <?php
                    _e(
                        'Display caption text on images loaded as lightbox in galleries.',
                        'advanced-gutenberg'
                    )
                    ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Default thumbnail', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <div class="setting-actions-wrapper">
                    <input type="hidden" id="post_default_thumb" name="post_default_thumb" value="<?php echo esc_attr($rp_default_thumb['url']); ?>" />
                    <input type="hidden" id="post_default_thumb_id" name="post_default_thumb_id" value="<?php echo esc_attr($rp_default_thumb['id']); ?>" />
                    <div class="setting-actions" id="post_default_thumb_actions">
                        <img class="thumb-selected"
                             src="<?php echo esc_url( $rp_default_thumb['url'] ); ?>"
                             alt="thumb"
                             data-default="<?php echo esc_url( $default_thumb ); ?>"
                        />
                        <i class="dashicons dashicons-edit" id="thumb_edit" title="<?php esc_attr_e( 'Edit', 'advanced-gutenberg' ); ?>"></i>
                        <i class="dashicons dashicons-no" id="thumb_remove" title="<?php esc_attr_e( 'Reset to default', 'advanced-gutenberg' ); ?>"></i>
                    </div>
                </div>
                <p class="description">
                    <?php
                    _e(
                        'Set the default post thumbnail to use in Content Display blocks for posts without featured image.',
                        'advanced-gutenberg'
                    )
                    ?>
                </p>
            </td>
        </tr>
    </table>

    <div class="advgb-form-buttons-bottom">
        <button type="submit"
                class="button button-primary"
                name="save_settings_images"
        >
            <?php esc_html_e( 'Save Image Settings', 'advanced-gutenberg' ) ?>
        </button>
    </div>
</form>
