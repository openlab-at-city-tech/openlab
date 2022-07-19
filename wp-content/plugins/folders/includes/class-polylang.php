<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WCP_Folder_PolyLang {
    private $active;
    private $pl_term_taxonomy_id;
    private $total;
    private $table_filebird_polylang;
    public $delete_process_id;

    public function __construct()
    {
        $this->active = false;
        $this->total = 0;
        $this->delete_process_id = null;
        add_action("admin_init", array($this, 'init'));
    }


    public function init()
    {
        global $wpdb, $polylang;
        $this->active = function_exists("pll_get_post_translations");

        if ($this->active) {
            if (isset($polylang->curlang) && is_object($polylang->curlang))
            {
                $this->pl_term_taxonomy_id = $polylang->curlang->term_taxonomy_id;

                add_filter('premio_folder_item_in_taxonomy', array($this, 'items_in_taxonomy'), 10, 2);
                add_filter('premio_folder_un_categorized_items', array($this, 'un_categorized_items'), 10, 2);
                add_filter('premio_folder_all_categorized_items', array($this, 'all_categorized_items'), 10, 2);
            }
        }
    }

    public function set_total($post_type){
        if ($this->active) {
            $where = "posts.post_status = 'inherit' OR posts.post_status = 'private'";
            if($post_type != 'attachment') {
                $where = "post_status != 'trash'";
            }
            global $wpdb;
            $query = "SELECT COUNT(tmp.ID) FROM
            (   
                SELECT posts.ID
                FROM {$wpdb->posts} AS posts
                LEFT JOIN {$wpdb->term_relationships} AS trs 
                ON posts.ID = trs.object_id
                LEFT JOIN {$wpdb->postmeta} AS postmeta
                ON (posts.ID = postmeta.post_id AND postmeta.meta_key = '_wp_attached_file')
                WHERE posts.post_type = '{$post_type}'
                AND trs.term_taxonomy_id IN ({$this->pl_term_taxonomy_id})
                AND ({$where})
                GROUP BY posts.ID
            ) as tmp";
            $this->total = (int)$wpdb->get_var($query);
        }
    }

    public function items_in_taxonomy($term_id, $arg = array()) {
        if ($this->active) {
            $post_type = isset($arg['post_type']) ? $arg['post_type'] : "";
            $taxonomy = isset($arg['taxonomy']) ? $arg['taxonomy'] : "";
            $where = "posts.post_status = 'inherit' OR posts.post_status = 'private'";
            if($post_type != 'attachment') {
                $where = "post_status != 'trash'";
            }
            global $wpdb;
            $term_taxonomy_id = get_term_by('id', (int)$term_id, $taxonomy, OBJECT)->term_taxonomy_id;
            $query = "SELECT COUNT(tmp.ID) FROM
            (
                SELECT posts.ID FROM {$wpdb->posts} AS posts  
                LEFT JOIN {$wpdb->term_relationships} AS tr1 
                ON (posts.ID = tr1.object_id) 
                INNER JOIN {$wpdb->term_relationships} AS tr2 
                ON (posts.ID = tr2.object_id and tr2.term_taxonomy_id IN ($term_taxonomy_id)) 
                LEFT JOIN {$wpdb->postmeta} AS postmeta ON ( posts.ID = postmeta.post_id AND postmeta.meta_key = '_wp_attached_file' ) 
                WHERE (tr1.term_taxonomy_id IN ({$this->pl_term_taxonomy_id})) 
                AND posts.post_type = '{$post_type}' 
                AND (({$where})) 
                GROUP BY posts.ID
            ) as tmp
        ";
            $counter = (int)$wpdb->get_var($query);
            return $counter ? $counter : 0;
        }
        return null;
    }

    public function un_categorized_items($post_type, $taxonomy) {
        if ($this->active) {
            global $wpdb;
            $where = "posts.post_status = 'inherit' OR posts.post_status = 'private'";
            if($post_type != 'attachment') {
                $where = "post_status != 'trash'";
            }
            $query = "SELECT COUNT(tmp.ID) FROM 
                (
                    SELECT posts.ID
                    FROM {$wpdb->posts} AS posts 
                    INNER JOIN {$wpdb->term_relationships} AS tr1 
                    ON posts.ID = tr1.object_id AND tr1.term_taxonomy_id IN ({$this->pl_term_taxonomy_id})
                    INNER JOIN {$wpdb->term_relationships} AS tr2 
                    ON (tr2.object_id = posts.ID)
                    JOIN {$wpdb->term_taxonomy} as tx
                    ON tx.term_taxonomy_id = tr2.term_taxonomy_id AND tx.taxonomy = '{$taxonomy}' 
                    
                    WHERE posts.post_type = '{$post_type}' 
                    AND ({$where})
                    GROUP BY posts.ID
                ) as tmp";
            $fileInFolder = (int)$wpdb->get_var($query);
            $fileInFolder = !($fileInFolder)?0:$fileInFolder;
            $this->set_total($post_type);
            return $this->total - $fileInFolder;
        }
        return null;
    }

    public function all_categorized_items($post_type) {
        if ($this->active) {
            $this->set_total($post_type);
            return $this->total;
        }
        return null;
    }
}
if(class_exists('WCP_Folder_PolyLang')) {
    $WCP_Folder_PolyLang = new WCP_Folder_PolyLang();

}