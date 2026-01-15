<?php
use PublishPress\Blocks\Utilities;

defined( 'ABSPATH' ) || die;

/**
 * Auto Insert Blocks Metaboxes
 *
 * @since 3.3.0
 */
class AdvancedGutenbergAutoInsertMetaboxes {
    public $proActive;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->proActive = Utilities::isProActive();

        $this->initHooks();
    }

    /**
     * Initialize WordPress hooks
     */
    private function initHooks()
    {
        add_action('add_meta_boxes', array($this, 'addAutoInsertMetaBoxes'));
    }

    /**
     * Add custom metaboxes for auto insert blocks
     */
    public function addAutoInsertMetaBoxes()
    {
        if (!Utilities::settingIsEnabled('auto_insert_blocks')) {
            return;
        }
        add_meta_box(
            'advgb_block_selection',
            __('Block Selection', 'advanced-gutenberg'),
            array($this, 'blockSelectionMetaBox'),
            'advgb_insert_block',
            'normal',
            'high'
        );

        add_meta_box(
            'advgb_position_settings',
            __('Position Settings', 'advanced-gutenberg'),
            array($this, 'positionSettingsMetaBox'),
            'advgb_insert_block',
            'normal',
            'high'
        );

        add_meta_box(
            'advgb_targeting_options',
            __('Targeting Options', 'advanced-gutenberg'),
            array($this, 'targetingOptionsMetaBox'),
            'advgb_insert_block',
            'normal',
            'high'
        );

        add_meta_box(
            'advgb_rule_settings',
            __('Rule Settings', 'advanced-gutenberg'),
            array($this, 'ruleSettingsMetaBox'),
            'advgb_insert_block',
            'side',
            'default'
        );
    }

    /**
     * Block selection metabox
     */
    public function blockSelectionMetaBox($post)
    {
        $block_id = get_post_meta($post->ID, '_advgb_block_id', true);
        $reusable_blocks = $this->getReusableBlocks();
        ?>
        <table class="form-table">
            <tr>
                <th><label for="advgb_block_id"><?php _e('Reusable Block', 'advanced-gutenberg'); ?> <span class="required">*</span></label></th>
                <td>
                    <?php if ( empty( $reusable_blocks ) ) : ?>
                        <div class="no-reusable-blocks-message">
                            <p>
                                <?php _e( 'You do not have any reusable blocks.', 'advanced-gutenberg' ); ?>
                                <a class="advgb-pro-link" target="_blank" href="<?php echo admin_url( 'edit.php?post_type=wp_block' ); ?>">
                                    <?php _e( 'Click here to add new reusable block', 'advanced-gutenberg' ); ?>
                                </a>
                            </p>
                        </div>
                        <select style="display: none;" name="advgb_block_id" id="advgb_block_id" required></select>
                    <?php else : ?>
                        <select name="advgb_block_id" id="advgb_block_id" class="advgb-editor-aib-select2 regular-text" required>
                            <option value=""><?php _e( 'Select a reusable block...', 'advanced-gutenberg' ); ?></option>
                            <?php foreach ( $reusable_blocks as $block ) : ?>
                                <option value="<?php echo esc_attr( $block['id'] ); ?>" <?php selected( $block_id, $block['id'] ); ?>>
                                    <?php echo esc_html( $block['title'] ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Position settings metabox with enhanced options
     */
    public function positionSettingsMetaBox($post)
    {
        wp_nonce_field('advgb_auto_insert_meta', 'advgb_auto_insert_nonce');

        $position = get_post_meta($post->ID, '_advgb_position', true);
        $position_value = get_post_meta($post->ID, '_advgb_position_value', true);
        $excluded_blocks = get_post_meta($post->ID, '_advgb_excluded_blocks', true);
        $blocks = get_post_meta($post->ID, '_advgb_blocks', true);

        if (empty($excluded_blocks)) {
            $excluded_blocks = [];
        }

        if (empty($position_value)) {
            $position_value = 1;
        }
        $position_options_grouped = [
            __('General', 'advanced-gutenberg') => [
                'beginning' => __('Beginning of post', 'advanced-gutenberg'),
                'end'       => __('End of post', 'advanced-gutenberg'),
            ],
            __('Paragraph', 'advanced-gutenberg') => [
                'after_paragraph'  => __('After Nth paragraph', 'advanced-gutenberg'),
                'before_paragraph' => __('Before Nth paragraph', 'advanced-gutenberg'),
            ],
            __('Heading', 'advanced-gutenberg') => [
                'after_heading'  => __('After Nth heading', 'advanced-gutenberg'),
                'before_heading' => __('Before Nth heading', 'advanced-gutenberg'),
            ],
            __('Any Block', 'advanced-gutenberg') => [
                'after_block'  => __('After Nth block', 'advanced-gutenberg'),
                'before_block' => __('Before Nth block', 'advanced-gutenberg'),
            ],
            __('Specific Block', 'advanced-gutenberg') => [
                'after_specific_block'  => __('After specific Nth block', 'advanced-gutenberg'),
                'before_specific_block' => __('Before specific Nth block', 'advanced-gutenberg'),
            ],
        ];

        $needs_position_value = ['after_heading', 'before_heading', 'after_paragraph', 'before_paragraph', 'after_block', 'before_block', 'after_specific_block', 'before_specific_block'];
        $needs_specific_block = ['after_specific_block', 'before_specific_block'];
        $needs_excluded_blocks = ['after_block', 'before_block'];

        $pro_class = ! $this->proActive ? 'advgb-blur' : '';
        ?>
        <table class="form-table">
            <tr class="position-table-row">
                <th><label for="advgb_position"><?php _e('Insert Position', 'advanced-gutenberg'); ?> <span class="required">*</span></label></th>
                <td>
                    <select name="advgb_position" id="advgb_position" class="advgb-editor-aib-select2 regular-text">
                        <?php foreach ($position_options_grouped as $group_label => $options) : ?>
                            <optgroup label="<?php echo esc_attr($group_label); ?>">
                                <?php foreach ($options as $key => $label) : ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($position, $key); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!$this->proActive) : ?>
                        <p class="description advgb-pro-required invalid-position" style="display:none;">
                            <a class="advgb-pro-link" href="<?php echo esc_url(ADVANCED_GUTENBERG_UPGRADE_LINK); ?>" target="_blank">
                               <span class="dashicons dashicons-lock advgb-pro-loc-icon"></span>
                            </a>
                           <?php
                            printf(
                                __('The selected option requires the Pro version. Please select one of the general options or %1$sUpgrade to Pro%2$s.', 'advanced-gutenberg'),
                                '<a href="'. esc_url(ADVANCED_GUTENBERG_UPGRADE_LINK) .'" target="_blank">',
                                '</a>'
                            );
                            ?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr id="position-value-row"
                style="<?php echo in_array($position, $needs_position_value) ? '' : 'display:none;'; ?>">
                <th class="<?php echo esc_attr($pro_class); ?>"><label for="advgb_position_value"><?php _e('Position Number', 'advanced-gutenberg'); ?></label></th>
                <td class="advgb-promo-overlay-area">
                    <input class="<?php echo esc_attr($pro_class); ?>" type="number" name="<?php echo $this->proActive ? 'advgb_position_value' : ''; ?>" id="advgb_position_value"
                        value="<?php echo esc_attr($position_value); ?>" class="small-text">
                    <p class="<?php echo esc_attr($pro_class); ?> description">
                        <?php _e('Enter the number (e.g., 3 for "after 3rd heading").', 'advanced-gutenberg'); ?>
                    </p>
                     <?php if (!$this->proActive) : ?>
                        <a class="advgb-pro-link" href="<?php echo esc_url(ADVANCED_GUTENBERG_UPGRADE_LINK); ?>" target="_blank">
                            <div class="advgb-pro-small-overlay-text">
                                    <span class="dashicons dashicons-lock"></span> <?php _e('Pro feature', 'advanced-gutenberg'); ?>
                            </div>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <tr id="excluded-blocks-row" style="<?php echo in_array($position, $needs_excluded_blocks) ? '' : 'display:none;'; ?>">
                <th class="<?php echo esc_attr($pro_class); ?>"><label><?php _e('Exclude from counting', 'advanced-gutenberg'); ?></label></th>
                <td class="<?php echo esc_attr($pro_class); ?>">
                    <?php
                    $core_blocks = array(
                        'core/paragraph' => __('Paragraph', 'advanced-gutenberg'),
                        'core/heading' => __('Heading', 'advanced-gutenberg'),
                        'core/image' => __('Image', 'advanced-gutenberg'),
                        'core/list' => __('List', 'advanced-gutenberg'),
                        'core/quote' => __('Quote', 'advanced-gutenberg'),
                        'core/separator' => __('Separator', 'advanced-gutenberg'),
                        'core/spacer' => __('Spacer', 'advanced-gutenberg'),
                    );

                    foreach ($core_blocks as $block_name => $block_label): ?>
                        <label>
                            <input type="checkbox" name="<?php echo $this->proActive ? 'advgb_excluded_blocks[]' : ''; ?>" value="<?php echo esc_attr($block_name); ?>" <?php checked(in_array($block_name, $excluded_blocks)); ?> />
                            <?php echo esc_html($block_label); ?>
                        </label><br>
                    <?php endforeach; ?>
                    <p class="description">
                        <?php _e('Selected blocks will not be counted when determining position.', 'advanced-gutenberg'); ?>
                    </p>
                </td>
            </tr>
            <tr id="specific-blocks-row" style="<?php echo in_array($position, $needs_specific_block) ? '' : 'display:none;'; ?>">
                <th class="<?php echo esc_attr($pro_class); ?>"><label><?php _e('Search Block', 'advanced-gutenberg'); ?></label></th>
                <td class="<?php echo esc_attr($pro_class); ?>">
                    <p class="description">
                        <?php _e('Search and select blocks after this reusable block should be inserted.', 'advanced-gutenberg'); ?>
                    </p>
                    <div class="user-group">
                        <div class="advg-insert-block-values">
                            <?php
                            if (!empty($blocks)) {
                                foreach ($blocks as $block_name => $block_label) {
                                    if (empty($block_name) || empty($block_label)) {
                                        continue;
                                    }
                                ?>
                                <input type="hidden" name="<?php echo $this->proActive ? 'advgb_blocks[' . esc_attr($block_name) . ']' : ''; ?>" value="<?php echo esc_attr($block_label); ?>" data-block-id="<?php echo esc_attr($block_name); ?>">
                                <?php }
                            } ?>
                        </div>
                        <select class="advg-insert-block-select2"
                            data-placeholder="<?php echo esc_attr__('Search blocks...', 'advanced-gutenberg'); ?>"
                            multiple="multiple"
                            style="width: 100%;">
                            <?php
                            if (!empty($blocks)) {
                                foreach ($blocks as $block_name => $block_label) {
                                    if (empty($block_name) || empty($block_label)) {
                                        continue;
                                    }
                                ?>
                                    <option value="<?php echo esc_attr($block_name); ?>" selected>
                                        <?php echo esc_html($block_label); ?>
                                    </option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Enhanced targeting options metabox
     */
    public function targetingOptionsMetaBox($post)
    {
        $post_types = get_post_meta($post->ID, '_advgb_post_types', true);
        $taxonomies = get_post_meta($post->ID, '_advgb_taxonomies', true);
        $authors = get_post_meta($post->ID, '_advgb_authors', true);
        $post_ids = get_post_meta($post->ID, '_advgb_post_ids', true);
        $exclude_post_ids = get_post_meta($post->ID, '_advgb_exclude_post_ids', true);
        $post_months = get_post_meta($post->ID, '_advgb_post_months', true);
        $post_years = get_post_meta($post->ID, '_advgb_post_years', true);

        if (empty($post_types)) {
            $post_types = ['post'];
        }
        if (empty($taxonomies)) {
            $taxonomies = [];
        }
        if (empty($post_ids)) {
            $post_ids = [];
        }
        if (empty($exclude_post_ids)) {
            $exclude_post_ids = [];
        }
        if (empty($authors)) {
            $authors = [];
        }
        if (empty($post_months)) {
            $post_months = [];
        }
        if (empty($post_years)) {
            $post_years = [];
        }

        $pro_class = ! $this->proActive ? 'advgb-blur' : '';
        ?>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Post Types', 'advanced-gutenberg'); ?> <span class="required">*</span></label></th>
                <td>
                    <div id="post-types-selection">
                        <?php
                        $available_post_types = get_post_types(array('public' => true), 'objects');
                        foreach ($available_post_types as $post_type):
                            if (in_array($post_type->name, ['attachment'])) {
                                continue;
                            }
                            $additional_class = ! $this->proActive && $post_type->name !== 'post' ? $pro_class : '';
                        ?>
                            <label class="<?php echo esc_attr($additional_class); ?>">
                                <input type="checkbox" name="advgb_post_types[]" value="<?php echo esc_attr($post_type->name); ?>"
                                    <?php checked(in_array($post_type->name, $post_types)); ?> class="post-type-checkbox">
                                <?php echo esc_html($post_type->label); ?>
                            </label>
                                <?php if (!$this->proActive && $post_type->name !== 'post') : ?>
                                    <a class="advgb-pro-link" href="<?php echo esc_url(ADVANCED_GUTENBERG_UPGRADE_LINK); ?>" target="_blank">
                                        <span class="dashicons dashicons-lock advgb-pro-loc-icon"></span>
                                    </a>
                                <?php endif; ?>
                            <br>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>

            <tr>
                <th><label><?php _e('Taxonomies', 'advanced-gutenberg'); ?></label></th>
                <td>
                    <div id="taxonomies-selection">
                        <p class="description">
                            <?php _e('You can restrict this block insertion to only post with specific taxonomy terms.', 'advanced-gutenberg'); ?>
                        </p>
                        <?php $this->renderAllTaxonomies($post_types, $taxonomies, $available_post_types); ?>
                    </div>
                </td>
            </tr>

            <tr>
                <th class="<?php echo esc_attr($pro_class); ?>"><label><?php _e('Authors', 'advanced-gutenberg'); ?></label></th>
                <td class="advgb-promo-overlay-area">
                    <p class="<?php echo esc_attr($pro_class); ?> description">
                        <?php _e('Only target posts from specific authors.', 'advanced-gutenberg'); ?>
                    </p>
                    <div class="user-group <?php echo esc_attr($pro_class); ?>">
                        <select name="advgb_authors[]" class="advg-insert-author-select2"
                            data-placeholder="<?php echo esc_attr__('Search author...', 'advanced-gutenberg'); ?>"
                            multiple="multiple"
                            style="width: 100%;">
                            <?php
                            if (!empty($authors)) {
                                foreach ($authors as $selected_author) {
                                    $author_id = (int) $selected_author;
                                    if (empty($author_id)) {
                                        continue;
                                    }
                                    $author_user = get_userdata($author_id);
                                    if (!$author_user || is_wp_error($author_user)) {
                                        continue;
                                    }
                                ?>
                                    <option value="<?php echo esc_attr($author_user->ID); ?>" selected>
                                        <?php echo esc_html($author_user->display_name); ?>
                                    </option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                     <?php if (!$this->proActive) : ?>
                        <a class="advgb-pro-link" href="<?php echo esc_url(ADVANCED_GUTENBERG_UPGRADE_LINK); ?>" target="_blank">
                            <div class="advgb-pro-small-overlay-text">
                                    <span class="dashicons dashicons-lock"></span> <?php _e('Pro feature', 'advanced-gutenberg'); ?>
                            </div>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>

            <tr class="post-created-in-months">
                <th class="<?php echo esc_attr($pro_class); ?>"><label><?php _e('Post Created in Months', 'advanced-gutenberg'); ?></label></th>
                <td>
                    <p class="<?php echo esc_attr($pro_class); ?> description">
                        <?php _e('Target posts created in specific months of the year.', 'advanced-gutenberg'); ?>
                    </p>
                    <div class="<?php echo esc_attr($pro_class); ?> checkbox-group">
                        <?php
                        $months = [
                            1 => __('January', 'advanced-gutenberg'),
                            2 => __('February', 'advanced-gutenberg'),
                            3 => __('March', 'advanced-gutenberg'),
                            4 => __('April', 'advanced-gutenberg'),
                            5 => __('May', 'advanced-gutenberg'),
                            6 => __('June', 'advanced-gutenberg'),
                            7 => __('July', 'advanced-gutenberg'),
                            8 => __('August', 'advanced-gutenberg'),
                            9 => __('September', 'advanced-gutenberg'),
                            10 => __('October', 'advanced-gutenberg'),
                            11 => __('November', 'advanced-gutenberg'),
                            12 => __('December', 'advanced-gutenberg')
                        ];
                        foreach ($months as $month_num => $month_name): ?>
                            <label>
                                <input type="checkbox" name="advgb_post_months[]" value="<?php echo esc_attr($month_num); ?>" <?php checked(in_array($month_num, $post_months)); ?> />
                                <?php echo esc_html($month_name); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>

            <tr>
                <th class="<?php echo esc_attr($pro_class); ?>"><label for="advgb_post_years"><?php _e('Post Created in Years', 'advanced-gutenberg'); ?></label></th>
                <td>
                    <p class="<?php echo esc_attr($pro_class); ?> description">
                        <?php _e('Comma-separated list of years (e.g., 2023,2024,2025).', 'advanced-gutenberg'); ?>
                    </p>
                    <input type="text" name="advgb_post_years" id="advgb_post_years"
                        value="<?php echo esc_attr(implode(',', $post_years)); ?>" class="<?php echo esc_attr($pro_class); ?> regular-text">
                </td>
            </tr>

            <tr>
                <th class="<?php echo esc_attr($pro_class); ?>"><label><?php _e('Target Posts', 'advanced-gutenberg'); ?></label></th>
                <td class="advgb-promo-overlay-area">
                    <div class="post-search-wrap <?php echo esc_attr($pro_class); ?>">
                            <p class="description"><?php _e('Search and select posts or enter their IDs to limit this rules to specific posts.', 'advanced-gutenberg'); ?></p>
                            <div class="post-ids-search-container">
                                <select class="advg-insert-post-select2 include-posts"
                                    data-placeholder="<?php echo esc_attr__('Search posts...', 'advanced-gutenberg'); ?>"
                                    multiple="multiple"
                                    style="width: 100%;">
                                    <?php
                                    if (!empty($post_ids)) {
                                        foreach ($post_ids as $post_id) {
                                            $post = get_post($post_id);
                                            if ($post) {
                                                echo '<option value="' . esc_attr($post->ID) . '" selected>' . esc_html($post->post_title) . '</option>';
                                            }
                                        }
                                    } ?>
                                </select>
                                <input type="hidden" name="advgb_post_ids" id="advgb_post_ids" value="<?php echo esc_attr(implode(',', $post_ids)); ?>">
                            </div>
                            <div class="advgb-or-separator">
                                <span><?php _e('OR', 'advanced-gutenberg'); ?></span>
                            </div>
                            <input style="width: 100%;" type="text" class="regular-text post-ids-manual-input" placeholder="<?php esc_attr_e('Enter comma-separated list of post IDs to include (e.g., 123,456,789).', 'advanced-gutenberg'); ?>" value="<?php echo esc_attr(implode(',', $post_ids)); ?>">
                    </div>
                    <?php if (!$this->proActive) : ?>
                        <a class="advgb-pro-link" href="<?php echo esc_url(ADVANCED_GUTENBERG_UPGRADE_LINK); ?>" target="_blank">
                            <div class="advgb-pro-small-overlay-text">
                                <span class="dashicons dashicons-lock"></span> <?php _e('Pro feature', 'advanced-gutenberg'); ?>
                            </div>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th class="<?php echo esc_attr($pro_class); ?>"><label><?php _e('Exclude Posts', 'advanced-gutenberg'); ?></label></th>
                <td class="advgb-promo-overlay-area">
                    <div class="post-search-wrap <?php echo esc_attr($pro_class); ?>">
                            <p class="description"><?php _e('Search and select posts or enter their IDs to exclude them from auto insert.', 'advanced-gutenberg'); ?></p>
                            <div class="post-ids-search-container">
                                <select class="advg-insert-post-select2 exclude-posts"
                                    data-placeholder="<?php echo esc_attr__('Search posts...', 'advanced-gutenberg'); ?>"
                                    multiple="multiple"
                                    style="width: 100%;">
                                    <?php
                                    if (!empty($exclude_post_ids)) {
                                        foreach ($exclude_post_ids as $post_id) {
                                            $post = get_post($post_id);
                                            if ($post) {
                                                echo '<option value="' . esc_attr($post->ID) . '" selected>' . esc_html($post->post_title) . '</option>';
                                            }
                                        }
                                    } ?>
                                </select>
                                <input type="hidden" name="advgb_exclude_post_ids" id="advgb_exclude_post_ids" value="<?php echo esc_attr(implode(',', $exclude_post_ids)); ?>">
                            </div>
                            <div class="advgb-or-separator">
                                <span><?php _e('OR', 'advanced-gutenberg'); ?></span>
                            </div>
                            <input style="width: 100%;" type="text" class="regular-text post-ids-manual-input" placeholder="<?php esc_attr_e('Enter comma-separated post IDs to exclude (e.g., 123,456,789).', 'advanced-gutenberg'); ?>" value="<?php echo esc_attr(implode(',', $exclude_post_ids)); ?>">
                    </div>

                    <?php if (!$this->proActive) : ?>
                        <a class="advgb-pro-link" href="<?php echo esc_url(ADVANCED_GUTENBERG_UPGRADE_LINK); ?>" target="_blank">
                            <div class="advgb-pro-small-overlay-text">
                                <span class="dashicons dashicons-lock"></span> <?php _e('Pro feature', 'advanced-gutenberg'); ?>
                            </div>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>

        </table>
        <?php
    }

    /**
     * Rule settings metabox
     */
    public function ruleSettingsMetaBox($post)
    {
        $priority = get_post_meta($post->ID, '_advgb_priority', true);

        if (empty($priority)) {
            $priority = 10;
        }
        ?>
        <table class="form-table advgb-auto-insert-settngs-metabox">
            <tr>
                <th><label for="advgb_priority"><?php _e('Priority', 'advanced-gutenberg'); ?></label></th>
                <td>
                    <input type="number" name="advgb_priority" id="advgb_priority" value="<?php echo esc_attr($priority); ?>"
                        min="1" class="small-text">
                    <p class="description"><?php _e('This option decides which rule runs first if you have multiple rules targeting the same position in a post. For example, 9 will run before 10.', 'advanced-gutenberg'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Get reusable blocks for the admin interface
     */
    public function getReusableBlocks()
    {
        $blocks = get_posts(array(
            'post_type' => 'wp_block',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        $reusable_blocks = [];
        foreach ($blocks as $block) {
            $reusable_blocks[] = array(
                'id' => $block->ID,
                'title' => $block->post_title,
                'content' => $block->post_content
            );
        }

        return $reusable_blocks;
    }

    /**
     * Render all taxonomies with post type classes
     */
    public function renderAllTaxonomies(
        $selected_post_types,
        $selected_taxonomies,
        $all_post_types
    )
    {
        $pro_class = ! $this->proActive ? 'advgb-blur' : '';

        $rendered_taxonomies = [];

        foreach ($all_post_types as $post_type) {
            $taxonomies = get_object_taxonomies($post_type->name, 'objects');

            foreach ($taxonomies as $taxonomy) {
                if (in_array($taxonomy->name, ['post_format', 'pp_notify_user', 'pp_notify_role', 'pp_notify_email', 'pp_editorial_meta', 'pp_notify_user', 'pp_notify_role', 'pp_notify_email'])) {
                    continue;
                }
                if (isset($rendered_taxonomies[$taxonomy->name])) {
                    // Add post type class to existing taxonomy
                    $rendered_taxonomies[$taxonomy->name]['post_types'][] = $post_type->name;
                    continue;
                }

                $rendered_taxonomies[$taxonomy->name] = [
                    'label' => $taxonomy->label,
                    'terms' => [],
                    'post_types' => [$post_type->name]
                ];
            }
        }

        foreach ($rendered_taxonomies as $tax_name => $tax_data) {
            $post_type_classes = implode(' ', array_map(function ($pt) {
                return 'post-type-' . $pt;
            }, $tax_data['post_types']));
            $is_visible = !empty($selected_post_types) ? array_intersect($tax_data['post_types'], $selected_post_types) : false;
            $display_style = $is_visible ? '' : 'display: none;';

            $additional_class = ! $this->proActive && ! in_array($tax_name, ['category', 'post_tag']) ? $pro_class : '';
            ?>
            <div class="advgb-promo-overlay-area taxonomy-group <?php echo esc_attr($post_type_classes); ?>" style="<?php echo esc_attr($display_style); ?>">
                <h4 class="<?php echo esc_attr($additional_class); ?>"><?php echo esc_html($tax_data['label']); ?></h4>
                <div class="<?php echo esc_attr($additional_class); ?>">
                    <select name="advgb_taxonomies[<?php echo esc_attr($tax_name); ?>][]" class="advg-insert-taxonomy-select2"
                        data-taxonomy="<?php echo esc_attr($tax_name); ?>"
                        data-placeholder="<?php echo esc_attr__('Search terms...', 'advanced-gutenberg'); ?>"
                        multiple="multiple"
                        style="width: 100%;">
                        <?php
                        $selected_terms = isset($selected_taxonomies[$tax_name]) ? $selected_taxonomies[$tax_name] : [];
                        if (!empty($selected_terms)) {
                            foreach ($selected_terms as $selected_terms) {
                                $term_id = (int) $selected_terms;
                                if (empty($term_id)) {
                                    continue;
                                }
                                $term = get_term($term_id);
                                if (!$term || is_wp_error($term)) {
                                    continue;
                                }
                            ?>
                                <option value="<?php echo esc_attr($term->term_id); ?>" selected>
                                    <?php echo esc_html($term->name); ?>
                                </option>
                            <?php }
                        } ?>
                    </select>
                </div>

                <?php if (! $this->proActive && ! in_array($tax_name, ['category', 'post_tag'])) : ?>
                    <a class="advgb-pro-link" href="<?php echo esc_url(ADVANCED_GUTENBERG_UPGRADE_LINK); ?>" target="_blank">
                        <div class="advgb-pro-small-overlay-text">
                            <span class="dashicons dashicons-lock"></span> <?php _e('Pro feature', 'advanced-gutenberg'); ?>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
            <?php
        }
        ?>
        <?php
    }

}