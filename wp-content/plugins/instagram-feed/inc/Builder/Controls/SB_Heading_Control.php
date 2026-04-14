<?php

/**
 * Customizer Builder
 * Heading Text Control
 *
 * @since 4.0
 */

namespace InstagramFeed\Builder\Controls;

if (!defined('ABSPATH')) {
	exit;
}

class SB_Heading_Control extends SB_Controls_Base
{
	/**
	 * Get control type.
	 *
	 * Getting the Control Type
	 *
	 * @return string
	 * @since 4.0
	 * @access public
	 */
	public function get_type()
	{
		return 'heading';
	}

	/**
	 * Output Control
	 *
	 * @return HTML
	 * @since 4.0
	 * @access public
	 */
	public function get_control_output($controlEditingTypeModel)
	{
	}
}
