<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */

defined('ABSPATH') || exit;

if (!class_exists('WPCATypeManager')) {
    /**
     * Manage module objects
     */
    final class WPCATypeManager extends WPCACollection
    {
        /**
         * Constructor
         */
        public function __construct()
        {
            parent::__construct();
            add_action(
                'init',
                [$this,'set_modules'],
                999
            );
        }

        /**
         * @param string $name
         * @return $this
         */
        public function add($name)
        {
            return parent::put($name, new WPCACollection());
        }

        /**
         * Set initial modules
         *
         * @since   4.0
         * @return  void
         */
        public function set_modules()
        {
            do_action('wpca/types/init', $this);

            $modules = [
                'static',
                'post_type',
                'author',
                'page_template',
                'taxonomy',
                'date',
                'bbpress',
                'bp_member',
                'pods',
                'polylang',
                'qtranslate',
                'translatepress',
                'transposh',
                'weglot',
                'wpml'
            ];

            foreach ($modules as $name) {
                $class_name = WPCACore::CLASS_PREFIX . 'Module_' . $name;

                if (!class_exists($class_name)) {
                    continue;
                }

                $class = new $class_name();

                if (!($class instanceof WPCAModule_Base) || !$class->can_enable()) {
                    continue;
                }

                foreach ($this->all() as $post_type) {
                    $post_type->put($name, $class);
                }
            }

            do_action('wpca/modules/init', $this);

            //initiate all modules once with backwards compatibility on can_enable()
            $initiated = [];
            foreach ($this->all() as $post_type_name => $post_type) {
                if (!WPCACore::get_option($post_type_name, 'legacy.date_module', false)) {
                    $post_type->remove('date');
                }

                foreach ($post_type->all() as $key => $module) {
                    if (!isset($initiated[$key])) {
                        $initiated[$key] = 1;
                        $module->initiate();
                    }
                }
            }
        }
    }
}
