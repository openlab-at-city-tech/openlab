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
        $slideshows_per_page = 10;
        $table_page = $this->get_pagenum();
        $this->items = array_slice($table_data, (($table_page - 1) * $slideshows_per_page), $slideshows_per_page);
        $total_slideshows = count($table_data);
        $this->set_pagination_args(array(
            'total_items' => $total_slideshows,
            'per_page'    => $slideshows_per_page,
            'total_pages' => ceil($total_slideshows/$slideshows_per_page)
        ));
    }

    protected function get_views() {
        global $wpdb;
        $views = array();
        $paramaters = array('action', 'slideshows', 'post_status', '_wpnonce', 'paged');
        $current = ( !empty($_REQUEST['post_status']) ? $_REQUEST['post_status'] : 'all');
   
        $all = remove_query_arg($paramaters);
        $class = ($current == 'all' ? ' class="current"' :'');
        $views['all'] = "<a href='" . esc_url($all) . "' {$class} >" . esc_html__('Published', 'ml-slider') . " (" . $this->slideshow_count('all') . ")</a>";
        
        if ($this->slideshow_count('trash') != 0) {
            $class = ($current == 'trash' ? ' class="current"' :'');
            $views['trash'] = "<a href='" . esc_url($all) . "&post_status=trash' {$class} >" . esc_html__('Trash', 'ml-slider') . " (" . $this->slideshow_count('trash') . ")</a>";
        }
        
        return $views;
    }

    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'slides' => esc_html__('Preview', 'ml-slider'),
            'post_title' => esc_html__('Title', 'ml-slider'),
            'post_date' => esc_html__('Created', 'ml-slider'),
            'ID' => esc_html__('Shortcode', 'ml-slider')
        );

        return $columns;
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


    public function get_hidden_columns()
    {
        return array();
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
          'post_title' => array('post_title',false),
          'post_date' => array('post_date',false),
          'ID' => array('ID',false)
        );
        return $sortable_columns;
    }

    public function column_default($item, $column_name)
    {
        switch($column_name) {
            case 'slides':
                return $this->slideshow_thumb($item[ 'ID' ]);
            case 'post_title':
                return $item[ $column_name ];
            case 'post_date':
                $date = strtotime($item[ $column_name ]);
                $dateFormat = get_option('date_format');
                $timeFormat = get_option( 'time_format' );
                return ucfirst( wp_date( $dateFormat.' \a\t '.$timeFormat, $date ) );
            case 'ID':
                return $this->shortcodeColumn($item[$column_name]);
            default:
                return print_r($item, true);
        }
    }

    public function shortcodeColumn($slideshowID)
    {
        return ('<pre class="copy-shortcode tipsy-tooltip" original-title="' . __('Click to copy shortcode.', 'ml-slider') . '"><div class="text-orange cursor-pointer whitespace-normal inline">[metaslider id="'. esc_attr($slideshowID) .'"]</div></pre><span class="copy-message" style="display:none;"><div class="dashicons dashicons-yes"></div></span>');
    }

    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="slideshows[]" value="%d" />', (int)$item['ID']
        );
    }

    protected function process_action()
    {
        if (isset($_POST['_wpnonce']) && ! empty($_POST['_wpnonce'])) {
            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_SPECIAL_CHARS );
            $action = 'bulk-' . $this->_args['plural'];
            if ( ! wp_verify_nonce($nonce, $action)) {
                wp_die( 'Cannot process action' );
            }
        }

        if (isset($_POST['delete_all'])) {
            $slideshows = $this->table_data('', 'trash');
            foreach($slideshows as $slideshow_id) {
                wp_delete_post($slideshow_id['ID'], true);
            }
        }

        if(isset($_GET['_wpnonce'])) {
            if ( ! wp_verify_nonce($_GET['_wpnonce'], 'metaslider-action')) {
                wp_die( 'Cannot process action' );
            }
        }

        $action = $this->current_action();
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
        return;
    }

    private function table_data($search='', $status='all')
    {
        global $wpdb;
        $wpdbTable = $wpdb->prefix . 'posts';
        $columns = ['slides', 'post_title', 'post_date'];
        $orderBy = isset($_GET['orderby']) && in_array($_GET['orderby'], $columns, true ) ? $_GET['orderby'] : 'ID';
        $order = isset($_GET['order']) && 'desc' === $_GET['order'] ? 'desc' : 'asc';
        $orderBySql = sanitize_sql_orderby( "{$orderBy} {$order}" );
        $status = isset($_GET['post_status']) && 'trash' === $_GET['post_status'] ? 'trash' : 'all';

        if (!empty($search)) {
            $slides_query = $wpdb->prepare("SELECT ID, post_title, post_date FROM $wpdbTable WHERE post_type = %s AND post_status = %s AND post_title LIKE %s ORDER BY $orderBySql", array('ml-slider', 'publish', '%'. $wpdb->esc_like($search). '%'));  // WPCS: unprepared SQL OK.
        } else {
            if( $status == 'all' ) {
                $slides_query = $wpdb->prepare("SELECT ID, post_title, post_date FROM $wpdbTable WHERE post_type = %s AND post_status = %s ORDER BY $orderBySql", array('ml-slider', 'publish'));  // WPCS: unprepared SQL OK.
            } else {
                $slides_query = $wpdb->prepare("SELECT ID, post_title, post_date FROM $wpdbTable WHERE post_type = %s AND post_status = %s ORDER BY $orderBySql", array('ml-slider', 'trash'));  // WPCS: unprepared SQL OK.
            }         
        }

        $query_results = $wpdb->get_results($slides_query, ARRAY_A ); // WPCS: unprepared SQL OK.
        return $query_results;
    }

    private function slideshow_count($status = 'all')
    {
        global $wpdb;
        $wpdbTable = $wpdb->prefix . 'posts';
        if ($status == 'trash') {
            $slides_query = $wpdb->prepare("SELECT ID, post_title, post_date FROM $wpdbTable WHERE post_type = %s AND post_status = %s", array('ml-slider', 'trash'));  // WPCS: unprepared SQL OK.
        } else {
            $slides_query = $wpdb->prepare("SELECT ID, post_title, post_date FROM $wpdbTable WHERE post_type = %s AND post_status = %s", array('ml-slider', 'publish'));  // WPCS: unprepared SQL OK.
        }

        $wpdb->get_results($slides_query, ARRAY_A ); // WPCS: unprepared SQL OK.
        return $wpdb->num_rows;
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

    public function get_slides($slideshowId, $status)
    {
        $slides = get_posts(array(
            'post_type' => array('ml-slide'),
            'post_status' => array($status),
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

    public function slideshow_thumb($slideshowId)
    {
        $slides = $this->get_slides($slideshowId, 'publish');
        $numberOfSlides = count($slides);
        $logo = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(dirname(__FILE__) . '/assets/metaslider.svg'));
        $thumbHtml = "<div class='w-16 h-16 bg-gray-light'>";
        if ($numberOfSlides === 0) {
            $thumbHtml .= "<img src='". $logo ."' class='thumb-logo'>";
        } else {
            if ($numberOfSlides === 1){
                $thumbHtml .= $this->getslide_thumb($slides[0]->ID);
            } else {
                $thumbHtml .= "<div class='relative w-16 h-16 image-wrap'>";
                foreach ($slides as $key => $slide) {
                    $thumbHtml .= "<div class='bg-gray-light absolute block inset-0 transition-all duration-1000 ease-linear autoplay'>";
                    $thumbHtml .= $this->getslide_thumb($slide->ID);
                    $thumbHtml .= "</div>";
                }
                $thumbHtml .= "</div>";
            }        
        }
        $thumbHtml .= "</div>";
        return $thumbHtml;
    }

    public function column_post_title($item)
    {
        $page = empty($_REQUEST['page']) ? 'metaslider' : sanitize_key($_REQUEST['page']);
        if(isset($_GET['post_status']) && $_GET['post_status'] == 'trash') {
            $restoreUrl = wp_nonce_url('?page=' . $page . '&post_status=trash&action=restore&slideshows=' . absint($item['ID']), 'metaslider-action' );
            $deleteUrl = wp_nonce_url('?page=' . $page . '&post_status=trash&action=permanent&slideshows=' . absint($item['ID']), 'metaslider-action' );

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
            $deleteUrl = wp_nonce_url('?page=' . $page . '&action=delete&slideshows=' . absint($item['ID']), 'metaslider-action' );

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

    public function extra_tablenav( $which ) {
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
}
