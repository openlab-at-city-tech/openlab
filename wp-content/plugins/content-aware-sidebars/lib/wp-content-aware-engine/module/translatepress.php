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
 * TranslatePress Module
 *
 * Detects if current content is:
 * a) in specific language
 *
 */
class WPCAModule_translatepress extends WPCAModule_Base
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
    public function in_context()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function can_enable()
    {
        return defined('TRP_PLUGIN_VERSION')
            && class_exists('TRP_Translate_Press');
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        $data = [$this->id];
        $current_language = get_locale();
        if ($current_language) {
            $data[] = $current_language;
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function _get_content($args = [])
    {
        $trp_instance = TRP_Translate_Press::get_trp_instance();
        $langs = $trp_instance->get_component('languages')->get_language_names(
            $trp_instance->get_component('settings')->get_setting('publish-languages')
        );

        if ($args['include']) {
            $langs = array_intersect_key($langs, array_flip($args['include']));
        }
        return $langs;
    }
}
