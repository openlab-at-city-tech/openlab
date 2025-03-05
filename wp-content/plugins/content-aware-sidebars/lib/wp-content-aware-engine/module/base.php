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
 * All modules should extend this one.
 *
 */
abstract class WPCAModule_Base
{
    /**
     * @var string
     */
    protected $query_name;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @var string
     */
    protected $icon;

    /**
     * Default condition value
     * Use to target any condition content
     *
     * @var string
     */
    protected $default_value = '';

    /**
     * @var string
     */
    protected $category = 'general';

    /**
     * @param string $id
     * @param string $title
     * @param string $description
     * @param string $placeholder
     */
    public function __construct($id, $title, $description = '', $placeholder = '')
    {
        $this->id = $id;
        $this->name = $title;
        $this->description = $description;
        $this->placeholder = $placeholder;
    }

    /**
     * Initiate module
     *
     * @since  2.0
     * @return void
     */
    public function initiate()
    {
        if (is_admin()) {
            add_action(
                'wp_ajax_wpca/module/' . $this->id,
                [$this,'ajax_print_content']
            );
        }
    }

    /**
     * @since  9.0
     * @return bool
     */
    public function can_enable()
    {
        return true;
    }

    /**
     * Set module info in list
     * @since 2.0
     * @param array $list
     *
     * @return array
     */
    public function list_module($list)
    {
        $list[] = [
            'id'            => $this->id,
            'text'          => $this->name,
            'icon'          => $this->get_icon(),
            'placeholder'   => $this->placeholder,
            'default_value' => $this->default_value,
        ];
        return $list;
    }

    /**
     * Default query join
     *
     * @global wpdb   $wpdb
     * @since  1.0
     * @return string
     */
    public function db_join()
    {
        global $wpdb;

        $name = $this->get_query_name();
        $key = $this->get_data_key();

        return "LEFT JOIN $wpdb->postmeta $name ON $name.post_id = p.ID AND $name.meta_key = '$key' ";
    }

    /**
     * @since  1.0
     *
     * @return string
     */
    final public function get_id()
    {
        return $this->id;
    }

    /**
     * @since 7.0
     *
     * @return string
     */
    final public function get_name()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    final public function get_icon()
    {
        return $this->icon;
    }

    /**
     * @since 7.0
     *
     * @return string
     */
    final public function get_category()
    {
        return $this->category;
    }

    /**
     * @since  6.0
     * @return string
     */
    public function get_query_name()
    {
        return $this->query_name ? $this->query_name : $this->id;
    }

    /**
     * @since  6.0
     * @return string
     */
    public function get_data_key()
    {
        return WPCACore::PREFIX . $this->id;
    }

    /**
     * Save data on POST
     *
     * @since  1.0
     * @param  int  $post_id
     * @return void
     */
    public function save_data($post_id)
    {
        $meta_key = $this->get_data_key();
        $old = array_flip(get_post_meta($post_id, $meta_key, false));
        $new = isset($_POST['conditions'][$this->id]) ? $_POST['conditions'][$this->id] : '';

        if (is_array($new)) {
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
     * Get data for condition group
     *
     * @since  2.0
     * @param  array  $group_data
     * @param  int    $post_id
     * @return array
     */
    public function get_group_data($group_data, $post_id)
    {
        $data = get_post_custom_values($this->get_data_key(), $post_id);
        if ($data) {
            $group_data[$this->id] = [
                'label'         => $this->name,
                'icon'          => $this->get_icon(),
                'placeholder'   => $this->placeholder,
                'data'          => $this->get_content(['include' => $data]),
                'default_value' => $this->default_value
            ];
        }
        return $group_data;
    }

    /**
     * Get content for sidebar edit screen
     *
     * @since   1.0
     * @param   array     $args
     * @return  array
     */
    abstract protected function _get_content($args = []);

    /**
     * Determine if current content is relevant
     *
     * @since  1.0
     * @return boolean
     */
    abstract public function in_context();

    /**
     * Get data from current content
     *
     * @since  1.0
     * @return array|string
     */
    abstract public function get_context_data();

    /**
     * Remove posts if they have data from
     * other contexts (meaning conditions arent met)
     *
     * @since  3.2
     * @param array $posts
     * @param boolean $in_context
     * @return array
     */
    public function filter_excluded_context($posts, $in_context = false)
    {
        if (!$in_context) {
            foreach ($posts as $id => $group) {
                if (get_post_custom_values($this->get_data_key(), $id) !== null) {
                    unset($posts[$id]);
                }
            }
        }
        return $posts;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    protected function parse_query_args($args)
    {
        return $args;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    protected function get_content($args)
    {
        $args = array_merge([
            'include' => [],
            'paged'   => 1,
            'search'  => false,
            'limit'   => -1,
        ], $args);
        return $this->_get_content($this->parse_query_args($args));
    }

    /**
     * Print JSON for AJAX request
     *
     * @since   1.0
     * @return  void
     */
    final public function ajax_print_content()
    {
        if (!isset(
            $_POST['current_id'],
            $_POST['action'],
            $_POST['paged'],
            $_POST['post_type']
        )) {
            wp_die();
        }

        if (!check_ajax_referer(WPCACore::PREFIX . $_POST['current_id'], 'nonce', false)) {
            wp_die();
        }

        $parent_type = get_post_type_object($_POST['post_type']);
        if (!current_user_can($parent_type->cap->edit_post, $_POST['current_id'])) {
            wp_die();
        }

        $response = $this->get_content([
            'paged'       => $_POST['paged'],
            'search'      => isset($_POST['search']) ? $_POST['search'] : false,
            'limit'       => isset($_POST['limit']) ? $_POST['limit'] : 20,
            'item_object' => $_POST['action']
        ]);

        //ECMAScript has no standard to guarantee
        //prop order in an object, send array instead
        //todo: fix in each module
        $fix_response = [];
        foreach ($response as $id => $title) {
            if (!is_array($title)) {
                $fix_response[] = [
                    'id'   => $id,
                    'text' => $title
                ];
            } else {
                $fix_response[] = $title;
            }
        }

        wp_send_json($fix_response);
    }

    /**
     * Destructor
     *
     * @since 4.0
     */
    public function __destruct()
    {
        if (is_admin()) {
            remove_action(
                'wp_ajax_wpca/module/' . $this->id,
                [$this,'ajax_print_content']
            );
        }
    }
}
