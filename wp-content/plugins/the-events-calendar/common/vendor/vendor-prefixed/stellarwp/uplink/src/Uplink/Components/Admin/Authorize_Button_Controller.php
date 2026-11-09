<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Components\Admin;

use InvalidArgumentException;
use TEC\Common\StellarWP\Uplink\Auth\Admin\Disconnect_Controller;
use TEC\Common\StellarWP\Uplink\Auth\Auth_Url_Builder;
use TEC\Common\StellarWP\Uplink\Auth\Authorizer;
use TEC\Common\StellarWP\Uplink\Auth\Token\Contracts\Token_Manager;
use TEC\Common\StellarWP\Uplink\Components\Controller;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Resources\Collection;
use TEC\Common\StellarWP\Uplink\View\Contracts\View;

final class Authorize_Button_Controller extends Controller {

	/**
	 * The view file, without ext, relative to the root views directory.
	 */
	public const VIEW = 'admin/authorize-button';

	/**
	 * @var Authorizer
	 */
	private $authorizer;

	/**
	 * @var Token_Manager
	 */
	private $token_manager;

	/**
	 * @var Auth_Url_Builder
	 */
	private $url_builder;

	/**
	 * @var Collection
	 */
	private $resources;

	/**
	 * @var Disconnect_Controller
	 */
	private $disconnect_controller;

	/**
	 * @param  View  $view  The View Engine to render views.
	 * @param  Authorizer  $authorizer  Determines if the current user can perform actions.
	 * @param  Token_Manager  $token_manager  The Token Manager.
	 * @param  Auth_Url_Builder  $url_builder  The Auth URL Builder.
	 */
	public function __construct(
		View $view,
		Authorizer $authorizer,
		Token_Manager $token_manager,
		Auth_Url_Builder $url_builder,
		Collection $resources,
		Disconnect_Controller $disconnect_controller
	) {
		parent::__construct( $view );

		$this->authorizer            = $authorizer;
		$this->token_manager         = $token_manager;
		$this->url_builder           = $url_builder;
		$this->resources             = $resources;
		$this->disconnect_controller = $disconnect_controller;
	}

	/**
	 * Renders the authorize-button view.
	 *
	 * @param  array{slug?: string, domain?: string, license?: string} $args The Product slug and license domain.
	 *
	 * @see src/views/admin/authorize-button.php
	 *
	 * @throws InvalidArgumentException
	 */
	public function render( array $args = [] ): void {
		global $pagenow;

		$slug    = $args['slug'] ?? '';
		$domain  = $args['domain'] ?? '';
		$license = $args['license'] ?? '';

		if ( empty ( $slug ) ) {
			throw new InvalidArgumentException( __( 'The Product slug cannot be empty', 'tribe-common' ) );
		}

		$url = $this->url_builder->set_license( $license )->build( $slug, $domain );

		if ( ! $url ) {
			return;
		}

		$plugin = $this->resources->offsetGet( $slug );

		if ( ! $plugin ) {
			return;
		}

		$authenticated = false;
		$target        = '_blank';
		$link_text     = __( 'Connect', 'tribe-common' );
		$classes       = [
			'button',
			'uplink-authorize',
			'not-authorized',
		];

		if ( ! $this->authorizer->can_auth() ) {
			$target    = '_self';
			$link_text = __( 'Contact your network administrator to connect', 'tribe-common' );
			$url       = get_admin_url( get_current_blog_id(), 'network/' );
		} elseif ( $this->token_manager->get( $plugin ) ) {
			$authenticated = true;
			$target        = '_self';
			$link_text     = __( 'Disconnect', 'tribe-common' );
			$url           = $this->disconnect_controller->get_url( $plugin );
			$classes[2]    = 'authorized';
		}

		$hook_prefix = Config::get_hook_prefix();

		/**
		 * Filter the link text.
		 *
		 * @param  string  $link_text  The current link text.
		 * @param  bool  $authenticated  Whether they are authenticated.
		 * @param  string|null  $pagenow  The value of WordPress's pagenow.
		 */
		$link_text = apply_filters(
			"stellarwp/uplink/$hook_prefix/view/authorize_button/link_text",
			$link_text,
			$authenticated,
			$pagenow
		);

		/**
		 * Filter the link text for the slug.
		 *
		 * @param  string  $link_text  The current link text.
		 * @param  bool  $authenticated  Whether they are authenticated.
		 * @param  string|null  $pagenow  The value of WordPress's pagenow.
		 */
		$link_text = apply_filters(
			"stellarwp/uplink/$hook_prefix/$slug/view/authorize_button/link_text",
			$link_text,
			$authenticated,
			$pagenow
		);

		/**
		 * Filter the hyperlink url.
		 *
		 * @param  string  $url  The current hyperlink url.
		 * @param  bool  $authenticated  Whether they are authenticated.
		 * @param  string|null  $pagenow  The value of WordPress's pagenow.
		 */
		$url = apply_filters(
			"stellarwp/uplink/$hook_prefix/view/authorize_button/url",
			$url,
			$authenticated,
			$pagenow
		);

		/**
		 * Filter the link target.
		 *
		 * @param  string  $target  The current link target.
		 * @param  bool  $authenticated  Whether they are authenticated.
		 * @param  string|null  $pagenow  The value of WordPress's pagenow.
		 */
		$target = apply_filters(
			"stellarwp/uplink/$hook_prefix/view/authorize_button/target",
			$target,
			$authenticated,
			$pagenow
		);

		/**
		 * Filter the HTML wrapper tag.
		 *
		 * @param  string  $tag  The HTML tag to use for the wrapper.
		 * @param  bool  $authenticated  Whether they are authenticated.
		 * @param  string|null  $pagenow  The value of WordPress's pagenow.
		 */
		$tag = apply_filters(
			"stellarwp/uplink/$hook_prefix/view/authorize_button/tag",
			'div',
			$authenticated,
			$pagenow
		);

		/**
		 * Filter the CSS classes
		 *
		 * @param  array  $classes  An array of CSS classes.
		 * @param  bool  $authenticated  Whether they are authenticated.
		 * @param  string|null  $pagenow  The value of WordPress's pagenow.
		 */
		$classes = (array) apply_filters(
			"stellarwp/uplink/$hook_prefix/view/authorize_button/classes",
			$classes,
			$authenticated,
			$pagenow
		);

		echo $this->view->render( self::VIEW, [
			'link_text' => $link_text,
			'url'       => $url,
			'target'    => $target,
			'tag'       => $tag,
			'classes'   => $this->classes( $classes ),
			'slug'      => $slug,
		] );
	}

}
