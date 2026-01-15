<?php
namespace ElementsKit_Lite\Modules\ElementsKit_Icon_Pack;

defined('ABSPATH') || exit;

class Init {
	public static function get_url() {
		return \ElementsKit_Lite::module_url() . 'elementskit-icon-pack/';
	}

	public static function get_dir() {
		return \ElementsKit_Lite::module_dir() . 'elementskit-icon-pack/';
	}

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_icon_css'] );

		if (!self::is_svg_icon_experiment()) {
			add_action('elementor/frontend/before_enqueue_scripts', array($this, 'enqueue_frontend'));
		}

		add_action('elementor/preview/enqueue_styles', array($this, 'enqueue_frontend'));
		add_filter('elementor/icons_manager/additional_tabs', array($this, 'register_icon_pack_to_elementor'));
		add_filter('elementor/widget/render_content', array($this, 'filter_widget_content'), 10, 2);
	}

	// Enqueue admin CSS for widget icons
	public function enqueue_admin_icon_css( $hook ) {
		if ( $hook !== 'elementor_page_elementor-element-manager' ) {
			return;
		}

		wp_enqueue_style( 'widget-icons', \ElementsKit_Lite::widget_url() . 'init/assets/css/editor.css', [], \ElementsKit_Lite::version() );

		// Inline CSS to adjust icon display in Elementor's Element Manager
		$css = 'td .ekit-widget-icon{max-width:13px;overflow:hidden;min-height:auto;font-size:inherit;}td .ekit-widget-icon:after{display:none}';
		wp_add_inline_style( 'widget-icons', $css );
}

	public function enqueue_frontend() {
		wp_enqueue_style( 'elementor-icons-ekiticons', self::get_url() . 'assets/css/ekiticons.css', array(), \ElementsKit_Lite::version() );
	}

	public function register_icon_pack_to_elementor($font) {
		$font_new['ekiticons'] = array(
			'name'          => 'ekiticons',
			'label'         => esc_html__('ElementsKit Icon Pack', 'elementskit-lite'),
			'prefix'        => 'icon-',
			'displayPrefix' => 'icon',
			'labelIcon'     => 'icon icon-ekit',
			'ver'           => \ElementsKit_Lite::version(),
			'fetchJson'     => self::get_url() . 'assets/js/ekiticons.json',
			'native'        => true,
		);

		if (!self::is_svg_icon_experiment()) {
			$font_new['ekiticons']['url'] = self::get_url() . 'assets/css/ekiticons.css';
		} else {
			$font_new['ekiticons']['enqueue'] = [self::get_url() . 'assets/css/ekiticons.css'];
		}

		return array_merge($font, $font_new);
	}

	public function filter_widget_content($widget_content, $widget) {
		// Check if it's in Elementor editor
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return $widget_content; // return normal content
		}

		$ekit_svg_icon = self::is_svg_icon_experiment();

		// Match all <i> tags with class containing "icon icon-"
		if ($ekit_svg_icon && strpos($widget_content, 'icon icon-') !== false) {
			$widget_content = preg_replace_callback('/<i[^>]*class="([^"]*)"[^>]*><\/i>/', function ($matches) {
				$full_class_string = $matches[1]; // Get the entire class string

				// Check if this icon has the icon icon- pattern
				if (strpos($full_class_string, 'icon icon-') !== false) {
					// Extract the specific icon name
					preg_match('/icon icon-([^\s"]+)/', $full_class_string, $icon_matches);
					$icon_name = $icon_matches[1]; // This gives us "down-arrow1"

					$icon = [
						'library' => 'ekiticons',
						'value' => 'icon icon-' . $icon_name,
					];

					// Get the ElementsKit icon HTML
					$get_icon = self::get_icon_html($icon);

					// Now extract any additional classes
					$extra_classes = preg_replace('/\s*icon icon-[^\s"]+\s*/', ' ', $full_class_string);
					$extra_classes = trim($extra_classes);

					// If we have extra classes, add them to the generated icon HTML
					if (!empty($extra_classes)) {
						// Add extra classes to the generated icon
						$get_icon = str_replace('class="', 'class="' . $extra_classes . ' ', $get_icon);
					}

					return $get_icon;
				}

				// If no match found, return the original
				return $matches[0];
			}, $widget_content);
		}

		return $widget_content;
	}

	public static function is_svg_icon_experiment() {
		$elementskit_options = get_option( 'elementskit_options' );
		$inline_svg = $elementskit_options['user_data']['inline_svg']['is_enable'] ?? false;
		return apply_filters( 'elementskit_font_icon_inline_svg', $inline_svg );
	}

	public static function get_svg_icon($icon, $attributes = []) {
		// go for ekit svg icon
		$file = \ElementsKit_Lite::module_dir() . 'elementskit-icon-pack/' . 'assets/json/icons.json';
		$get_file_content = \Elementor\Utils::file_get_contents($file);

		// check condition for svg icon
		if (!$get_file_content) {
			return '';
		}

		$icons = json_decode($get_file_content, true);
		$icon_name = str_replace('icon icon-', '', strtolower($icon['value']));
		$svg = isset($icons[$icon_name]) ? $icons[$icon_name] : false;

		if ( empty($svg['paths']) ) {
			return '';
		}

		$attributes['class'][] = 'ekit-svg-icon';
		$attributes['class'][] = sprintf('icon-%s', $icon_name);
		$attributes['viewBox'] = $svg['viewBox'];
		$attributes['xmlns'] = 'http://www.w3.org/2000/svg';

		$svg_html = sprintf('<svg %s>', \Elementor\Utils::render_html_attributes($attributes));

		foreach ($svg['paths'] as $path) {
			$svg_html .= sprintf('<path d="%s"></path>', esc_attr($path));
		}

		$svg_html .= '</svg>';

		return $svg_html;
	}

	public static function get_icon_html($icon, $attributes = [], $tag = 'i') {
		if ( empty( $icon['library'] ) ) {
			return '';
		}

		if ( 'ekiticons' === $icon['library'] && self::is_svg_icon_experiment() ) {
			$content = self::get_svg_icon($icon, $attributes);

			if ( ! empty( $content ) ) {
				return $content;
			}
		} else {
			return \Elementor\Icons_Manager::try_get_icon_html($icon, $attributes, $tag);
		}
	}

	// TODO: remove this function if not used
	public static function icon($icon, $attributes = [], $tag = 'i') {
		if ( empty( $icon['library'] ) ) {
			return '';
		}

		if ( 'ekiticons' === $icon['library'] && self::is_svg_icon_experiment() ) {
			$content = self::get_svg_icon($icon, $attributes);

			if ( ! empty( $content ) ) {
				\Elementor\Utils::print_unescaped_internal_string($content);
				return true;
			}
		} else {
			\Elementor\Icons_Manager::render_icon($icon, $attributes, $tag);
		}
	}
}
