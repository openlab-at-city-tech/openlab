<?php
namespace ElementsKit_Lite\Core;

defined('ABSPATH') || exit;

class Editor_Promotion {

	use \ElementsKit_Lite\Traits\Singleton;

	public function init() {
		// Enqueue promotion scripts
		add_action('elementor/editor/before_enqueue_scripts', [$this, 'enqueue_editor_scripts']);
	}

	/**
	 * Get promotion widgets data
	 */
	private function get_promotion_widgets_data() {
		$widget_list = \ElementsKit_Lite\Config\Widget_List::instance()->get_list('all');
		$promotion_data = [];

		foreach ($widget_list as $slug => $widget) {
			if (isset($widget['package']) && $widget['package'] === 'pro-disabled') {
				$promotion_data[] = [
					'name' => 'ekit-' . $slug,
					'title' => isset($widget['title']) ? $widget['title'] : ucwords(str_replace('-', ' ', $slug)),
					'icon' => isset($widget['icon']) ? $widget['icon'] : 'eicon-star',
					'categories' => ['elementskit'],
					'promotion' => [
						'title' => sprintf(__('%s Widget', 'elementskit-lite'), isset($widget['title']) ? $widget['title'] : ucwords(str_replace('-', ' ', $slug))),
						'description' => sprintf(
							__( 'Unlock the %s widget and dozens of powerful ElementsKit Pro features to design faster, smarter, and more flexible websites.', 'elementskit-lite'),
							isset($widget['title']) ? $widget['title'] : ucwords(str_replace('-', ' ', $slug))
						),
						'upgrade_url' => 'https://wpmet.com/plugin/elementskit/pricing/',
						'upgrade_text' => __('Upgrade Now', 'elementskit-lite'),
					],
				];
			}
		}
		return $promotion_data;
	}

	/**
	 * Enqueue editor scripts for promotion
	 */
	public function enqueue_editor_scripts() {
		$promotion_widgets = $this->get_promotion_widgets_data();

		wp_enqueue_script(
			'elementskit-editor-promotion',
			\ElementsKit_Lite::widget_url() . 'init/assets/js/editor-promotion.js',
			['elementor-editor', 'elementor-common'],
			\ElementsKit_Lite::version(),
			true
		);

		wp_localize_script(
			'elementskit-editor-promotion',
			'ekitPromotion',
			[
				'promotionWidgets' => $promotion_widgets,
				'upgradeUrl' => 'https://wpmet.com/plugin/elementskit/pricing/',
				'debug' => true,
				'i18n' => [
					'proFeature' => __('Pro Feature', 'elementskit-lite'),
					'upgradeNow' => __('Upgrade Now', 'elementskit-lite'),
					'learnMore' => __('Learn More', 'elementskit-lite'),
				],
			]
		);

		// Enqueue styles
		wp_enqueue_style(
			'elementskit-editor-promotion',
			\ElementsKit_Lite::widget_url() . 'init/assets/css/editor-promotion.css',
			[],
			\ElementsKit_Lite::version()
		);
	}
}
