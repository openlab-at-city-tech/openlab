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
 * WeGlot Module
 *
 * Detects if current content is:
 * a) in specific language
 *
 */
class WPCAModule_weglot extends WPCAModule_Base
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
        return defined('WEGLOT_VERSION')
            && function_exists('weglot_get_current_language')
            && function_exists('weglot_get_original_language')
            && function_exists('weglot_get_destination_languages')
            && function_exists('weglot_get_languages_available');
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        $data = [$this->id];
        $current_language = weglot_get_current_language();
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
        $langs = [];
        $codes = [
            weglot_get_original_language() => 1
        ];
        foreach (weglot_get_destination_languages() as $language) {
            if (isset($language['language_to'])) {
                $codes[$language['language_to']] = 1;
            }
        }

        foreach (weglot_get_languages_available() as $language) {
            if (isset($codes[$language->getInternalCode()])) {
                $langs[$language->getInternalCode()] = $language->getEnglishName();
            }
        }

        if ($args['include']) {
            $langs = array_intersect_key($langs, array_flip($args['include']));
        }
        return $langs;
    }
}
