<?php

/**
 * Lists settings, default values and display of BASIC layout.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Config_Layout_Basic {

    const LAYOUT_NAME = 'Basic';
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
