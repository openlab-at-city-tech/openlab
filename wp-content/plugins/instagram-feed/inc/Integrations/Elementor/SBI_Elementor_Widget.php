<?php

namespace InstagramFeed\Integrations\Elementor;

use Elementor\Widget_Base;
use InstagramFeed\Builder\SBI_Db;
use InstagramFeed\Integrations\SBI_Integration;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class SBI_Elementor_Widget
 *
 * @since 6.2.9
 */
class SBI_Elementor_Widget extends Widget_Base
{
	/**
	 * Get widget name.
	 *
	 * Retrieve Instagram Feed widget name.
	 *
	 * @return string Widget name.
	 * @since 6.2.9
	 * @access public
	 */
	public function get_name()
	{
		return 'sbi-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Instagram Feed widget title.
	 *
	 * @return string Widget title.
	 * @since 6.2.9
	 * @access public
	 */
	public function get_title()
	{
		return esc_html__('Instagram Feed', 'instagram-feed');
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Instagram Feed widget icon.
	 *
	 * @return string Widget icon.
	 * @since 6.2.9
	 * @access public
	 */
	public function get_icon()
	{
		return 'sb-elem-icon sb-elem-instagram';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Instagram Feed widget belongs to.
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
		return array('sbiscripts', 'elementor-preview');
	}

	/**
	 * Register Instagram Feed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 6.2.9
	 * @access protected
	 */
	protected function register_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Instagram Feed Settings', 'instagram-feed'),
			]
		);

		$this->add_control(
			'feed_id',
			[
				'label' => esc_html__('Select a Feed', 'instagram-feed'),
				'type' => 'sbi_feed_control',
				'label_block' => true,
				'dynamic' => ['active' => true],
				'options' => SBI_Db::elementor_feeds_query($default = true),
				'default' => 0,
				'description' => esc_html__('Select a feed to display. If you don\'t have any feeds yet then you can create one in the Instagram Feed settings page.', 'instagram-feed'),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Instagram Feed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 6.2.9
	 * @access protected
	 */
	protected function render()
	{
		$settings = $this->get_settings_for_display();

		if (!empty($settings['feed_id']) && $settings['feed_id'] != 0) {
			$output = do_shortcode(shortcode_unautop('[instagram-feed feed=' . $settings['feed_id'] . ']'));
		} else {
			$output = is_admin() ? SBI_Integration::get_widget_cta() : esc_html__('No feed selected to display.', 'instagram-feed');
		}

		echo apply_filters('sbi_output', $output, $settings);
	}
}
