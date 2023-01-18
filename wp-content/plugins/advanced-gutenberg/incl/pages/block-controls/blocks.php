<?php
defined( 'ABSPATH' ) || die;
?>
<form method="post">
    <div class="advgb-roles-wrapper">
        <?php wp_nonce_field( 'advgb_controls_block_nonce', 'advgb_controls_block_nonce_field' ); ?>
        <div class="advgb-search-wrapper">
            <input type="text"
                   class="blocks-search-input advgb-search-input"
                   placeholder="<?php esc_attr_e( 'Search blocks', 'advanced-gutenberg' ) ?>"
            >
        </div>
        <div class="advgb-toggle-wrapper">
            <?php _e('Enable or disable controls for all blocks', 'advanced-gutenberg') ?>
            <div class="advgb-switch-button">
                <label class="switch">
                    <input type="checkbox" name="toggle_all_blocks" id="toggle_all_blocks">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        <div class="inline-button-wrapper">
            <span class="advgb-enable-one-block-msg" style="display: none;">
                <span>
                    <span>
                        <?php
                        esc_attr_e(
                            'To save this configuration, enable at least one block.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </span>
                    <span class="dashicons dashicons-warning"></span>
                </span>
            </span>
            <button class="button button-primary save-profile-button"
                    type="submit"
                    name="save_blocks"
            >
                <span>
                    <?php esc_html_e( 'Save Blocks', 'advanced-gutenberg' ) ?>
                </span>
            </button>
        </div>
    </div>

    <!-- Blocks list -->
    <div class="tab-content block-list-tab">
        <div class="advgb-block-feature-loading-msg" style="display: block;">
            <?php _e( 'Loading...', 'advanced-gutenberg' ) ?>
        </div>
        <div class="blocks-section">
            <input type="hidden" name="blocks_list" id="blocks_list" />
        </div>
    </div>

    <!-- Save button -->
    <div class="advgb-form-buttons-bottom">
        <button class="button button-primary save-profile-button"
                type="submit"
                name="save_blocks"
        >
            <span>
                <?php esc_html_e( 'Save Blocks', 'advanced-gutenberg' ) ?>
            </span>
        </button>
        <span class="advgb-enable-one-block-msg" style="display: none;">
            <span>
                <span class="dashicons dashicons-warning"></span>
                <span>
                    <?php
                    esc_attr_e(
                        'To save this configuration, enable at least one block.',
                        'advanced-gutenberg'
                    )
                    ?>
                </span>
            </span>
        </span>
    </div>
</form>
