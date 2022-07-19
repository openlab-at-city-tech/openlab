<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WCP_Folder_WPML {
    private $is_wpml_active;
    private $total;
    private $lang;
    private $table_icl_translations;

    protected $post_translations;
    private $sitepress;
    private $settings;

    public function __construct()
    {
        $this->is_wpml_active = false;
        $this->total = 0;
        add_action("admin_init", array($this, 'init'));
    }

    public function init()
    {
        global $sitepress, $wpdb;
        $is_wpml_active = $sitepress !== null && get_class($sitepress) === "SitePress";

        if ($is_wpml_active) {
            $settings = $sitepress->get_setting('custom_posts_sync_option', array());
            if ($sitepress->get_current_language() !== 'all') {
                $this->is_wpml_active = true;
                $this->settings = $settings;
                $this->lang = $sitepress->get_current_language();
                $this->table_icl_translations = $wpdb->prefix . 'icl_translations';
            }
            $this->sitepress = $sitepress;
            $this->post_translations = $sitepress->post_translations();
        }

        if ($this->is_wpml_active) {
            add_filter('premio_folder_item_in_taxonomy', array($this, 'items_in_taxonomy'), 10, 2);
            add_filter('premio_folder_un_categorized_items', array($this, 'un_categorized_items'), 10, 2);
            add_filter('premio_folder_all_categorized_items', array($this, 'all_categorized_items'), 10, 2);
        }
    }

    public function set_total($post_type){
        if($this->is_wpml_active && isset($this->settings[$post_type]) && $this->settings[$post_type]) {
            global $wpdb;
            $query = "SELECT COUNT(DISTINCT(p.id))
                FROM {$this->table_icl_translations} AS wpmlt
                INNER JOIN {$wpdb->posts} AS p ON p.id = wpmlt.element_id
                WHERE wpmlt.element_type =  'post_{$post_type}'
                AND wpmlt.language_code =  '{$this->lang}'";
            if($post_type == 'attachment') {
                $query .= " AND (p.post_status = 'inherit' OR p.post_status = 'private')";
            } else {
                $query .= " AND p.post_status != 'trash'";
            }
            $this->total = (int)$wpdb->get_var($query);
        }
    }

    public function items_in_taxonomy($term_id, $arg = array()) {
        $post_type = isset($arg['post_type'])?$arg['post_type']:"";
        $taxonomy = isset($arg['taxonomy'])?$arg['taxonomy']:"";
        if($this->is_wpml_active && isset($this->settings[$post_type]) && $this->settings[$post_type]) {
            global $wpdb;
            $term_taxonomy_id = get_term_by('id', (int)$term_id, $taxonomy, OBJECT)->term_taxonomy_id;
            $join = "INNER JOIN {$wpdb->term_relationships} AS term_rela ON term_rela.object_id = wpmlt.element_id";
            $where = "wpmlt.element_type =  'post_{$post_type}' AND term_rela.term_taxonomy_id = {$term_taxonomy_id} AND wpmlt.language_code =  '{$this->lang}'";
            $query = "SELECT wpmlt.element_id FROM {$this->table_icl_translations} AS wpmlt " . $join . " WHERE " . $where;
            $all_ids = $wpdb->get_col($query);
            $counter = 0;
            if (count($all_ids) > 0) {
                if($post_type == 'attachment') {
                    $query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE `ID` IN (" . implode(',', $all_ids) . ") AND (post_status = 'inherit' OR post_status = 'private')";
                    $counter = $wpdb->get_var($query);
                } else {
                    $query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE `ID` IN (" . implode(',', $all_ids) . ") AND post_status != 'trash'";
                    $counter = $wpdb->get_var($query);
                }
            }
            return !empty($counter) ? $counter : 0;
        }
        return null;
    }

    public function un_categorized_items($post_type, $taxonomy) {

        if($this->is_wpml_active && isset($this->settings[$post_type]) && $this->settings[$post_type]) {

            global $wpdb;
            $query = "SELECT COUNT(DISTINCT(tmp_table.ID))
                FROM (SELECT * FROM {$this->table_icl_translations} as wpmlt
                        INNER JOIN {$wpdb->posts} as p on p.id = wpmlt.element_id
                        WHERE wpmlt.element_type = 'post_{$post_type}'
                        and wpmlt.language_code = '{$this->lang}') as tmp_table
                        JOIN {$wpdb->term_relationships} as term_relationships on tmp_table.element_id = term_relationships.object_id
                        JOIN {$wpdb->term_taxonomy} as term_taxonomy on term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id 
                        WHERE taxonomy = '{$taxonomy}'";
            $fileInFolder = (int)$wpdb->get_var($query);

            $this->set_total($post_type);

            return $this->total - $fileInFolder;
        }
        return null;
    }

    public function all_categorized_items($post_type) {
        if($this->is_wpml_active && isset($this->settings[$post_type]) && $this->settings[$post_type]) {
            $this->set_total($post_type);
            return $this->total;
        }
        return null;
    }
}
if(class_exists('WCP_Folder_WPML')) {
    $folder_WPML = new WCP_Folder_WPML();
}