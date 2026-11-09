<?php

namespace Imagely\NGG\Display;

use Imagely\NGG\DisplayType\ControllerFactory;
use Imagely\NGG\DisplayedGallery\TriggerManager;
use Imagely\NGG\Util\Filesystem;

/**
 * View class for rendering templates
 */
class View {

	/**
	 * Template path
	 *
	 * @var string
	 */
	public $template = '';

	/**
	 * Template parameters
	 *
	 * @var array
	 */
	public $params = [];

	/**
	 * Element queue
	 *
	 * @var array
	 */
	public $queue = [];

	/**
	 * Legacy template path
	 *
	 * @var string
	 */
	public $legacy_template = '';

	/**
	 * Default root directory
	 *
	 * @var string
	 */
	public static $default_root_dir = NGG_PLUGIN_DIR;

	/**
	 * Constructor
	 *
	 * @param string $template The template path.
	 * @param array  $params Template parameters.
	 * @param string $legacy_template_filename Legacy template filename.
	 */
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
				try {
					$value = $this->get_template_abspath( $value );
				} catch ( \RuntimeException $e ) {
					// A caller-controlled parameter (e.g. a shortcode attribute) can share the
					// '_template' prefix without being a real template path. Such a value must not
					// fatal the render; nor should the raw, unvalidated value be kept, since a
					// '_template'-keyed variable is expected to be an already-resolved path and a
					// preserved attacker string could be mistaken for one downstream. Drop the key.
					unset( $e );
					continue;
				}
			}
			$retval[ $key ] = $value;
		}

		return $retval;
	}

	/**
	 * Validates if a template filename is valid and secure.
	 *
	 * @param string $filename The filename to validate.
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
		//
		// This gate was inert in both directions. The offset was -3, so it compared the last three
		// characters ("php") against the four-character needle ".php" and could never return 0 -
		// substr_compare( 'a.php', '.php', -3 ) is 66, not 0. And the sense was inverted against
		// the comment: it returned false when the name *did* end in ".php", so correcting only the
		// offset would have rejected every legitimate template instead. Both are fixed together.
		if ( substr_compare( $filename, '.php', -4 ) !== 0 ) {
			return false;
		}

		return true;
	}

	/**
	 * Gets the absolute path to a template
	 *
	 * @param string|null $template The template name.
	 * @return string
	 * @throws \RuntimeException If template is not valid or doesn't exist.
	 */
	// phpcs:ignore PHPCompatibility.FunctionDeclarations.NewNullableTypes -- Explicit nullable required for PHP 8.4.
	public function get_template_abspath( ?string $template = null ): string {
		if ( ! $template ) {
			$template = $this->template;
		}

		// SECURITY: refuse stream-wrapper syntax ("://") on every branch, before any
		// filesystem call. is_valid_template_filename() already makes this refusal, but
		// it only runs on the absolute-path branch below; the relative branch reaches
		// find_template_abspath()'s file_exists() with the raw value intact. A value such
		// as "phar:///path/to/image.png/x.php" makes PHP open the named Phar archive. On
		// PHP <= 7.4 that unserialises the archive's metadata object (object injection);
		// PHP 8.0 defers metadata unserialization to an explicit getMetadata() call, so
		// the file op itself is inert there, but a wrapper is still never a valid
		// template and must not reach the filesystem regardless.
		if ( false !== strpos( str_replace( '\\', '/', (string) $template ), '://' ) ) {
			throw new \RuntimeException( esc_html( (string) $template ) . ' is not a valid MVC template' );
		}

		$legacy_template_path = ! empty( $this->legacy_template ) ? $this->legacy_template : '';

		if ( 0 === strpos( $template, DIRECTORY_SEPARATOR ) ) {
			// $value is an absolute path, but it must be validated first
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( ! $this->is_valid_template_filename( $template ) || ! @file_exists( $template ) ) {
				$display_name = esc_html( $template );
				throw new \RuntimeException( esc_html( $display_name ) . ' is not a valid MVC template' );
			}
		} else {
			$template = $this->find_template_abspath( $template, $legacy_template_path );
		}

		return $template;
	}

	/**
	 * Rasterizes a view element
	 *
	 * @param ViewElement $element The element to rasterize.
	 * @return string
	 */
	public function rasterize_object( ViewElement $element ) {
		return $element->rasterize();
	}

	/**
	 * Starts a new element
	 *
	 * @param string      $id The element ID.
	 * @param string|null $type The element type.
	 * @param mixed|null  $context The element context.
	 * @return ViewElement
	 */
	public function start_element( $id, $type = null, $context = null ) {
		if ( null === $type ) {
			$type = 'element';
		}

		$count   = count( $this->queue );
		$element = new ViewElement( $id, $type );

		if ( null !== $context ) {
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

	/**
	 * Ends the current element
	 *
	 * @return ViewElement
	 */
	public function end_element() {
		$content = ob_get_clean();

		$element = array_pop( $this->queue );

		if ( null !== $content ) {
			$element->append( $content );
		}

		return $element;
	}

	/**
	 * Renders a sub-template for the view
	 *
	 * @param string     $__template The template path.
	 * @param array|null $__params Template parameters.
	 * @param bool       $__return Unused.
	 * @return NULL
	 */
	public function include_template( $__template, $__params = null, $__return = false ) {
		// Use underscores to prefix local variables to avoid conflicts wth template vars.
		if ( $__params === null ) { // phpcs:ignore WordPress.PHP.YodaConditions.NotYoda
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
			if ( $__image_before_target === $__target ) {
				$__image = isset( $__params['image'] ) ? $__params['image'] : null;
				$this->start_element( 'nextgen_gallery.image_panel', 'item', $__image );
			}

			if ( $__image_after_target === $__target ) {
				$this->end_element();
			}

			// EXTR_SKIP keeps the already-resolved $__target (and the other local
			// render state) from being overwritten by a caller-supplied key of the
			// same name, so the path validated above is the path that is included.
			extract( $__params, EXTR_SKIP );

			include $__target;

			if ( $__image_before_target === $__target ) {
				$__image = isset( $__params['image'] ) ? $__params['image'] : null;
				$this->start_element( 'nextgen_gallery.image', 'item', $__image );
			}

			if ( $__image_after_target === $__target ) {
				$this->end_element();
			}
		}

		return null;
	}

	/**
	 * Gets the absolute path of an MVC template file
	 *
	 * @param string       $template The template path.
	 * @param string|false $legacy_template Non-POPE path coming from 'templates' in the plugin root.
	 * @return string
	 * @throws \RuntimeException If template is not found or invalid.
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
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message for debugging only
			throw new \RuntimeException( "{$retval} is not a valid MVC template" );
		}

		return $retval;
	}

	/**
	 * Gets the template override directory
	 *
	 * @param null|string $module_id The module ID.
	 * @return string
	 */
	public function get_template_override_dir( $module_id = null ) {
		$root = \trailingslashit( path_join( WP_CONTENT_DIR, 'ngg' ) );
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! @file_exists( $root ) && \wp_is_writable( \trailingslashit( WP_CONTENT_DIR ) ) ) {
			\wp_mkdir_p( $root );
		}

		$modules = \trailingslashit( path_join( $root, 'modules' ) );

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! @file_exists( $modules ) && \wp_is_writable( $root ) ) {
			\wp_mkdir_p( $modules );
		}

		if ( $module_id ) {
			$module_dir = \trailingslashit( path_join( $modules, $module_id ) );
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( ! @file_exists( $module_dir ) && \wp_is_writable( $modules ) ) {
				\wp_mkdir_p( $module_dir );
			}

			$template_dir = \trailingslashit( \path_join( $module_dir, 'templates' ) );
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( ! @file_exists( $template_dir ) && \wp_is_writable( $module_dir ) ) {
				\wp_mkdir_p( $template_dir );
			}

			return $template_dir;
		}

		return $modules;
	}

	/**
	 * Gets the absolute path to a template override
	 *
	 * @param string $module The module name.
	 * @param string $filename The template filename.
	 * @return string|null
	 */
	public function get_template_override_abspath( $module, $filename ) {
		$abspath = FileSystem::get_instance()->join_paths( $this->get_template_override_dir( $module ), $filename );
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( @file_exists( $abspath ) ) {
			return $abspath;
		}

		return null;
	}

	/**
	 * Renders the view (template)
	 *
	 * @param bool $return_output (optional) Whether to return the content or echo it.
	 * @return string|NULL
	 */
	public function render( $return_output = false ) {
		$content = $this->rasterize_object( $this->render_object() );

		if ( ! $return_output ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content is escaped in templates
			echo $content;
		}

		return $content;
	}

	/**
	 * Renders the view object
	 *
	 * @return ViewElement
	 */
	public function render_object() {
		// Use underscores to prefix local variables to avoid conflicts with template vars.
		$__element = $this->start_element( $this->template, 'template' );

		// Template variables are a caller-controlled bag (shortcode attributes flow into it), so
		// they must never be extracted into this method's own scope - a parameter named like a
		// local (e.g. __element) would otherwise overwrite the ViewElement this method returns and
		// fatal in rasterize_object(). Give them their own isolated scope instead.
		$this->include_isolated( $this->get_template_abspath(), $this->get_template_vars() );

		$this->end_element();

		$displayed_gallery = $this->get_param( 'displayed_gallery' );
		if ( $displayed_gallery && $this->get_param( 'display_type_rendering' ) ) {
			$triggers = TriggerManager::get_instance();
			$triggers->render( $__element, $displayed_gallery );

			// Allow 'trigger icons' and albums' breadcrumbs and thumbnails to inject themselves.
			$__element = \apply_filters( 'ngg_display_type_rendering_object', $__element, $this->get_param( 'displayed_gallery' ) );
		}

		return $__element;
	}

	/**
	 * Includes a template with the given variables in scope and nothing else.
	 *
	 * Deliberately holds no local that a template variable could collide with (both locals are
	 * obscurely underscore-prefixed and skipped by EXTR_SKIP), and stays a method on the view so
	 * templates keep access to $this->start_element() / $this->include_template().
	 *
	 * @param string $__ngg_target The absolute path of the template to include.
	 * @param array  $__ngg_vars   The template variables to expose in the template's scope.
	 * @return void
	 */
	private function include_isolated( $__ngg_target, array $__ngg_vars ) {
		extract( $__ngg_vars, EXTR_SKIP );
		include $__ngg_target;
	}

	/**
	 * Adds a template parameter
	 *
	 * @param string $key The parameter key.
	 * @param mixed  $value The parameter value.
	 * @return void
	 */
	public function set_param( $key, $value ) {
		$this->params[ $key ] = $value;
	}

	/**
	 * Removes a template parameter
	 *
	 * @param string $key The parameter key.
	 * @return void
	 */
	public function remove_param( $key ) {
		unset( $this->params[ $key ] );
	}

	/**
	 * Gets the value of a template parameter
	 *
	 * @param string $key The parameter key.
	 * @param mixed  $default_value The default value.
	 * @return mixed
	 */
	public function get_param( $key, $default_value = null ) {
		if ( isset( $this->params[ $key ] ) ) {
			return $this->params[ $key ];
		} else {
			return $default_value;
		}
	}
}
