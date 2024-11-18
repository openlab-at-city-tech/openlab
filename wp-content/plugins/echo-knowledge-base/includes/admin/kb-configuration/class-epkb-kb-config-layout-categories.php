<?php

/**
 * Lists settings, default values and display of CATEGORY FOCUSED layout.
 *
 * @copyright   Copyright (C) 2020, Echo Plugins
 */
class EPKB_KB_Config_Layout_Categories {

    const LAYOUT_NAME = 'Categories';
	const CATEGORY_LEVELS = 6;

	/**
	 * Defines KB configuration for this theme.
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => 'false' )
	 *
	 * @return array with both basic and theme-specific configuration
	 */
	public static function get_fields_specification() {

        $config_specification = array(
        );

		return $config_specification;
	}
}
