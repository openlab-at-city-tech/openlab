<?php
/**
 * Plugin Name: TrustedLogin Client Test
 * phpcs:disable
 */

/**
 * Autoloader for the TrustedLogin Client
 *
 * @param string $class The fully-qualified class name.
 * @see https://www.php-fig.org/psr/psr-4/examples/
 * @return void
 */
spl_autoload_register(
	function ( $class ) {
		$prefix   = 'TEC\\Common\\TrustedLogin\\';
		$base_dir = __DIR__ . '/src/';
		$len      = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}
		$relative_class = substr( $class, $len );
		$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
/**
 * Configuration for TrustedLogin Client
 *
 * @see https://docs.trustedlogin.com/Client/configuration
 */
$public_key = '90bd9d918670ea15';
$config     = array(
	'auth'    => array(
		'api_key' => $public_key,
	),
	'vendor'  => array(
		'namespace'   => 'pro-block-builder',
		'title'       => 'Pro Block Builder',
		'email'       => 'support@example.com',
		'website'     => 'https://example.com',
		'support_url' => 'https://help.example.com',
	),
	'role'    => 'editor',
	'caps'    => array(
		'add' => array(
			'gf_full_access' => 'Support will need to see and edit the forms, entries, and Gravity Forms settings on your site.',
		),
	),
	'webhook' => array(
		'url'           => 'https://example.com/webhook',
		'create_ticket' => true,
		'debug_data'    => true,
	),
);
$config     = new \TEC\Common\TrustedLogin\Config( $config );
try {
	new \TEC\Common\TrustedLogin\Client(
		$config
	);
} catch ( \Exception $exception ) {
	error_log( $exception->getMessage() );

	add_action(
		'admin_notices',
		function () use ( $exception ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			?>
		<div class="notice notice-error">
			<p><?php echo $exception->getMessage(); ?></p>
		</div>
			<?php
		}
	);
}
