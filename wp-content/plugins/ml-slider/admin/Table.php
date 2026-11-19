<?php
if (!defined('ABSPATH')) {
    die('No direct access.');
}
if (!class_exists('WP_List_table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class MetaSlider_Admin_Table extends WP_List_table
{
    public function prepare_items()
    {
        $this->process_action();

        // Check if we filter by theme
        $nonce_valid = isset($_REQUEST['search_wpnonce']) && wp_verify_nonce(sanitize_key($_REQUEST['search_wpnonce']), 'metaslider_search_slideshows');
        if ($nonce_valid && isset($_POST['metaslider_theme'])) {
            $theme_param = sanitize_text_field($_POST['metaslider_theme']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- only used as the base URL for add_query_arg()/remove_query_arg() below, which handle escaping.
            $current_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            if ($theme_param !== '') {
                $_GET['metaslider_theme'] = $theme_param;
                $_SERVER['REQUEST_URI'] = add_query_arg('metaslider_theme', $theme_param, $current_uri);
            } else {
                unset($_GET['metaslider_theme']);
                $_SERVER['REQUEST_URI'] = remove_query_arg('metaslider_theme', $current_uri);
            }
        }
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        if (isset($_REQUEST['s'])) {
            $table_data = $this->table_data(sanitize_text_field(wp_unslash($_REQUEST['s'])));
        } else {
            $table_data = $this->table_data();
        }

        //pagination
        $global_settings = get_option( 'metaslider_global_settings' );
        $slideshows_per_page = isset($global_settings['dashboardItems']) ? $global_settings['dashboardItems'] : 10;
        $table_page = $this->get_pagenum();
        $this->items = array_slice($table_data, (($table_page - 1) * $slideshows_per_page), $slideshows_per_page);
        $total_slideshows = count($table_data);
        $this->set_pagination_args(array(
            'total_items' => $total_slideshows,
            'per_page'    => $slideshows_per_page,
            'total_pages' => ceil($total_slideshows/$slideshows_per_page)
        ));
    }

    private function table_data($search='', $status='publish')
    {
        global $wpdb;
        $wpdbTable = $wpdb->prefix . 'posts';
        $columns = ['slides', 'post_title', 'post_date', 'slide_count', 'slideshow_type', 'slideshow_theme'];
        $global_settings = get_option( 'metaslider_global_settings' );

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- validated against the $columns allow-list on the next line.
        $orderBy = $_GET['orderby'] ?? $global_settings['dashboardSort'] ?? 'ID';
        $orderBy = in_array($orderBy, $columns, true) ? $orderBy : 'ID';

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- only ever compared against the literal 'asc' below.
        $order = $_GET['order'] ?? $global_settings['dashboardOrder'] ?? 'asc';

        $status = isset($_GET['post_status']) && 'trash' === $_GET['post_status'] ? 'trash' : 'publish';

        $query_results = $this->get_slideshow_query($status, $search);

        foreach ($query_results as &$each_slide) {
            $theme = get_post_meta($each_slide['ID'], 'metaslider_slideshow_theme', true);

            if ($theme && isset($theme['title'])) {
                $each_slide['slideshow_theme'] = $theme['title'];
                $each_slide['slideshow_theme_folder'] = $theme['folder'] ?? '';
            } else {
                $each_slide['slideshow_theme'] = '';
                $each_slide['slideshow_theme_folder'] = '';
            }

            $slideshow_slides = $this->get_slides($each_slide['ID'], $status);
            $each_slide['slideshow_thumb'] = $slideshow_slides;
            $each_slide['slideshow_type'] = $this->get_slide_types($slideshow_slides);
            $each_slide['slide_count'] = count($slideshow_slides);
        }

        $theme_filter = isset($_REQUEST['metaslider_theme']) ? sanitize_text_field($_REQUEST['metaslider_theme']) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        if ($theme_filter !== '') {
            $query_results = array_values(array_filter($query_results, function ($slide) use ($theme_filter) {
                return $theme_filter === '__none__'
                    ? $slide['slideshow_theme_folder'] === ''
                    : $slide['slideshow_theme_folder'] === $theme_filter;
            }));
        }

        if ($orderBy === 'slide_count') {
            if ($order === 'asc') {
                usort($query_results, function($a, $b) {
                    return $a['slide_count'] - $b['slide_count'];
                });
            } else {
                usort($query_results, function($a, $b) {
                    return $b['slide_count'] - $a['slide_count'];
                });
            }
        } elseif ($orderBy === 'post_date') {
            usort($query_results, function($a, $b) use ($orderBy, $order) {
                $aDate = !empty($a[$orderBy]) ? strtotime($a[$orderBy]) : 0;
                $bDate = !empty($b[$orderBy]) ? strtotime($b[$orderBy]) : 0;
                return ($order === 'asc') 
                    ? $aDate - $bDate
                    : $bDate - $aDate;
            });
        } else {
            usort($query_results, function($a, $b) use ($orderBy, $order) {
                return ($order === 'asc') ? strcmp($a[$orderBy], $b[$orderBy]) : strcmp($b[$orderBy], $a[$orderBy]);
            });
        }

        return $query_results;
    }

    public function no_items()
    {
        if (!empty($_GET['post_status']) && $_GET['post_status'] === 'trash') {
            esc_html_e(
                'You don\'t have any trashed slideshows.',
                'ml-slider'
            );
        } else {
            printf(
                esc_html__(
                    'You don\'t have any slideshows yet. Click %shere%s to create a new slideshow.',
                    'ml-slider'
                ),
                '<a href="' . esc_url(wp_nonce_url(admin_url("admin-post.php?action=metaslider_create_slider"), "metaslider_create_slider")) . '">','</a>'
            );
        }
        
    }   

    protected function get_views() {
        global $wpdb;
        
        $views = [];
        $parameters = ['action', 'slideshows', 'post_status', '_wpnonce', 'paged'];
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- only ever compared against fixed status strings below.
        $current = $_REQUEST['post_status'] ?? 'all';
        $base_url = remove_query_arg($parameters);
    
        $views['all'] = $this->generate_view_link($base_url, $current, 'all', 'Published');
        
        if ($trash_link = $this->generate_view_link($base_url, $current, 'trash', 'Trash')) {
            $views['trash'] = $trash_link;
        }
    
        return $views;
    }

    /**
     * Generate a status filter link for the slideshow table.
     *
     * @param string $base_url Base admin URL.
     * @param string $current Current status filter.
     * @param string $status Status represented by the link.
     * @param string $label Link label.
     * @return string|null
     */
    private function generate_view_link($base_url, $current, $status, $label)
    {
        $count = $this->slideshow_count($status);
        if ($count == 0 && $status === 'trash') {
            return null;
        }
        $url = ($status === 'all') ? $base_url : add_query_arg('post_status', $status, $base_url);
        $class = ($current === $status) ? ' class="current"' : '';
        return "<a href='" . esc_url($url) . "' {$class}>" . esc_html__($label, 'ml-slider') . " ({$count})</a>";
    }
    
    private function slideshow_count($status = 'all')
    {
        if ($status === 'all') {
            $status = 'publish';
        }
        $results = $this->get_slideshow_query($status);
        return count($results);
    }

    public function get_bulk_actions()
    {
        if (isset($_REQUEST['post_status']) && $_REQUEST['post_status'] == "trash") {
            $actions = array(
                'restore'    => __('Restore', 'ml-slider'),
                'permanent'  => __('Delete Permanently', 'ml-slider')
            );
        } else {
            $actions = array(
                'delete'    => __('Trash', 'ml-slider')
            );
        }
        
        return $actions;
    }

    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" aria-label="' . esc_attr__('Select all slideshows', 'ml-slider') . '" />',
            'slideshow_thumb' => esc_html__('Preview', 'ml-slider'),
            'post_title' => esc_html__('Title', 'ml-slider'),
            'slideshow_type' => esc_html__('Type of Slides', 'ml-slider'),
            'slide_count' => esc_html__('Number of Slides', 'ml-slider'),
            'slideshow_theme' => esc_html__('Theme', 'ml-slider'),
            'post_date' => esc_html__('Created', 'ml-slider'),
            'ID' => esc_html__('Shortcode', 'ml-slider')
        );

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- only ever compared against the literal 'trash' below.
        $status = $_REQUEST['post_status'] ?? 'publish';
        if ($status !== 'trash') {
            $columns['used_on'] = __('Usage', 'ml-slider');
        }

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
          'post_title' => array('post_title', false),
          'post_date' => array('post_date', false),
          'slideshow_type' => array('slideshow_type',false),
          'slide_count' => array('slide_count',false),
          'slideshow_theme' => array('slideshow_theme',false)
        );
        return $sortable_columns;
    }

    public function getslide_thumb($slideId)
    {
        $logo = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(dirname(__FILE__) . '/assets/metaslider.svg'));
        if (get_post_type($slideId) == 'attachment') {
            $thumbnail_id = $slideId;
        } else {
            $thumbnail_id = get_post_thumbnail_id($slideId);
        }

        $image = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');

        if (isset($image[0])) {
            // File-name titles make poor alt text, so only the image's own alt text is used
            $image_alt  = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
            $slidethumb = "<img src='". esc_url($image[0]) ."' alt='" . esc_attr($image_alt) . "'>";
        } else {
            $slidethumb = "<img src='". $logo ."' class='thumb-logo' alt=''>";
        }
        return $slidethumb;
    }

    private function get_slideshow_query($status = 'publish', $search = '') {
        global $wpdb;
        $wpdbTable = $wpdb->prefix . 'posts';
        
        $query = "SELECT ID, post_title, post_date FROM $wpdbTable WHERE post_type = %s AND post_status = %s";
        $params = ['ml-slider', $status];
    
        if (!empty($search)) {
            $query .= " AND post_title LIKE %s";
            $params[] = '%' . $wpdb->esc_like($search) . '%';
        }

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query fragments are fixed SQL above, with all dynamic values passed through $wpdb->prepare().
        $prepared_query = $wpdb->prepare($query, $params);
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $prepared_query is prepared immediately above.
        return $wpdb->get_results($prepared_query, ARRAY_A);
    }

    public function get_slides($slideshowId, $status)
    {
        $post_status = $status === 'trash' ? array('trash', 'publish') : array($status);
        $slides = get_posts(array(
            'post_type' => array('ml-slide'),
            'post_status' => $post_status,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'lang' => '',
            'suppress_filters' => 1,
            // phpcs:ignore WordPressVIPMinimum.Performance.NoPaging.posts_per_page_posts_per_page
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'ml-slider',
                    'field' => 'slug',
                    'terms' => (int) $slideshowId
                )
            )
        ));

        return $slides;
    }

    public function get_slide_types($slides)
    {
        $slide_list = array();
        foreach ($slides as $slide) {
            $post_title = explode(' - ', $slide->post_title);
            $add_space = str_replace('_', ' ', $post_title[1]);
            if($add_space == 'html overlay') {
                $slide_list[] = 'Layer';
            } else {
                $slide_list[] = ucwords($add_space);
            }
        }
        $final_list = array_values(array_unique($slide_list));
        $type_list = '';
        foreach ($final_list as $list) {
            $type_list .= $list . '<br>';
        }
        return $type_list;
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="slideshows[]" value="%d" aria-label="%s" />',
            (int)$item['ID'],
            /* translators: %s: slideshow title */
            esc_attr(sprintf(__('Select %s', 'ml-slider'), $item['post_title']))
        );
    }

    public function column_slideshow_thumb($item)
    {
        $logo = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(dirname(__FILE__) . '/assets/metaslider.svg'));
        $placeholder = "<img src='". $logo ."' class='thumb-logo' alt=''>";
        $numberOfSlides = count($item['slideshow_thumb']);

        $thumbHtml = "<div class='w-16 h-16 bg-gray-light slidethumb'>";
        if ($numberOfSlides === 0) {
            $thumbHtml .= $placeholder;
        } else {
            if ($numberOfSlides === 1){
                $thumbHtml .= isset($item['slideshow_thumb']->ID) 
                            ? $this->getslide_thumb($item['slideshow_thumb']->ID) : $placeholder;
            } else {
                foreach ($item['slideshow_thumb'] as $thumb) {
                    $thumbHtml .= isset($thumb->ID) ? $this->getslide_thumb($thumb->ID) : $placeholder;
                }
            }        
        }
        $thumbHtml .= "</div>";
        return $thumbHtml;
    }

    public function column_post_title($item)
    {
        $page = empty($_REQUEST['page']) ? 'metaslider' : sanitize_key($_REQUEST['page']);
        if(isset($_GET['post_status']) && $_GET['post_status'] == 'trash') {
            $restoreUrl = wp_nonce_url('?page=' . $page . '&post_status=trash&action=restore&slideshows=' . absint($item['ID']), 'bulk-' . $this->_args['plural'] );
            $deleteUrl = wp_nonce_url('?page=' . $page . '&post_status=trash&action=permanent&slideshows=' . absint($item['ID']), 'bulk-' . $this->_args['plural'] );

            $actions = [
                'restore' => '<a href="' . esc_url($restoreUrl) . '">' . esc_html__('Restore', 'ml-slider') . '</a>',
                'permanent'  => '<a class="submitdelete" href="' . esc_url($deleteUrl) . '">' . esc_html__('Delete Permanently', 'ml-slider') . '</a>',
            ];

            return sprintf(
                '%1$s %2$s',
                '<a class="row-title">' . esc_html($item['post_title']) . '</a>',
                $this->row_actions($actions)
            );
        } else {
            $editUrl = '?page=' . $page . '&id=' . absint($item['ID']);
            $deleteUrl = wp_nonce_url('?page=' . $page . '&action=delete&slideshows=' . absint($item['ID']), 'bulk-' . $this->_args['plural'] );

            $actions = [
                'edit' => '<a href="' . esc_url($editUrl) . '">' . esc_html__('Edit', 'ml-slider') . '</a>',
                'trash'  => '<a class="submitdelete" href="' . esc_url($deleteUrl) . '">' . esc_html__('Trash', 'ml-slider') . '</a>',
            ];

            return sprintf(
                '%1$s %2$s',
                '<a class="row-title" href="' . esc_url($editUrl) . '">' . esc_html($item['post_title']) . '</a>',
                $this->row_actions($actions)
            );
        }
    }

    public function column_slideshow_type($item)
    {
        return $item['slideshow_type'];
    }

    public function column_slide_count($item)
    {
        return $item['slide_count'];
    }

    public function column_slideshow_theme($item)
    {
        return $item['slideshow_theme'];
    }

    public function column_post_date($item)
    {
        $date = strtotime($item['post_date']);
        $dateFormat = get_option('date_format');
        $timeFormat = get_option( 'time_format' );
        return ucfirst( wp_date( $dateFormat.' \a\t '.$timeFormat, $date ) );
    }

    public function column_ID($item)
    {
        return ('<pre class="copy-shortcode tipsy-tooltip" original-title="' . __('Click to copy shortcode.', 'ml-slider') . '"><div class="text-orange cursor-pointer whitespace-normal inline">[metaslider id="'. esc_attr($item['ID']) .'"]</div></pre><span class="copy-message" style="display:none;"><div class="dashicons dashicons-yes"></div></span>');
    }

    public function column_used_on($item)
    {
        $slideshow_id = $item['ID'];
        $global_settings = metaslider_global_settings();
        // Load usage on click, the lookup can be slow on large sites (#2613)
        $on_click = !empty($global_settings['dashboardUsageOnClick']);
        $pages = $on_click ? '' : self::get_posts_using_slideshow($slideshow_id);
        
        if ($on_click || $pages !== esc_html__('Not found.', 'ml-slider')) {
            return '<button class="open-modal button" data-id="' . esc_attr($slideshow_id) . '">'
                . esc_html__('View Usage', 'ml-slider') . '</button>
                <div class="modal-overlay" id="overlay-' . esc_attr($slideshow_id) . '" style="display: none;"></div>
                <div class="shortcode-modal bg-white shadow" id="modal-' . esc_attr($slideshow_id) . '" style="display: none;">
                    <div class="modal-content">
                        <span class="close-modal" data-id="' . esc_attr($slideshow_id) . '">&times;</span>
                        <h3 class="text-lg font-medium m-0 leading-6 text-gray-darkest">'
                            . esc_html__('Content Using This Slideshow', 'ml-slider') . '</h3>
                            <div class="usage-results">' . $pages . '</div>
                    </div>
                </div>';
        } else {
            return esc_html__('Not found.', 'ml-slider');
        }
    }
    
    
    /**
     * @since 3.114.1 Public and static, so the "View Usage" AJAX request can use it (#2613)
     */
    public static function get_posts_using_slideshow($slideshow_id)
    {
        global $wpdb;
        $results = [];

        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title, post_type, post_content FROM {$wpdb->posts} WHERE post_status = %s AND post_content LIKE %s",
                'publish',
                '%' . $wpdb->esc_like('[metaslider') . '%'
            )
        );

        // Organize by post type
        $grouped_results = [];
    
        foreach ($posts as $post) {
            // REGEXP in SQL is too slow on large sites, so match the ID here (#2613)
            if (!preg_match('/\[metaslider[^]]*id=["\']?' . $slideshow_id . '["\']?[^]]*]/', $post->post_content)) {
                continue;
            }
            $post_type_obj = get_post_type_object($post->post_type);
            $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : ucfirst($post->post_type);
    
            $grouped_results[$post_type_label][] = sprintf(
                '<li><a href="%s" target="_blank">%s</a></li>',
                esc_url(get_permalink($post->ID)),
                esc_html($post->post_title)
            );
        }

        $theme_mods = get_option('theme_mods_' . get_option('stylesheet'), []);
        if (is_array($theme_mods)) {
            foreach ($theme_mods as $key => $value) {
                if (is_string($value) && preg_match('/\[metaslider[^]]*id=["\']?' . $slideshow_id . '["\']?[^]]*]/', $value)) {
                    $grouped_results[esc_html__('Theme Setting', 'ml-slider')][] = "<li>" . esc_html($key) . "</li>";
                }
            }
        }

        $all_options = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_value LIKE %s",
                '%' . $wpdb->esc_like("[metaslider id=\"$slideshow_id\"") . '%'
            )
        );
    
        foreach ($all_options as $option) {
            if (is_string($option->option_value) && preg_match('/\[metaslider[^]]*id=["\']?' . $slideshow_id . '["\']?[^]]*]/', $option->option_value)) {
                $grouped_results[esc_html__('Option', 'ml-slider')][] = "<li>" . esc_html($option->option_name) . "</li>";
            }
        }

        $output = '';
        foreach ($grouped_results as $category => $items) {
            $output .= "<h4>{$category}:</h4><ul>" . implode('', $items) . "</ul>";
        }
    
        return empty($output) ? esc_html__('Not found.', 'ml-slider') : $output;
    }          

    public function extra_tablenav( $which )
    {
        if ( $which == "top" ) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- output through esc_attr() below.
            echo '<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page'] ?? 'metaslider') . '">';
            $this->render_filters();
            if (isset($_REQUEST['post_status']) && $_REQUEST['post_status'] == "trash") {
                if ( ! empty($this->table_data('', 'trash'))) {
                    submit_button( __( 'Empty Trash' ), 'apply', 'delete_all', false );
                }
            }
        }
    }

    /**
     * Renders table nav filter dropdowns; currently includes theme, extendable for future filters.
     * 
     * @since 3.110.0
     * 
     * @return void
     */
    private function render_filters()
    {
        $status = (isset($_GET['post_status']) && 'trash' === $_GET['post_status']) ? 'trash' : 'publish';
        $slideshows = $this->get_slideshow_query($status);

        $themes = [];
        $has_no_theme = false;
        foreach ($slideshows as $slideshow) {
            $theme = get_post_meta($slideshow['ID'], 'metaslider_slideshow_theme', true);
            if ($theme && isset($theme['folder'], $theme['title'])) {
                $themes[$theme['folder']] = $theme['title'];
            } else {
                $has_no_theme = true;
            }
        }

        if (empty($themes) && !$has_no_theme) return;

        asort($themes);

        $current = isset($_REQUEST['metaslider_theme']) ? sanitize_text_field($_REQUEST['metaslider_theme']) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash

        echo '<div class="alignleft actions">';
        echo '<select name="metaslider_theme" id="metaslider-theme-filter">';
        echo '<option value="">' . esc_html__('All Themes', 'ml-slider') . '</option>';
        foreach ($themes as $folder => $title) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($folder),
                selected($current, $folder, false),
                esc_html($title)
            );
        }
        if ($has_no_theme) {
            printf(
                '<option value="__none__"%s>%s</option>',
                selected($current, '__none__', false),
                esc_html__('(No Theme)', 'ml-slider')
            );
        }
        echo '</select> ';
        submit_button(__('Filter', 'ml-slider'), 'button', 'filter_action', false);
        echo '</div>';
    }

    public function check_num_rows()
    {
        $table_data = $this->table_data();
        return $table_data;
    }

    /**
     * Trash, restore or permanently delete the selected slideshows
     *
     * @since 3.114.0 Only acts on slideshows the user can delete
     */
    protected function process_action()
    {
        $action = $this->current_action();

        if (isset($_POST['delete_all'])
            || ($action && in_array($action, array('delete', 'restore', 'permanent')))
        ) {
            // Check nonce
            if (! isset($_REQUEST['_wpnonce']) 
                || empty($_REQUEST['_wpnonce'])
                || ! wp_verify_nonce(
                    sanitize_key($_REQUEST['_wpnonce']), 
                    'bulk-' . $this->_args['plural']
                )
            ) {
                wp_die('Cannot process action', 'ml-slider');
            }

            if (isset($_POST['delete_all'])) {
                $slideshows = $this->table_data('', 'trash');
                foreach($slideshows as $slideshow_id) {
                    wp_delete_post($slideshow_id['ID'], true);
                }

                return;
            }
    
            if(isset($_REQUEST['slideshows'])) {

                if(is_array($_REQUEST['slideshows'])) {
                    $slideshows = array_map('intval', $_REQUEST['slideshows']);
                } else {
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- intval() cast on the next line.
                    $toArray = array($_REQUEST['slideshows']);
                    $slideshows = array_map('intval', $toArray);
                }
            } else {
                //single slider
                if(isset($_REQUEST['id'])) {
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- intval() cast on the next line.
                    $toArray = array($_REQUEST['id']);
                    $slideshows = array_map('intval', $toArray);
                }
            }

            // Only act on slideshows the user can delete, never on other posts
            $slideshows = array_filter(isset($slideshows) ? $slideshows : array(), function ($slideshow_id) {
                return 'ml-slider' === get_post_type($slideshow_id) && current_user_can('delete_post', $slideshow_id);
            });

            switch ( $action ) {
                case 'delete':
                    foreach($slideshows as $slideshow_id) {
                        wp_update_post(array(
                            'ID' => $slideshow_id,
                            'post_status' => 'trash'
                        ));
                    }
                    break;
                case 'permanent':
                    foreach($slideshows as $slideshow_id) {
                        wp_delete_post( $slideshow_id, true);
                    }
                    break;
                case 'restore':
                    foreach($slideshows as $slideshow_id) {
                        wp_update_post(array(
                            'ID' => $slideshow_id,
                            'post_status' => 'publish'
                        ));
    
                        $slides = $this->get_slides($slideshow_id, 'trash');
                        foreach ($slides as $key => $slide) {
                            wp_update_post(array(
                                'ID' => $slide->ID,
                                'post_status' => 'publish'
                            ));
                        }
                    }
                    break;
                default:
                    return;
                    break;
            }
        }

        return;
    }
}
