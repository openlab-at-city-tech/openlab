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
 * Pods Module
 * Requires version 2.6+
 *
 * Detects if current content is:
 * a) any or specific Pods Page
 *
 */
class WPCAModule_pods extends WPCAModule_Base
{
    /**
     * @var string
     */
    protected $category = 'plugins';

    public function __construct()
    {
        parent::__construct('pods', __('Pods Pages', WPCA_DOMAIN));
        $this->placeholder = __('All Pods Pages', WPCA_DOMAIN);
        $this->default_value = $this->id;
        $this->query_name = 'cpo';
    }

    /**
     * @inheritDoc
     */
    public function can_enable()
    {
        return defined('PODS_DIR')
            && function_exists('pod_page_exists')
            && function_exists('is_pod_page')
            && function_exists('pods_api');
    }

    /**
     * @inheritDoc
     */
    public function in_context()
    {
        return is_pod_page();
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        $data = [
            $this->id
        ];
        $pod_page = pod_page_exists();
        $data[] = $pod_page['id'];
        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function parse_query_args($args)
    {
        return [
            'ids'    => $args['include'] ? $args['include'] : false,
            'where'  => '',
            'limit'  => $args['limit'],
            'search' => $args['search']
        ];
    }

    /**
     * @inheritDoc
     */
    protected function _get_content($args = [])
    {
        $pods = [];
        $results = pods_api()->load_pages($this->parse_query_args($args));
        foreach ($results as $result) {
            $pods[$result['id']] = $result['name'];
        }
        if ($args['search']) {
            $pods = array_filter($pods, function ($pod) use ($args) {
                return mb_stripos($pod, $args['search']) !== false;
            });
        }

        return $pods;
    }
}
