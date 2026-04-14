<?php

namespace InstagramFeed\Integrations\Divi;

use ET_Builder_Module;
use InstagramFeed\Builder\SBI_Db;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class SBInstagramFeed
 *
 * @since 6.2.9
 */
class SBInstagramFeed extends ET_Builder_Module
{
	/**
	 * Module slug.
	 *
	 * @since 6.2.9
	 *
	 * @var string
	 */
	public $slug = 'sb_instagram_feed';

	/**
	 * Visual builder support.
	 *
	 * @since 6.2.9
	 *
	 * @var array
	 */
	public $vb_support = 'on';

	/**
	 * Module properties initialization.
	 *
	 * @since 6.2.9
	 */
	public function init()
	{
		$this->name = esc_html__('Instagram Feed', 'instagram-feed');
	}

	/**
	 * Module's specific fields.
	 *
	 * @return array
	 * @since 6.2.9
	 */
	public function get_fields()
	{
		$feeds_list = SBI_Db::elementor_feeds_query($default = true);

		return [
			'feed_id' => [
				'label' => esc_html__('Feed', 'instagram-feed'),
				'type' => 'select',
				'option_category' => 'basic_option',
				'toggle_slug' => 'main_content',
				'options' => $feeds_list,
			]
		];
	}

	/**
	 * Module's advanced fields.
	 *
	 * @return array
	 * @since 6.2.9
	 */
	public function get_advanced_fields_config()
	{
		return [
			'link_options' => false,
			'text' => false,
			'background' => false,
			'borders' => false,
			'box_shadow' => false,
			'button' => false,
			'filters' => false,
			'fonts' => false,
		];
	}

	/**
	 * Render module.
	 *
	 * @param array  $attrs Module attributes.
	 * @param string $content Module content.
	 * @param string $render_slug Module slug.
	 *
	 * @return string
	 * @since 6.2.9
	 */
	public function render($attrs, $content, $render_slug)
	{
		$feed_id = $this->props['feed_id'];

		if (empty($feed_id)) {
			return '';
		}

		return do_shortcode(
			sprintf(
				'[instagram-feed feed="%1$s"]',
				absint($feed_id)
			)
		);
	}
}
