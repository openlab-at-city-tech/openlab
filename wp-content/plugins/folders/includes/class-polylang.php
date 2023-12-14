<?php
/**
 * Class Folders PolyLang
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}

class WCP_Folder_PolyLang
{

    /**
     * The Name of this plugin.
     *
     * @var    string    $active    Checking for Plugin is active or not
     * @since  1.0.0
     * @access public
     */
    private $active;

    /**
     * The Name of this plugin.
     *
     * @var    string    $poly_lang_term_taxonomy_id    Poly Lang taxonomy id
     * @since  1.0.0
     * @access public
     */
    private $poly_lang_term_taxonomy_id;

    /**
     * The Name of this plugin.
     *
     * @var    string    $total    total posts in taxonomy
     * @since  1.0.0
     * @access public
     */
    private $total;


    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->active = false;
        $this->total  = 0;
        add_action("admin_init", [$this, 'init']);

    }//end __construct()


    /**
     * Filters the taxonomy data
     *
     * @since  1.0.0
     * @access public
     * @return
     */
    public function init()
    {
        global $wpdb, $polylang, $typenow;
        $this->active = function_exists("pll_get_post_translations") && function_exists("pll_is_translated_post_type");

        if ($this->active) {
            if (isset($polylang->curlang) && is_object($polylang->curlang)) {
                if(method_exists($polylang->curlang, 'get_tax_prop')) {
                    $this->poly_lang_term_taxonomy_id = $polylang->curlang->get_tax_prop('language', 'term_taxonomy_id');
                } else {
                    $this->poly_lang_term_taxonomy_id = $polylang->curlang->term_taxonomy_id;
                }

                add_filter('premio_folder_item_in_taxonomy', [$this, 'items_in_taxonomy'], 10, 2);
                add_filter('premio_folder_un_categorized_items', [$this, 'un_categorized_items'], 10, 2);
                add_filter('premio_folder_all_categorized_items', [$this, 'all_categorized_items'], 10, 2);
            }
        }

    }//end init()


    /**
     * Get total number on taxonomies used in Polylang
     *
     * @since  1.0.0
     * @access public
     * @return $total
     */
    public function set_total($post_type)
    {
        if ($this->active) {
            $where = "posts.post_status = 'inherit' OR posts.post_status = 'private'";
            if ($post_type != 'attachment') {
                $where = "post_status != 'trash'";
            }

            global $wpdb;
            $query       = "SELECT COUNT(tmp.ID) FROM
            (   
                SELECT posts.ID
                FROM {$wpdb->posts} AS posts
                LEFT JOIN {$wpdb->term_relationships} AS trs 
                ON posts.ID = trs.object_id
                LEFT JOIN {$wpdb->postmeta} AS postmeta
                ON (posts.ID = postmeta.post_id AND postmeta.meta_key = '_wp_attached_file')
                WHERE posts.post_type = '%s'
                AND trs.term_taxonomy_id IN (%s)
                AND ({$where})
                GROUP BY posts.ID
            ) as tmp";
            $query       = $wpdb->prepare($query, [$post_type, $this->poly_lang_term_taxonomy_id]);
            $this->total = (int) $wpdb->get_var($query);
        }//end if

    }//end set_total()


    /**
     * Check the items in taxonomies
     *
     * @since  1.0.0
     * @access public
     * @return $counter
     */
    public function items_in_taxonomy($term_id, $arg=[])
    {
        if ($this->active) {
            $post_type = isset($arg['post_type']) ? $arg['post_type'] : "";
            $taxonomy  = isset($arg['taxonomy']) ? $arg['taxonomy'] : "";
            $where     = "posts.post_status = 'inherit' OR posts.post_status = 'private'";
            if ($post_type != 'attachment') {
                $where = "post_status != 'trash'";
            }

            global $wpdb;
            $term_taxonomy_id = get_term_by('id', (int) $term_id, $taxonomy, OBJECT)->term_taxonomy_id;
            $query            = "SELECT COUNT(tmp.ID) FROM
                (
                SELECT posts.ID FROM {$wpdb->posts} AS posts  
                LEFT JOIN {$wpdb->term_relationships} AS tr1 
                ON (posts.ID = tr1.object_id) 
                INNER JOIN {$wpdb->term_relationships} AS tr2 
                ON (posts.ID = tr2.object_id and tr2.term_taxonomy_id IN (%s)) 
                LEFT JOIN {$wpdb->postmeta} AS postmeta ON ( posts.ID = postmeta.post_id AND postmeta.meta_key = '_wp_attached_file' ) 
                WHERE (tr1.term_taxonomy_id IN (%s)) 
                AND posts.post_type = '%s' 
                AND (({$where})) 
                GROUP BY posts.ID
            ) as tmp";
            $query            = $wpdb->prepare($query, [$term_taxonomy_id, $this->poly_lang_term_taxonomy_id, $post_type]);
            $counter          = (int) $wpdb->get_var($query);
            return $counter ? $counter : 0;
        }//end if

        return null;

    }//end items_in_taxonomy()


    /**
     * Check the items in uncategorized taxonomies
     *
     * @since  1.0.0
     * @access public
     * @return $counter
     */
    public function un_categorized_items($post_type, $taxonomy)
    {
        if ($this->active) {
            global $wpdb;
            $where = "posts.post_status = 'inherit' OR posts.post_status = 'private'";
            if ($post_type != 'attachment') {
                $where = "post_status != 'trash'";
            }

            $query        = "SELECT COUNT(tmp.ID) FROM 
                (
                    SELECT posts.ID
                    FROM {$wpdb->posts} AS posts 
                    INNER JOIN {$wpdb->term_relationships} AS tr1 
                    ON posts.ID = tr1.object_id AND tr1.term_taxonomy_id IN (%s)
                    INNER JOIN {$wpdb->term_relationships} AS tr2 
                    ON (tr2.object_id = posts.ID)
                    JOIN {$wpdb->term_taxonomy} as tx
                    ON tx.term_taxonomy_id = tr2.term_taxonomy_id AND tx.taxonomy = '%s'                     
                    WHERE posts.post_type = '%s' 
                    AND ({$where})
                    GROUP BY posts.ID
                ) as tmp";
            $query        = $wpdb->prepare($query, [$this->poly_lang_term_taxonomy_id, $taxonomy, $post_type]);
            $fileInFolder = (int) $wpdb->get_var($query);
            $fileInFolder = !($fileInFolder) ? 0 : $fileInFolder;
            $this->set_total($post_type);
            return ($this->total - $fileInFolder);
        }//end if

        return null;

    }//end un_categorized_items()


    /**
     * Check the items in taxonomies
     *
     * @since  1.0.0
     * @access public
     * @return $counter
     */
    public function all_categorized_items($post_type)
    {
        if ($this->active) {
            $this->set_total($post_type);
            return $this->total;
        }

        return null;

    }//end all_categorized_items()


}//end class

if (class_exists('WCP_Folder_PolyLang')) {
    $WCP_Folder_PolyLang = new WCP_Folder_PolyLang();
}
