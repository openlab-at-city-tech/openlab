<?php

namespace PublishPress\Blocks;

/*
 * Methods used across all the classes
 */
if (! class_exists('\\PublishPress\\Blocks\\Utilities')) {
    class Utilities
    {
        /**
         * Check if a setting is enabled
         *
         * @param string $setting The setting from advgb_settings option field
         *
         * @return boolean
         * @since 3.1.0
         */
        public static function settingIsEnabled($setting)
        {
            $saved_settings = get_option('advgb_settings');

            if (isset($saved_settings[ $setting ]) && ! $saved_settings[ $setting ]) {
                return false;
            }

            if (! isset($saved_settings[ $setting ]) || $saved_settings[ $setting ]) {
                return true;
            }

            return false;
        }

        public static function getProBlocks()
        {
            $pro_blocks_details = [];
            $pro_blocks_details[] = [
                'name' => 'advgb/countdown',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M15.07 1.01h-6v2h6v-2zm-4 13h2v-6h-2v6zm8.03-6.62l1.42-1.42c-.43-.51-.9-.99-1.41-1.41l-1.42 1.42C16.14 4.74 14.19 4 12.07 4c-4.97 0-9 4.03-9 9s4.02 9 9 9 9-4.03 9-9c0-2.11-.74-4.06-1.97-5.61zm-7.03 12.62c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"></path></svg>',
                'title' => __('Countdown', 'advanced-gutenberg'),
                'description' => __('Easily create a customizable countdown.', 'advanced-gutenberg'),
                'category' => 'advgb-category',
                'iconColor' => '#655997',
                'isPro' => true
            ];

            $pro_blocks_details[] = [
                'name' => 'advgb/pricing-table',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><g><rect fill="none" height="24" width="24"></rect><path d="M14,2H6C4.9,2,4,2.9,4,4v16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2V8L14,2z M6,20V4h7v4h5v12H6z M11,19h2v-1h1 c0.55,0,1-0.45,1-1v-3c0-0.55-0.45-1-1-1h-3v-1h4v-2h-2V9h-2v1h-1c-0.55,0-1,0.45-1,1v3c0,0.55,0.45,1,1,1h3v1H9v2h2V19z"></path></g></svg>',
                'title' => __('Pricing Table', 'advanced-gutenberg'),
                'description' => __('Easily create a customizable pricing table.', 'advanced-gutenberg'),
                'category' => 'advgb-category',
                'iconColor' => '#655997',
                'isPro' => true
            ];

            $pro_blocks_details[] = [
                'name' => 'advgb/feature',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"></path></svg>',
                'title' => __('Feature', 'advanced-gutenberg'),
                'description' => __('Use block to create a list inside the Feature List" block.', 'advanced-gutenberg'),
                'category' => 'advgb-category',
                'parent' => ['advgb/feature-list'],
                'iconColor' => '#655997',
                'isPro' => true
            ];

            $pro_blocks_details[] = [
                'name' => 'advgb/feature-list',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><g><rect fill="none" height="24" width="24"></rect></g><g><g><path d="M20,3H4C2.9,3,2,3.9,2,5v14c0,1.1,0.9,2,2,2h16c1.1,0,2-0.9,2-2V5 C22,3.9,21.1,3,20,3z M20,19H4V5h16V19z" fill-rule="evenodd"></path><polygon fill-rule="evenodd" points="19.41,10.42 17.99,9 14.82,12.17 13.41,10.75 12,12.16 14.82,15"></polygon><rect fill-rule="evenodd" height="2" width="5" x="5" y="7"></rect><rect fill-rule="evenodd" height="2" width="5" x="5" y="11"></rect><rect fill-rule="evenodd" height="2" width="5" x="5" y="15"></rect></g></g></svg>',
                'title' => __('Feature List', 'advanced-gutenberg'),
                'description' => __('Use block to create a list inside the Feature List" block.', 'advanced-gutenberg'),
                'category' => 'advgb-category',
                'iconColor' => '#655997',
                'isPro' => true
            ];

            return $pro_blocks_details;
        }

        public static function isProActive() {

            $proActive = defined('ADVANCED_GUTENBERG_PRO_LOADED');

            return $proActive;
        }

        /**
         * Load tooltip JS and CSS for admin pages
         *
         * @return void
         * @since 3.0.0
         */
        public static function enqueueToolTipsAssets()
        {

            wp_enqueue_style(
                'ppb-tooltips-css',
                ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/lib/pp-tooltips/css/tooltip.min.css',
                [],
                ADVANCED_GUTENBERG_VERSION
            );

            wp_enqueue_script(
                'ppb-tooltips-js',
                ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/lib/pp-tooltips/js/tooltip.min.js',
                [],
                ADVANCED_GUTENBERG_VERSION,
                true
            );

        }
    }
}
