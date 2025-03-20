<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */

defined('ABSPATH') || exit;

if (!class_exists('WPCACore')) {
    $domain = explode('/', plugin_basename(__FILE__));
    define('WPCA_DOMAIN', $domain[0]);
    define('WPCA_PATH', plugin_dir_path(__FILE__));

    /**
     * Core for WordPress Content Aware Engine
     */
    final class WPCACore
    {
        /**
         * Using class prefix instead of namespace
         * for PHP5.2 compatibility
         */
        const CLASS_PREFIX = 'WPCA';

        /**
         * Prefix for data (keys) stored in database
         */
        const PREFIX = '_ca_';

        /**
         * Post Type for condition groups
         */
        const TYPE_CONDITION_GROUP = 'condition_group';

        /**
         * Post Statuses for condition groups
         */
        /**
         * @deprecated
         */
        const STATUS_NEGATED = 'negated';
        /**
         * @deprecated
         */
        const STATUS_PUBLISHED = 'publish';
        const STATUS_OR = 'wpca_or';
        const STATUS_EXCEPT = 'wpca_except';

        /**
         * Exposures for condition groups
         */
        const EXP_SINGULAR = 0;
        const EXP_SINGULAR_ARCHIVE = 1;
        const EXP_ARCHIVE = 2;

        /**
         * @deprecated 7.0
         */
        const CAPABILITY = 'edit_theme_options';

        /**
         * Name for generated nonces
         */
        const NONCE = '_ca_nonce';

        const OPTION_CONDITION_TYPE_CACHE = '_ca_condition_type_cache';
        const OPTION_POST_TYPE_OPTIONS = '_ca_post_type_options';

        /**
         * Post Types that use the engine
         * @var WPCATypeManager
         */
        private static $type_manager;

        /**
         * Conditions retrieved from database
         * @var array
         */
        private static $condition_cache = [];

        /**
         * Objects retrieved from database
         * @var array
         */
        private static $post_cache = [];

        private static $wp_query_original = [];

        private static $filtered_modules = [];

        /**
         * Constructor
         */
        public static function init()
        {
            spl_autoload_register([__CLASS__,'_autoload_class_files']);

            if (is_admin()) {
                add_action(
                    'admin_enqueue_scripts',
                    [__CLASS__,'add_group_script_styles'],
                    9
                );
                add_action(
                    'delete_post',
                    [__CLASS__,'sync_group_deletion']
                );
                add_action(
                    'trashed_post',
                    [__CLASS__,'sync_group_trashed']
                );
                add_action(
                    'untrashed_post',
                    [__CLASS__,'sync_group_untrashed']
                );
                add_action(
                    'wpca/modules/save-data',
                    [__CLASS__,'save_condition_options'],
                    10,
                    3
                );

                add_action(
                    'wp_ajax_wpca/add-rule',
                    [__CLASS__,'ajax_update_group']
                );
            }

            add_action(
                'init',
                [__CLASS__,'add_group_post_type'],
                99
            );

            add_action(
                'init',
                [__CLASS__,'schedule_cache_condition_types'],
                99
            );

            add_action(
                'wpca/cache_condition_types',
                [__CLASS__,'cache_condition_types'],
                999
            );
        }

        /**
         * Get type manager
         *
         * @since   4.0
         * @return  WPCATypeManager
         */
        public static function types()
        {
            if (!isset(self::$type_manager)) {
                self::$type_manager = new WPCATypeManager();
            }
            return self::$type_manager;
        }

        /**
         * @since 8.0
         *
         * @return void
         */
        public static function schedule_cache_condition_types()
        {
            if (wp_next_scheduled('wpca/cache_condition_types') !== false) {
                return;
            }

            wp_schedule_event(get_gmt_from_date('today 02:00:00', 'U'), 'daily', 'wpca/cache_condition_types');
        }

        /**
         * Cache condition types currently in use
         *
         * @since 8.0
         *
         * @return void
         */
        public static function cache_condition_types()
        {
            $all_modules = [];
            $modules_by_type = [];
            $cache = [];

            $types = self::types();
            foreach ($types as $type => $modules) {
                $modules_by_type[$type] = [];
                $cache[$type] = [];
                foreach ($modules as $module) {
                    $modules_by_type[$type][$module->get_data_key()] = $module->get_id();
                    $all_modules[$module->get_data_key()] = $module->get_data_key();
                }
            }

            if (!$all_modules) {
                update_option(self::OPTION_CONDITION_TYPE_CACHE, []);
                return;
            }

            global $wpdb;

            $query = '
SELECT p.post_type, m.meta_key
FROM ' . $wpdb->posts . ' p
INNER JOIN ' . $wpdb->posts . ' c ON c.post_parent = p.ID
INNER JOIN ' . $wpdb->postmeta . ' m ON m.post_id = c.ID
WHERE p.post_type IN (' . self::sql_prepare_in(array_keys($modules_by_type)) . ')
AND m.meta_key IN (' . self::sql_prepare_in($all_modules) . ')
GROUP BY p.post_type, m.meta_key
';

            $results = (array) $wpdb->get_results($query);

            foreach ($results as $result) {
                if (isset($modules_by_type[$result->post_type][$result->meta_key])) {
                    $cache[$result->post_type][] = $modules_by_type[$result->post_type][$result->meta_key];
                }
            }

            update_option(self::OPTION_CONDITION_TYPE_CACHE, $cache);
        }

        /**
         * Register group post type
         *
         * @since   1.0
         * @return  void
         */
        public static function add_group_post_type()
        {
            //This is just a safety placeholder,
            //authorization will be done with parent object's cap
            $capability = 'edit_theme_options';
            $capabilities = [
                'edit_post'          => $capability,
                'read_post'          => $capability,
                'delete_post'        => $capability,
                'edit_posts'         => $capability,
                'delete_posts'       => $capability,
                'edit_others_posts'  => $capability,
                'publish_posts'      => $capability,
                'read_private_posts' => $capability
            ];

            register_post_type(self::TYPE_CONDITION_GROUP, [
                'labels' => [
                    'name'          => __('Condition Groups', WPCA_DOMAIN),
                    'singular_name' => __('Condition Group', WPCA_DOMAIN),
                ],
                'capabilities'        => $capabilities,
                'public'              => false,
                'hierarchical'        => false,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'show_ui'             => false,
                'show_in_menu'        => false,
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => false,
                'show_in_rest'        => false,
                'has_archive'         => false,
                'rewrite'             => false,
                'query_var'           => false,
                'supports'            => ['title'],
                'can_export'          => false,
                'delete_with_user'    => false
            ]);

            register_post_status(self::STATUS_NEGATED, [
                'label'                     => _x('Negated', 'condition status', WPCA_DOMAIN),
                'public'                    => false,
                'exclude_from_search'       => true,
                'show_in_admin_all_list'    => false,
                'show_in_admin_status_list' => false,
            ]);
            register_post_status(self::STATUS_EXCEPT, [
                'label'                     => _x('Exception', 'condition status', WPCA_DOMAIN),
                'public'                    => false,
                'exclude_from_search'       => true,
                'show_in_admin_all_list'    => false,
                'show_in_admin_status_list' => false,
            ]);
            register_post_status(self::STATUS_OR, [
                'label'                     => _x('Or', 'condition status', WPCA_DOMAIN),
                'public'                    => false,
                'exclude_from_search'       => true,
                'show_in_admin_all_list'    => false,
                'show_in_admin_status_list' => false,
            ]);
        }

        /**
         * Get group IDs by their parent ID
         *
         * @since   1.0
         * @param   int    $parent_id
         * @return  array
         */
        private static function get_group_ids_by_parent($parent_id)
        {
            if (!self::types()->has(get_post_type($parent_id))) {
                return [];
            }

            global $wpdb;
            return (array)$wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = '%d'", $parent_id));
        }

        /**
         * Delete groups from database when their parent is deleted
         *
         * @since  1.0
         * @param  int    $post_id
         * @return void
         */
        public static function sync_group_deletion($post_id)
        {
            $groups = self::get_group_ids_by_parent($post_id);
            if ($groups) {
                foreach ($groups as $group_id) {
                    //Takes care of metadata and terms too
                    wp_delete_post($group_id, true);
                }
            }
        }

        /**
         * Trash groups when their parent is trashed
         *
         * @since   1.0
         * @param   int    $post_id
         * @return  void
         */
        public static function sync_group_trashed($post_id)
        {
            $groups = self::get_group_ids_by_parent($post_id);
            if ($groups) {
                foreach ($groups as $group_id) {
                    wp_trash_post($group_id);
                }
            }
        }

        /**
         * Untrash groups when their parent is untrashed
         *
         * @since   1.0
         * @param   int    $post_id
         * @return  void
         */
        public static function sync_group_untrashed($post_id)
        {
            $groups = self::get_group_ids_by_parent($post_id);
            if ($groups) {
                foreach ($groups as $group_id) {
                    wp_untrash_post($group_id);
                }
            }
        }

        /**
         * @param string $post_type
         * @return array
         */
        public static function get_conditional_modules($post_type)
        {
            if (!isset(self::$filtered_modules[$post_type])) {
                return [];
            }
            return self::$filtered_modules[$post_type];
        }

        /**
         * Get filtered condition groups
         *
         * @since  2.0
         * @return array
         */
        public static function get_conditions($post_type)
        {
            global $wpdb, $wp_query, $post;

            if (!self::types()->has($post_type) || (!$wp_query->query && !$post) || is_admin()) {
                return [];
            }

            // Return cache if present
            if (isset(self::$condition_cache[$post_type])) {
                return self::$condition_cache[$post_type];
            }

            $where = [];
            $join = [];

            $cache = [
                $post_type
            ];

            $modules = self::types()->get($post_type)->all();
            $modules = self::filter_condition_type_cache($post_type, $modules);

            //avoid combining as long as negated conditions are being deprecated
            // foreach (self::types() as $other_type => $other_modules) {
            //     if ($other_type == $post_type) {
            //         continue;
            //     }
            //     if (self::filter_condition_type_cache($other_type, $other_modules->all()) === $modules) {
            //         $cache[] = $other_type;
            //     }
            // }

            self::fix_wp_query();

            $in_context_by_module_id = [];
            foreach ($modules as $module) {
                $id = $module->get_id();
                $in_context = apply_filters("wpca/module/$id/in-context", $module->in_context());
                $in_context_by_module_id[$id] = $in_context;

                if (!$in_context) {
                    continue;
                }

                $data = $module->get_context_data();

                if (empty($data)) {
                    $in_context_by_module_id[$id] = false;
                    continue;
                }

                if (is_array($data)) {
                    $name = $module->get_query_name();
                    $data = "($name.meta_value IS NULL OR $name.meta_value IN (" . self::sql_prepare_in($data) . '))';
                }
                $join[$id] = $module->db_join();
                $where[$id] = apply_filters("wpca/module/$id/db-where", $data);
                self::$filtered_modules[$post_type][] = $module;
            }

            $use_negated_conditions = self::get_option($post_type, 'legacy.negated_conditions', false);

            // Check if there are any conditions for current content
            $groups_in_context = [];
            if (!empty($where)) {
                $post_status = [
                    self::STATUS_PUBLISHED,
                    self::STATUS_OR,
                    self::STATUS_EXCEPT
                ];

                if ($use_negated_conditions) {
                    $post_status[] = self::STATUS_NEGATED;
                }

                $chunk_size = count($join);
                if (defined('WPCA_SQL_JOIN_SIZE') && is_integer(WPCA_SQL_JOIN_SIZE) && WPCA_SQL_JOIN_SIZE > 0) {
                    $chunk_size = WPCA_SQL_JOIN_SIZE;
                }

                $joins = array_chunk($join, $chunk_size);
                $joins_max = count($joins) - 1;
                $wheres = array_chunk($where, $chunk_size);
                $group_ids = [];

                $where2 = [];
                $where2[] = "p.post_type = '" . self::TYPE_CONDITION_GROUP . "'";
                $where2[] = "p.post_status IN ('" . implode("','", $post_status) . "')";
                $where2[] = 'p.menu_order ' . (is_archive() || is_home() ? '>=' : '<=') . ' 1';

                foreach ($joins as $i => $join) {
                    if ($i == $joins_max) {
                        $groups_in_context = $wpdb->get_results(
                            'SELECT p.ID, p.post_parent, p.post_status ' .
                            "FROM $wpdb->posts p " .
                            implode(' ', $join) . '
                            WHERE
                            ' . implode(' AND ', $wheres[$i]) . '
                            AND ' . implode(' AND ', $where2) .
                            (!empty($group_ids) ? ' AND p.id IN (' . implode(',', $group_ids) . ')' : ''),
                            OBJECT_K
                        );
                        break;
                    }

                    $group_ids = array_merge($group_ids, $wpdb->get_col(
                        'SELECT p.ID ' .
                        "FROM $wpdb->posts p " .
                        implode(' ', $join) . '
                        WHERE
                        ' . implode(' AND ', $wheres[$i]) . '
                        AND ' . implode(' AND ', $where2)
                    ));
                }
            }

            $groups_negated = [];
            if ($use_negated_conditions) {
                $groups_negated = $wpdb->get_results($wpdb->prepare(
                    'SELECT p.ID, p.post_parent ' .
                    "FROM $wpdb->posts p " .
                    "WHERE p.post_type = '%s' " .
                    "AND p.post_status = '%s' ",
                    self::TYPE_CONDITION_GROUP,
                    self::STATUS_NEGATED
                ), OBJECT_K);
            }

            if (!empty($groups_in_context) || !empty($groups_negated)) {
                //Force update of meta cache to prevent lazy loading
                update_meta_cache('post', array_keys($groups_in_context + $groups_negated));
            }

            //Exclude types that have unrelated content in same group
            foreach ($modules as $module) {
                $groups_in_context = $module->filter_excluded_context(
                    $groups_in_context,
                    $in_context_by_module_id[$module->get_id()]
                );
            }

            //exclude exceptions
            $excepted = [];
            foreach ($groups_in_context as $group_id => $group) {
                if ($group->post_status == self::STATUS_EXCEPT) {
                    $excepted[$group->post_parent] = 1;
                }
            }

            //condition group => type
            $valid = [];
            foreach ($groups_in_context as $group) {
                $valid[$group->ID] = $group->post_parent;
            }

            foreach ($valid as $group_id => $parent_id) {
                if (isset($excepted[$parent_id])) {
                    unset($valid[$group_id]);
                }
            }

            if ($use_negated_conditions) {
                //Filter negated groups
                //type => group
                $handled_already = array_flip($valid);
                foreach ($groups_negated as $group) {
                    if (isset($valid[$group->ID])) {
                        unset($valid[$group->ID]);
                    } else {
                        $valid[$group->ID] = $group->post_parent;
                    }
                    if (isset($handled_already[$group->post_parent])) {
                        unset($valid[$group->ID]);
                    }
                    $handled_already[$group->post_parent] = 1;
                }
            }

            self::restore_wp_query();

            foreach ($cache as $cache_type) {
                self::$condition_cache[$cache_type] = $valid;
            }

            return self::$condition_cache[$post_type];
        }

        /**
         * Get filtered posts from a post type
         *
         * @since  1.0
         * @return array|bool
         */
        public static function get_posts($post_type)
        {
            if (isset(self::$post_cache[$post_type])) {
                return self::$post_cache[$post_type];
            }

            $valid = self::get_conditions($post_type);

            //if cache hasn't been set, method was called too early
            if (!isset(self::$condition_cache[$post_type])) {
                return false;
            }

            self::$post_cache[$post_type] = [];

            $results = [];

            if (!empty($valid)) {
                $data = new WP_Query([
                    'post__in'               => array_values($valid),
                    'post_type'              => $post_type,
                    'post_status'            => 'publish',
                    'posts_per_page'         => -1,
                    'ignore_sticky_posts'    => true,
                    'update_post_term_cache' => false,
                    'update_post_meta_cache' => true,
                    'suppress_filters'       => true,
                    'no_found_rows'          => true,
                    'orderby'                => 'none'
                ]);

                $results = array_merge($results, $data->posts);
            }

            //legacy sorting
            uasort($results, function (WP_Post $post_a, WP_Post $post_b) {
                //asc
                if ($post_a->menu_order != $post_b->menu_order) {
                    return $post_a->menu_order < $post_b->menu_order ? -1 : 1;
                }

                $post_a_handle = get_post_meta($post_a->ID, '_ca_handle', true);
                $post_b_handle = get_post_meta($post_b->ID, '_ca_handle', true);

                //desc
                if ($post_a_handle != $post_b_handle) {
                    return $post_a_handle > $post_b_handle ? -1 : 1;
                }

                //desc
                if ($post_a->post_date != $post_b->post_date) {
                    return $post_a->post_date > $post_b->post_date ? -1 : 1;
                }

                return 0;
            });

            $results = array_reduce($results, function ($carry, $post) {
                $carry[$post->ID] = (object)[
                    'ID'         => $post->ID,
                    'post_type'  => $post->post_type,
                    'handle'     => get_post_meta($post->ID, '_ca_handle', true),
                    'menu_order' => $post->menu_order,
                    'post_date'  => $post->post_date
                ];
                return $carry;
            }, []);

            self::$post_cache[$post_type] = apply_filters("wpca/posts/$post_type", $results);

            return self::$post_cache[$post_type];
        }

        public static function render_group_meta_box($post, $screen, $context = 'normal', $priority = 'default')
        {
            if (!($post instanceof WP_Post) || !self::types()->has($post->post_type)) {
                return;
            }

            $post_type_obj = get_post_type_object($post->post_type);

            if (!current_user_can($post_type_obj->cap->edit_post, $post->ID)) {
                return;
            }

            $template = WPCAView::make('condition_options', [
                'post_type' => $post->post_type
            ]);
            add_action('wpca/group/settings', [$template,'render'], -1, 2);

            $template = WPCAView::make('group_template', [
                'post_type' => $post->post_type
            ]);
            add_action('admin_footer', [$template,'render']);

            $template = WPCAView::make('condition_template');
            add_action('admin_footer', [$template,'render']);

            $view = WPCAView::make('meta_box', [
                'post_type' => $post->post_type,
                'nonce'     => wp_nonce_field(self::PREFIX . $post->ID, self::NONCE, true, false),
            ]);

            $title = isset($post_type_obj->labels->ca_title) ? $post_type_obj->labels->ca_title : __('Conditional Logic', WPCA_DOMAIN);

            add_meta_box(
                'cas-rules',
                $title,
                [$view,'render'],
                $screen,
                $context,
                $priority
            );
        }

        /**
         * Insert new condition group for a post type
         * Uses current post per default
         *
         * @param int|null $post_id
         * @return int|WP_Error
         */
        public static function add_condition_group($post_id = null)
        {
            $post = get_post($post_id);

            //Make sure to go from auto-draft to draft
            if ($post->post_status == 'auto-draft') {
                wp_update_post([
                    'ID'          => $post->ID,
                    'post_title'  => '',
                    'post_status' => 'draft'
                ]);
            }

            return wp_insert_post([
                'post_status' => self::STATUS_OR,
                'menu_order'  => self::EXP_SINGULAR_ARCHIVE,
                'post_type'   => self::TYPE_CONDITION_GROUP,
                'post_author' => $post->post_author,
                'post_parent' => $post->ID,
            ]);
        }

        /**
         * Get condition groups for a post type
         * Uses current post per default
         *
         * @since  1.0
         * @param  WP_Post|int    $post_id
         * @return array
         */
        private static function get_condition_groups($post_id = null)
        {
            $post = get_post($post_id);
            $groups = [];

            if ($post) {
                $groups = get_posts([
                    'posts_per_page' => -1,
                    'post_type'      => self::TYPE_CONDITION_GROUP,
                    'post_parent'    => $post->ID,
                    'post_status'    => [self::STATUS_PUBLISHED,self::STATUS_NEGATED,self::STATUS_EXCEPT, self::STATUS_OR],
                    'order'          => 'DESC',
                    'orderby'        => 'post_status'
                ]);
            }
            return $groups;
        }

        /**
         * AJAX callback to update a condition group
         *
         * @since 1.0
         * @return  void
         */
        public static function ajax_update_group()
        {
            if (!isset($_POST['current_id']) ||
                !check_ajax_referer(self::PREFIX . $_POST['current_id'], 'token', false)) {
                wp_send_json_error(__('Unauthorized request', WPCA_DOMAIN), 403);
            }

            $parent_id = (int)$_POST['current_id'];
            $parent_type = get_post_type_object($_POST['post_type']);

            if (!current_user_can($parent_type->cap->edit_post, $parent_id)) {
                wp_send_json_error(__('Unauthorized request', WPCA_DOMAIN), 403);
            }

            $response = [
                'message' => __('Conditions updated', WPCA_DOMAIN)
            ];

            //Make sure some rules are sent
            if (!isset($_POST['conditions'])) {
                //Otherwise we delete group
                if ($_POST['id'] && wp_delete_post(intval($_POST['id']), true) === false) {
                    wp_send_json_error(__('Could not delete conditions', WPCA_DOMAIN), 500);
                }

                $response['removed'] = true;
                wp_send_json($response);
            }

            //If ID was not sent at this point, it is a new group
            if (!$_POST['id']) {
                $post_id = self::add_condition_group($parent_id);
                $response['new_post_id'] = $post_id;
            } else {
                $post_id = (int)$_POST['id'];
            }

            wp_update_post([
                'ID'          => $post_id,
                'post_status' => self::sanitize_status($_POST['status']),
                'menu_order'  => (int)$_POST['exposure']
            ]);

            //Prune condition type cache, will rebuild within 24h
            update_option(self::OPTION_CONDITION_TYPE_CACHE, []);

            foreach (self::types()->get($parent_type->name)->all() as $module) {
                //send $_POST here
                $module->save_data($post_id);
            }

            do_action('wpca/modules/save-data', $post_id, $parent_type->name);

            wp_send_json($response);
        }

        /**
         * @param string $status
         *
         * @return string
         */
        private static function sanitize_status($status)
        {
            switch ($status) {
                case self::STATUS_NEGATED:
                    return self::STATUS_NEGATED;
                case self::STATUS_EXCEPT:
                    return self::STATUS_EXCEPT;
                case self::STATUS_OR:
                case self::STATUS_PUBLISHED:
                default:
                    return self::STATUS_OR;
            }
        }

        /**
         * Save registered meta for condition group
         *
         * @since  3.2
         * @param  int  $group_id
         * @return void
         */
        public static function save_condition_options($group_id, $post_type)
        {
            $meta_keys = self::get_condition_meta_keys($post_type);
            foreach ($meta_keys as $key => $default_value) {
                $value = isset($_POST[$key]) ? $_POST[$key] : false;
                if ($value) {
                    update_post_meta($group_id, $key, $value);
                } elseif (get_post_meta($group_id, $key, true)) {
                    delete_post_meta($group_id, $key);
                }
            }
        }

        public static function add_group_script_styles($hook)
        {
            $current_screen = get_current_screen();

            wp_register_style(
                self::PREFIX . 'condition-groups',
                plugins_url('/assets/css/condition_groups.css', __FILE__),
                [],
                WPCA_VERSION
            );

            if (self::types()->has($current_screen->post_type) && $current_screen->base == 'post') {
                self::enqueue_scripts_styles($current_screen->post_type);
            }
        }

        /**
         * Get condition option defaults
         *
         * @since  3.2
         * @param  string  $post_type
         * @return array
         */
        public static function get_condition_meta_keys($post_type)
        {
            $group_meta = [
                '_ca_autoselect' => 0
            ];
            return apply_filters('wpca/condition/meta', $group_meta, $post_type);
        }

        /**
         * Register and enqueue scripts and styles
         * for post edit screen
         *
         * @since 1.0
         *
         * @param string $post_type
         */
        public static function enqueue_scripts_styles($post_type = '')
        {
            $post_type = empty($post_type) ? get_post_type() : $post_type;

            $group_meta = self::get_condition_meta_keys($post_type);

            $groups = self::get_condition_groups();
            $data = [];
            $i = 0;
            foreach ($groups as $group) {
                $data[$i] = [
                    'id'         => $group->ID,
                    'status'     => $group->post_status,
                    'exposure'   => $group->menu_order,
                    'conditions' => []
                ];

                foreach (self::types()->get($post_type)->all() as $module) {
                    $data[$i]['conditions'] = $module->get_group_data($data[$i]['conditions'], $group->ID);
                }

                foreach ($group_meta as $meta_key => $default_value) {
                    $value = get_post_meta($group->ID, $meta_key, true);
                    if ($value === false) {
                        $value = $default_value;
                    }
                    $data[$i][$meta_key] = $value;
                }
                $i++;
            }

            $conditions = [
                'general' => [
                    'text'     => __('General'),
                    'children' => []
                ],
                'post_type' => [
                    'text'     => __('Post Types'),
                    'children' => []
                ],
                'taxonomy' => [
                    'text'     => __('Taxonomies'),
                    'children' => []
                ],
                'plugins' => [
                    'text'     => __('Plugins'),
                    'children' => []
                ]
            ];

            foreach (self::types()->get($post_type)->all() as $module) {
                $category = $module->get_category();
                if (!isset($conditions[$category])) {
                    $category = 'general';
                }

                //array_values used for backwards compatibility
                $conditions[$category]['children'] = array_values($module->list_module($conditions[$category]['children']));
            }

            foreach ($conditions as $key => $condition) {
                if (empty($condition['children'])) {
                    unset($conditions[$key]);
                }
            }

            //Make sure to use packaged version
            if (wp_script_is('select2', 'registered')) {
                wp_deregister_script('select2');
                wp_deregister_style('select2');
            }

            $plugins_url = plugins_url('', __FILE__);

            //Add to head to take priority
            //if being added under other name
            wp_register_script(
                'select2',
                $plugins_url . '/assets/js/select2.min.js',
                ['jquery'],
                '4.0.3',
                false
            );

            wp_register_script(
                'backbone.trackit',
                $plugins_url . '/assets/js/backbone.trackit.min.js',
                ['backbone'],
                '0.1.0',
                true
            );

            wp_register_script(
                'backbone.epoxy',
                $plugins_url . '/assets/js/backbone.epoxy.min.js',
                ['backbone'],
                '1.3.3',
                true
            );

            wp_register_script(
                self::PREFIX . 'condition-groups',
                $plugins_url . '/assets/js/condition_groups.min.js',
                ['jquery','select2','backbone.trackit','backbone.epoxy'],
                WPCA_VERSION,
                true
            );

            wp_enqueue_script(self::PREFIX . 'condition-groups');
            wp_localize_script(self::PREFIX . 'condition-groups', 'WPCA', [
                'searching'        => __('Searching', WPCA_DOMAIN),
                'noResults'        => __('No results found.', WPCA_DOMAIN),
                'loadingMore'      => __('Loading more results', WPCA_DOMAIN),
                'unsaved'          => __('Conditions have unsaved changes. Do you want to continue and discard these changes?', WPCA_DOMAIN),
                'newGroup'         => __('New condition group', WPCA_DOMAIN),
                'newCondition'     => __('Meet ALL of these conditions', WPCA_DOMAIN),
                'conditions'       => array_values($conditions),
                'groups'           => $data,
                'meta_default'     => $group_meta,
                'post_type'        => $post_type,
                'text_direction'   => is_rtl() ? 'rtl' : 'ltr',
                'condition_not'    => __('Not', WPCA_DOMAIN) . ' (No longer supported)',
                'condition_or'     => __('Or', WPCA_DOMAIN),
                'condition_except' => __('Except', WPCA_DOMAIN)
            ]);
            wp_enqueue_style(self::PREFIX . 'condition-groups');

            //@todo remove when ultimate member includes fix
            wp_dequeue_style('um_styles');

            //@todo remove when events calendar pro plugin includes fix
            wp_dequeue_script('tribe-select2');
        }

        /**
         * Modify wp_query for plugin compatibility
         *
         * @since  5.0
         * @return void
         */
        private static function fix_wp_query()
        {
            $query = [];

            //When themes don't declare WooCommerce support,
            //conditionals are not working properly for Shop
            if (defined('WOOCOMMERCE_VERSION') && function_exists('is_shop') && is_shop() && !is_post_type_archive('product')) {
                $query = [
                    'is_archive'           => true,
                    'is_post_type_archive' => true,
                    'is_page'              => false,
                    'is_singular'          => false,
                    'query_vars'           => [
                        'post_type' => 'product'
                    ]
                ];
            }

            self::set_wp_query($query);
        }

        /**
         * Restore original wp_query
         *
         * @since  5.0
         * @return void
         */
        private static function restore_wp_query()
        {
            self::set_wp_query(self::$wp_query_original);
            self::$wp_query_original = [];
        }

        /**
         * Set properties in wp_query and save original value
         *
         * @since 5.0
         * @param array  $query
         */
        private static function set_wp_query($query)
        {
            global $wp_query;
            foreach ($query as $key => $val) {
                $is_array = is_array($val);

                if (!isset(self::$wp_query_original[$key])) {
                    self::$wp_query_original[$key] = $is_array ? [] : $wp_query->$key;
                }

                if ($is_array) {
                    foreach ($val as $k1 => $v1) {
                        if (!isset(self::$wp_query_original[$key][$k1])) {
                            self::$wp_query_original[$key][$k1] = $wp_query->{$key}[$k1];
                        }
                        $wp_query->{$key}[$k1] = $v1;
                    }
                } else {
                    $wp_query->$key = $val;
                }
            }
        }

        /**
         * Autoload class files
         *
         * @since 1.0
         * @param   string    $class
         * @return  void
         */
        private static function _autoload_class_files($class)
        {
            if (strpos($class, self::CLASS_PREFIX) === 0) {
                $class = str_replace(self::CLASS_PREFIX, '', $class);
                $class = self::str_replace_first('_', '/', $class);
                $class = strtolower($class);
                $file = WPCA_PATH . $class . '.php';
                if (file_exists($file)) {
                    include $file;
                }
            }
        }

        /**
         * Helper function to replace first
         * occurence of substring
         *
         * @since 1.0
         * @param   string    $search
         * @param   string    $replace
         * @param   string    $subject
         * @return  string
         */
        private static function str_replace_first($search, $replace, $subject)
        {
            $pos = strpos($subject, $search);
            if ($pos !== false) {
                $subject = substr_replace($subject, $replace, $pos, strlen($search));
            }
            return $subject;
        }

        /**
         * @since 8.0
         * @param array $input
         *
         * @return string
         */
        public static function sql_prepare_in($input)
        {
            $output = array_map(function ($value) {
                return "'" . esc_sql($value) . "'";
            }, $input);
            return implode(',', $output);
        }

        /**
         * @since 8.0
         * @param string $type
         * @param array $modules
         *
         * @return array
         */
        private static function filter_condition_type_cache($type, $modules)
        {
            $included_conditions = get_option(self::OPTION_CONDITION_TYPE_CACHE, []);

            if (!$included_conditions || !isset($included_conditions[$type])) {
                return $modules;
            }

            $included_conditions_lookup = array_flip($included_conditions[$type]);
            $filtered_modules = [];

            foreach ($modules as $module) {
                if (isset($included_conditions_lookup[$module->get_id()])) {
                    $filtered_modules[] = $module;
                }
            }

            return $filtered_modules;
        }

        /**
         * @param string $post_type
         * @param string $name
         * @param mixed|null $default_value
         *
         * @return mixed|null
         */
        public static function get_option($post_type, $name, $default_value = null)
        {
            if (!self::types()->has($post_type)) {
                return $default_value;
            }

            $value = get_option(self::OPTION_POST_TYPE_OPTIONS, []);
            $levels = explode('.', $post_type . '.' . $name);

            foreach ($levels as $option_level) {
                if (!is_array($value) || !isset($value[$option_level])) {
                    return $default_value;
                }
                $value = $value[$option_level];
            }
            return $value;
        }

        /**
         * @param string $post_type
         * @param string $name
         * @param mixed $value
         *
         * @return bool
         */
        public static function save_option($post_type, $name, $value)
        {
            if (!self::types()->has($post_type)) {
                return false;
            }

            $options = get_option(self::OPTION_POST_TYPE_OPTIONS, []);
            $keys = explode('.', $post_type . '.' . $name);
            $array = &$options;

            foreach ($keys as $key) {
                if (!isset($array[$key]) || !is_array($array[$key])) {
                    $array[$key] = [];
                }
                $array = &$array[$key];
            }
            $array = $value;

            return update_option(self::OPTION_POST_TYPE_OPTIONS, $options);
        }
    }
}
