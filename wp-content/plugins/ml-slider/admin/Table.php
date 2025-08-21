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
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        if (isset($_REQUEST['s'])) {
            $table_data = $this->table_data(sanitize_text_field($_REQUEST['s']));
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

        $orderBy = $_GET['orderby'] ?? $global_settings['dashboardSort'] ?? 'ID';
        $orderBy = in_array($orderBy, $columns, true) ? $orderBy : 'ID';

        $order = $_GET['order'] ?? $global_settings['dashboardOrder'] ?? 'asc';

        $status = isset($_GET['post_status']) && 'trash' === $_GET['post_status'] ? 'trash' : 'publish';

        $query_results = $this->get_slideshow_query($status, $search);

        foreach ($query_results as &$each_slide) {
            $theme = get_post_meta($each_slide['ID'], 'metaslider_slideshow_theme', true);

            if ($theme && isset($theme['title'])) {
                $each_slide['slideshow_theme'] = $theme['title'];
            } else {
                $each_slide['slideshow_theme'] = '';
            }

            $slideshow_slides = $this->get_slides($each_slide['ID'], $status);
            $each_slide['slideshow_thumb'] = $slideshow_slides;
            $each_slide['slideshow_type'] = $this->get_slide_types($slideshow_slides);
            $each_slide['slide_count'] = count($slideshow_slides);
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
        $current = $_REQUEST['post_status'] ?? 'all';
        $base_url = remove_query_arg($parameters);
    
        // Helper function to generate view links
        $generate_view_link = function ($status, $label) use ($base_url, $current) {
            $count = $this->slideshow_count($status);
            if ($count == 0 && $status === 'trash') {
                return null;
            }
            $url = ($status === 'all') ? $base_url : add_query_arg('post_status', $status, $base_url);
            $class = ($current === $status) ? ' class="current"' : '';
            return "<a href='" . esc_url($url) . "' {$class}>" . esc_html__($label, 'ml-slider') . " ({$count})</a>";
        };
    
        $views['all'] = $generate_view_link('all', 'Published');
        
        if ($trash_link = $generate_view_link('trash', 'Trash')) {
            $views['trash'] = $trash_link;
        }
    
        return $views;
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
            'cb' => '<input type="checkbox" />',
            'slideshow_thumb' => esc_html__('Preview', 'ml-slider'),
            'post_title' => esc_html__('Title', 'ml-slider'),
            'slideshow_type' => esc_html__('Type of Slides', 'ml-slider'),
            'slide_count' => esc_html__('Number of Slides', 'ml-slider'),
            'slideshow_theme' => esc_html__('Theme', 'ml-slider'),
            'post_date' => esc_html__('Created', 'ml-slider'),
            'ID' => esc_html__('Shortcode', 'ml-slider')
        );

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
            $image = wp_get_attachment_image_src($slideId, 'thumbnail');
        } else {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($slideId), 'thumbnail');
        }

        if (isset($image[0])) {
            $slidethumb = "<img src='". esc_url($image[0]) ."'>";
        } else {
            $slidethumb = "<img src='". $logo ."' class='thumb-logo'>";
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

        $prepared_query = $wpdb->prepare($query, $params); // WPCS: unprepared SQL OK.
        return $wpdb->get_results($prepared_query, ARRAY_A); // WPCS: unprepared SQL OK.
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
            '<input type="checkbox" name="slideshows[]" value="%d" />', (int)$item['ID']
        );
    }

    public function column_slideshow_thumb($item)
    {
        $logo = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(dirname(__FILE__) . '/assets/metaslider.svg'));
        $placeholder = "<img src='". $logo ."' class='thumb-logo'>";
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
        $pages = $this->get_posts_using_slideshow($slideshow_id);
        
        if ($pages !== esc_html__('Not found.', 'ml-slider')) {
            return '<button class="open-modal button" data-id="' . esc_attr($slideshow_id) . '">'
                . esc_html__('View Usage', 'ml-slider') . '</button>
                <div class="modal-overlay" id="overlay-' . esc_attr($slideshow_id) . '" style="display: none;"></div>
                <div class="shortcode-modal bg-white shadow" id="modal-' . esc_attr($slideshow_id) . '" style="display: none;">
                    <div class="modal-content">
                        <span class="close-modal" data-id="' . esc_attr($slideshow_id) . '">&times;</span>
                        <h3 class="text-lg font-medium m-0 leading-6 text-gray-darkest">'
                            . esc_html__('Content Using This Slideshow', 'ml-slider') . '</h3>' 
                            . $pages . '
                    </div>
                </div>';
        } else {
            return esc_html__('Not found.', 'ml-slider');
        }
    }
    
    
    private function get_posts_using_slideshow($slideshow_id)
    {
        global $wpdb;
        $results = [];

        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_status = %s AND (post_content LIKE %s OR post_content REGEXP %s)",
                'publish',
                '%' . $wpdb->esc_like("[metaslider id=\"$slideshow_id\"") . '%',
                '\\[metaslider[^]]*id=[\"\\\']?' . $slideshow_id . '[\"\\\']?[^]]*\\]'
            )
        );

        // Organize by post type
        $grouped_results = [];
    
        foreach ($posts as $post) {
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
            $output .= "<h5>{$category}:</h5><ul>" . implode('', $items) . "</ul>";
        }
    
        return empty($output) ? esc_html__('Not found.', 'ml-slider') : $output;
    }          

    public function extra_tablenav( $which )
    {
		if ( $which == "top" ) {
            if (isset($_REQUEST['post_status']) && $_REQUEST['post_status'] == "trash") {
                if ( ! empty($this->table_data('', 'trash'))) {
                    submit_button( __( 'Empty Trash' ), 'apply', 'delete_all', false );
                }
            }
        }
	}

    public function check_num_rows()
    {
        $table_data = $this->table_data();
        return $table_data;
    }

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
                    $toArray = array($_REQUEST['slideshows']);
                    $slideshows = array_map('intval', $toArray);
                }
            } else {
                //single slider
                if(isset($_REQUEST['id'])) {
                    $toArray = array($_REQUEST['id']);
                    $slideshows = array_map('intval', $toArray);
                }
            }

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
