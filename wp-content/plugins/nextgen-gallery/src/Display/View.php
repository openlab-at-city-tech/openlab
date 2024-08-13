<?php

namespace Imagely\NGG\Display;

use Imagely\NGG\DisplayType\ControllerFactory;
use Imagely\NGG\DisplayedGallery\TriggerManager;
use Imagely\NGG\Util\Filesystem;

class View {

	public $template        = '';
	public $params          = [];
	public $queue           = [];
	public $legacy_template = '';

	public static $default_root_dir = NGG_PLUGIN_DIR;

	public function __construct( $template, $params = [], $legacy_template_filename = '' ) {
		$this->template        = $template;
		$this->params          = (array) $params;
		$this->legacy_template = $legacy_template_filename;

		// Allow display types' global template / 'view' setting to be used in place of whatever is passed as
		// $template from the display type controller.
		if ( isset( $params['displayed_gallery'] )
			&& isset( $params['displayed_gallery']->display_type )
			&& isset( $params['display_type_rendering'] )
			&& $params['display_type_rendering']
			&& ControllerFactory::has_controller( $params['displayed_gallery']->display_type ) ) {
			$controller     = ControllerFactory::get_controller( $params['displayed_gallery']->display_type );
			$this->template = $controller->get_display_type_view_abspath( $template, $params );
		}
	}

	/**
	 * Returns the variables to be used in the template
	 *
	 * @return array
	 */
	public function get_template_vars() {
		$retval = [];

		foreach ( $this->params as $key => $value ) {
			if ( strpos( $key, '_template' ) === 0 ) {
				$value = $this->get_template_abspath( $value );
			}
			$retval[ $key ] = $value;
		}

		return $retval;
	}

	/**
	 * @param string $filename
	 * @return bool
	 */
	public function is_valid_template_filename( $filename ) {
		$fs = Filesystem::get_instance();

		$filename = str_replace( '\\', '/', $filename );

		// Do not allow PHP input streams as a source.
		if ( false !== strpos( $filename, '://' ) ) {
			return false;
		}

		// Prevent all "../" attempts.
		if ( false !== strpos( $filename, '../' ) ) {
			return false;
		}

		// Bitnami stores files in /opt/bitnami, but PHP's ReflectionClass->getFileName() can report /bitnami
		// which causes this method to reject files for being outside the server document root.
		if ( 0 === strpos( $filename, '/bitnami', 0 ) ) {
			$filename = '/opt' . $filename;
		}

		// The template must reside in the WordPress root or its plugin, theme, or content directories.
		$permitted_directories = [
			$fs->get_document_root(),
			$fs->get_document_root( 'plugin' ),
			$fs->get_document_root( 'plugin_mu' ),
			$fs->get_document_root( 'theme' ),
			$fs->get_document_root( 'content' ),

		];
		$found = false;
		foreach ( $permitted_directories as $directory ) {
			if ( $found ) {
				continue;
			}
			if ( 0 === strpos( $filename, $directory ) ) {
				$found = true;
			}
		}
		if ( ! $found ) {
			return false;
		}

		// Filename must end with ".php".
		if ( substr_compare( $filename, '.php', -3 ) === 0 ) {
			return false;
		}

		return true;
	}

	public function get_template_abspath( string $template = null ): string {
		if ( ! $template ) {
			$template = $this->template;
		}

		$legacy_template_path = ! empty( $this->legacy_template ) ? $this->legacy_template : '';

		if ( 0 === strpos( $template, DIRECTORY_SEPARATOR ) ) {
			// $value is an absolute path, but it must be validated first
			if ( ! $this->is_valid_template_filename( $template ) || ! @file_exists( $template ) ) {
				$display_name = esc_html( $template );
				throw new \RuntimeException( esc_html( $display_name ) . ' is not a valid MVC template' );
			}
		} else {
			$template = $this->find_template_abspath( $template, $legacy_template_path );
		}

		return $template;
	}

	public function rasterize_object( ViewElement $element ) {
		return $element->rasterize();
	}

	/**
	 * @param $id
	 * @param $type
	 * @param $context
	 * @return ViewElement
	 */
	public function start_element( $id, $type = null, $context = null ) {
		if ( $type == null ) {
			$type = 'element';
		}

		$count   = count( $this->queue );
		$element = new ViewElement( $id, $type );

		if ( $context != null ) {
			if ( ! is_array( $context ) ) {
				$context = [ 'object' => $context ];
			}

			foreach ( $context as $context_name => $context_value ) {
				$element->set_context( $context_name, $context_value );
			}
		}

		$this->queue[] = $element;

		if ( $count > 0 ) {
			$old_element = $this->queue[ $count - 1 ];

			$content = ob_get_contents();
			ob_clean();

			$old_element->append( $content );
			$old_element->append( $element );
		}

		ob_start();

		return $element;
	}

	public function end_element() {
		$content = ob_get_clean();

		$element = array_pop( $this->queue );

		if ( $content != null ) {
			$element->append( $content );
		}

		return $element;
	}

	/**
	 * Renders a sub-template for the view
	 *
	 * @param string $__template
	 * @param array  $__params
	 * @param bool   $__return Unused
	 * @return NULL
	 */
	public function include_template( $__template, $__params = null, $__return = false ) {
		// Use underscores to prefix local variables to avoid conflicts wth template vars.
		if ( $__params === null ) {
			$__params = [];
		}

		// Existing templates copied from the NextGEN source will include these template paths; alias them for compatibility.
		if ( 'photocrati-nextgen_gallery_display#image/before' === $__template ) {
			$__template = 'GalleryDisplay/ImageBefore';
		} elseif ( 'photocrati-nextgen_gallery_display#image/after' === $__template ) {
			$__template = 'GalleryDisplay/ImageAfter';
		} elseif ( 'photocrati-nextgen_gallery_display#container/before' === $__template ) {
			$__template = 'GalleryDisplay/ContainerBefore';
		} elseif ( 'photocrati-nextgen_gallery_display#container/after' === $__template ) {
			$__template = 'GalleryDisplay/ContainerAfter';
		} elseif ( 'photocrati-nextgen_gallery_display#list/before' === $__template ) {
			$__template = 'GalleryDisplay/ListBefore';
		} elseif ( 'photocrati-nextgen_gallery_display#list/after' === $__template ) {
			$__template = 'GalleryDisplay/ListAfter';
		}

		$__params['template_origin'] = $this->template;

		$__target              = $this->get_template_abspath( $__template );
		$__origin_target       = $this->get_template_abspath( $this->template );
		$__image_before_target = $this->get_template_abspath( 'GalleryDisplay/ImageBefore' );
		$__image_after_target  = $this->get_template_abspath( 'GalleryDisplay/ImageAfter' );

		if ( $__origin_target !== $__target ) {
			if ( $__target == $__image_before_target ) {
				$__image = isset( $__params['image'] ) ? $__params['image'] : null;
				$this->start_element( 'nextgen_gallery.image_panel', 'item', $__image );
			}

			if ( $__target == $__image_after_target ) {
				$this->end_element();
			}

			extract( $__params );

			include $__target;

			if ( $__target == $__image_before_target ) {
				$__image = isset( $__params['image'] ) ? $__params['image'] : null;
				$this->start_element( 'nextgen_gallery.image', 'item', $__image );
			}

			if ( $__target == $__image_after_target ) {
				$this->end_element();
			}
		}

		return null;
	}

	/**
	 * Gets the absolute path of an MVC template file
	 *
	 * @param string       $template
	 * @param string|false $module (optional)
	 * @param string       $legacy_template Non-POPE path coming from 'templates' in the plugin root.
	 * @return string
	 */
	public function find_template_abspath( $template, $legacy_template = '' ) {
		$fs = Filesystem::get_instance();

		// Legacy file overrides are stored in the form module_name#path.
		if ( ! empty( $legacy_template ) ) {
			list($legacy_template, $module) = $fs->parse_formatted_path( $legacy_template );
		}

		// Append the '.php' suffix if necessary.
		if ( substr( $template, -strlen( '.php' ) ) !== '.php' ) {
			$template = $template . '.php';
		}
		if ( substr( $legacy_template, -strlen( '.php' ) ) !== '.php' ) {
			$legacy_template = $legacy_template . '.php';
		}

		// First check if the template is in the override dir.
		if ( ! empty( $module ) ) {
			$retval = $this->get_template_override_abspath( $module, $legacy_template );
		}

		// $template is an absolute path to an existing file.
		if ( file_exists( $template ) ) {
			return $template;
		}

		// Use static:: here so this class can be extended by other plugins.
		if ( ! isset( $retval ) ) {
			$retval = path_join( static::$default_root_dir, 'templates' . DIRECTORY_SEPARATOR . $template );
		}

		// In case this class has been extended we should use NGG provided templates if they aren't overridden.
		if ( ! file_exists( $retval ) ) {
			$retval = path_join( self::$default_root_dir, 'templates' . DIRECTORY_SEPARATOR . $template );
		}

		if ( ! file_exists( $retval ) ) {
			throw new \RuntimeException( "{$retval} is not a valid MVC template" );
		}

		return $retval;
	}

	/**
	 * @param null|string $module_id
	 * @return string
	 */
	public function get_template_override_dir( $module_id = null ) {
		$root = \trailingslashit( path_join( WP_CONTENT_DIR, 'ngg' ) );
		if ( ! @file_exists( $root ) && is_writable( \trailingslashit( WP_CONTENT_DIR ) ) ) {
			\wp_mkdir_p( $root );
		}

		$modules = \trailingslashit( path_join( $root, 'modules' ) );

		if ( ! @file_exists( $modules ) && is_writable( $root ) ) {
			\wp_mkdir_p( $modules );
		}

		if ( $module_id ) {
			$module_dir = \trailingslashit( path_join( $modules, $module_id ) );
			if ( ! @file_exists( $module_dir ) && is_writable( $modules ) ) {
				\wp_mkdir_p( $module_dir );
			}

			$template_dir = \trailingslashit( \path_join( $module_dir, 'templates' ) );
			if ( ! @file_exists( $template_dir ) && is_writable( $module_dir ) ) {
				\wp_mkdir_p( $template_dir );
			}

			return $template_dir;
		}

		return $modules;
	}

	/**
	 * @param $module
	 * @param $filename
	 * @return string|null
	 */
	public function get_template_override_abspath( $module, $filename ) {
		$abspath = FileSystem::get_instance()->join_paths( $this->get_template_override_dir( $module ), $filename );
		if ( @file_exists( $abspath ) ) {
			return $abspath;
		}

		return null;
	}

	/**
	 * Renders the view (template)
	 *
	 * @param bool $return (optional)
	 * @return string|NULL
	 */
	public function render( $return = false ) {
		$content = $this->rasterize_object( $this->render_object() );

		if ( ! $return ) {
			echo $content;
		}

		return $content;
	}

	/**
	 * @return ViewElement
	 */
	public function render_object() {
		// Use underscores to prefix local variables to avoid conflicts with template vars.
		$__element = $this->start_element( $this->template, 'template' );

		$template_vars = $this->get_template_vars();
		extract( $template_vars );

		include $this->get_template_abspath();

		$this->end_element();

		if ( ( $displayed_gallery = $this->get_param( 'displayed_gallery' ) ) && $this->get_param( 'display_type_rendering' ) ) {
			$triggers = TriggerManager::get_instance();
			$triggers->render( $__element, $displayed_gallery );

			// Allow 'trigger icons' and albums' breadcrumbs and thumbnails to inject themselves.
			$__element = \apply_filters( 'ngg_display_type_rendering_object', $__element, $this->get_param( 'displayed_gallery' ) );
		}

		return $__element;
	}

	/**
	 * Adds a template parameter
	 *
	 * @param $key
	 * @param $value
	 */
	public function set_param( $key, $value ) {
		$this->params[ $key ] = $value;
	}

	/**
	 * Removes a template parameter
	 *
	 * @param $key
	 */
	public function remove_param( $key ) {
		unset( $this->params[ $key ] );
	}

	/**
	 * Gets the value of a template parameter
	 *
	 * @param $key
	 * @param null $default
	 * @return mixed
	 */
	public function get_param( $key, $default = null ) {
		if ( isset( $this->params[ $key ] ) ) {
			return $this->params[ $key ];
		} else {
			return $default;
		}
	}
}
