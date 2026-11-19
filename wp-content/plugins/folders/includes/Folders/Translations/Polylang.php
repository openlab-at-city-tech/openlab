<?php
namespace Folders\Folders\Translations;

defined( 'ABSPATH' ) || exit;

/**
 * Handles folder-taxonomy counts on sites running Polylang, for post
 * types that Polylang itself manages languages/translations for
 * (pll_is_translated_post_type()) - e.g. attachments, once the site
 * owner turns on Polylang's "Media" module (Languages -> Settings ->
 * Media: Activate), or any custom post type explicitly enabled under
 * Languages -> Settings -> Custom Post Types and Taxonomies.
 *
 * Ported from the legacy WCP_Pro_Folder_PolyLang class
 * (premio-pro/includes/class-polylang.php) and adapted to this plugin's
 * auto-loaded class structure: `Folders\Loader` instantiates every
 * class under `includes/` with `new $class_name()` on `init`, so the
 * constructor below takes no required arguments (unchanged from the
 * legacy class, which already worked this way).
 *
 * GATE, AND WHY IT'S ON THE POST TYPE, NOT THE TAXONOMY: an earlier
 * version of this port gated on pll_is_translated_taxonomy($folder_tax).
 * Verified live against a real Polylang site, that was wrong: folder
 * taxonomies are shared structure (there is one folder tree, not one per
 * language) and pll_is_translated_taxonomy() returned true for them
 * regardless, even though they never appear in Polylang's own "Custom
 * taxonomies to translate" settings screen - so that gate never actually
 * turned off. What genuinely varies per site is whether Polylang assigns
 * a *language to the content itself* (posts/pages always; attachments
 * only once "Media" is activated; other post types only if explicitly
 * enabled) - that's pll_is_translated_post_type($post_type), checked
 * with the actual $post_type each filter already receives. When it's
 * true (translation deliberately turned on for that content), folder
 * counts scope to the current admin language, same as the legacy class.
 * When it's false (the default for Media on a fresh Polylang install),
 * every method below returns null and
 * Folders\Folders\Actions\FoldersItems falls back to its own raw,
 * language-agnostic SQL counts, so counts stay true totals until the
 * site owner explicitly opts a post type into Polylang translation.
 */
class Polylang {

    /**
     * @var bool Whether Polylang is active at all.
     */
    private $active = false;

    /**
     * @var int Term taxonomy ID of the currently selected admin language.
     */
    private $poly_lang_term_taxonomy_id;

    /**
     * @var int
     */
    private $total = 0;


    /**
     * Register {@see init()} on `admin_init` and `rest_api_init`.
     *
     * `admin_init` covers normal admin page loads. REST requests never fire it,
     * so the folder count requests made through the REST API need
     * `rest_api_init` too; otherwise the filters would not be registered and
     * counts would ignore the selected language.
     */
    public function __construct()
    {
        // admin_init covers the classic wp-admin page load (Media
        // Library screen render). admin_init never fires for REST API
        // requests (they're dispatched through index.php, not
        // wp-admin/*.php), so the media page's AJAX-via-REST calls to
        // folders-settings/v1/get-folder-items need rest_api_init too,
        // otherwise these filters are never registered for that request
        // and FoldersItems silently falls back to its raw,
        // language-agnostic counts regardless of the selected language.
        add_action("admin_init", [$this, 'init']);
        add_action("rest_api_init", [$this, 'init']);

    }//end __construct()


    /**
     * Hook into the folder-count filters, if Polylang is active.
     *
     * @return void
     */
    public function init()
    {
        $this->active = function_exists("pll_get_post_translations") && function_exists("pll_is_translated_post_type");

        if (!$this->active) {
            return;
        }

        // Folders\Folders\Actions\FoldersItems gates on has_filter(),
        // not on what the filter returns - it trusts a *registered*
        // filter's return value as-is, null included, rather than falling
        // back to its own raw count when a registered callback happens to
        // return null. So these filters can only be added when we've
        // actually resolved a language to scope to; resolve it once, here,
        // eagerly (see resolve_term_taxonomy_id_for_current_language() -
        // both branches it tries are available immediately, no lazy
        // per-callback resolution needed) and only hook in when that
        // succeeds. When it doesn't (Polylang active but no determinable
        // language - "Show all languages", or a REST request with no
        // saved preference yet), leave the filters unregistered entirely
        // so FoldersItems' own raw, language-agnostic counts run - same as
        // the "Show all languages" behaviour already verified live.
        $this->poly_lang_term_taxonomy_id = $this->resolve_term_taxonomy_id_for_current_language();

        if ($this->poly_lang_term_taxonomy_id) {
            add_filter('premio_folder_item_in_taxonomy', [$this, 'items_in_taxonomy'], 10, 2);
            add_filter('premio_folder_un_categorized_items', [$this, 'un_categorized_items'], 10, 2);
            add_filter('premio_folder_all_categorized_items', [$this, 'all_categorized_items'], 10, 2);
            // Counts depend on the admin language: cache them per language.
            add_filter('premio_folders_count_context', [$this, 'count_context']);
        }

        $user_id = get_current_user_id();
        $current = function_exists('pll_current_language') ? pll_current_language() : '';
        $previous = get_user_meta($user_id, '_admin_lang_last', true);
        $previous = $previous ? $previous : 'all';
        $current = $current ? $current : 'all';

        if ($previous !== $current) {
            delete_transient("premio_folders_without_trash");
            update_user_meta($user_id, '_admin_lang_last', $current);
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
        return 'pll-' . $this->poly_lang_term_taxonomy_id;
    }

    /**
     * Resolve the term_taxonomy_id of the language the wp-admin top bar
     * currently has selected, for the folder-count queries below to
     * scope to.
     *
     * On a normal wp-admin page load (Media Library screen render),
     * Polylang has already resolved that selection into
     * $polylang->curlang by the time this runs, and $polylang->curlang
     * is unset/falsy specifically when "Show all languages" is selected
     * - verified live, this is what the rest of this class already
     * relied on.
     *
     * That doesn't hold for the REST API requests the media page's JS
     * also calls directly (folders-settings/v1/get-folder-items):
     * is_admin() is false for a request dispatched through index.php
     * rather than wp-admin/*.php, so Polylang never even instantiates
     * its admin-side PLL_Admin_Base object for this request - there's no
     * $polylang->curlang to read at all here, and it isn't a
     * Polylang-aware content route that Polylang's own REST language
     * support resolves either.
     *
     * What IS shared across both request types: PLL_Admin_Base's own
     * init_user() (hooked to 'setup_theme', so it runs before our
     * admin_init/rest_api_init hook either way) persists the top bar's
     * selection into the 'pll_filter_content' user meta key - written
     * whenever the admin navigates with a plain (non-ajax) ?lang= query
     * arg, and read back on every later request to build curlang. That
     * user meta value, not the "pll_language" cookie (that one's for
     * Polylang's front-end language detection, never updated by the top
     * bar switcher - confirmed live, it stayed on a stale language while
     * the admin bar and page render had already moved on), is the actual
     * persisted signal, so read it directly here the same way Polylang
     * itself does.
     *
     * @return int 0 means "no determinable language" - the caller
     *             should treat that the same as "Show all languages"
     *             (return null, don't scope).
     */
    private function resolve_term_taxonomy_id_for_current_language()
    {
        global $polylang;

        if (isset($polylang->curlang) && is_object($polylang->curlang)) {
            if (method_exists($polylang->curlang, 'get_tax_prop')) {
                return (int) $polylang->curlang->get_tax_prop('language', 'term_taxonomy_id');
            }

            return (int) $polylang->curlang->term_taxonomy_id;
        }

        $user_id = get_current_user_id();
        $slug = $user_id ? get_user_meta($user_id, 'pll_filter_content', true) : '';

        if ($slug) {
            $lang = get_term_by('slug', $slug, 'language');
            if ($lang) {
                return (int) $lang->term_taxonomy_id;
            }
        }

        return 0;

    }//end resolve_term_taxonomy_id_for_current_language()

    /**
     * Whether Polylang actually assigns a language to this content type
     * (posts/pages by default; attachments only once the "Media" module
     * is activated; other post types only if explicitly enabled under
     * Custom Post Types and Taxonomies). This is the real opt-in gate -
     * see the class docblock for why it's per post type, not per
     * taxonomy.
     *
     * @param string $post_type
     * @return bool
     */
    private function post_type_is_translated($post_type)
    {
        if (empty($post_type) || !$this->active) {
            return false;
        }

        if (function_exists('pll_is_translated_post_type')) {
            return (bool) pll_is_translated_post_type($post_type);
        }

        return false;

    }//end post_type_is_translated()

    /**
     * Count the items in a folder for the current Polylang language.
     *
     * Filter callback for `premio_folder_item_in_taxonomy`.
     *
     * @param int   $term_id Folder term ID.
     * @param array $arg     Arguments with `post_type` and `taxonomy`.
     * @return int|null Item count, or null when Polylang does not translate the post type.
     */
    public function items_in_taxonomy($term_id, $arg = [])
    {
        $post_type = isset($arg['post_type']) ? $arg['post_type'] : "";
        $taxonomy = isset($arg['taxonomy']) ? $arg['taxonomy'] : "";

        if (!$this->post_type_is_translated($post_type)) {
            return null;
        }

        $where = "posts.post_status = 'inherit' OR posts.post_status = 'private'";
        if ($post_type != 'attachment') {
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
            ON (posts.ID = tr2.object_id and tr2.term_taxonomy_id IN (%s)) 
            LEFT JOIN {$wpdb->postmeta} AS postmeta ON ( posts.ID = postmeta.post_id AND postmeta.meta_key = '_wp_attached_file' ) 
            WHERE (tr1.term_taxonomy_id IN (%s)) 
            AND posts.post_type = '%s' 
            AND (({$where})) 
            GROUP BY posts.ID
        ) as tmp";
        $query = $wpdb->prepare($query, [$term_taxonomy_id, $this->poly_lang_term_taxonomy_id, $post_type]);
        $counter = (int)$wpdb->get_var($query);
        return $counter ? $counter : 0;

    }//end items_in_taxonomy()

    /**
     * Count the items not in any folder for the current Polylang language.
     *
     * Filter callback for `premio_folder_un_categorized_items`.
     *
     * @param string $post_type Post type.
     * @param string $taxonomy  Folder taxonomy name.
     * @return int|null Unassigned item count, or null when Polylang does not translate the post type.
     */
    public function un_categorized_items($post_type, $taxonomy)
    {
        if (!$this->post_type_is_translated($post_type)) {
            return null;
        }

        global $wpdb;
        $where = "posts.post_status = 'inherit' OR posts.post_status = 'private'";
        if ($post_type != 'attachment') {
            $where = "post_status != 'trash'";
        }

        $query = "SELECT COUNT(tmp.ID) FROM 
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
        $query = $wpdb->prepare($query, [$this->poly_lang_term_taxonomy_id, $taxonomy, $post_type]);
        $fileInFolder = (int)$wpdb->get_var($query);
        $fileInFolder = !($fileInFolder) ? 0 : $fileInFolder;
        $this->set_total($post_type);
        return ($this->total - $fileInFolder);

    }//end un_categorized_items()

    /**
     * Count all items of a post type in the current Polylang language and store it in {@see $total}.
     *
     * Attachments count when inherit or private; other post types when not trashed.
     *
     * @param string $post_type Post type.
     * @return void
     */
    public function set_total($post_type)
    {
        if (!$this->active || !$this->poly_lang_term_taxonomy_id) {
            return;
        }

        $where = "posts.post_status = 'inherit' OR posts.post_status = 'private'";
        if ($post_type != 'attachment') {
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
            WHERE posts.post_type = '%s'
            AND trs.term_taxonomy_id IN (%s)
            AND ({$where})
            GROUP BY posts.ID
        ) as tmp";
        $query = $wpdb->prepare($query, [$post_type, $this->poly_lang_term_taxonomy_id]);
        $this->total = (int)$wpdb->get_var($query);

    }//end set_total()

    /**
     * Count all items of a post type for the current Polylang language.
     *
     * Filter callback for `premio_folder_all_categorized_items`.
     *
     * @param string $post_type Post type.
     * @return int|null Item count, or null when Polylang does not translate the post type.
     */
    public function all_categorized_items($post_type)
    {
        if (!$this->post_type_is_translated($post_type)) {
            return null;
        }

        $this->set_total($post_type);
        return $this->total;

    }//end all_categorized_items()


}//end class
