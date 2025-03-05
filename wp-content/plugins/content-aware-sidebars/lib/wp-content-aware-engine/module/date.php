<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */

defined('ABSPATH') || exit;

/**
 * Date Module
 *
 * @deprecated 9.1
 */
class WPCAModule_date extends WPCAModule_Base
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'date',
            __('Dates', WPCA_DOMAIN)
        );
        $this->placeholder = __('Date Archives', WPCA_DOMAIN);
        $this->default_value = '0000-00-00';
        //$this->query_name = 'cd';
    }

    /**
     * @inheritDoc
     */
    public function in_context()
    {
        return is_date();
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        global $wpdb;

        $name = $this->get_query_name();

        return $wpdb->prepare(
            "($name.meta_value IS NULL OR '%s' = $name.meta_value)",
            '0000-00-00'
        );
    }

    /**
     * Get content
     *
     * @since  1.0
     * @return array
     */
    protected function _get_content($args = [])
    {
        $data = [];
        if ($args['include']) {
            $data = array_intersect_key($data, array_flip($args['include']));
        }
        return $data;
    }
}
