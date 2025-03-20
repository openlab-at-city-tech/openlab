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
 * Page Template Module
 *
 * Detects if current content has:
 * a) any or specific page template
 *
 *
 */
class WPCAModule_page_template extends WPCAModule_Base
{
    /**
     * Cached search string
     * @var string
     */
    protected $search_string;

    public function __construct()
    {
        parent::__construct('page_template', __('Page Templates', WPCA_DOMAIN));
        $this->placeholder = __('All Templates', WPCA_DOMAIN);
        $this->icon = 'dashicons-media-code';
        $this->default_value = $this->id;
        $this->query_name = 'cpt';
    }

    /**
     * @inheritDoc
     */
    public function in_context()
    {
        if (is_singular() && !('page' == get_option('show_on_front') && get_option('page_on_front') == get_the_ID())) {
            $template = get_post_meta(get_the_ID(), '_wp_page_template', true);
            return ($template && $template != 'default');
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        return [
            $this->id,
            get_post_meta(get_the_ID(), '_wp_page_template', true)
        ];
    }

    /**
     * @inheritDoc
     */
    protected function _get_content($args = [])
    {
        $templates = array_flip(get_page_templates());
        if ($args['include']) {
            $templates = array_intersect_key($templates, array_flip($args['include']));
        } elseif ($args['search']) {
            $this->search_string = $args['search'];
            $templates = array_filter($templates, [$this,'_filter_search']);
        }
        return $templates;
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
}
