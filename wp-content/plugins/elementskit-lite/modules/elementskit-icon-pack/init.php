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
			$widget_content = self::replace_icon_tags($widget_content);
		}

		return $widget_content;
	}

	public static function is_svg_icon_experiment() {
		$elementskit_options = get_option( 'elementskit_options' );
		$inline_svg = $elementskit_options['user_data']['inline_svg']['is_enable'] ?? false;
		return apply_filters( 'elementskit_font_icon_inline_svg', $inline_svg );
	}

	public static function replace_icon_tags( string $widget_content ): string {
		return preg_replace_callback(
			'/<i[^>]*class="([^"]*)"[^>]*><\/i>/',
			function ( array $matches ): string {
				$original_tag  = $matches[0];
				$class_string  = $matches[1];

				// Bail early if this is not an ElementsKit icon tag
				if ( strpos( $class_string, 'icon icon-' ) === false ) {
					return $original_tag;
				}

				// Extract the icon name e.g. "icon icon-down-arrow1" → "down-arrow1"
				preg_match( '/icon icon-([^\s"]+)/', $class_string, $icon_matches );
				$icon_name = $icon_matches[1] ?? '';

				// Generate the ElementsKit icon HTML
				$icon_html = self::get_icon_html( [
					'library' => 'ekiticons',
					'value'   => 'icon icon-' . $icon_name,
				] );

				if ( empty( $icon_html ) ) {
					return $original_tag;
				}

				// Strip the "icon icon-{name}" segment to get any remaining classes
				$extra_classes = trim( preg_replace( '/\s*icon icon-[^\s"]+\s*/', ' ', $class_string ) );

				// Prepend extra classes to the generated icon HTML if any exist
				if ( ! empty( $extra_classes ) ) {
					$icon_html = str_replace( 'class="', 'class="' . $extra_classes . ' ', $icon_html );
				}

				return $icon_html;
			},
			$widget_content
		);
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
		$icon_name = str_replace('icon icon-', '', $icon['value']);
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
