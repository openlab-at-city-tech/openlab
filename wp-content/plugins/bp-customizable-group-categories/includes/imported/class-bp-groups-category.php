<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if (!class_exists('BPCGC_Groups_Tag')) :

    class BPCGC_Groups_Tag {

        /**
         * Setup BP_Groups_Tag.
         *
         * @access public
         * @since BuddyPress Customizable Group Categories (1.0.0)
         *
         * @uses buddypress() to get BuddyPress main instance.
         * @static
         */
        public static function start() {

            $bp = buddypress();

            if (empty($bp->groups->tag)) {
                $bp->groups->tag = new self;
            }

            return $bp->groups->tag;
        }

        /**
         * Constructor.
         *
         * @access public
         * @since BuddyPress Customizable Group Categories (1.0.0)
         */
        public function __construct() {
            $this->setup_globals();
            $this->setup_hooks();
        }

        /**
         * Set globals.
         *
         * @access private
         * @since BuddyPress Customizable Group Categories (1.0.0)
         */
        private function setup_globals() {
            $this->term = 0;
            $this->tax_query = array();
        }

        /**
         * Set hooks.
         *
         * @access private
         * @since BuddyPress Customizable Group Categories (1.0.0)
         */
        private function setup_hooks() {

            // Actions
            add_action('bp_actions', array($this, 'groups_directory'), 1);
            add_action('groups_create_group_step_save_group-details', array($this, 'set_group_tags'));
            add_action('groups_group_details_edited', array($this, 'set_group_tags'), 1);
            add_action('groups_group_settings_edited', array($this, 'group_changed_visibility'), 1);
            add_action('groups_delete_group', array($this, 'remove_relationships'), 1);

            add_filter('bp_groups_get_paged_groups_sql', array($this, 'parse_select'), 10, 3);
            add_filter('bp_groups_get_total_groups_sql', array($this, 'parse_total'), 10, 3);
            add_filter('bp_get_total_group_count', array($this, 'total_group_count'), 10, 1);
            add_filter('bp_get_total_group_count_for_user', array($this, 'total_group_count_for_user'), 10, 2);
        }

        /**
         * Set the tag action to be a directory one
         *
         * BP Default & standalone themes.
         *
         * @access public
         * @since BuddyPress Customizable Group Categories (1.0.0)
         */
        public function groups_directory() {
            if (bp_is_groups_component() && bp_is_current_action('tag')) {

                $this->term = BPCGC_Groups_Terms::get_term_by('slug', bp_action_variable(0));

                if (empty($this->term)) {
                    return;
                }

                bp_update_is_directory(true, 'groups');

                do_action('groups_directory_groups_setup');

                bp_core_load_template(apply_filters('groups_template_directory_groups', 'groups/index'));
            }
        }

        /**
         * Build a WP_Tax_Query if needed
         *
         * @access public
         * @since BuddyPress Customizable Group Categories (1.0.0)
         */
        public function parse_select($query = '', $sql_parts = array(), $args = array()) {

            if (!empty($_GET['cat'])) {

                $cat_slug = filter_input(INPUT_GET, 'cat');

                if ($cat_slug === 'cat_all') {
                    $terms = get_terms('bp_group_categories');
                    $term_ids = wp_list_pluck($terms, 'term_id');
                } else {
                    $this->term = BPCGC_Groups_Terms::get_term_by('slug', $cat_slug);
                    $term_ids = $this->term->term_id;
                }
            } elseif ( isset( $this->term->term_id ) ) {
                $term_ids = $this->term->term_id;
            }

            if (!empty($term_ids)) {
                $tax_query = new WP_Tax_Query(array(
                    array(
                        'taxonomy' => 'bp_group_categories',
                        'terms' => $term_ids,
                        'field' => 'term_id',
                    )
                ));

                $clauses = $tax_query->get_sql('g', 'id');
                $this->tax_query = $clauses;

                $sql_parts['from'] .= $clauses['join'];
                $sql_parts['where'] .= $clauses['where'];

                $query = "{$sql_parts['select']} FROM {$sql_parts['from']} WHERE {$sql_parts['where']} {$sql_parts['orderby']} {$sql_parts['pagination']}";
            }

            return $query;
        }

        /**
         * Adjust total sql query
         *
         * @access public
         * @since BuddyPress Customizable Group Categories (1.0.0)
         */
        public function parse_total($query = '', $sql_parts = array(), $args = array()) {

            if (!empty($_GET['cat'])) {

                $cat_slug = filter_input(INPUT_GET, 'cat');

                if ($cat_slug === 'cat_all') {
                    $terms = get_terms('bp_group_categories');
                    $term_ids = wp_list_pluck($terms, 'term_id');
                } else {
                    $this->term = BPCGC_Groups_Terms::get_term_by('slug', $cat_slug);
                    $term_ids = $this->term->term_id;
                }
            } elseif ( isset( $this->term->term_id ) ) {
                $term_ids = $this->term->term_id;
            }

            if (!empty($term_ids) && !empty($this->tax_query)) {
                $sql_parts['from'] .= $this->tax_query['join'];
                $sql_parts['where'] .= $this->tax_query['where'];
                $query = "SELECT COUNT(DISTINCT g.id) FROM {$sql_parts['from']} WHERE {$sql_parts['where']}";
            }
            return $query;
        }

        /**
         * Adjust Groups directory All Groups count
         *
         * @access public
         * @since BP Groups Taxo (1.0.0)
         */
        public function total_group_count($count = 0) {

            if (!empty($this->term->count)) {
                $count = absint($this->term->count);
            }
            return $count;
        }

        /**
         * Adjust Groups directory My Groups count
         *
         * @access public
         * @since BP Groups Taxo (1.0.0)
         */
        public function total_group_count_for_user($count = 0, $user_id = 0) {
            if (!empty($this->term) && !empty($user_id) && !empty($count)) {
                $user_groups = $this->get_user_groups($user_id);

                if (empty($user_groups)) {
                    return $count;
                }

                $current_tag_groups = BPCGC_Groups_Terms::get_objects_in_term($this->term->term_id);
                $count = count(array_intersect($user_groups, $current_tag_groups));
            }
            return $count;
        }

        /**
         * Return a user's groups
         *
         * @access public
         * @since BP Groups Taxo (1.0.0)
         */
        public function get_user_groups($user_id = false) {
            global $wpdb;
            $bp = buddypress();

            if (empty($user_id)) {
                return array();
            }

            $sql = array(
                'select' => "SELECT DISTINCT m.group_id FROM {$bp->groups->table_name_members} m, {$bp->groups->table_name} g",
                'where' => array(
                    'join' => 'm.group_id = g.id',
                    'user' => $wpdb->prepare('m.user_id = %d', $user_id),
                    'confirmed' => 'm.is_confirmed = 1',
                    'banned' => 'm.is_banned = 0',
                )
            );

            $hide_hidden = (!is_super_admin() && $user_id != bp_loggedin_user_id() );

            if (!empty($hide_hidden)) {
                $sql['where']['status'] = $wpdb->prepare('g.status != %s', 'hidden');
            }

            $where = 'WHERE ' . join(' AND ', $sql['where']);
            $sql_col = $sql['select'] . ' ' . $where;

            return $wpdb->get_col(apply_filters('bp_groups_tags_get_user_groups_sql', $sql_col, $sql));
        }

        /**
         * Check if current user can manage tags
         *
         * @access public
         * @since BuddyPress Customizable Group Categories (1.0.0)
         * @static
         */
        public static function can_manage_tags() {
            $retval = false;

            if (bp_is_group_create() && is_user_logged_in()) {
                $retval = true;
            }

            if (bp_is_item_admin()) {
                $retval = true;
            }

            if (bp_current_user_can('bp_moderate')) {
                $retval = true;
            }

            return $retval;
        }

        /**
         * Set group tags
         *
         * @access public
         * @since BuddyPress Customizable Group Categories (1.0.0)
         * @static
         */
        public static function set_group_tags($group_id = 0) {
            if (empty($group_id)) {
                $group_id = bp_get_new_group_id() ? bp_get_new_group_id() : bp_get_current_group_id();
            }

            // A nicest way would be to use specific capabilities when registering term
            // and mapping those capabilities..
            if (!self::can_manage_tags()) {
                return false;
            }

            $term_ids = array();
            $previous_term_ids = array();

            if (!empty($_POST['_group_categories'])) {
                $term_ids = wp_parse_id_list($_POST['_group_categories']);
            }

            if (!empty($_POST['_group_previous_categories'])) {
                $previous_term_ids = wp_parse_id_list($_POST['_group_previous_categories']);
            }

            if (empty($term_ids) && empty($previous_term_ids)) {
                return false;
            }

            //echo '<pre>'.print_r($group_id, true).'</pre>';
            //wp_die('<pre>' . print_r($term_ids, true) . '</pre>');

            if (empty($term_ids) && !empty($previous_term_ids)) {
                // Remove terms
                return BPCGC_Groups_Terms::remove_object_terms($group_id, $previous_term_ids);
            } else if ($term_ids != $previous_term_ids) {
                // Set terms
                return BPCGC_Groups_Terms::set_object_terms($group_id, $term_ids);
            }
        }

        /**
         * Update term count if a group changed its visibility
         *
         * @access public
         * @since BuddyPress Customizable Group Categories (1.0.0)
         */
        public function group_changed_visibility($group_id = 0) {
            if (empty($group_id)) {
                $group_id = bp_get_current_group_id();
            }

            // We need to update term count in case an hidden group changed its visibility and vice versa
            $group_terms = BPCGC_Groups_Terms::get_object_terms($group_id);
            $terms = wp_list_pluck($group_terms, 'term_id');

            if (!empty($terms)) {
                BPCGC_Groups_Terms::update_term_count($terms);
            }
        }

        /**
         * Remove all group relationships
         *
         * In case a group is deleted.
         *
         * @access public
         * @since BuddyPress Customizable Group Categories (1.0.0)
         */
        public function remove_relationships($group_id = 0) {
            BPCGC_Groups_Terms::delete_object_term_relationships($group_id);
        }

    }

    endif;

add_action('bp_init', array('BPCGC_Groups_Tag', 'start'), 12);

