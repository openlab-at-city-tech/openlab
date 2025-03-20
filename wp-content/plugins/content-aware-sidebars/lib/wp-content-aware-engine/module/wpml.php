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
 * WPML Module
 * Requires version 2.4.3+
 *
 * Detects if current content is:
 * a) in specific language
 *
 */
class WPCAModule_wpml extends WPCAModule_Base
{
    /**
     * @var string
     */
    protected $category = 'plugins';

    public function __construct()
    {
        parent::__construct('language', __('Languages', WPCA_DOMAIN));
        $this->icon = 'dashicons-translation';
        $this->query_name = 'cl';
    }

    /**
     * @inheritDoc
     */
    public function can_enable()
    {
        return defined('ICL_SITEPRESS_VERSION')
            && defined('ICL_LANGUAGE_CODE')
            && function_exists('icl_get_languages');
    }

    /**
     * @inheritDoc
     */
    public function in_context()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        $data = [$this->id];
        $data[] = ICL_LANGUAGE_CODE;
        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function _get_content($args = [])
    {
        $langs = [];

        foreach (icl_get_languages('skip_missing=N') as $lng) {
            $langs[$lng['language_code']] = $lng['native_name'];
        }

        if ($args['include']) {
            $langs = array_intersect_key($langs, array_flip($args['include']));
        }
        return $langs;
    }
}
