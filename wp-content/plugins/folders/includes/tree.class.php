<?php
/**
 * Class Folders Tree
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}

class WCP_Tree
{


    /**
     * Define the core functionality to shoe taxonomies
     *
     * @since 1.0.0
     */
    public function __construct()
    {

    }//end __construct()


    /**
     * Get tree data into taxonomies (Root Folder)
     *
     * @since  1.0.0
     * @access public
     * @return $categories
     */
    public static function get_full_tree_data($postType, $orderBy="", $order="")
    {
        $isAjax = (defined('DOING_AJAX') && DOING_AJAX) ? 1 : 0;
        $type   = filter_input(INPUT_GET, $postType);
        if ((isset($type) && !empty($type)) || ! $isAjax) {
            update_option("selected_".$postType."_folder", "");
        }

        return self::get_folder_category_data($postType, 0, 0, $orderBy, $order);

    }//end get_full_tree_data()


    /**
     * Get tree data into taxonomies (Child Folder)
     *
     * @since  1.0.0
     * @access public
     * @return $categories
     */
    public static function get_folder_category_data($postType, $parent=0, $parentStatus=0, $orderBy="", $order="")
    {

        $arg = [
            'taxonomy'              => $postType,
            'hide_empty'            => false,
            'parent'                => $parent,
            'hierarchical'          => false,
            'update_count_callback' => '_update_generic_term_count',
        ];
        if (!empty($orderBy) && !empty($order)) {
            $arg['orderby'] = $orderBy;
            $arg['order']   = $order;
        } else {
            $arg['orderby']    = 'meta_value_num';
            $arg['order']      = 'ASC';
            $arg['meta_query'] = [
                [
                    'key'  => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ],
            ];
        }

        $terms = get_terms($arg);

        $string        = "";
        $sticky_string = "";
        $child         = 0;
        $isAjax        = (defined('DOING_AJAX') && DOING_AJAX) ? 1 : 0;
        if (!empty($terms)) {
            $child = count($terms);
            foreach ($terms as $key => $term) {
                if (!empty($orderBy) && !empty($order)) {
                    update_term_meta($term->term_id, "wcp_custom_order", ($key + 1));
                }

                $status = get_term_meta($term->term_id, "is_active", true);
                $return = self::get_folder_category_data($postType, $term->term_id, $status, $orderBy, $order);
                $type   = filter_input(INPUT_GET, $postType);
                if ($postType == "attachment") {
                    if (isset($type) && $type == $term->slug) {
                        update_option("selected_".$postType."_folder", $term->term_id);
                    }

                    if (!isset($type) && $isAjax) {
                        $termId = get_option("selected_".$postType."_folder");
                    }
                } else {
                    if (isset($type) && $type == $term->slug) {
                        update_option("selected_".$postType."_folder", $term->term_id);
                    }

                    if (!isset($type) && $isAjax) {
                        $termId = get_option("selected_".$postType."_folder");
                    }
                }

                $count = ($term->trash_count != 0) ? $term->trash_count : 0;

                // Free/Pro URL Change
                $nonce     = wp_create_nonce('wcp_folder_term_'.$term->term_id);

                $folder_info    = get_term_meta($term->term_id, "folder_info", true);
                $folder_info = shortcode_atts([
                    'is_sticky' => 0,
                    'is_high'   => 0,
                    'is_locked' => 0,
                    'is_active' => 0,
                ], $folder_info);

                $status = intval($folder_info['is_high']);
                $is_active = intval($folder_info['is_active']);
                $is_sticky = intval($folder_info['is_sticky']);

                $class     = "";
                if ($is_sticky == 1) {
                    $class .= " is-sticky";
                }

                if ($status == 1) {
                    $class .= " is-high";
                }

                if ($is_active == 1) {
                    $class .= " jstree-open";
                }

                $string .= "<li id='".esc_attr($term->term_id)."' class='".esc_attr($class)."' data-slug='".esc_attr($term->slug)."' data-nonce='".esc_attr($nonce)."' data-folder='".esc_attr($term->term_id)."' data-child='".esc_attr($child)."' data-count='".esc_attr($count)."' data-parent='".esc_attr($parent)."'>
                                ".esc_attr($term->name)."
                                <ul>".$return['string']."</ul>
                            </li>";

                $sticky_string .= $return['sticky_string'];
            }//end foreach
        }//end if

        return [
            'string'        => $string,
            'sticky_string' => $sticky_string,
            'child'         => $child,
        ];

    }//end get_folder_category_data()


    /**
     * Get option data into taxonomies (Parent Folder)
     *
     * @since  1.0.0
     * @access public
     * @return $categories
     */
    public static function get_option_data_for_select($postType)
    {
        $string  = "<option value='0'>".esc_html__("Parent Folder", "folders")."</option>";
        $string .= self::get_folder_option_data($postType, 0, '');
        return $string;

    }//end get_option_data_for_select()


    /**
     * Get option data into taxonomies (Child Folder)
     *
     * @since  1.0.0
     * @access public
     * @return $categories
     */
    public static function get_folder_option_data($postType, $parent=0, $space="")
    {
        $terms = get_terms(
            [
                'taxonomy'     => $postType,
                'hide_empty'   => false,
                'parent'       => $parent,
                'orderby'      => 'meta_value_num',
                'order'        => 'ASC',
                'hierarchical' => false,
                'meta_query'   => [
                    [
                        'key'  => 'wcp_custom_order',
                        'type' => 'NUMERIC',
                    ],
                ],
            ]
        );

        $selected_term = get_option("selected_".$postType."_folder");

        $string = "";
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if(isset($term->term_id) && isset($term->name)) {
                    $selected = ($selected_term == $term->term_id) ? "selected" : "";
                    $string .= "<option " . esc_attr($selected) . " value='" . esc_attr($term->term_id) . "'>" . esc_attr($space) . esc_attr($term->name) . "</option>";
                    $string .= self::get_folder_option_data($postType, $term->term_id, trim($space) . "- ");
                }
            }
        }

        return $string;

    }//end get_folder_option_data()


}//end class
