<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Uplink\Admin\Fields\Field;
use TEC\Common\StellarWP\Uplink\Admin\Fields\Form;
use TEC\Common\StellarWP\Uplink\API\V3\Auth\Contracts\Auth_Url;
use TEC\Common\StellarWP\Uplink\API\V3\Auth\Contracts\Token_Authorizer;
use TEC\Common\StellarWP\Uplink\Auth\Admin\Disconnect_Controller;
use TEC\Common\StellarWP\Uplink\Auth\Auth_Url_Builder;
use TEC\Common\StellarWP\Uplink\Auth\Authorizer;
use TEC\Common\StellarWP\Uplink\Components\Admin\Authorize_Button_Controller;
use TEC\Common\StellarWP\Uplink\Resources\Collection;
use TEC\Common\StellarWP\Uplink\Resources\Plugin;
use TEC\Common\StellarWP\Uplink\Resources\Service;
use TEC\Common\StellarWP\Uplink\Resources\Resource;
use TEC\Common\StellarWP\Uplink\Site\Data;
use Throwable;
use RuntimeException;

/**
 * Get the uplink container.
 *
 * @throws \RuntimeException
 *
 * @return ContainerInterface
 */
function get_container(): ContainerInterface {
	return Config::get_container();
}

/**
 * Displays the token authorization button, which allows admins to
 * authorize their product through your origin server and clear the
 * token locally by disconnecting.
 *
 * @param string $slug The Product slug to render the button for.
 * @param string $domain An optional domain associated with a license key to pass along.
 * @param string $license The license that should be authenticated before token generation.
 */
function render_authorize_button( string $slug, string $domain = '', string $license = '' ): void {
	try {
		get_container()->get( Authorize_Button_Controller::class )
			->render( [
				'slug'    => $slug,
				'domain'  => $domain,
				'license' => $license,
			] );
	} catch ( Throwable $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "Unable to render authorize button: {$e->getMessage()} {$e->getFile()}:{$e->getLine()} {$e->getTraceAsString()}" );
		}
	}
}

/**
 * Get the stored authorization token, automatically detects multisite.
 *
 * @param  string  $slug  The plugin/service slug to use to determine if we use network/single site token storage.
 *
 * @throws \RuntimeException
 *
 * @return string|null
 */
function get_authorization_token( string $slug ): ?string {
	$resource = get_resource( $slug );

	return $resource ? $resource->get_token() : null;
}

/**
 * Check if a license is authorized.
 *
 * @note This response may be cached.
 *
 * @param  string  $license  The license key.
 * @param  string  $slug     The plugin/service slug.
 * @param  string  $token    The stored token.
 * @param  string  $domain   The user's license domain.
 *
 * @return bool
 */
function is_authorized( string $license, string $slug, string $token, string $domain ): bool {
	try {
		return get_container()
			->get( Token_Authorizer::class )
			->is_authorized( $license, $slug, $token, $domain );
	} catch ( Throwable $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "An Authorization error occurred: {$e->getMessage()} {$e->getFile()}:{$e->getLine()} {$e->getTraceAsString()}" );
		}

		return false;
	}
}

/**
 * If the current user is allowed to perform token authorization.
 *
 * Without being filtered, this just runs a is_super_admin() check.
 *
 * @throws \RuntimeException
 *
 * @return bool
 */
function is_user_authorized(): bool {
	return get_container()->get( Authorizer::class )->can_auth();
}

/**
 * Build a brand's authorization URL, with the uplink_callback base64 query variable.
 *
 * @param  string  $slug  The Product slug to render the button for.
 * @param  string  $domain  An optional domain associated with a license key to pass along.
 * @param  string  $license  An optional license key to pass along.
 *
 * @return string
 */
function build_auth_url( string $slug, string $domain = '', string $license = ''): string {
	try {
		return Config::get_container()->get( Auth_Url_Builder::class )
			->set_license( $license )
			->build( $slug, $domain, $license );
	} catch ( Throwable $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "Error building auth URL: {$e->getMessage()} {$e->getFile()}:{$e->getLine()} {$e->getTraceAsString()}" );
		}

		return '';
	}
}

/**
 * Get a resource (plugin/service) from the collection.
 *
 * @param  string  $slug  The resource slug to find.
 *
 * @throws \RuntimeException
 *
 * @return Resource|Plugin|Service|null
 */
function get_resource( string $slug ) {
	return get_container()->get( Collection::class )->offsetGet( $slug );
}

/**
 * Get a resource's license key.
 *
 * @param  string  $slug  The plugin/service slug.
 * @param  string  $type  The type of key to get (any, network, local, default).
 *
 * @throws \RuntimeException
 *
 * @return string
 */
function get_license_key( string $slug, string $type = 'any' ): string {
	$resource = get_resource( $slug );

	if ( ! $resource ) {
		return '';
	}

	return $resource->get_license_key( $type );
}

/**
 * Set a resource's license key.
 *
 * @param  string  $slug The plugin/service slug.
 * @param  string  $license The license key to store.
 * @param  string  $type  The type of key to set (any, network, local, default).
 *
 * @throws \RuntimeException
 *
 * @return bool
 */
function set_license_key( string $slug, string $license, string $type = 'local' ): bool {
	$resource = get_resource( $slug );

	if ( ! $resource ) {
		return false;
	}

	$result = $resource->set_license_key( $license, $type );

	// Force update the key status.
	$resource->validate_license( $license, $type === 'network' );

	return $result;
}

/**
 * Get the disconnect token URL.
 *
 * @param  string  $slug The plugin/service slug.
 *
 * @throws \RuntimeException
 *
 * @return string
 */
function get_disconnect_url( string $slug ): string {
	$resource = get_resource( $slug );

	if ( ! $resource ) {
		return '';
	}

	return get_container()->get( Disconnect_Controller::class )->get_url( $resource );
}

/**
 * Retrieve an Origin's auth url, if it exists.
 *
 * @param  string  $slug The product/service slug.
 *
 * @throws \RuntimeException
 *
 * @return string
 */
function get_auth_url( string $slug ): string {
	return get_container()->get( Auth_Url::class )->get( $slug );
}

/**
 * Get the current site's license domain, multisite friendly.
 *
 * @throws \RuntimeException
 *
 * @return string
 */
function get_license_domain(): string {
	return get_container()->get( Data::class )->get_domain();
}

/**
 * Get the field object for a resource's slug.
 *
 * @param  string  $slug  The resource's slug to get the field for.
 *
 * @throws RuntimeException
 *
 * @return Field
 */
function get_field( string $slug ): Field {
	$resource = get_container()->get( Collection::class )->offsetGet( $slug );

	if ( ! $resource ) {
		throw new RuntimeException( "Resource not found for slug: {$slug}" );
	}

	return get_container()->get( Field::class )->set_resource( $resource );
}

/**
 * Get the form object for all plugins.
 *
 * @throws RuntimeException
 *
 * @return Form
 */
function get_form(): Form {
	return get_container()->get( Form::class );
}

/**
 * Get all plugins.
 *
 * @throws RuntimeException
 *
 * @return Collection
 */
function get_plugins(): Collection {
	return get_container()->get( Collection::class )->get_plugins();
}
