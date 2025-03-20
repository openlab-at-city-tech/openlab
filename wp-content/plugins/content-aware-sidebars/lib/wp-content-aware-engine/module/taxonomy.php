<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */

defined('ABSPATH') || exit;

/**
 *
 * Taxonomy Module
 *
 * Detects if current content has/is:
 * a) any term of specific taxonomy or specific term
 * b) taxonomy archive or specific term archive
 *
 */
class WPCAModule_taxonomy extends WPCAModule_Base
{
    /**
     * when condition has select terms,
     * set this value in postmeta
     * @see parent::filter_excluded_context()
     */
    const VALUE_HAS_TERMS = '-1';

    /**
     * @var string
     */
    protected $category = 'taxonomy';

    /**
     * Registered public taxonomies
     *
     * @var array
     */
    private $taxonomy_objects = [];

    /**
     * Terms of a given singular
     *
     * @var array
     */
    private $post_terms = [];

    /**
     * Taxonomies for a given singular
     * @var array
     */
    private $post_taxonomies = [];

    public function __construct()
    {
        parent::__construct('taxonomy', __('Taxonomies', WPCA_DOMAIN));
        $this->query_name = 'ct';
    }

    /**
     * @inheritDoc
     */
    public function initiate()
    {
        parent::initiate();
        add_action(
            'created_term',
            [$this,'term_ancestry_check'],
            10,
            3
        );

        if (is_admin()) {
            foreach ($this->_get_taxonomies() as $taxonomy) {
                add_action(
                    'wp_ajax_wpca/module/' . $this->id . '-' . $taxonomy->name,
                    [$this,'ajax_print_content']
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function in_context()
    {
        //check if post_taxonomies contains more than self::VALUE_HAS_TERMS
        return count($this->get_context_data()) > 1;
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        if (!empty($this->post_taxonomies)) {
            return $this->post_taxonomies;
        }

        $this->post_taxonomies[] = self::VALUE_HAS_TERMS;

        if (is_singular()) {
            $tax = $this->_get_taxonomies();
            // Check if content has any taxonomies supported
            foreach (get_object_taxonomies(get_post_type()) as $taxonomy) {
                //Only want taxonomies selectable in admin
                if (isset($tax[$taxonomy])) {
                    //Check term caches, Core most likely used it
                    $terms = get_object_term_cache(get_the_ID(), $taxonomy);
                    if ($terms === false) {
                        $terms = wp_get_object_terms(get_the_ID(), $taxonomy);
                    }
                    if ($terms) {
                        $this->post_taxonomies[] = $taxonomy;
                        $this->post_terms = array_merge($this->post_terms, $terms);
                    }
                }
            }
        } elseif (is_tax() || is_category() || is_tag()) {
            $term = get_queried_object();
            $this->post_taxonomies[] = $term->taxonomy;
            $this->post_terms[] = $term;
        }

        return $this->post_taxonomies;
    }

    /**
     * @inheritDoc
     */
    public function filter_excluded_context($posts, $in_context = false)
    {
        $posts = parent::filter_excluded_context($posts, $in_context);
        if ($in_context) {
            $post_terms_by_tax = [];
            //@todo archive pages should be migrated to use AND as well, keep OR for now
            $legacy_use_or = is_archive();
            foreach ($this->post_terms as $term) {
                $post_terms_by_tax[$term->taxonomy][$term->term_taxonomy_id] = $term->term_taxonomy_id;
            }

            $check_terms = [];
            $keep_archive = [];
            $unset = [];

            //1. group's taxonomies must match all in post
            foreach ($posts as $condition_id => $condition_group) {
                $condition_taxonomies = get_post_meta($condition_id, '_ca_taxonomy', false);
                foreach ($condition_taxonomies as $taxonomy) {
                    //if value==-1, group has individual terms, so goto 2
                    if ($taxonomy == '-1') {
                        $check_terms[$condition_id] = $condition_group;
                    } elseif (isset($post_terms_by_tax[$taxonomy])) {
                        //if on archive page, bail after 1st match
                        if ($legacy_use_or) {
                            $keep_archive[$condition_id] = 1;
                            break;
                        }
                    } else {
                        //if group has more taxonomies than post, unset
                        $unset[$condition_id] = 1;
                        //break;
                    }
                }
            }

            //2. group's terms must match with minimum 1 in each taxonomy in post
            if (!empty($check_terms)) {
                //eager load groups terms
                $conditions_terms = wp_get_object_terms(array_keys($check_terms), array_keys($this->_get_taxonomies()), [
                    'fields'                 => 'all_with_object_id',
                    'orderby'                => 'none',
                    'update_term_meta_cache' => false
                ]);

                $conditions_to_unset = [];
                foreach ($conditions_terms as $term) {
                    if (!isset($conditions_to_unset[$term->object_id][$term->taxonomy])) {
                        $conditions_to_unset[$term->object_id][$term->taxonomy] = 0;
                    }
                    $has_tax_term = isset($post_terms_by_tax[$term->taxonomy][$term->term_taxonomy_id]);
                    $conditions_to_unset[$term->object_id][$term->taxonomy] |= $has_tax_term;
                    if ($legacy_use_or && $has_tax_term) {
                        $keep_archive[$term->object_id] = 1;
                    }
                }

                foreach ($check_terms as $condition_id => $condition_group) {
                    //if group has no terms in these taxonomies, it has terms in others, so unset
                    if (!isset($conditions_to_unset[$condition_id])) {
                        $unset[$condition_id] = 1;
                        continue;
                    }

                    foreach ($conditions_to_unset[$condition_id] as $taxonomy => $should_keep) {
                        //if group has a taxonomy with no term match, unset
                        if (!$should_keep) {
                            $unset[$condition_id] = 1;
                            break;
                        }
                    }
                }
            }

            foreach ($unset as $id => $value) {
                if (!isset($keep_archive[$id])) {
                    unset($posts[$id]);
                }
            }
        }

        return $posts;
    }

    /**
     * @inheritDoc
     */
    protected function _get_content($args = [])
    {
        $total_items = wp_count_terms($args['taxonomy'], [
            'hide_empty' => $args['hide_empty']
        ]);

        $start = $args['offset'];
        $end = $start + $args['number'];
        $walk_tree = false;
        $retval = [];

        if ($total_items) {
            $taxonomy = get_taxonomy($args['taxonomy']);

            if ($taxonomy->hierarchical && !$args['search'] && !$args['include']) {
                $args['number'] = 0;
                $args['offset'] = 0;

                $walk_tree = true;
            }

            $terms = new WP_Term_Query($args);

            if ($walk_tree) {
                $sorted_terms = [];
                foreach ($terms->terms as $term) {
                    $sorted_terms[$term->parent][] = $term;
                }
                $i = 0;
                $this->_walk_tree($sorted_terms, $sorted_terms[0], $i, $start, $end, 0, $retval);
            } else {
                //Hierarchical taxonomies use ids instead of slugs
                //see http://codex.wordpress.org/Function_Reference/wp_set_post_objects
                $value_var = ($taxonomy->hierarchical ? 'term_id' : 'slug');

                foreach ($terms->terms as $term) {
                    //term names are encoded
                    $retval[$term->$value_var] = htmlspecialchars_decode($term->name);
                }
            }
        }
        return $retval;
    }

    /**
     *  Get hierarchical content with level param
     *
     * @since  3.7.2
     * @param  array  $all_terms
     * @param  array  $terms
     * @param  int    $i
     * @param  int    $start
     * @param  int    $end
     * @param  int    $level
     * @param  array  &$retval
     * @return void
     */
    protected function _walk_tree($all_terms, $terms, &$i, $start, $end, $level, &$retval)
    {
        foreach ($terms as $term) {
            if ($i >= $end) {
                break;
            }

            if ($i >= $start) {
                $retval[] = [
                    'id'    => $term->term_id,
                    'text'  => htmlspecialchars_decode($term->name),
                    'level' => $level
                ];
            }

            $i++;

            if (isset($all_terms[$term->term_id])) {
                $this->_walk_tree($all_terms, $all_terms[$term->term_id], $i, $start, $end, $level + 1, $retval);
            }
        }
    }

    /**
     * Get registered public taxonomies
     *
     * @since   1.0
     * @return  array
     */
    protected function _get_taxonomies()
    {
        // List public taxonomies
        if (empty($this->taxonomy_objects)) {
            foreach (get_taxonomies(['public' => true], 'objects') as $tax) {
                $this->taxonomy_objects[$tax->name] = $tax;
            }
            if (defined('POLYLANG_VERSION')) {
                unset($this->taxonomy_objects['language']);
            }
        }
        return $this->taxonomy_objects;
    }

    /**
     * @inheritDoc
     */
    public function get_group_data($group_data, $post_id)
    {
        $ids = array_flip((array)get_post_custom_values(WPCACore::PREFIX . $this->id, $post_id));

        //Fetch all terms and group by tax to prevent lazy loading
        $terms = wp_get_object_terms(
            $post_id,
            array_keys($this->_get_taxonomies())
            // array(
            // 	'update_term_meta_cache' => false
            // )
        );
        $terms_by_tax = [];
        foreach ($terms as $term) {
            $terms_by_tax[$term->taxonomy][] = $term;
        }

        $title_count = $this->get_title_count();
        foreach ($this->_get_taxonomies() as $taxonomy) {
            $posts = isset($terms_by_tax[$taxonomy->name]) ? $terms_by_tax[$taxonomy->name] : 0;

            if ($posts || isset($ids[$taxonomy->name])) {
                $group_data[$this->id . '-' . $taxonomy->name] = $this->get_list_data($taxonomy, $title_count[$taxonomy->label]);
                $group_data[$this->id . '-' . $taxonomy->name]['label'] = $group_data[$this->id . '-' . $taxonomy->name]['text'];

                if ($posts) {
                    $retval = [];

                    //Hierarchical taxonomies use ids instead of slugs
                    //see http://codex.wordpress.org/Function_Reference/wp_set_post_objects
                    $value_var = ($taxonomy->hierarchical ? 'term_id' : 'slug');

                    foreach ($posts as $post) {
                        $retval[$post->$value_var] = $post->name;
                    }
                    $group_data[$this->id . '-' . $taxonomy->name]['data'] = $retval;
                }
            }
        }
        return $group_data;
    }

    /**
     * Count taxonomy labels to find shared ones
     *
     * @return array
     */
    protected function get_title_count()
    {
        $title_count = [];
        foreach ($this->_get_taxonomies() as $taxonomy) {
            if (!isset($title_count[$taxonomy->label])) {
                $title_count[$taxonomy->label] = 0;
            }
            $title_count[$taxonomy->label]++;
        }
        return $title_count;
    }

    /**
     * @param WP_Taxonomy $taxonomy
     * @param int $title_count
     * @return array
     */
    protected function get_list_data($taxonomy, $title_count)
    {
        $placeholder = '/' . sprintf(__('%s Archives', WPCA_DOMAIN), $taxonomy->labels->singular_name);
        $placeholder = $taxonomy->labels->all_items . $placeholder;
        $label = $taxonomy->label;

        if (count($taxonomy->object_type) === 1 && $title_count > 1) {
            $post_type = get_post_type_object($taxonomy->object_type[0]);
            $label .= ' (' . $post_type->label . ')';
        }

        return [
            'text'          => $label,
            'icon'          => $taxonomy->hierarchical ? 'dashicons-category' : 'dashicons-tag',
            'placeholder'   => $placeholder,
            'default_value' => $taxonomy->name
        ];
    }

    /**
     * @inheritDoc
     */
    public function list_module($list)
    {
        $title_count = $this->get_title_count();
        foreach ($this->_get_taxonomies() as $taxonomy) {
            $data = $this->get_list_data($taxonomy, $title_count[$taxonomy->label]);
            $data['id'] = $this->id . '-' . $taxonomy->name;
            $list[] = $data;
        }
        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function parse_query_args($args)
    {
        if (isset($args['item_object'])) {
            preg_match('/taxonomy-(.+)$/i', $args['item_object'], $matches);
            $args['item_object'] = isset($matches[1]) ? $matches[1] : '___';
            $taxonomy_name = $args['item_object'];
        } else {
            $taxonomy_name = 'category';
        }

        $this->remove_conflict_filters();

        return [
            'include'                => $args['include'],
            'taxonomy'               => $taxonomy_name,
            'number'                 => $args['limit'],
            'offset'                 => ($args['paged'] - 1) * $args['limit'],
            'orderby'                => 'name',
            'order'                  => 'ASC',
            'search'                 => $args['search'],
            'hide_empty'             => false,
            'update_term_meta_cache' => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function save_data($post_id)
    {
        $meta_key = WPCACore::PREFIX . $this->id;
        $old = array_flip(get_post_meta($post_id, $meta_key, false));
        $tax_input = $_POST['conditions'];

        $has_select_terms = false;

        //Save terms
        //Loop through each public taxonomy
        foreach ($this->_get_taxonomies() as $taxonomy) {
            //If no terms, maybe delete old ones
            if (!isset($tax_input[$this->id . '-' . $taxonomy->name])) {
                $terms = [];
                if (isset($old[$taxonomy->name])) {
                    delete_post_meta($post_id, $meta_key, $taxonomy->name);
                }
            } else {
                $terms = $tax_input[$this->id . '-' . $taxonomy->name];

                $found_key = array_search($taxonomy->name, $terms);
                //If meta key found maybe add it
                if ($found_key !== false) {
                    if (!isset($old[$taxonomy->name])) {
                        add_post_meta($post_id, $meta_key, $taxonomy->name);
                    }
                    unset($terms[$found_key]);
                //Otherwise maybe delete it
                } elseif (isset($old[$taxonomy->name])) {
                    delete_post_meta($post_id, $meta_key, $taxonomy->name);
                }

                //Hierarchical taxonomies use ids instead of slugs
                //see http://codex.wordpress.org/Function_Reference/wp_set_post_terms
                if ($taxonomy->hierarchical) {
                    $terms = array_unique(array_map('intval', $terms));
                }
            }

            if (!empty($terms)) {
                $has_select_terms = true;
            }

            $this->remove_conflict_filters();
            wp_set_object_terms($post_id, $terms, $taxonomy->name);
            $this->restore_conflict_filters();
        }

        if ($has_select_terms && !isset($old[self::VALUE_HAS_TERMS])) {
            add_post_meta($post_id, $meta_key, self::VALUE_HAS_TERMS);
        } elseif (!$has_select_terms && isset($old[self::VALUE_HAS_TERMS])) {
            delete_post_meta($post_id, $meta_key, self::VALUE_HAS_TERMS);
        }
    }

    /**
     * Auto-select children of selected ancestor
     *
     * @since  1.0
     * @param  int    $term_id
     * @param  int    $tt_id
     * @param  string $taxonomy
     * @return void
     */
    public function term_ancestry_check($term_id, $tt_id, $taxonomy)
    {
        if (is_taxonomy_hierarchical($taxonomy)) {
            $this->remove_conflict_filters();
            $term = get_term($term_id, $taxonomy);

            if ($term->parent != '0') {
                // Get sidebars with term ancestor wanting to auto-select term
                $query = new WP_Query([
                    'post_type'   => WPCACore::TYPE_CONDITION_GROUP,
                    'post_status' => [WPCACore::STATUS_OR,WPCACore::STATUS_EXCEPT,WPCACore::STATUS_PUBLISHED],
                    'meta_query'  => [
                        [
                            'key'     => WPCACore::PREFIX . 'autoselect',
                            'value'   => 1,
                            'compare' => '='
                        ]
                    ],
                    'tax_query' => [
                        [
                            'taxonomy'         => $taxonomy,
                            'field'            => 'id',
                            'terms'            => get_ancestors($term_id, $taxonomy),
                            'include_children' => false
                        ]
                    ]
                ]);
                if ($query && $query->found_posts) {
                    foreach ($query->posts as $post) {
                        wp_set_post_terms($post->ID, $term_id, $taxonomy, true);
                    }
                    do_action('wpca/modules/auto-select/' . $this->category, $query->posts, $term);
                }
            }
            $this->restore_conflict_filters();
        }
    }

    private function remove_conflict_filters()
    {
        global $sitepress;
        if(!empty($sitepress)) {
            remove_filter('get_terms_args', [$sitepress, 'get_terms_args_filter']);
            remove_filter('get_term', [$sitepress, 'get_term_adjust_id'], 1, 1);
            remove_filter('terms_clauses', [$sitepress, 'terms_clauses']);
        }
    }

    private function restore_conflict_filters()
    {
        global $sitepress;
        if(!empty($sitepress)) {
            add_filter('get_terms_args', [$sitepress, 'get_terms_args_filter'], 10 ,2);
            add_filter('get_term', [$sitepress, 'get_term_adjust_id'], 1);
            add_filter('terms_clauses', [$sitepress, 'terms_clauses'], 10, 3);
        }
    }
}
