<?php
/**
 * Admin page header
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

$page = ( ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'settings' );

?>

<h1>jQuery Migrate</h1>

<div class="notice notice-info">
    <p>
        <?php _e( 'jQuery is a framework that makes creating interactive elements on your website quick and easy for theme and plugin authors.', 'enable-jquery-migrate-helper' ); ?>
    </p>

    <p>
        <?php _e( 'Thanks to this versatility, WordPress has included a version of the jQuery library for a long time.', 'enable-jquery-migrate-helper' ); ?>
    </p>

    <p>
        <?php _e( 'Due to the large amount of plugins and themes using the library, the version has not received any major upgrades to avoid breaking changes. This has unfortunately led to many plugins and themes just using obsolete code (even when WordPress has allowed for more modern code to be used).', 'enable-jquery-migrate-helper' ); ?>
    </p>

    <p>
        <?php _e( 'When WordPress facilitated for, and is performing, upgrades of the version of jQuery included, some themes and plugins may stop working as expected, because their code was outdated.', 'enable-jquery-migrate-helper' ); ?>
    </p>
</div>

<nav class="nav-tab-wrapper" aria-label="Secondary menu">
    <a class="nav-tab <?php echo ( 'settings' === $page ? 'nav-tab-active' : '' ); ?>" href="<?php echo esc_url( admin_url( 'tools.php?page=jqmh' ) ); ?>"><?php _e( 'Settings', 'enable-jquery-migrate-helper' ); ?></a>
    <a class="nav-tab <?php echo ( 'logs' === $page ? 'nav-tab-active' : '' ); ?>" href="<?php echo esc_url( admin_url( 'tools.php?page=jqmh&tab=logs' ) ); ?>"><?php _e( 'Logged deprecations', 'enable-jquery-migrate-helper' ); ?></a>
</nav>

<?php
switch ( $page ) {
	case 'logs':
        include_once __DIR__ . '/logs.php';
        break;
	case 'settings':
	default:
        include_once __DIR__ . '/settings.php';
}