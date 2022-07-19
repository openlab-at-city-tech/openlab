<?php
if(!defined('ABSPATH')) exit;
class WCP_Tree {

    public function __construct() {

    }

    public static function get_full_tree_data($post_type, $order_by = "", $order = "") {
        $isAjax = (defined('DOING_AJAX') && DOING_AJAX)?1:0;
        $type = filter_input(INPUT_GET, $post_type, FILTER_SANITIZE_STRING);
        if((isset($type) && !empty($type)) || ! $isAjax) {
            update_option("selected_" . $post_type . "_folder", "");
        }
        return self::get_folder_category_data($post_type, 0, 0, $order_by, $order);
    }

    public static function get_folder_category_data($post_type, $parent = 0, $parentStatus = 0, $order_by = "", $order = "") {

        $arg = array(
            'hide_empty' => false,
            'parent'   => $parent,
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count',
        );
        if(!empty($order_by) && !empty($order)) {
            $arg['orderby'] = $order_by;
            $arg['order'] = $order;
        } else {
            $arg['orderby'] = 'meta_value_num';
            $arg['order'] = 'ASC';
            $arg['meta_query'] = [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]];
        }

        $terms = get_terms( $post_type, $arg);

        $string = "";
        $sticky_string = "";
        $child = 0;
        $isAjax = (defined('DOING_AJAX') && DOING_AJAX)?1:0;
        if(!empty($terms)) {
            $child = count($terms);
            foreach($terms as $key=>$term) {

                if(!empty($order_by) && !empty($order)) {
                    update_term_meta($term->term_id, "wcp_custom_order", ($key+1));
                }

                $is_sticky = get_term_meta($term->term_id, "is_folder_sticky", true);

                $status = get_term_meta($term->term_id, "is_active", true);
                $return = self::get_folder_category_data($post_type, $term->term_id, $status, $order_by, $order);
                $class = ($status == 1 && $return['child']>0)?"active":"";
                $class .= ($return['child'])>0?" has-sub-tree":"";
                $term_var = filter_input(INPUT_GET, "term", FILTER_SANITIZE_STRING);
                $type = filter_input(INPUT_GET, $post_type, FILTER_SANITIZE_STRING);
                if($post_type == "attachment") {
                    $class .= (isset($term_var) && $term_var == $term->slug)?" active-item active-term":"";
                    if(isset($type) && $type == $term->slug) {
                        update_option("selected_".$post_type."_folder", $term->term_id);
                    }
                    if(!isset($type) && $isAjax) {
                        $termId = get_option("selected_".$post_type."_folder");
                        $class .= ($termId == $term->term_id)?" active-item active-term":"";
                    }
                } else {
                    $class .= (isset($type) && $type == $term->slug)?" active-item active-term":"";
                    if(isset($type) && $type == $term->slug) {
                        update_option("selected_" . $post_type . "_folder", $term->term_id);
                    }
                    if(!isset($type) && $isAjax) {
                        $termId = get_option("selected_".$post_type."_folder");
                        $class .= ($termId == $term->term_id)?" active-item active-term":"";
                    }
                }
                $status = get_term_meta($term->term_id, "is_highlighted", true);
                $class .= ($status == 1)?" is-high":"";
                $sticky_class = ($status == 1)?"is-high":"";
                $count = ($term->trash_count != 0)?$term->trash_count:0;
                if($is_sticky == 1) {
                    $class .= " is-sticky";
                }

                $count_sticky = ($term->trash_count != 0)?"<span class='folder-count'>{$term->trash_count}</span>":"";

                if($is_sticky == 1) {
                    //$sticky_string .= "<li data-folder-id='{$term->term_id}' class='sticky-fldr {$sticky_class} sticky-folder-{$term->term_id}'><a href='javascript:;'><span class='folder-title'>{$term->name}</span>{$count_sticky}<span class='update-inline-record'><i class='pfolder-edit-folder'></i></span><span class='star-icon'><i class='pfolder-star'></i></span></a></li>";
                }

                /* Free/Pro URL Change*/
//                $string .= "<li data-nonce='{$term_nonce}' data-star='{$highlight_nonce}' data-rename='{$rename_nonce}' data-delete='{$delete_nonce}' data-slug='{$term->slug}' class='ui-state-default route wcp_folder_{$term->term_id} {$class}' id='wcp_folder_{$term->term_id}' data-folder-id='{$term->term_id}'>";
//                $string .= "<h3 class='title' title='{$term->name}' id='title_{$term->term_id}'>";
//                $string .= "<span class='ui-icon'>";
//                $string .= "<i class='wcp-icon pfolder-folder-close'></i>";
//                $string .= "<input type='checkbox' class='checkbox' value='{$term->term_id}' />";
//                $string .= "</span>";
//                $string .= "<span class='title-text'>{$term->name}</span>";
//                $string .= "{$count} <span class='update-inline-record'><i class='pfolder-edit-folder'></i></span> <span class='star-icon'><i class='pfolder-star'></i></span>";
//                $string .= "<span class='folder-sticky-icon'><i class='pfolder-pin'></i></span>";
//                //$string .= "<span class='move-folder-icon' ><i class='pfolder-move'></i></span>";
//                $string .= "</h3>";
//                $string .= "<span class='nav-icon'><i class='wcp-icon pfolder-arrow-down'></i></span>";
//                $string .= "<ul class='space' id='space_{$term->term_id}'>";
//                $string .= $return['string'];
//                $string .= "</ul></li>";

//                $parent = (empty($parent))?"#":$parent;
                $nonce = wp_create_nonce('wcp_folder_term_'.$term->term_id);
                $is_sticky = get_term_meta($term->term_id, "is_folder_sticky", true);
                $status = get_term_meta($term->term_id, "is_highlighted", true);
                $is_active = get_term_meta($term->term_id, "is_active", true);
                $class = "";
                if($is_sticky == 1) {
                    $class .= " is-sticky";
                }
                if($status == 1) {
                    $class .= " is-high";
                }
                if($is_active == 1) {
                    $class .= " jstree-open";
                }

                $string .= "<li id='{$term->term_id}' class='{$class}' data-slug='{$term->slug}' data-nonce='{$nonce}' data-folder='{$term->term_id}' data-child='{$child}' data-count='{$count}' data-parent='{$parent}'>
                                {$term->name}
                                <ul>{$return['string']}</ul>
                            </li>";

                $sticky_string .= $return['sticky_string'];
            }
        }
        return array(
            'string' =>$string,
            'sticky_string' =>$sticky_string,
            'child' => $child
        );
    }

    public static function get_option_data_for_select($post_type) {
        $string = "<option value='0'>Parent Folder</option>";
        $string .=  self::get_folder_option_data($post_type, 0, '');
        return $string;
    }

    public static function get_folder_option_data($post_type, $parent = 0, $space = "") {
        $terms = get_terms( $post_type, array(
            'hide_empty' => false,
            'parent'   => $parent,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'meta_query' => [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]]
        ) );

        $selected_term = get_option("selected_" . $post_type . "_folder");

        $string = "";
        if(!empty($terms)) {
            foreach($terms as $term) {
                $selected = ($selected_term == $term->term_id)?"selected":"";
                $string .= "<option {$selected} value='{$term->term_id}'>{$space}{$term->name}</option>";
                $string .= self::get_folder_option_data($post_type, $term->term_id, trim($space)."- ");
            }
        }
        return $string;
    }
}