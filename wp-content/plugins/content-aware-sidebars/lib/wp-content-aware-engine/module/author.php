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
 * Author Module
 *
 * Detects if current content is:
 * a) post type written by any or specific author
 * b) any or specific author archive
 *
 */
class WPCAModule_author extends WPCAModule_Base
{
    public function __construct()
    {
        parent::__construct('author', __('Authors', WPCA_DOMAIN));
        $this->placeholder = __('All Authors', WPCA_DOMAIN);
        $this->icon = 'dashicons-admin-users';
        $this->default_value = $this->id;
        $this->query_name = 'ca';
    }

    /**
     * @inheritDoc
     */
    public function in_context()
    {
        return (is_singular() && !is_front_page()) || is_author();
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        global $post;
        return [
            $this->id,
            (string)(is_singular() ? $post->post_author : get_query_var('author'))
        ];
    }

    /**
     * @inheritDoc
     */
    protected function parse_query_args($args)
    {
        $new_args = [
            'number'      => $args['limit'],
            'offset'      => ($args['paged'] - 1) * $args['limit'],
            'search'      => $args['search'],
            'fields'      => ['ID','display_name'],
            'orderby'     => 'display_name',
            'order'       => 'ASC',
            'include'     => $args['include'],
            'count_total' => false,
        ];
        if ($new_args['search']) {
            if (false !== strpos($new_args['search'], '@')) {
                $new_args['search_columns'] = ['user_email'];
            } elseif (is_numeric($new_args['search'])) {
                $new_args['search_columns'] = ['user_login', 'ID'];
            } else {
                $new_args['search_columns'] = ['user_nicename', 'user_login', 'display_name'];
            }
            $new_args['search'] = '*' . $new_args['search'] . '*';
        }
        return $new_args;
    }

    /**
     * @inheritDoc
     */
    protected function _get_content($args = [])
    {
        $user_query = new WP_User_Query($args);
        $author_list = [];

        if ($user_query->results) {
            foreach ($user_query->get_results()  as $user) {
                $author_list[] = [
                    'id'   => $user->ID,
                    'text' => $user->display_name
                ];
            }
        }
        return $author_list;
    }
}
