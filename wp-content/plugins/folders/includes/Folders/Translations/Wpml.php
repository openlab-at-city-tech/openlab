<?php
namespace Folders\Folders\Translations;

defined( 'ABSPATH' ) || exit;

/**
 * Handles folder-taxonomy counts on sites running WPML, for post types
 * where WPML's own "Custom Post Type Translation Sync" option
 * (`custom_posts_sync_option`) is turned on.
 *
 * Ported from the legacy WCP_Pro_Folder_WPML class
 * (premio-pro/includes/class-wpml.php) and adapted to this plugin's
 * auto-loaded class structure: `Folders\Loader` instantiates every
 * class under `includes/` with `new $class_name()` on `init`, so the
 * constructor below takes no required arguments (unchanged from the
 * legacy class, which already worked this way).
 *
 * This class only takes over counting for a given post type when WPML's
 * sync option is explicitly enabled for it ($this->settings[$post_type]).
 * When it isn't, every method below returns null and
 * Folders\Folders\Actions\FoldersItems falls back to its own raw,
 * language-agnostic SQL counts - so folder counts stay accurate totals on
 * WPML sites by default, and only become language-scoped where the site
 * owner has explicitly opted a post type into WPML's translation sync.
 */
class Wpml {

    /**
     * @var string
     */
    protected $post_translations;

    /**
     * @var bool
     */
    private $isWPMLActive;

    /**
     * @var int
     */
    private $total;

    /**
     * @var string Current selected language.
     */
    private $lang;

    /**
     * @var string
     */
    private $tableIclTranslations;

    /**
     * @var object
     */
    private $sitepress;

    /**
     * @var array WPML "Custom Post Type Translation Sync" settings, keyed by post type.
     */
    private $settings;


    /**
     * Register {@see init()} on `admin_init` and `rest_api_init`.
     *
     * REST requests never fire `admin_init`, so the folder count requests made
     * through the REST API need `rest_api_init` too.
     */
    public function __construct()
    {
        $this->isWPMLActive = false;
        $this->total = 0;
        // See Folders\Folders\Translations\Polylang::__construct() -
        // admin_init never fires for REST API requests, so the media
        // page's AJAX-via-REST calls to
        // folders-settings/v1/get-folder-items need rest_api_init too.
        add_action("admin_init", [$this, 'init']);
        add_action("rest_api_init", [$this, 'init']);

    }//end __construct()


    /**
     * Read the WPML status and settings and hook into the folder count filters.
     *
     * Filters are only registered when WPML is active and a specific language
     * (not "all") is selected. Also clears the cached folder counts when the
     * admin language has changed since the user's last request.
     *
     * @return void
     */
    public function init()
    {
        global $sitepress, $wpdb;
        $isWPMLActive = $sitepress !== null && get_class($sitepress) === "SitePress";

        if ($isWPMLActive) {
            $settings = $sitepress->get_setting('custom_posts_sync_option', []);
            if ($sitepress->get_current_language() !== 'all') {
                $this->isWPMLActive = true;
                $this->settings = $settings;
                $this->lang = $sitepress->get_current_language();
                $this->tableIclTranslations = $wpdb->prefix . 'icl_translations';
            }

            $this->sitepress = $sitepress;
            $this->post_translations = $sitepress->post_translations();

            $user_id = get_current_user_id();

            $current = apply_filters('wpml_current_language', null);
            $previous = get_user_meta($user_id, '_icl_admin_language_last', true);
            $previous = $previous ? $previous : 'all';
            $current = $current ? $current : 'all';

            if ($previous !== $current) {
                delete_transient("premio_folders_without_trash");
                update_user_meta($user_id, '_icl_admin_language_last', $current);
            }
        }

        if ($this->isWPMLActive) {
            add_filter('premio_folder_item_in_taxonomy', [$this, 'items_in_taxonomy'], 10, 2);
            add_filter('premio_folder_un_categorized_items', [$this, 'un_categorized_items'], 10, 2);
            add_filter('premio_folder_all_categorized_items', [$this, 'all_categorized_items'], 10, 2);
            // Counts depend on the admin language: cache them per language.
            add_filter('premio_folders_count_context', [$this, 'count_context']);
        }

    }//end init()

    /**
     * Cache key part for folder counts: the current admin language.
     *
     * Filter callback for `premio_folders_count_context`.
     *
     * @param string $context Current context.
     * @return string
     */
    public function count_context($context = '')
    {
        return 'wpml-' . $this->lang;
    }

    /**
     * Count the items in a folder for the current WPML language.
     *
     * Filter callback for `premio_folder_item_in_taxonomy`.
     *
     * @param int   $term_id Folder term ID.
     * @param array $arg     Arguments with `post_type` and `taxonomy`.
     * @return int|string|null Item count, or null when WPML sync is off for the post type
     *                         (the default count is used instead).
     */
    public function items_in_taxonomy($term_id, $arg = [])
    {
        $post_type = isset($arg['post_type']) ? $arg['post_type'] : "";
        $taxonomy = isset($arg['taxonomy']) ? $arg['taxonomy'] : "";
        if ($this->isWPMLActive && isset($this->settings[$post_type]) && $this->settings[$post_type]) {
            global $wpdb;
            $term_taxonomy_id = get_term_by('id', (int)$term_id, $taxonomy, OBJECT)->term_taxonomy_id;
            $query = "SELECT count(wpmlt.element_id) as total_records FROM {$this->tableIclTranslations} AS wpmlt 
                                    INNER JOIN {$wpdb->term_relationships} AS term_rela ON term_rela.object_id = wpmlt.element_id
                                    WHERE wpmlt.element_type =  'post_" . esc_attr($post_type) . "' 
                                        AND term_rela.term_taxonomy_id = '%s' 
                                        AND wpmlt.language_code =  '%s'";

            $query = $wpdb->prepare($query, [$term_taxonomy_id, $this->lang]);
            $all_ids = $wpdb->get_var($query);

            return !empty($all_ids) ? $all_ids : 0;
        }//end if

        return null;

    }//end items_in_taxonomy()

    /**
     * Count the items not in any folder for the current WPML language.
     *
     * Filter callback for `premio_folder_un_categorized_items`.
     *
     * @param string $post_type Post type.
     * @param string $taxonomy  Folder taxonomy name.
     * @return int|null Unassigned item count, or null when WPML sync is off for the post type.
     */
    public function un_categorized_items($post_type, $taxonomy)
    {

        if ($this->isWPMLActive && isset($this->settings[$post_type]) && $this->settings[$post_type]) {
            global $wpdb;
            $subQuery = "SELECT * FROM {$this->tableIclTranslations} as wpmlt
                        INNER JOIN {$wpdb->posts} as p on p.id = wpmlt.element_id
                        WHERE wpmlt.element_type = 'post_" . esc_attr($post_type) . "'
                        and wpmlt.language_code = '%s'";
            $select = "SELECT COUNT(DISTINCT(tmp_table.ID))
                             FROM ({$subQuery}) as tmp_table";
            $join = " JOIN {$wpdb->term_relationships} as term_relationships on tmp_table.element_id = term_relationships.object_id ";
            $join .= " JOIN {$wpdb->term_taxonomy} as term_taxonomy on term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id ";
            $where = ["taxonomy = '%s'"];

            if ($this->sitepress->is_translated_taxonomy($taxonomy)) {
                $icl_taxonomies = "tax_" . $taxonomy;
                $join .= " LEFT JOIN {$wpdb->prefix}icl_translations AS icl_t
                                    ON icl_t.element_id = term_taxonomy.term_taxonomy_id
                                        AND icl_t.element_type = '{$icl_taxonomies}'";

                $where[] = " ( ( icl_t.element_type = '{$icl_taxonomies}' AND icl_t.language_code = '{$this->lang}' )
                                    OR icl_t.element_type != '{$icl_taxonomies}' OR icl_t.element_type IS NULL ) ";
            }

            $query = $select . $join . " WHERE " . implode(' AND ', $where);

            $query = $wpdb->prepare($query, [$this->lang, $taxonomy]);
            $fileInFolder = (int)$wpdb->get_var($query);

            $this->set_total($post_type);

            return ($this->total - $fileInFolder);
        }

        return null;

    }//end un_categorized_items()

    /**
     * Count all items of a post type in the current WPML language and store it in {@see $total}.
     *
     * Attachments count when inherit or private; other post types when not trashed.
     *
     * @param string $post_type Post type.
     * @return void
     */
    public function set_total($post_type)
    {
        if ($this->isWPMLActive && isset($this->settings[$post_type]) && $this->settings[$post_type]) {
            global $wpdb;
            $select = "SELECT COUNT(DISTINCT(P.id))
                FROM {$this->tableIclTranslations} AS wpmlt
                INNER JOIN {$wpdb->posts} AS P ON P.id = wpmlt.element_id";
            $where = ["wpmlt.element_type =  'post_" . esc_attr($post_type) . "'"];
            $where[] = "wpmlt.language_code =  '%s'";
            if ($post_type == 'attachment') {
                $where[] = " (P.post_status = 'inherit' OR P.post_status = 'private')";
            } else {
                $where[] = " P.post_status != 'trash'";
            }

            $join = apply_filters('folders_count_join_query', "");
            $where = apply_filters('folders_count_where_query', $where);

            $query = $select . $join . " WHERE " . implode(' AND ', $where);

            $query = $wpdb->prepare($query, [$this->lang]);
            $this->total = (int)$wpdb->get_var($query);
        }

    }//end set_total()

    /**
     * Count all items of a post type for the current WPML language.
     *
     * Filter callback for `premio_folder_all_categorized_items`.
     *
     * @param string $post_type Post type.
     * @return int|null Item count, or null when WPML sync is off for the post type.
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
