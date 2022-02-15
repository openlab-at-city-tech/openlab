<?php
defined('ABSPATH') || die;

// Check users permissions
if ( !current_user_can('administrator') ) {
    wp_die( esc_html__('You do not have permission to manage Block Access', 'advanced-gutenberg') );
}

wp_enqueue_style('advgb_profile_style');
wp_enqueue_script('advgb_update_list');
wp_enqueue_script('advgb_block_access_js');
wp_enqueue_script('wp-blocks');
wp_enqueue_script('wp-element');
wp_enqueue_script('wp-data');
wp_enqueue_script('wp-components');
wp_enqueue_script('wp-block-library');
wp_enqueue_script('wp-editor');
wp_enqueue_script('wp-edit-post');
wp_enqueue_script('wp-plugins');
do_action('enqueue_block_editor_assets');

// Block Categories
$blockCategories = array();
if (function_exists('get_block_categories')) {
    $blockCategories = get_block_categories(get_post());
} elseif (function_exists('gutenberg_get_block_categories')) {
    $blockCategories = gutenberg_get_block_categories(get_post());
}

wp_add_inline_script(
    'wp-blocks',
    sprintf('wp.blocks.setCategories( %s );', wp_json_encode($blockCategories)),
    'after'
);

// Current role
if( isset( $_REQUEST['user_role'] ) && !empty( $_REQUEST['user_role'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- advgb_nonce in place
    $current_user_role = sanitize_text_field($_REQUEST['user_role']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- advgb_nonce in place
} else {
    $current_user_role = 'administrator';
}

// Get disabled blocks by user roles option
$advgb_blocks_user_roles = !empty( get_option('advgb_blocks_user_roles') ) ? get_option( 'advgb_blocks_user_roles' ) : [];
$advgb_blocks_user_roles = array_key_exists( $current_user_role, $advgb_blocks_user_roles ) ? (array)$advgb_blocks_user_roles[$current_user_role] : [];

// Saved blocks (the ones detected by PP Blocks)
// @TODO if advgb_blocks_list is empty, maybe refresh to display the blocks automatically (?)
$advgb_blocks_list = !empty( get_option( 'advgb_blocks_list' ) ) ? get_option( 'advgb_blocks_list' ) : [];

// Deactivate these blocks
$advgb_blocks_deactivate_force = array(
    'advgb/container'
);
$advgb_block_status_ = null;
?>

<form method="post">
    <?php wp_nonce_field('advgb_nonce', 'advgb_nonce_field'); ?>
    <div>

        <?php
        if ( isset($_GET['save_access']) && $_GET['save_access'] === 'success' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display message, no action ?>
            <div class="ju-notice-msg ju-notice-success">
                <?php esc_html_e('Block Access saved successfully!', 'advanced-gutenberg') ?>
                <i class="dashicons dashicons-dismiss ju-notice-close"></i>
            </div>
        <?php
    } elseif ( isset($_GET['save_access']) && $_GET['save_access'] === 'error' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- advgb_nonce in place
            ?>
            <div class="ju-notice-msg ju-notice-error">
                <?php esc_html_e('Block Access can\'t be saved. Please try again.', 'advanced-gutenberg') ?>
                <i class="dashicons dashicons-dismiss ju-notice-close"></i>
            </div>
            <?php
        } else {
            // Nothing to do here
        }
        ?>

        <div class="advgb-header profile-header">
            <h1 class="header-title"><?php esc_html_e('Block Access', 'advanced-gutenberg') ?></h1>
        </div>

        <div class="profile-title" style="padding-bottom: 20px;">
            <div class="advgb-roles-wrapper">
                <select name="user_role" id="user_role">
                    <?php
                    global $wp_roles;
                    $roles_list = $wp_roles->get_names();
                    foreach ($roles_list as $roles => $role_name) :
                        $role_name = translate_user_role($role_name);
                        ?>
                        <option value="<?php echo esc_attr($roles); ?>" <?php selected( $current_user_role, $roles ); ?>>
                            <?php echo esc_html($role_name); ?>
                        </option>
                    <?php
                    endforeach;
                    ?>
                </select>
                <div class="advgb-search-wrapper">
                    <input type="text" class="blocks-search-input advgb-search-input"
                           placeholder="<?php esc_attr_e('Search blocks', 'advanced-gutenberg') ?>"
                    >
                    <i class="mi mi-search"></i>
                </div>
                <div class="inline-button-wrapper">
                    <span id="block-update-notice">
                        <?php esc_html_e('Blocks list updated.', 'advanced-gutenberg') ?>
                    </span>

                    <button class="button button-primary pp-primary-button save-profile-button"
                            type="submit"
                            name="advgb_block_access_save"
                    >
                        <span><?php esc_html_e('Save Block Access', 'advanced-gutenberg') ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!--Blocks list -->
        <div id="blocks-list-tab" class="tab-content">
            <div>
                <?php
                foreach( $blockCategories as $blockCategory ) {
                    ?>
                    <div class="category-block clearfix">
                        <h3 class="category-name">
                            <span>
                                <?php echo esc_html($blockCategory['title']); ?>
                            </span><i class="mi"></i>
                        </h3>
                        <ul class="blocks-list">
                            <?php
                            foreach ($advgb_blocks_list as $block) {
                                if( $blockCategory['slug'] === $block['category'] ) {
                                    // Convert object to array
                                    $block = (array)$block;

                                    // Disable some blocks such as Container
                                    if( in_array($block['name'], $advgb_blocks_deactivate_force) ) {
                                        $advgb_block_status_ = false;
                                    } else {
                                        $advgb_block_status_ = empty( $advgb_blocks_user_roles['active_blocks'] ) || ( in_array($block['name'], $advgb_blocks_user_roles['active_blocks']) || !in_array($block['name'], $advgb_blocks_user_roles['inactive_blocks']) );
                                    }
                                    ?>
                                    <li class="block-item block-access-item ju-settings-option">
                                        <label class="ju-setting-label">
                                            <span class="block-icon"<?php echo isset( $block['iconColor'] ) && !empty( $block['iconColor'] ) ? ' style="color:' . esc_attr($block['iconColor']) . ';"' : ''; ?>>
                                                <?php
                                                echo wp_specialchars_decode( $block['icon'], ENT_QUOTES ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                ?>
                                            </span>
                                            <span class="block-title">
                                                <?php echo esc_html($block['title']); ?>
                                            </span>
                                        </label>
                                        <div class="ju-switch-button">
                                            <label class="switch">
                                                <input type="checkbox" name="blocks[]" value="<?php echo esc_attr( $block['name'] ); ?>"<?php echo ( $advgb_block_status_ ) ? ' checked="checked"' : '' ?>>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </li>
                                <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                }

                // Extract the slug from the listed categories
                $blockCategoriesSlug = [];
                foreach( $blockCategories as $blockCategory ) {
                    $blockCategoriesSlug[] = $blockCategory['slug'];
                }

                // Generate hidden fields with all the saved blocks (except the ones not listed in this page to avoid saving them as inactive)
                foreach ($advgb_blocks_list as $block) {
                    if( $block['name'] && in_array( $block['category'], $blockCategoriesSlug ) ) {
                    ?>
                        <input type="hidden" name="blocks_list[]" value="<?php echo esc_attr( $block['name'] ); ?>">
                    <?php
                    } elseif ( $block['name'] && !in_array( $block['category'], $blockCategoriesSlug ) ) {
                        ?>
                        <input type="hidden" name="blocks_list_undetected[]" value="<?php echo esc_attr( $block['name'] ); ?>">
                        <?php
                    } else {
                        // Nothing to do here
                    }
                }
                ?>
            </div>
        </div>

        <!--Save button-->
        <button class="button button-primary pp-primary-button save-profile-button"
                type="submit"
                name="advgb_block_access_save"
                style="margin-top: 20px;"
        >
            <span><?php esc_html_e('Save Block Access', 'advanced-gutenberg') ?></span>
        </button>
    </div>
</form>
