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
 * Post Type Module
 *
 * Detects if current content is:
 * a) specific post type or specific post
 * b) specific post type archive or home
 *
 */
class WPCAModule_post_type extends WPCAModule_Base
{
    /**
     * @var string
     */
    protected $category = 'post_type';

    /**
     * Registered public post types
     *
     * @var array
     */
    private $_post_types;

    /**
     * Conditions to inherit from post ancestors
     * @var array
     */
    private $_post_ancestor_conditions;

    public function __construct()
    {
        parent::__construct('post_type', __('Post Types', WPCA_DOMAIN));
        $this->query_name = 'cp';
    }

    /**
     * @inheritDoc
     */
    public function initiate()
    {
        parent::initiate();

        add_action(
            'transition_post_status',
            [$this,'post_ancestry_check'],
            10,
            3
        );

        if (is_admin()) {
            foreach ($this->post_types() as $post_type) {
                add_action(
                    'wp_ajax_wpca/module/' . $this->id . '-' . $post_type,
                    [$this,'ajax_print_content']
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function _get_content($args = [])
    {
        $walk_tree = false;
        $start = ($args['paged'] - 1) * $args['posts_per_page'];
        $end = $start + $args['posts_per_page'];

        //WordPress searches in title and content by default
        //We want to search in title and slug
        if (!empty($args['search'])) {
            $exclude_query = '';
            if (!empty($args['post__not_in'])) {
                $exclude_query = ' AND ID NOT IN (' . implode(',', $args['post__not_in']) . ')';
            }

            $columns = [
                ['post_title', 'LIKE', '%' . $args['search'] . '%'],
                ['post_name', 'LIKE', '%' . $args['search'] . '%'],
            ];

            if (is_numeric($args['search'])) {
                $columns[] = ['ID', '=', $args['search']];
            }

            $where = [];
            $values = [];
            foreach ($columns as $column_value) {
                list($column, $operator, $value) = $column_value;
                $prepared_value = is_numeric($value) ? '%d' : '%s';
                $where[] = "$column $operator '$prepared_value'";
                $values[] = $value;
            }

            //Using unprepared (safe) exclude because WP is not good at parsing arrays
            global $wpdb;
            $posts = $wpdb->get_results($wpdb->prepare(
                "
				SELECT ID, post_title, post_type, post_parent, post_status, post_password
                FROM $wpdb->posts
                WHERE (" . implode(' OR ', $where) . ")
                AND post_status IN('" . implode("','", $args['post_status']) . "')
                AND post_type = '%s'
                $exclude_query
				ORDER BY post_title ASC
				LIMIT %d,%d
				",
                array_merge($values, [
                    $args['post_type'],
                    $start,
                    $args['posts_per_page']
                ])
            ));
        } else {
            if (is_post_type_hierarchical($args['post_type']) && !isset($args['post__in'])) {
                $args['posts_per_page'] = -1;
                $args['paged'] = 0;
                $args['orderby'] = 'menu_order title';

                $walk_tree = true;
            }
            $query = new WP_Query($args);
            $posts = $query->posts;
        }

        $retval = [];

        if ($walk_tree) {
            $pages_sorted = [];
            foreach ($posts as $post) {
                $pages_sorted[$post->post_parent][] = $post;
            }
            $i = 0;
            $this->_walk_tree($pages_sorted, $pages_sorted[0], $i, $start, $end, 0, $retval);
        } else {
            foreach ($posts as $post) {
                $retval[$post->ID] = $this->post_title($post);
            }
        }

        return $retval;
    }

    /**
     * Get hierarchical content with level param
     *
     * @since  3.7.2
     * @param  array  $all_pages
     * @param  array  $pages
     * @param  int    $i
     * @param  int    $start
     * @param  int    $end
     * @param  int    $level
     * @param  array  &$retval
     * @return void
     */
    protected function _walk_tree($all_pages, $pages, &$i, $start, $end, $level, &$retval)
    {
        foreach ($pages as $page) {
            if ($i >= $end) {
                break;
            }

            if ($i >= $start) {
                $retval[] = [
                    'id'    => $page->ID,
                    'text'  => $this->post_title($page),
                    'level' => $level
                ];
            }

            $i++;

            if (isset($all_pages[$page->ID])) {
                $this->_walk_tree($all_pages, $all_pages[$page->ID], $i, $start, $end, $level + 1, $retval);
            }
        }
    }

    /**
     * Get registered public post types
     *
     * @since   4.0
     * @return  array
     */
    public function post_types()
    {
        if (!$this->_post_types) {
            // List public post types
            foreach (get_post_types(['public' => true], 'names') as $post_type) {
                $this->_post_types[$post_type] = $post_type;
            }
            unset($this->_post_types['guest-author']);
        }
        return $this->_post_types;
    }

    /**
     * @inheritDoc
     */
    public function get_group_data($group_data, $post_id)
    {
        $ids = get_post_custom_values(WPCACore::PREFIX . $this->id, $post_id);
        if ($ids) {
            $lookup = array_flip((array)$ids);
            foreach ($this->post_types() as $post_type) {
                $post_type_obj = get_post_type_object($post_type);
                $data = $this->get_content([
                    'include'   => $ids,
                    'post_type' => $post_type
                ]);

                if ($data || isset($lookup[$post_type])) {
                    $placeholder = $post_type_obj->labels->all_items;
                    switch ($post_type) {
                        case 'post':
                            $placeholder .= ' / ' . __('Blog Page', WPCA_DOMAIN);
                            break;
                        case 'product':
                            $placeholder .= ' / ' . __('Shop Page', WPCA_DOMAIN);
                            break;
                        default:
                            if ($post_type_obj->has_archive) {
                                $placeholder .= ' / ' . sprintf(__('%s Archives', WPCA_DOMAIN), $post_type_obj->labels->singular_name);
                            }
                            break;
                    }

                    $group_data[$this->id . '-' . $post_type] = [
                        'label'         => $post_type_obj->label,
                        'icon'          => $post_type_obj->menu_icon,
                        'placeholder'   => $placeholder,
                        'default_value' => $post_type
                    ];

                    if ($data) {
                        $group_data[$this->id . '-' . $post_type]['data'] = $data;
                    }
                }
            }
        }
        return $group_data;
    }

    /**
     * @inheritDoc
     */
    public function in_context()
    {
        return ((is_singular() || is_home()) && !is_front_page()) || is_post_type_archive();
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        if (is_singular()) {
            return [
                get_post_type(),
                get_queried_object_id()
            ];
        }

        // Home has post as default post type
        $post_type = get_query_var('post_type');
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        } elseif (!$post_type) {
            $post_type = 'post';
        }

        return [
            $post_type
        ];
    }

    /**
     * @inheritDoc
     */
    protected function parse_query_args($args)
    {
        if (isset($args['item_object'])) {
            preg_match('/post_type-(.+)$/i', $args['item_object'], $matches);
            $post_type_name = isset($matches[1]) ? $matches[1] : '___';
        } else {
            $post_type_name = isset($args['post_type']) ? $args['post_type'] : 'category';
        }

        $exclude = [];
        if ($post_type_name == 'page' && 'page' == get_option('show_on_front')) {
            $exclude[] = intval(get_option('page_on_front'));
            $exclude[] = intval(get_option('page_for_posts'));
        }

        $post_status = ['publish','private','future','draft'];
        if ($post_type_name == 'attachment') {
            $post_status = ['inherit'];
        }

        $new_args = [
            'post__not_in'           => $exclude,
            'post_type'              => $post_type_name,
            'post_status'            => $post_status,
            'orderby'                => 'title',
            'order'                  => 'ASC',
            'paged'                  => $args['paged'],
            'posts_per_page'         => $args['limit'],
            'search'                 => $args['search'],
            'ignore_sticky_posts'    => true,
            'update_post_term_cache' => false,
            'suppress_filters'       => true,
            'no_found_rows'          => true,
        ];

        //future proof in case this is considered a bug https://core.trac.wordpress.org/ticket/28099
        if (!empty($args['include'])) {
            $new_args['post__in'] = $args['include'];
        }

        return $new_args;
    }

    /**
     * @inheritDoc
     */
    public function list_module($list)
    {
        foreach ($this->post_types() as $post_type) {
            $post_type_obj = get_post_type_object($post_type);

            $name = $post_type_obj->label;
            $placeholder = $post_type_obj->labels->all_items;

            switch ($post_type) {
                case 'post':
                    $name .= ' / ' . __('Blog', WPCA_DOMAIN);
                    $placeholder .= ' / ' . __('Blog Page', WPCA_DOMAIN);
                    break;
                case 'product':
                    $name .= ' / ' . __('Shop', WPCA_DOMAIN);
                    $placeholder .= ' / ' . __('Shop Page', WPCA_DOMAIN);
                    break;
                default:
                    if ($post_type_obj->has_archive) {
                        $placeholder .= ' / ' . sprintf(__('%s Archives', WPCA_DOMAIN), $post_type_obj->labels->singular_name);
                    }
                    break;
            }

            $list[] = [
                'id'            => $this->id . '-' . $post_type,
                'icon'          => $post_type_obj->menu_icon,
                'text'          => $name,
                'placeholder'   => $placeholder,
                'default_value' => $post_type
            ];
        }
        return $list;
    }

    /**
     * Get post title and state
     *
     * @since  3.7
     * @param  WP_Post  $post
     * @return string
     */
    public function post_title($post)
    {
        $post_states = [];

        if (!empty($post->post_password)) {
            $post_states['protected'] = __('Password protected');
        }

        if (is_sticky($post->ID)) {
            $post_states['sticky'] = __('Sticky');
        }

        switch ($post->post_status) {
            case 'private':
                $post_states['private'] = __('Private');
                break;
            case 'draft':
                $post_states['draft'] = __('Draft');
                break;
            case 'pending':
                /* translators: post state */
                $post_states['pending'] = _x('Pending', 'post state');
                break;
            case 'scheduled':
                $post_states['scheduled'] = __('Scheduled');
                break;
            default:
                break;
        }

        $post_title = $post->post_title ? $post->post_title : __('(no title)');
        //$post_states = apply_filters('display_post_states', $post_states, $post);

        return $post_title . ' ' . ($post_states ? ' (' . implode(', ', $post_states) . ')' : '');
    }

    /**
     * @inheritDoc
     */
    public function save_data($post_id)
    {
        $meta_key = WPCACore::PREFIX . $this->id;
        $old = array_flip(get_post_meta($post_id, $meta_key, false));
        $new = [];

        foreach ($this->post_types() as $post_type) {
            $id = $this->id . '-' . $post_type;
            if (isset($_POST['conditions'][$id])) {
                $new = array_merge($new, $_POST['conditions'][$id]);
            }
        }

        if ($new) {
            //$new = array_unique($new);
            // Skip existing data or insert new data
            foreach ($new as $new_single) {
                if (isset($old[$new_single])) {
                    unset($old[$new_single]);
                } else {
                    add_post_meta($post_id, $meta_key, $new_single);
                }
            }
            // Remove existing data that have not been skipped
            foreach ($old as $old_key => $old_value) {
                delete_post_meta($post_id, $meta_key, $old_key);
            }
        } elseif (!empty($old)) {
            // Remove any old values when $new is empty
            delete_post_meta($post_id, $meta_key);
        }
    }

    /**
     * Check if post ancestors have sidebar conditions
     *
     * @since  1.0
     * @param  string  $new_status
     * @param  string  $old_status
     * @param  WP_Post $post
     * @return void
     */
    public function post_ancestry_check($new_status, $old_status, $post)
    {
        if (!WPCACore::types()->has($post->post_type) && $post->post_type != WPCACore::TYPE_CONDITION_GROUP && $post->post_parent) {
            $status = [
                'publish' => 1,
                'private' => 1,
                'future'  => 1
            ];

            // Only new posts are relevant
            if (!isset($status[$old_status]) && isset($status[$new_status])) {
                $post_type = get_post_type_object($post->post_type);
                if ($post_type->hierarchical && $post_type->public) {
                    // Get sidebars with post ancestor wanting to auto-select post
                    $query = new WP_Query([
                        'post_type'   => WPCACore::TYPE_CONDITION_GROUP,
                        'post_status' => [WPCACore::STATUS_OR,WPCACore::STATUS_EXCEPT,WPCACore::STATUS_PUBLISHED],
                        'meta_query'  => [
                            'relation' => 'AND',
                            [
                                'key'     => WPCACore::PREFIX . 'autoselect',
                                'value'   => 1,
                                'compare' => '='
                            ],
                            [
                                'key'     => WPCACore::PREFIX . $this->id,
                                'value'   => get_post_ancestors($post),
                                'type'    => 'numeric',
                                'compare' => 'IN'
                            ]
                        ]
                    ]);

                    if ($query && $query->found_posts) {
                        //Add conditions after Quick Select
                        //otherwise they will be removed there
                        $this->_post_ancestor_conditions = $query->posts;
                        add_action(
                            'save_post_' . $post->post_type,
                            [$this,'post_ancestry_add'],
                            99,
                            2
                        );
                        do_action('wpca/modules/auto-select/' . $this->category, $query->posts, $post);
                    }
                }
            }
        }
    }

    /**
     * Add sidebar conditions from post ancestors
     *
     * @since  3.1.1
     * @param  int      $post_id
     * @param  WP_Post  $post
     * @return void
     */
    public function post_ancestry_add($post_id, $post)
    {
        if ($this->_post_ancestor_conditions) {
            foreach ($this->_post_ancestor_conditions as $condition) {
                add_post_meta($condition->ID, WPCACore::PREFIX . $this->id, $post_id);
            }
        }
    }
}
