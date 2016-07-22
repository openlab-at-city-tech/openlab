<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://early-adopter.com/
 * @since      1.0.0
 *
 * @package    Bp_Customizable_Group_Categories
 * @subpackage Bp_Customizable_Group_Categories/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Bp_Customizable_Group_Categories
 * @subpackage Bp_Customizable_Group_Categories/includes
 * @author     Joe Unander <joe@early-adopter.com>
 */
class Bp_Customizable_Group_Categories_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        $this->domain = 'bp-customizable-group-categories';

        // Traditional WordPress plugin locale filter
        $locale = apply_filters('plugin_locale', get_locale(), $this->domain);
        $mofile = sprintf('%1$s-%2$s.mo', $this->domain, $locale);

        // Setup paths to current locale file
        $mofile_local = $this->lang_dir . $mofile;
        $mofile_global = WP_LANG_DIR . '/bp-customizable-group-categories/' . $mofile;

        // Look in global /wp-content/languages/bp-groups-taxo folder
        load_textdomain($this->domain, $mofile_global);

        // Look in local /wp-content/plugins/bp-groups-taxo/languages/ folder
        load_textdomain($this->domain, $mofile_local);

        load_plugin_textdomain(
                $this->domain, false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

}
