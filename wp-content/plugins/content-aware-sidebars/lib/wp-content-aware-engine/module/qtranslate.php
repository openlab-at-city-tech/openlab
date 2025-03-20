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
 * qTranslate X Module
 * Requires version v3.4.6.4+
 *
 * Detects if current content is:
 * a) in specific language
 *
 */
class WPCAModule_qtranslate extends WPCAModule_Base
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
    public function initiate()
    {
        parent::initiate();
        if (is_admin()) {
            global $q_config;
            //Disable multilanguage
            if (is_array($q_config['post_type_excluded'])) {
                foreach (WPCACore::types() as $name => $modules) {
                    $q_config['post_type_excluded'][] = $name;
                }
                $q_config['post_type_excluded'][] = WPCACore::TYPE_CONDITION_GROUP;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function can_enable()
    {
        return defined('QTX_VERSION')
            && function_exists('qtranxf_getLanguage');
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
        $data[] = qtranxf_getLanguage();
        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function _get_content($args = [])
    {
        global $q_config;

        $langs = [];

        if (isset($q_config['language_name'])) {
            foreach ((array)get_option('qtranslate_enabled_languages') as $lng) {
                if (isset($q_config['language_name'][$lng])) {
                    $langs[$lng] = $q_config['language_name'][$lng];
                }
            }
        }

        if ($args['include']) {
            $langs = array_intersect_key($langs, array_flip($args['include']));
        }
        return $langs;
    }
}
