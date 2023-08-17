<?php
/**
 * Class Folders WPML
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}

class WCP_Folder_WPML
{

    /**
     * The Name of this plugin.
     *
     * @var    string    $isWPMLActive    The Name of this plugin.
     * @since  1.0.0
     * @access private
     */
    private $isWPMLActive;

    /**
     * The Name of this plugin.
     *
     * @var    string    $total    Total number of taxonomies
     * @since  1.0.0
     * @access private
     */
    private $total;

    /**
     * The Name of this plugin.
     *
     * @var    string    $lang    Current Selected language
     * @since  1.0.0
     * @access private
     */
    private $lang;

    /**
     * The Name of this plugin.
     *
     * @var    string    $tableIclTranslations    WPML translation table
     * @since  1.0.0
     * @access private
     */
    private $tableIclTranslations;

    /**
     * The Name of this plugin.
     *
     * @var    string    $tableIclTranslations    WPML translated post
     * @since  1.0.0
     * @access protected
     */
    protected $post_translations;

    /**
     * The Name of this plugin.
     *
     * @var    string    $sitepress    sitepress
     * @since  1.0.0
     * @access protected
     */
    private $sitepress;

    /**
     * The Name of this plugin.
     *
     * @var    string    $sitepress    WPML Settings
     * @since  1.0.0
     * @access protected
     */
    private $settings;


    /**
     * Define the core functionality of the plugin.
     *
     * Set the WPML installation status and settings.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->isWPMLActive = false;
        $this->total        = 0;
        add_action("admin_init", [$this, 'init']);

    }//end __construct()


    /**
     * Check for the WPML settings and status
     *
     * @since  1.0.0
     * @access public
     * @return $isWPMLActive
     */
    public function init()
    {
        global $sitepress, $wpdb;
        $isWPMLActive = $sitepress !== null && get_class($sitepress) === "SitePress";

        if ($isWPMLActive) {
            $settings = $sitepress->get_setting('custom_posts_sync_option', []);
            if ($sitepress->get_current_language() !== 'all') {
                $this->isWPMLActive = true;
                $this->settings     = $settings;
                $this->lang         = $sitepress->get_current_language();
                $this->tableIclTranslations = $wpdb->prefix.'icl_translations';
            }

            $this->sitepress         = $sitepress;
            $this->post_translations = $sitepress->post_translations();
        }

        if ($this->isWPMLActive) {
            add_filter('premio_folder_item_in_taxonomy', [$this, 'items_in_taxonomy'], 10, 2);
            add_filter('premio_folder_un_categorized_items', [$this, 'un_categorized_items'], 10, 2);
            add_filter('premio_folder_all_categorized_items', [$this, 'all_categorized_items'], 10, 2);
        }

    }//end init()


    /**
     * Get the total taxonomies used in WPML
     *
     * @since  1.0.0
     * @access public
     * @return $total
     */
    public function set_total($post_type)
    {
        if ($this->isWPMLActive && isset($this->settings[$post_type]) && $this->settings[$post_type]) {
            global $wpdb;
            $select = "SELECT COUNT(DISTINCT(P.id))
                FROM {$this->tableIclTranslations} AS wpmlt
                INNER JOIN {$wpdb->posts} AS P ON P.id = wpmlt.element_id";
            $where = ["wpmlt.element_type =  'post_".esc_attr($post_type)."'"];
            $where[] = "wpmlt.language_code =  '%s'";
            if ($post_type == 'attachment') {
                $where[] = " (P.post_status = 'inherit' OR P.post_status = 'private')";
            } else {
                $where[] = " P.post_status != 'trash'";
            }

            $join = apply_filters( 'folders_count_join_query', "" );
            $where = apply_filters( 'folders_count_where_query', $where );

            $query = $select . $join . " WHERE ".implode( ' AND ', $where );

            $query       = $wpdb->prepare($query, [$this->lang]);
            $this->total = (int) $wpdb->get_var($query);
        }

    }//end set_total()


    /**
     * To get the items in taxonomies
     *
     * @since  1.0.0
     * @access public
     * @return $total
     */
    public function items_in_taxonomy($term_id, $arg=[])
    {
        $post_type = isset($arg['post_type']) ? $arg['post_type'] : "";
        $taxonomy  = isset($arg['taxonomy']) ? $arg['taxonomy'] : "";
        if ($this->isWPMLActive && isset($this->settings[$post_type]) && $this->settings[$post_type]) {
            global $wpdb;
            $term_taxonomy_id = get_term_by('id', (int) $term_id, $taxonomy, OBJECT)->term_taxonomy_id;
            $query            = "SELECT count(wpmlt.element_id) as total_records FROM {$this->tableIclTranslations} AS wpmlt 
                                    INNER JOIN {$wpdb->term_relationships} AS term_rela ON term_rela.object_id = wpmlt.element_id
                                    WHERE wpmlt.element_type =  'post_".esc_attr($post_type)."' 
                                        AND term_rela.term_taxonomy_id = '%s' 
                                        AND wpmlt.language_code =  '%s'";

            $query            = $wpdb->prepare($query, [$term_taxonomy_id, $this->lang]);
            $all_ids          = $wpdb->get_var($query);
//            $counter          = 0;
//            if (count($all_ids) > 0) {
//                $select = "SELECT COUNT(P.ID) as total_records FROM {$wpdb->posts} AS P";
//                $where = ["P.ID = (%s)"];
//
//                if($post_type == 'attachment') {
//                    $where[] = " (P.post_status = 'inherit' OR P.post_status = 'private')";
//                } else {
//                    $where[] = " P.post_status != 'trash'";
//                }
//
//                $join = apply_filters( 'folders_count_join_query', "" );
//                $where = apply_filters( 'folders_count_where_query', $where );
//
//                $query = $select . $join . " WHERE ".implode( ' AND ', $where );
////                $query   = $wpdb->prepare($query, [implode(',', $all_ids)]);
////                $counter = $wpdb->get_var($query);
//            }

            return !empty($all_ids) ? $all_ids : 0;
        }//end if

        return null;

    }//end items_in_taxonomy()


    /**
     * To get the items in taxonomies in uncategorized
     *
     * @since  1.0.0
     * @access public
     * @return $total
     */
    public function un_categorized_items($post_type, $taxonomy)
    {

        if ($this->isWPMLActive && isset($this->settings[$post_type]) && $this->settings[$post_type]) {
            global $wpdb;
            $subQuery = "SELECT * FROM {$this->tableIclTranslations} as wpmlt
                        INNER JOIN {$wpdb->posts} as p on p.id = wpmlt.element_id
                        WHERE wpmlt.element_type = 'post_".esc_attr($post_type)."'
                        and wpmlt.language_code = '%s'";
            $select  = "SELECT COUNT(DISTINCT(tmp_table.ID))
                             FROM ({$subQuery}) as tmp_table";
            $join    = " JOIN {$wpdb->term_relationships} as term_relationships on tmp_table.element_id = term_relationships.object_id ";
            $join   .= " JOIN {$wpdb->term_taxonomy} as term_taxonomy on term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id ";
            $where   = ["taxonomy = '%s'"];

            if ( $this->sitepress->is_translated_taxonomy( $taxonomy ) ) {
                $icl_taxonomies = "tax_" . $taxonomy;
                $join .= " LEFT JOIN {$wpdb->prefix}icl_translations AS icl_t
                                    ON icl_t.element_id = term_taxonomy.term_taxonomy_id
                                        AND icl_t.element_type = '{$icl_taxonomies}'";

                $where[] = " ( ( icl_t.element_type = '{$icl_taxonomies}' AND icl_t.language_code = '{$this->lang}' )
                                    OR icl_t.element_type != '{$icl_taxonomies}' OR icl_t.element_type IS NULL ) ";
            }

            $query = $select . $join . " WHERE ".implode( ' AND ', $where );

            $query        = $wpdb->prepare($query, [$this->lang, $taxonomy]);
            $fileInFolder = (int) $wpdb->get_var($query);

            $this->set_total($post_type);

            return ($this->total - $fileInFolder);
        }

        return null;

    }//end un_categorized_items()


    /**
     * To get the items in taxonomies in all categories
     *
     * @since  1.0.0
     * @access public
     * @return $total
     */
    public function all_categorized_items($post_type)
    {
        if ($this->isWPMLActive && isset($this->settings[$post_type]) && $this->settings[$post_type]) {
            $this->set_total($post_type);
            return $this->total;
        }

        return null;

    }//end all_categorized_items()


}//end class

if (class_exists('WCP_Folder_WPML')) {
    $folder_WPML = new WCP_Folder_WPML();
}
