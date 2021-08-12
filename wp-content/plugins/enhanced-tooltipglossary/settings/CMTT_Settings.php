<?php

namespace CM;

include_once plugin_dir_path(__FILE__) . 'Settings.php';
include_once plugin_dir_path(__FILE__) . './fields/CMTT_Glossary_Index_Page_ID.php';

class CMTT_Settings extends Settings {
    
    protected static $abbrev = 'cmtt';
    protected static $dir = __DIR__;

    public static function init() {
        self::load_config();

        add_action(self::abbrev('_save_options_after'), [__CLASS__, 'beforeSaveSettings'], 10, 2);
        add_action(self::abbrev('_save_options_after'), [__CLASS__, 'afterSaveSettings'], 100, 2);
        add_filter(self::abbrev('_before_saving_option'), [__CLASS__, 'beforeSaveOption'], 10, 2);
        add_filter(self::abbrev('_before_sanitizing_option'), [__CLASS__, 'beforeSanitizingOption'], 10, 2);
        add_filter(self::abbrev('-custom-settings-tab-content-50'), array(__CLASS__, 'outputLabelsSettings'));
    }

    public static function outputLabelsSettings() {
        $view = CMTT_PLUGIN_DIR . '/views/backend/settings_labels.phtml';
        ob_start();
        include $view;
        $content = ob_get_clean();

        return $content;
    }

    public static function beforeSanitizingOption($option_value, $option_name) {
        if (in_array($option_name, array('cmtt_glossaryPermalink'))) {
            $option_value = sanitize_title($option_value);
        }
        return $option_value;
    }

    public static function beforeSaveOption($option_value, $option_name) {
        if ($option_name == 'cmtt_index_letters') {
            $option_value = array_map('mb_strtolower', explode(',', $option_value));
        }
        return $option_value;
    }

    public static function beforeSaveSettings($post, $messages) {

        if (isset($post['cmtt_removeAllOptions'])) {
            self::_cleanupOptions();
            $messages = 'CM Tooltip Glossary data options have been removed from the database.';
        }

        if (isset($post['cmtt_removeAllItems'])) {
            self::_cleanupItems();
            $messages = 'CM Tooltip Glossary data terms have been removed from the database.';
        }
    }

    public static function afterSaveSettings($post, $messages) {
        $enqueeFlushRules = false;
        /*
         * Update the page options
         */
        \CMTT_Glossary_Index::tryGenerateGlossaryIndexPage();
        if (isset($post["cmtt_glossaryPermalink"]) && $post["cmtt_glossaryPermalink"] !== \CM\CMTT_Settings::get('cmtt_glossaryPermalink')) {
            /*
             * Update glossary post permalink
             */
            $glossaryPost = array(
                'ID'        => $post["cmtt_glossaryID"],
                'post_name' => $post["cmtt_glossaryPermalink"]
            );
            wp_update_post($glossaryPost);
            $enqueeFlushRules = true;
        }

        if (empty($post["cmtt_glossaryPermalink"])) {
            $post["cmtt_glossaryPermalink"] = 'glossary';
        }
        update_option('cmtt_glossaryPermalink', $post["cmtt_glossaryPermalink"]);

        if (apply_filters('cmtt_enqueueFlushRules', $enqueeFlushRules, $post)) {
            self::_flush_rewrite_rules();
        }

        unset($post['cmtt_glossaryID'], $post['cmtt_glossaryPermalink'], $post['cmtt_glossarySave']);
    }

    /**
     * Function cleans up the plugin, removing the terms, resetting the options etc.
     *
     * @return string
     */
    protected static function _cleanupOptions($force = true) {
        /*
         * Remove the data from the other tables
         */
        do_action('cmtt_do_cleanup');

//        $glossaryIndexPageId = CMTT_Glossary_Index::getGlossaryIndexPageId();
//        if (!empty($glossaryIndexPageId)) {
//            wp_delete_post($glossaryIndexPageId);
//        }

        /*
         * Remove the options
         */
        $optionNames = wp_load_alloptions();

        $options_names = array_filter(array_keys($optionNames), function ($k) {
            return strpos($k, 'cmtt_') === 0;
        });
        foreach ($options_names as $optionName) {
            delete_option($optionName);
        }
    }

    /**
     * Function cleans up the plugin, removing the terms, resetting the options etc.
     *
     * @return string
     */
    protected static function _cleanupItems($force = true) {

        do_action('cmtt_do_cleanup_items_before');

        $glossary_index = self::getGlossaryItems();

        /*
         * Remove the glossary terms
         */
        foreach ($glossary_index as $post) {
            wp_delete_post($post->ID, $force);
        }

        $tags = get_terms(array(
            'taxonomy'   => 'glossary-tags',
            'hide_empty' => false,
        ));

        foreach ($tags as $tag) {
            wp_delete_term($tag->term_id, 'glossary-tags');
        }

        $categories = get_terms(array(
            'taxonomy'   => 'glossary-categories',
            'hide_empty' => false,
        ));

        foreach ($categories as $category) {
            wp_delete_term($category->term_id, 'glossary-categories');
        }

        /*
         * Invalidate the list of all glossary items stored in cache
         */
        do_action('cmtt_do_cleanup_items_after');
    }

}
