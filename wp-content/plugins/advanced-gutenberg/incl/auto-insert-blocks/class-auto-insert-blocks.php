<?php
use PublishPress\Blocks\Utilities;

defined('ABSPATH') || die;

/**
 * Auto Insert Blocks functionality
 *
 * @since 3.4.0
 */
class AdvancedGutenbergAutoInsertBlocks
{
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
        add_action('init', [$this, 'registerAutoInsertPostType']);
        add_action('save_post', [$this, 'saveAutoInsertRule']);
        add_filter('the_content', [$this, 'autoInsertBlocks'], -10);
        add_filter('parent_file', [$this, 'setAutoInsertMenuParent']);
        add_filter('submenu_file', [$this, 'setAutoInsertSubmenuFile']);
        add_action('admin_notices', [$this, 'displayAdminNotices']);

        // AJAX handlers
        add_action('wp_ajax_advgb_search_taxonomy_terms', [$this, 'searchTaxonomyTerms']);
        add_action('wp_ajax_advgb_insert_search_author', [$this, 'searchUsers']);
        add_action('wp_ajax_advgb_insert_search_block', [$this, 'searchBlocks']);
        add_action('wp_ajax_advgb_insert_search_posts', [$this, 'searchPosts']);

        // Column hooks
        add_filter('manage_advgb_insert_block_posts_columns', [$this, 'addAutoInsertColumns']);
        add_action('manage_advgb_insert_block_posts_custom_column', [$this, 'populateAutoInsertColumns'], 10, 2);
        add_action('admin_head-edit.php', [$this, 'remove_quick_edit_row']);
        add_filter('post_row_actions', [$this, 'addDuplicateAction'], 10, 2);
        add_action('admin_post_duplicate_auto_insert_block', [$this, 'handleDuplicateAction']);
        add_action('months_dropdown_results', [$this, 'removeMonthFilters'], 10, 2);

        // Scripts and styles
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }


    /**
     * Enqueue assets for auto insert blocks pages
     */
    public function enqueueAdminAssets($hook)
    {
        global $post_type;

        if ($post_type === 'advgb_insert_block' && ($hook === 'edit.php' || $hook === 'post.php' || $hook === 'post-new.php')) {

            AdvancedGutenbergMain::commonAdminPagesAssets();

            wp_enqueue_style(
                'advgb_auto_insert_admin_css',
                ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/css/auto-insert-admin.css',
                [],
                ADVANCED_GUTENBERG_VERSION
            );
            wp_enqueue_style(
                'ppma_selet2_css',
                ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/lib/ppma-select2/css/select2-full.min.css',
                [],
                ADVANCED_GUTENBERG_VERSION
            );

            wp_enqueue_script(
                'ppma_selet2_js',
                ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/lib/ppma-select2/js/select2-full.min.js',
                ['jquery'],
                ADVANCED_GUTENBERG_VERSION
            );

            wp_enqueue_script(
                'advgb_auto_insert_admin_js',
                ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/js/auto-insert-admin.js',
                ['jquery', 'ppma_selet2_js'],
                ADVANCED_GUTENBERG_VERSION
            );

            wp_localize_script(
                'advgb_auto_insert_admin_js',
                'advgbAutoInsertI18n',
                [
                    'nonce' => wp_create_nonce('advgb_auto_insert_nonce'),
                    'proVer' => $this->proActive
                ]
            );
        }
    }


    /**
     * Register auto insert blocks post type
     */
    public function registerAutoInsertPostType()
    {
        if (!Utilities::settingIsEnabled('auto_insert_blocks')) {
            return;
        }

        register_post_type('advgb_insert_block', array(
            'labels' => array(
                'name' => __('Auto Insert Blocks', 'advanced-gutenberg'),
                'singular_name' => __('Auto Insert Block', 'advanced-gutenberg'),
                'add_new' => __('Add New Rule', 'advanced-gutenberg'),
                'add_new_item' => __('Add New Auto Insert Block', 'advanced-gutenberg'),
                'edit_item' => __('Edit Auto Insert Block', 'advanced-gutenberg'),
                'new_item' => __('New Auto Insert Block', 'advanced-gutenberg'),
                'view_item' => __('View Auto Insert Block', 'advanced-gutenberg'),
                'search_items' => __('Search Auto Insert Blocks', 'advanced-gutenberg'),
                'not_found' => __('No auto insert blocks found', 'advanced-gutenberg'),
                'not_found_in_trash' => __('No auto insert blocks found in trash', 'advanced-gutenberg'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'supports' => array('title'),
            'can_export' => false,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'query_var' => false,
            'rewrite' => false,
        ));
    }

    /**
     * Add the post type menu to our admin menu
     */
    public function addAutoInsertMenu()
    {
        if (!Utilities::settingIsEnabled('auto_insert_blocks')) {
            return;
        }

        add_submenu_page(
            'advgb_main',
            __('Auto Insert Blocks', 'advanced-gutenberg'),
            __('Auto Insert Blocks', 'advanced-gutenberg'),
            'manage_options',
            'edit.php?post_type=advgb_insert_block'
        );
    }

    /**
     * Modify post type menu parent
     */
    public function setAutoInsertMenuParent($parent_file)
    {
        global $current_screen;

        if ($current_screen && isset($current_screen->post_type) && $current_screen->post_type === 'advgb_insert_block') {
            return 'advgb_main';
        }

        return $parent_file;
    }

    /**
     * Highlight correct menu item
     */
    public function setAutoInsertSubmenuFile($submenu_file)
    {
        global $current_screen;

        if ($current_screen && isset($current_screen->post_type) && $current_screen->post_type === 'advgb_insert_block') {
            return 'edit.php?post_type=advgb_insert_block';
        }

        return $submenu_file;
    }

    /**
     * Add custom columns to auto insert blocks list
     */
    public function addAutoInsertColumns($columns)
    {
        $new_columns = [];
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['block'] = __('Reusable Block', 'advanced-gutenberg');
        $new_columns['position'] = __('Position', 'advanced-gutenberg');
        $new_columns['targeting'] = __('Targeting', 'advanced-gutenberg');
        $new_columns['priority'] = __('Priority', 'advanced-gutenberg');
        $new_columns['date'] = $columns['date'];

        return $new_columns;
    }

    /**
     * Populate custom columns content
     */
    public function populateAutoInsertColumns($column, $post_id)
    {
        switch ($column) {
            case 'block':
                $block_id = get_post_meta($post_id, '_advgb_block_id', true);
                if ($block_id) {
                    $block = get_post($block_id);
                    echo $block ? esc_html($block->post_title) : __('Block not found', 'advanced-gutenberg');
                } else {
                    echo '—';
                }
                break;

            case 'position':
                $position = get_post_meta($post_id, '_advgb_position', true);
                $blocks = get_post_meta($post_id, '_advgb_blocks', true);
                $position_value = get_post_meta($post_id, '_advgb_position_value', true);

                if ($position) {
                    $position_label = ucfirst(str_replace('_', ' ', $position));
                    if ($position == 'after_specific_block' && !empty($blocks)) {
                        $position_label .= ' <strong>' . implode(', ', array_values($blocks)) . '</strong>';
                    }
                    if ($position_value && in_array($position, array('after_heading', 'after_paragraph', 'after_block', 'after_specific_block'))) {
                        $position_label .= ' (' . $position_value . ')';
                    }
                    // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $position_label;
                } else {
                    echo '—';
                }
                break;
            case 'targeting':
                $post_types = get_post_meta($post_id, '_advgb_post_types', true);
                $taxonomies = get_post_meta($post_id, '_advgb_taxonomies', true);
                $authors = get_post_meta($post_id, '_advgb_authors', true);
                $post_ids = get_post_meta($post_id, '_advgb_post_ids', true);
                $exclude_post_ids = get_post_meta($post_id, '_advgb_exclude_post_ids', true);
                $post_months = get_post_meta($post_id, '_advgb_post_months', true);
                $post_years = get_post_meta($post_id, '_advgb_post_years', true);

                $targeting_info = [];

                if (!empty($post_types)) {
                    $post_type_labels = [];
                    foreach ($post_types as $post_type) {
                        $post_type_obj = get_post_type_object($post_type);
                        if ($post_type_obj) {
                            $post_type_labels[] = sprintf(
                                '<a href="%s">%s</a>',
                                esc_url(admin_url('edit.php?post_type=' . $post_type)),
                                esc_html($post_type_obj->labels->name)
                            );
                        }
                    }
                    $targeting_info[] = sprintf(
                        __('Post Types: %s', 'advanced-gutenberg'),
                        implode(', ', $post_type_labels)
                    );
                }

                if (!empty($authors)) {
                    $author_links = [];
                    foreach ($authors as $author_id) {
                        $author = get_user_by('ID', $author_id);
                        if ($author) {
                            $author_links[] = sprintf(
                                '<a href="%s">%s</a>',
                                esc_url(admin_url('user-edit.php?user_id=' . $author_id)),
                                esc_html($author->display_name)
                            );
                        }
                    }
                    $targeting_info[] = sprintf(
                        __('Authors: %s', 'advanced-gutenberg'),
                        implode(', ', $author_links)
                    );
                }

                if (!empty($taxonomies)) {
                    $taxonomy_groups = [];
                    foreach ($taxonomies as $taxonomy => $term_ids) {
                        $taxonomy_obj = get_taxonomy($taxonomy);
                        if (!$taxonomy_obj) {
                            continue;
                        }

                        $term_links = [];
                        foreach ($term_ids as $term_id) {
                            $term = get_term($term_id, $taxonomy);
                            if ($term && !is_wp_error($term)) {
                                $term_links[] = sprintf(
                                    '<a href="%s">%s</a>',
                                    esc_url(admin_url('term.php?taxonomy=' . $taxonomy . '&tag_ID=' . $term_id)),
                                    esc_html($term->name)
                                );
                            }
                        }

                        if (!empty($term_links)) {
                            $taxonomy_groups[] = sprintf(
                                '%s: %s',
                                esc_html($taxonomy_obj->labels->name),
                                implode(', ', $term_links)
                            );
                        }
                    }

                    if (!empty($taxonomy_groups)) {
                        $targeting_info[] = implode(' <br /> ', $taxonomy_groups);
                    }
                }

                if (!empty($post_months)) {
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
                    $months_name = [];
                    foreach ($post_months as $post_month):
                        $months_name[] = $months[$post_month];
                    endforeach;
                    $targeting_info[] = sprintf(
                        __('Post Created in: %s', 'advanced-gutenberg'),
                        implode(', ', $months_name)
                    );
                }

                if (!empty($post_years)) {
                    $targeting_info[] = sprintf(
                        __('Post Created in: %s', 'advanced-gutenberg'),
                        implode(', ', $post_years)
                    );
                }

                if (!empty($post_ids)) {
                    $post_links = [];
                    foreach ($post_ids as $post_id):
                        $post_links[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(admin_url('post.php?post=' . $post_id . '&action=edit')),
                            esc_html($post_id)
                        );
                    endforeach;
                    $targeting_info[] = sprintf(
                        __('Specific Post IDs: %s', 'advanced-gutenberg'),
                        implode(', ', $post_links)
                    );
                }

                if (!empty($exclude_post_ids)) {
                    $exclude_post_links = [];
                    foreach ($exclude_post_ids as $exclude_post_id):
                        $exclude_post_links[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(admin_url('post.php?post=' . $exclude_post_id . '&action=edit')),
                            esc_html($exclude_post_id)
                        );
                    endforeach;
                    $targeting_info[] = sprintf(
                        __('Exclude Post IDs: %s', 'advanced-gutenberg'),
                        implode(', ', $exclude_post_links)
                    );
                }

                echo !empty($targeting_info) ? implode('<br>', $targeting_info) : __('None', 'advanced-gutenberg');
                break;

            case 'priority':
                $priority = get_post_meta($post_id, '_advgb_priority', true);
                echo $priority ? esc_html($priority) : '10';
                break;
        }
    }

    /**
     * Auto insert blocks into content
     */
    public function autoInsertBlocks($content)
    {
        global $post;

        if (!Utilities::settingIsEnabled('auto_insert_blocks')) {
            return $content;
        }

        if (!$post || !is_singular()) {
            return $content;
        }

        $rules = $this->getActiveInsertionRules($post);
        if (empty($rules)) {
            return $content;
        }

        // Remove our filter temporarily
        remove_filter('the_content', [$this, 'autoInsertBlocks'], -10);

        $raw_content = $post->post_content;

        // Parse and apply insertions
        $blocks = parse_blocks($raw_content);
        foreach ($rules as $rule) {
            $blocks = $this->insertBlockByRule($blocks, $rule);
        }

        // Convert back to block markup
        $content_with_insertions = serialize_blocks($blocks);

        // Readd our filter
        add_filter('the_content', [$this, 'autoInsertBlocks'], -10);

        return $content_with_insertions;
    }

    /**
     * Get active insertion rules for a post
     */
    private function getActiveInsertionRules($post)
    {
        $rules = get_posts(array(
            'post_type' => 'advgb_insert_block',
            'post_status' => 'publish',
            'numberposts' => -1
        ));

        $active_rules = [];
        foreach ($rules as $rule) {
            if ($this->shouldApplyRule($rule, $post)) {
                $active_rules[] = array(
                    'id' => $rule->ID,
                    'block_id' => get_post_meta($rule->ID, '_advgb_block_id', true),
                    'position' => get_post_meta($rule->ID, '_advgb_position', true),
                    'allowed_blocks' => get_post_meta($rule->ID, '_advgb_blocks', true),
                    'excluded_blocks' => get_post_meta($rule->ID, '_advgb_excluded_blocks', true),
                    'position_value' => get_post_meta($rule->ID, '_advgb_position_value', true),
                    'priority' => get_post_meta($rule->ID, '_advgb_priority', true)
                );
            }
        }

        // Sort by priority
        usort($active_rules, function ($a, $b) {
            return intval($a['priority']) - intval($b['priority']);
        });

        return $active_rules;
    }

    /**
     * Check if rule should apply to current post
     */
    private function shouldApplyRule($rule, $post)
    {
        // Check specific post IDs first
        $post_ids = get_post_meta($rule->ID, '_advgb_post_ids', true);
        if (!empty($post_ids)) {
            return in_array($post->ID, $post_ids);
        }

        // Check excluded post IDs
        $exclude_post_ids = get_post_meta($rule->ID, '_advgb_exclude_post_ids', true);
        if (!empty($exclude_post_ids) && in_array($post->ID, $exclude_post_ids)) {
            return false;
        }

        // Check post types
        $allowed_post_types = get_post_meta($rule->ID, '_advgb_post_types', true);
        if (empty($allowed_post_types) || !empty($allowed_post_types) && !in_array($post->post_type, $allowed_post_types)) {
            return false;
        }

        // Check taxonomies
        $allowed_taxonomies = get_post_meta($rule->ID, '_advgb_taxonomies', true);
        if (!empty($allowed_taxonomies)) {
            $post_matches_taxonomy = false;

            foreach ($allowed_taxonomies as $taxonomy => $term_ids) {
                if (!empty($term_ids)) {
                    $post_terms = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));
                    if (!empty(array_intersect($term_ids, $post_terms))) {
                        $post_matches_taxonomy = true;
                        break;
                    }
                }
            }

            if (!$post_matches_taxonomy) {
                return false;
            }
        }
        // Check authors
        $allowed_authors = get_post_meta($rule->ID, '_advgb_authors', true);
        if (!empty($allowed_authors) && !in_array($post->post_author, $allowed_authors)) {
            return false;
        }

        // Check post creation months
        $allowed_months = get_post_meta($rule->ID, '_advgb_post_months', true);
        if (!empty($allowed_months)) {
            $post_month = intval(get_the_date('n', $post->ID));
            if (!in_array($post_month, $allowed_months)) {
                return false;
            }
        }

        // Check post creation years
        $allowed_years = get_post_meta($rule->ID, '_advgb_post_years', true);
        if (!empty($allowed_years)) {
            $post_year = intval(get_the_date('Y', $post->ID));
            if (!in_array($post_year, $allowed_years)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Insert block according to rule
     */
    public function insertBlockByRule($blocks, $rule)
    {
        // Load reusable block to insert
        $block_content = get_post($rule['block_id']);
        if (!$block_content) {
            return $blocks;
        }

        // Render the entire reusable block once
        $rendered_content = $this->renderReusableBlockLikeWordPressCore($block_content);

        $html_block = [
            'blockName' => 'core/html',
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => $rendered_content,
            'innerContent' => [$rendered_content],
        ];

        $insert_blocks = [$html_block];

        switch ($rule['position']) {
            case 'beginning':
                $blocks = array_merge($insert_blocks, $blocks);
                break;

            case 'end':
                $blocks = array_merge($blocks, $insert_blocks);
                break;

            default:
                /**
                 * Allow developers to hook into custom insertion logic.
                 *
                 * @param array  $blocks         The current list of blocks in the post.
                 * @param array  $insert_blocks  The blocks we want to insert.
                 * @param array  $rule           The insertion rule.
                 */
                $blocks = apply_filters('advgb_insert_block_by_rule_default', $blocks, $insert_blocks, $rule);
                break;
        }

        return $blocks;
    }


    /**
     * Render reusable block using the same method WordPress core uses
     */
    private function renderReusableBlockLikeWordPressCore($reusable_block)
    {
        global $wp_embed;

        if (!$reusable_block || 'wp_block' !== $reusable_block->post_type) {
            return '';
        }

        if ('publish' !== $reusable_block->post_status || !empty($reusable_block->post_password)) {
            return '';
        }

        // Handle embeds for reusable blocks
        $content = $wp_embed->run_shortcode($reusable_block->post_content);
        $content = $wp_embed->autoembed($content);

        // Apply Block Hooks if the function exists
        if (function_exists('apply_block_hooks_to_content_from_post_object')) {
            $content = apply_block_hooks_to_content_from_post_object($content, $reusable_block);
        }

        // Process blocks
        $content = do_blocks($content);

        return $content;
    }


    /**
     * Save auto insert rule meta data
     */
    public function saveAutoInsertRule($post_id)
    {
        if (!Utilities::settingIsEnabled('auto_insert_blocks')) {
            return;
        }

        // Check if this is an auto insert rule
        if (get_post_type($post_id) !== 'advgb_insert_block') {
            return;
        }

        // Check nonce
        if (!isset($_POST['advgb_auto_insert_nonce']) || !wp_verify_nonce($_POST['advgb_auto_insert_nonce'], 'advgb_auto_insert_meta')) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save block selection
        if (isset($_POST['advgb_block_id'])) {
            update_post_meta($post_id, '_advgb_block_id', intval($_POST['advgb_block_id']));
        }

        // Save position settings
        $position = isset($_POST['advgb_position']) ? sanitize_text_field($_POST['advgb_position']) : 'beginning';
        if (!$this->proActive && !in_array($position, ['beginning', 'end'])) {
            $position = 'beginning';
        }
        update_post_meta($post_id, '_advgb_position', $position);

        // Save targeting options
        $post_types = isset($_POST['advgb_post_types']) ? array_map('sanitize_text_field', $_POST['advgb_post_types']) : ['post'];
        if (!$this->proActive) {
            $post_types = ['post'];
        }
        update_post_meta($post_id, '_advgb_post_types', $post_types);

        $taxonomies = isset($_POST['advgb_taxonomies']) ? $_POST['advgb_taxonomies'] : [];
        $sanitized_taxonomies = [];
        foreach ($taxonomies as $taxonomy => $terms) {
            if (!$this->proActive && !in_array($taxonomy, ['category', 'post_tag'])) {
                continue;
            }
            $sanitized_taxonomies[sanitize_text_field($taxonomy)] = array_map('intval', $terms);
        }
        update_post_meta($post_id, '_advgb_taxonomies', $sanitized_taxonomies);

        // Save rule settings
        if (isset($_POST['advgb_priority'])) {
            update_post_meta($post_id, '_advgb_priority', intval($_POST['advgb_priority']));
        }

        do_action('advgb_insert_block_by_rule_updated', $post_id, $_POST);
    }

    /**
     * AJAX endpoint for searching taxonomy terms
     */
    public function searchTaxonomyTerms()
    {
        check_ajax_referer('advgb_auto_insert_nonce', 'nonce');

        if (!current_user_can('administrator')) {
            wp_die('Unauthorized');
        }

        $taxonomy = isset($_GET['taxonomy']) ? sanitize_text_field($_GET['taxonomy']) : 'category';
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $args = [
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'number' => 20
        ];
        if (!empty($search)) {
            $args['search'] = $search;
        }
        $terms = get_terms($args);

        $results = [];
        foreach ($terms as $term) {
            $results[] = [
                'id' => $term->term_id,
                'text' => $term->name
            ];
        }

        wp_send_json_success($results);
    }

    /**
     * AJAX endpoint for searching users
     */
    public function searchUsers()
    {
        check_ajax_referer('advgb_auto_insert_nonce', 'nonce');

        if (!current_user_can('administrator')) {
            wp_die('Unauthorized');
        }

        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

        $args = [
            'search_columns' => ['user_login', 'user_nicename', 'user_email', 'display_name'],
            'number' => 20
        ];
        if (!empty($search)) {
            $args['search'] = '*' . $search . '*';
        }

        $users = get_users($args);

        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->ID,
                'text' => $user->display_name . ' (' . $user->user_login . ')'
            ];
        }

        wp_send_json_success($results);
    }

    /**
     * AJAX endpoint for searching blocks
     */
    public function searchBlocks()
    {
        check_ajax_referer('advgb_auto_insert_nonce', 'nonce');

        if (!current_user_can('administrator')) {
            wp_die('Unauthorized');
        }

        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

        $advgb_blocks_list = get_option('advgb_blocks_list');
        $results = [];
        if ($advgb_blocks_list && is_array($advgb_blocks_list)) {
            foreach ($advgb_blocks_list as $index => $block_details) {
                $block_title = !empty($block_details['title']) ? $block_details['title'] : $block_details['name'];
                if (empty($search) || stripos($block_title, $search) !== false) {
                    $results[] = [
                        'id' => $block_details['name'],
                        'text' => $block_title,
                    ];
                }
            }
        }

        wp_send_json_success($results);
    }

    /**
     * AJAX endpoint for searching posts
     */
    public function searchPosts()
    {
        check_ajax_referer('advgb_auto_insert_nonce', 'nonce');

        if (!current_user_can('administrator')) {
            wp_die('Unauthorized');
        }

        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $post_types = isset($_GET['post_types']) ? array_map('sanitize_text_field', $_GET['post_types']) : ['post'];

        $args = [
            'post_type' => $post_types,
            'post_status' => 'publish',
            'posts_per_page' => 20,
            's' => $search
        ];

        $posts = get_posts($args);

        $results = [];
        foreach ($posts as $post) {
            $post_type_obj = get_post_type_object($post->post_type);
            $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : $post->post_type;

            $results[] = [
                'id' => $post->ID,
                'text' => $post->post_title . ' (' . $post_type_label . ' #' . $post->ID . ')'
            ];
        }

        wp_send_json_success($results);
    }

    /**
     * Remove quick edit option.
     */
    public function remove_quick_edit_row()
    {
        $post_type = (!empty($_GET['post_type'])) ? sanitize_text_field($_GET['post_type']) : '';
        if ($post_type == 'advgb_insert_block'):
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('span.inline').remove();
                });
            </script>
            <?php
        endif;
    }

    /**
     * Add duplicate action to row actions
     */
    public function addDuplicateAction($actions, $post)
    {
        if ($post->post_type === 'advgb_insert_block') {
            $duplicate_url = wp_nonce_url(
                add_query_arg([
                    'action' => 'duplicate_auto_insert_block',
                    'post_id' => $post->ID
                ], admin_url('admin-post.php')),
                'duplicate_auto_insert_block_' . $post->ID
            );

            $actions['duplicate'] = sprintf(
                '<a href="%s" title="%s">%s</a>',
                esc_url($duplicate_url),
                esc_attr__('Duplicate this auto insert rule', 'advanced-gutenberg'),
                __('Duplicate', 'advanced-gutenberg')
            );
        }

        return $actions;
    }

    /**
     * Handle duplicate action
     */
    public function handleDuplicateAction()
    {
        if (!isset($_GET['action']) || $_GET['action'] !== 'duplicate_auto_insert_block') {
            return;
        }

        if (!isset($_GET['post_id']) || !isset($_GET['_wpnonce'])) {
            $this->addAdminNotice(__('Invalid request', 'advanced-gutenberg'), 'error');
            wp_safe_redirect(admin_url('edit.php?post_type=advgb_insert_block'));
            exit;
        }

        $post_id = intval($_GET['post_id']);
        $nonce = $_GET['_wpnonce'];

        if (!wp_verify_nonce($nonce, 'duplicate_auto_insert_block_' . $post_id)) {
            $this->addAdminNotice(__('Security check failed', 'advanced-gutenberg'), 'error');
            wp_safe_redirect(admin_url('edit.php?post_type=advgb_insert_block'));
            exit;
        }

        if (!current_user_can('edit_post', $post_id)) {
            $this->addAdminNotice(__('You do not have permission to duplicate this item', 'advanced-gutenberg'), 'error');
            wp_safe_redirect(admin_url('edit.php?post_type=advgb_insert_block'));
            exit;
        }

        $new_post_id = $this->duplicateAutoInsertBlock($post_id);

        if ($new_post_id) {
            $this->addAdminNotice(
                sprintf(
                    __('Auto insert block duplicated successfully. <a href="%s">Edit the new rule</a>', 'advanced-gutenberg'),
                    admin_url('post.php?post=' . $new_post_id . '&action=edit')
                ),
                'success'
            );
        } else {
            $this->addAdminNotice(__('Error duplicating auto insert block', 'advanced-gutenberg'), 'error');
        }

        wp_safe_redirect(admin_url('edit.php?post_type=advgb_insert_block'));
        exit;
    }

    /**
     * Remove the month filter dropdown
     */
    public function removeMonthFilters($months, $post_type)
    {
        if ($post_type === 'advgb_insert_block') {
            // Return empty to remove the dropdown
            return [];
        }
        return $months;
    }

    /**
     * Duplicate auto insert block with all meta data
     */
    private function duplicateAutoInsertBlock($post_id)
    {
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'advgb_insert_block') {
            return false;
        }

        // Create new post
        $new_post = [
            'post_title' => $post->post_title . ' (' . __('Copy', 'advanced-gutenberg') . ')',
            'post_content' => $post->post_content,
            'post_status' => 'draft',
            'post_type' => 'advgb_insert_block',
            'post_author' => get_current_user_id(),
        ];

        $new_post_id = wp_insert_post($new_post);

        if (is_wp_error($new_post_id)) {
            return false;
        }

        // Get all post meta
        $meta_keys = get_post_custom_keys($post_id);

        if (!empty($meta_keys)) {
            foreach ($meta_keys as $meta_key) {
                // Skip internal meta keys
                if ($meta_key === '_edit_lock' || $meta_key === '_edit_last') {
                    continue;
                }

                $meta_values = get_post_custom_values($meta_key, $post_id);
                foreach ($meta_values as $meta_value) {
                    $meta_value = maybe_unserialize($meta_value);
                    add_post_meta($new_post_id, $meta_key, $meta_value);
                }
            }
        }

        return $new_post_id;
    }

    /**
     * Add admin notice
     */
    private function addAdminNotice($message, $type = 'info')
    {
        $notices = get_transient('advgb_auto_insert_notices');
        if (!is_array($notices)) {
            $notices = [];
        }

        $notices[] = [
            'message' => $message,
            'type' => $type
        ];

        set_transient('advgb_auto_insert_notices', $notices, 30);
    }

    /**
     * Display admin notices
     */
    public function displayAdminNotices()
    {
        $notices = get_transient('advgb_auto_insert_notices');
        if (!empty($notices)) {
            foreach ($notices as $notice) {
                printf(
                    '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
                    esc_attr($notice['type']),
                    $notice['message']
                );
            }

            delete_transient('advgb_auto_insert_notices');
        }
    }
}