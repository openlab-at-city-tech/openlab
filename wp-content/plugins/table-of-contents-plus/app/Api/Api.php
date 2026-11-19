<?php
namespace AIOSEO\TableOfContents\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the registration, validation and authorization of API routes.
 *
 * @since 1.0.0
 */
class Api {
	/**
	 * The REST API namespace.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $namespace = 'aioseoTableOfContents/v1';

	/**
	 * The routes we use in the rest API.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $routes = [
		// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
		'GET'    => [
			'options'      => [ 'callback' => [ 'VueSettings', 'getOptions' ], 'access' => 'any' ],
			'toc-settings' => [ 'callback' => [ 'TocSettings', 'getSettings' ], 'access' => 'aioseo_table_of_contents_page' ],
			'ping'         => [ 'callback' => [ 'Ping', 'ping' ], 'access' => 'any' ]
		],
		'POST'   => [
			'notifications/dismiss'   => [ 'callback' => [ 'Notifications', 'dismissNotifications' ], 'access' => 'any' ],
			'objects'                 => [ 'callback' => [ 'PostsTerms', 'searchForObjects' ], 'access' => [ 'aioseo_table_of_contents_page' ] ],
			'options'                 => [ 'callback' => [ 'VueSettings', 'saveChanges' ], 'access' => 'aioseo_table_of_contents_page' ],
			'plugins/deactivate'      => [ 'callback' => [ 'Plugins', 'deactivatePlugins' ], 'access' => 'install_plugins' ],
			'plugins/install'         => [ 'callback' => [ 'Plugins', 'installPlugins' ], 'access' => 'install_plugins' ],
			'toc-settings'            => [ 'callback' => [ 'TocSettings', 'saveSettings' ], 'access' => 'aioseo_table_of_contents_page' ],
			'settings/toggle-radio'   => [ 'callback' => [ 'VueSettings', 'toggleRadio' ], 'access' => 'aioseo_table_of_contents_page' ],
			'settings/items-per-page' => [ 'callback' => [ 'VueSettings', 'changeItemsPerPage' ], 'access' => 'aioseo_table_of_contents_page' ]
		]
		// phpcs:enable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
	];

	/**
	 * Class contructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'rest_allowed_cors_headers', [ $this, 'allowedHeaders' ] );
		add_action( 'rest_api_init', [ $this, 'registerRoutes' ] );
	}

	/**
	 * Registers the API routes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function registerRoutes() {
		$class = new \ReflectionClass( get_called_class() );
		foreach ( $this->routes as $method => $data ) {
			foreach ( $data as $route => $options ) {
				register_rest_route(
					$this->namespace,
					$route,
					[
						'methods'             => $method,
						'permission_callback' => empty( $options['permissions'] ) ? [ $this, 'validRequest' ] : [ $this, $options['permissions'] ],
						'callback'            => is_array( $options['callback'] )
							? [
								(
									! empty( $options['callback'][2] )
										? $options['callback'][2] . '\\' . $options['callback'][0]
										: (
											class_exists( $class->getNamespaceName() . '\\' . $options['callback'][0] )
												? $class->getNamespaceName() . '\\' . $options['callback'][0]
												: __NAMESPACE__ . '\\' . $options['callback'][0]
										)
								),
								$options['callback'][1]
							]
							: [ $this, $options['callback'] ]
					]
				);
			}
		}
	}

	/**
	 * Sets headers that are allowed for our API routes.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $allowHeaders The allowed request headers.
	 * @return array               The allowed request headers.
	 */
	public function allowedHeaders( $allowHeaders ) {
		if ( ! array_search( 'X-WP-Nonce', $allowHeaders, true ) ) {
			$allowHeaders[] = 'X-WP-Nonce';
		}

		return $allowHeaders;
	}

	/**
	 * Determine if the user is logged in and has the proper permissions.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_REST_Request $request The REST Request.
	 * @return bool                      Whether the user is allowed access to the route.
	 */
	public function validRequest( $request ) {
		return is_user_logged_in() && $this->validateAccess( $request );
	}

	/**
	 * Validates access for the routes.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_REST_Request $request The REST Request.
	 * @return bool                      Whether the user is allowed access to the route.
	 */
	private function validateAccess( $request ) {
		$routeData = $this->getRouteData( $request );
		if ( empty( $routeData ) || empty( $routeData['access'] ) ) {
			return false;
		}

		// Admins users always have access.
		if ( aioseoTableOfContents()->access->isAdmin() ) {
			return true;
		}

		switch ( $routeData['access'] ) {
			case 'any':
				// Users with any Table of Contents Plus permission can access the route.
				$user = wp_get_current_user();
				foreach ( $user->get_role_caps() as $capability => $enabled ) {
					if ( $enabled && preg_match( '/^aioseo_table_of_contents_/', (string) $capability ) ) {
						return true;
					}
				}

				return false;
			default:
				// The user has access if he has any of the required capabilities.
				if ( ! is_array( $routeData['access'] ) ) {
					$routeData['access'] = [ $routeData['access'] ];
				}

				foreach ( $routeData['access'] as $access ) {
					if ( current_user_can( $access ) ) {
						return true;
					}
				}

				return false;
		}
	}

	/**
	 * Returns the data for the route that is being accessed.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_REST_Request $request The REST Request.
	 * @return array                     The route data.
	 */
	private function getRouteData( $request ) {
		// NOTE: Since WordPress uses case-insensitive patterns to match routes,
		// we are forcing everything to lower case to ensure we have the proper route.
		// This prevents users with lower privileges from accessing routes they shouldn't.
		$route     = aioseoTableOfContents()->helpers->toLowercase( $request->get_route() );
		$route     = untrailingslashit( str_replace( '/' . $this->namespace . '/', '', $route ) );
		$routeData = isset( $this->routes[ $request->get_method() ][ $route ] ) ? $this->routes[ $request->get_method() ][ $route ] : [];

		// No direct route name, let's try the regexes.
		if ( empty( $routeData ) ) {
			foreach ( $this->routes[ $request->get_method() ] as $routeRegex => $routeInfo ) {
				$routeRegex = str_replace( '@', '\@', $routeRegex );
				if ( preg_match( "@{$routeRegex}@", (string) $route ) ) {
					$routeData = $routeInfo;
					break;
				}
			}
		}

		return $routeData;
	}
}