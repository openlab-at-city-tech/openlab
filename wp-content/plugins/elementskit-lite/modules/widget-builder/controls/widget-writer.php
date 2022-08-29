<?php

namespace ElementsKit_Lite\Modules\Widget_Builder\Controls;

use ElementsKit_Lite\Modules\Widget_Builder\Widget_File;

defined( 'ABSPATH' ) || exit;

class Widget_Writer {

	private $widget_name;
	private $widget_id;
	private $file_handler = '';
	private $file_name;
	private $enqueue_handler_prefix;
	private $widget_class_name;
	private $folder_name;
	private $control_prefix;
	private $name_prefix       = 'ekit_wb_';
	private $class_name_prefix = 'Ekit_Wb_';
	private $widget_obj;
	private $prepared_content = '';
	public $text_domain       = 'elementskit-lite';

	const TAB_CONTENT = 'Controls_Manager::TAB_CONTENT';
	const TAB_STYLE   = 'Controls_Manager::TAB_STYLE';
	const TAB_ADVANCE = 'Controls_Manager::TAB_ADVANCED';

	const CONTROL_GROUP_TYPE_SINGLE     = 'single';
	const CONTROL_GROUP_TYPE_RESPONSIVE = 'responsive';
	const CONTROL_GROUP_TYPE_GROUPED    = 'group';


	public function __construct( $widget, $widget_id, $txt_domain = 'elementskit-lite' ) {

		$this->widget_obj  = $widget;
		$this->file_name   = '';
		$this->text_domain = $txt_domain;
		$this->widget_id   = $widget_id;

		$this->folder_name       = $this->name_prefix . $this->widget_id;
		$this->widget_name       = $this->name_prefix . $this->widget_id;
		$this->widget_class_name = $this->class_name_prefix . $this->widget_id;
		$this->control_prefix    = $this->folder_name . '_';

		$this->enqueue_handler_prefix = 'ekit-wb-' . $this->widget_id;
	}


	public function start_backing( $wp_filesystem ) {

		$css_enqueue = $this->prepare_css_file( $this->widget_obj->css, $wp_filesystem );
		$js_enqueue  = $this->prepare_js_file( $this->widget_obj->js, $wp_filesystem );
		$include_js  = ! empty( $this->widget_obj->js_includes ) || ! empty( $this->widget_obj->css_includes );

		$content = $this->prepare_php_file();

		if ( $css_enqueue === true || $js_enqueue === true || $include_js === true ) {

			$content .= $this->write_construct_method( $css_enqueue, $js_enqueue );
		}

		$content .= $this->write_name_method();
		$content .= $this->write_title_method( $this->widget_obj->title );
		$content .= $this->write_categories_method( $this->widget_obj->categories );
		$content .= $this->write_icon_method( $this->widget_obj->icon );
		$content .= $this->write_register_control_method( $this->widget_obj->tabs );
		$content .= $this->write_render_method( $this->widget_obj->markup );
		$content .= $this->close_widget_class();

		$this->prepared_content = $content;
	}


	private function get_url_path() {

		$upload = wp_upload_dir();

		return $upload['baseurl'] . '/elementskit/custom_widgets/' . $this->folder_name;
	}


	private function get_file_path() {

		$upload     = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = $upload_dir . '/elementskit/custom_widgets/' . $this->folder_name;

		if ( ! is_dir( $upload_dir ) ) {
			wp_mkdir_p( $upload_dir );
		}

		return $upload_dir;
	}


	public static function delete_widget( $widget_id ) {

		$fl_sys = Widget_File::get_wp_filesystem_pointer();

		$wb = new self( array(), $widget_id );

		$dir = $wb->get_file_path();

		if ( file_exists( $dir ) ) {

			$fl_sys->delete( $dir, true );
		}

		return true; // :P
	}


	private function prepare_css_file( $content, $file_system ) {

		if ( empty( $content ) ) {
			return false;
		}

		$trimmed = trim( $content );

		if ( empty( $trimmed ) ) {
			return false;
		}

		$path = $this->get_file_path();

		return $file_system->put_contents( $path . '/style.css', $trimmed );
	}


	private function prepare_js_file( $content, $file_system ) {

		if ( empty( $content ) ) {
			return false;
		}

		$trimmed = trim( $content );

		if ( empty( $trimmed ) ) {
			return false;
		}

		$path = $this->get_file_path();

		return $file_system->put_contents( $path . '/script.js', $trimmed );
	}


	public function finish_backing( $file_system ) {

		$path = $this->get_file_path();

		return $file_system->put_contents( $path . '/widget.php', $this->prepared_content );
	}


	private function prepare_php_file() {

		$ret  = '<?php' . PHP_EOL . PHP_EOL;
		$ret .= 'namespace Elementor;' . PHP_EOL . PHP_EOL;
		$ret .= 'defined(\'ABSPATH\') || exit;' . PHP_EOL . PHP_EOL;
		$ret .= 'class ' . $this->widget_class_name . ' extends Widget_Base {' . PHP_EOL . PHP_EOL;

		return $ret;
	}


	private function write_construct_method( $css = false, $js = false ) {

		$nm       = $this->enqueue_handler_prefix;
		$url_path = $this->get_url_path();

		$ret  = "\t" . 'public function __construct($data = [], $args = null) {' . PHP_EOL;
		$ret .= "\t\t" . 'parent::__construct($data, $args);' . PHP_EOL . PHP_EOL;

		if ( $css === true ) {
			$ret .= "\t\t" . 'wp_register_style( \'' . $nm . '-style-handle\', \'' . $url_path . '/style.css\');' . PHP_EOL;
		}

		if ( $js === true ) {
			$ret .= "\t\t" . 'wp_register_script( \'' . $nm . '-script-handle\', \'' . $url_path . '/script.js\', [ \'elementor-frontend\' ], \'1.0.0\', true );' . PHP_EOL;
		}

		if ( ! empty( $this->widget_obj->css_includes ) ) {

			$ret .= PHP_EOL;

			foreach ( $this->widget_obj->css_includes as $idx => $cssInclude ) {

				$ret .= "\t\t" . 'wp_enqueue_style( \'' . $nm . '-' . $idx . '-style-handle\', \'' . $cssInclude . '\');' . PHP_EOL;
			}
		}

		if ( ! empty( $this->widget_obj->js_includes ) ) {

			$ret .= PHP_EOL;

			foreach ( $this->widget_obj->js_includes as $idx => $jsInclude ) {

				$ret .= "\t\t" . 'wp_enqueue_script( \'' . $nm . '-' . $idx . '-script-handle\', \'' . $jsInclude . '\', [ \'elementor-frontend\' ], \'1.0.0\', true );' . PHP_EOL;
			}
		}

		$ret .= "\t" . '}' . PHP_EOL . PHP_EOL;

		if ( $css === true ) {
			$ret .= "\n\t" . 'public function get_style_depends() {' . PHP_EOL;
			$ret .= "\t\t" . 'return [ \'' . $nm . '-style-handle\' ];' . PHP_EOL;
			$ret .= "\t" . '}' . PHP_EOL . PHP_EOL;
		}

		if ( $js === true ) {
			$ret .= "\n\t" . 'public function get_script_depends() {' . PHP_EOL;
			$ret .= "\t\t" . 'return [ \'' . $nm . '-script-handle\' ];' . PHP_EOL;
			$ret .= "\t" . '}' . PHP_EOL . PHP_EOL;
		}

		return $ret;
	}


	private function write_name_method() {

		$ret  = "\t" . 'public function get_name() {' . PHP_EOL;
		$ret .= "\t\t" . 'return \'' . $this->widget_name . '\';' . PHP_EOL;
		$ret .= "\t" . '}' . PHP_EOL . PHP_EOL;

		return $ret;
	}


	private function write_title_method( $title = 'empty_title' ) {

		$ret  = "\n\t" . 'public function get_title() {' . PHP_EOL;
		$ret .= "\t\t" . 'return esc_html__( \'' . esc_html($title) . '\', \'' . $this->text_domain . '\' );' . PHP_EOL;
		$ret .= "\t" . '}' . PHP_EOL . PHP_EOL;

		return $ret;
	}


	private function write_categories_method( $cat = array( 'basic' ) ) {

		$cat = empty( $cat ) ? array( 'basic' ) : $cat;

		$joined  = '\'';
		$joined .= implode( '\', \'', $cat );
		$joined .= '\'';

		$ret  = "\n\t" . 'public function get_categories() {' . PHP_EOL;
		$ret .= "\t\t" . 'return [' . $joined . '];' . PHP_EOL;
		$ret .= "\t" . '}' . PHP_EOL . PHP_EOL;

		return $ret;
	}


	private function write_icon_method( $icon = 'eicon-cog' ) {

		$ret  = "\n\t" . 'public function get_icon() {' . PHP_EOL;
		$ret .= "\t\t" . 'return \'' . $icon . '\';' . PHP_EOL;
		$ret .= "\t" . '}' . PHP_EOL . PHP_EOL;

		return $ret;
	}


	private function write_register_control_method( $conf = array() ) {

		$ret = "\n\t" . 'protected function register_controls() {' . PHP_EOL;

		if ( ! empty( $conf->content ) ) {

			foreach ( $conf->content as $indx => $section ) {

				$ret .= $this->write_section( $section->title, self::TAB_CONTENT, 'content', $indx );

				if ( ! empty( $section->controls ) ) {

					$ret .= $this->write_add_control( $section->controls );
				}

				$ret .= $this->close_section();
			}
		}

		if ( ! empty( $conf->style ) ) {

			foreach ( $conf->style as $indx => $section ) {

				$ret .= $this->write_section( $section->title, self::TAB_STYLE, 'style', $indx );

				if ( ! empty( $section->controls ) ) {

					$ret .= $this->write_add_control( $section->controls );
				}

				$ret .= $this->close_section();
			}
		}

		if ( ! empty( $conf->advanced ) ) {

			foreach ( $conf->advanced as $indx => $section ) {

				$ret .= $this->write_section( $section->title, self::TAB_ADVANCE, 'advance', $indx );

				if ( ! empty( $section->controls ) ) {

					$ret .= $this->write_add_control( $section->controls );
				}

				$ret .= $this->close_section();
			}
		}

		$ret .= "\t" . '}' . PHP_EOL . PHP_EOL;

		return $ret;
	}


	private function write_section( $label, $tab = 'Controls_Manager::TAB_CONTENT', $tab_name = 'content', $indx = '' ) {

		$key = $tab_name . '_section_' . $this->widget_id . '_' . $indx;

		$ret  = "\n\t\t" . '$this->start_controls_section(' . PHP_EOL;
		$ret .= "\t\t\t" . '\'' . $key . '\',' . PHP_EOL;
		$ret .= "\t\t\t" . 'array(' . PHP_EOL;

		$ret .= "\t\t\t\t" . '\'label\' => esc_html__( \'' . esc_html($label) . '\', \'' . $this->text_domain . '\' ),' . PHP_EOL;
		$ret .= "\t\t\t\t" . '\'tab\'   => ' . $tab . ',' . PHP_EOL;

		$ret .= "\t\t\t" . ')' . PHP_EOL;
		$ret .= "\t\t" . ');' . PHP_EOL;

		return $ret;
	}


	private function write_add_control( $controls = array() ) {

		$ret = '';

		foreach ( $controls as $controlObj ) {

			if ( $controlObj->control_group === self::CONTROL_GROUP_TYPE_SINGLE ) {

				$ret .= $this->prepare_add_control( $controlObj );

			} elseif ( $controlObj->control_group === self::CONTROL_GROUP_TYPE_RESPONSIVE ) {

				$ret .= $this->prepare_responsive_control( $controlObj );

			} elseif ( $controlObj->control_group === self::CONTROL_GROUP_TYPE_GROUPED ) {

				$ret .= $this->prepare_group_control( $controlObj );
			}
		}

		return $ret;
	}


	private function prepare_add_control( $controlObj ) {

		$factory = new CT_Factory();
		$cnt_obj = $factory->make( $controlObj->controlType, $this->text_domain );

		$ret  = "\n\t\t" . '$this->add_control(' . PHP_EOL;
		$ret .= "\t\t\t" . '\'' . $this->control_prefix . $controlObj->key . '\',' . PHP_EOL;
		$ret .= "\t\t\t" . 'array(' . PHP_EOL;

		$ret .= "\t\t\t\t" . '\'label\' => esc_html__( \'' . esc_html($controlObj->label) . '\', \'' . $this->text_domain . '\' ),' . PHP_EOL;
		$ret .= "\t\t\t\t" . '\'type\'  => ' . $controlObj->control_type . ',' . PHP_EOL;

		$ret .= $cnt_obj->start_writing_conf( $this->file_handler, $controlObj );

		$ret .= "\t\t\t" . ')' . PHP_EOL;
		$ret .= "\t\t" . ');' . PHP_EOL;

		return $ret;
	}


	private function prepare_responsive_control( $controlObj ) {

		$factory = new CT_Factory();
		$cnt_obj = $factory->make( $controlObj->controlType, $this->text_domain, 'responsive' );

		$ret  = "\n\t\t" . '$this->add_responsive_control(' . PHP_EOL;
		$ret .= "\t\t\t" . '\'' . $this->control_prefix . $controlObj->key . '\',' . PHP_EOL;
		$ret .= "\t\t\t" . 'array(' . PHP_EOL;

		$ret .= "\t\t\t\t" . '\'label\' => esc_html__( \'' . esc_html($controlObj->label) . '\', \'' . $this->text_domain . '\' ),' . PHP_EOL;
		$ret .= "\t\t\t\t" . '\'type\'  => ' . $controlObj->control_type . ',' . PHP_EOL;

		//$cnt_obj->start_writing_conf($this->file_handler, $controlObj);

		$ret .= "\t\t\t" . ')' . PHP_EOL;
		$ret .= "\t\t" . ');' . PHP_EOL;

		return $ret;
	}


	private function prepare_group_control( $controlObj ) {

		$factory = new CT_Factory();
		$cnt_obj = $factory->make( $controlObj->controlType, $this->text_domain, 'group' );

		$ret  = "\n\t\t" . '$this->add_group_control(' . PHP_EOL;
		$ret .= "\t\t\t" . '' . $controlObj->control_type . ',' . PHP_EOL;
		$ret .= "\t\t\t" . 'array(' . PHP_EOL;

		$ret .= "\t\t\t\t" . '\'name\' => \'' . $this->control_prefix . $controlObj->key . '\',' . PHP_EOL;

		$ret .= $cnt_obj->start_writing_conf( $this->file_handler, $controlObj );

		$ret .= "\t\t\t" . ')' . PHP_EOL;
		$ret .= "\t\t" . ');' . PHP_EOL;

		return $ret;
	}


	private function close_section() {

		return "\n\t\t" . '$this->end_controls_section();' . PHP_EOL . PHP_EOL;
	}


	private function write_render_method( $markup = '' ) {

		$markup = \ElementsKit_Lite\Libs\Template\Loader::instance()->replace_tags( $markup, $this->control_prefix );

		$ret = "\n\t" . 'protected function render() {' . PHP_EOL;

		if ( ! empty( $markup ) ) {

			$ret .= "\t\t" . '$settings = $this->get_settings_for_display();' . PHP_EOL . PHP_EOL;

			$ret .= "\t\t" . '?>' . PHP_EOL;
			$ret .= $markup . PHP_EOL;
			$ret .= "\t\t" . '<?php' . PHP_EOL;
		}

		$ret .= "\t" . '}' . PHP_EOL . PHP_EOL;

		return $ret;
	}


	private function close_widget_class() {

		return PHP_EOL . '}' . PHP_EOL;
	}


	private function close_php_writer() {

		fclose( $this->file_handler );

		return true;
	}


	/**
	 * @return string
	 */
	public function get_widget_class_name() {
		return $this->widget_class_name;
	}
}
