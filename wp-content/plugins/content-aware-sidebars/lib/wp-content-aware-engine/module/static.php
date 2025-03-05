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
 * Static Pages Module
 *
 * Detects if current content is:
 * a) front page
 * b) search results
 * c) 404 page
 *
 */
class WPCAModule_static extends WPCAModule_Base
{
    /**
     * Cached search string
     * @var string
     */
    protected $search_string;

    public function __construct()
    {
        parent::__construct('static', __('Special Pages', WPCA_DOMAIN));
        $this->icon = 'dashicons-layout';
        $this->query_name = 'cs';
    }

    /**
     * @inheritDoc
     */
    protected function _get_content($args = [])
    {
        $static = [
            'front-page' => __('Front Page', WPCA_DOMAIN),
            'search'     => __('Search Results', WPCA_DOMAIN),
            '404'        => __('404 Page', WPCA_DOMAIN)
        ];

        if ($args['include']) {
            $static = array_intersect_key($static, array_flip($args['include']));
        } elseif ($args['search']) {
            $this->search_string = $args['search'];
            $static = array_filter($static, [$this,'_filter_search']);
        }
        return $static;
    }

    /**
     * Filter content based on search
     *
     * @since  2.0
     * @param  string  $value
     * @return boolean
     */
    protected function _filter_search($value)
    {
        return mb_stripos($value, $this->search_string) !== false;
    }

    /**
     * @inheritDoc
     */
    public function in_context()
    {
        return is_front_page() || is_search() || is_404();
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        if (is_front_page()) {
            $val = 'front-page';
        } elseif (is_search()) {
            $val = 'search';
        } else {
            $val = '404';
        }
        return [
            $val
        ];
    }
}
