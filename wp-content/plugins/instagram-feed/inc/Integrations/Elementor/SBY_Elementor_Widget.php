<?php

namespace InstagramFeed\Integrations\Elementor;

use Elementor\Widget_Base;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class SBY_Elementor_Widget
 *
 * @since 6.2.9
 */
class SBY_Elementor_Widget extends Widget_Base
{
	/**
	 * Get widget name.
	 *
	 * Retrieve Youtube Feed widget name.
	 *
	 * @return string Widget name.
	 * @since 6.2.9
	 * @access public
	 */
	public function get_name()
	{
		return 'sby-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Youtube Feed widget title.
	 *
	 * @return string Widget title.
	 * @since 6.2.9
	 * @access public
	 */
	public function get_title()
	{
		return esc_html__('Youtube Feed', 'instagram-feed');
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Youtube Feed widget icon.
	 *
	 * @return string Widget icon.
	 * @since 6.2.9
	 * @access public
	 */
	public function get_icon()
	{
		return 'sb-elem-icon sb-elem-inactive sb-elem-youtube';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Youtube Feed widget belongs to.
	 *
	 * @return array Widget categories.
	 * @since 6.2.9
	 * @access public
	 */
	public function get_categories()
	{
		return array('smash-balloon');
	}

	/**
	 * Script dependencies.
	 *
	 * Load the widget scripts.
	 *
	 * @return array Widget scripts dependencies.
	 * @since 6.2.9
	 * @access public
	 */
	public function get_script_depends()
	{
		return array('elementor-handler');
	}
}
