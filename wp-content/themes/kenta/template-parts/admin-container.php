<?php
/**
 * Admin page
 *
 * @package Kenta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use LottaFramework\Utils;

$tabs = apply_filters( 'kenta_admin_page_tabs', [
	'customize'     => [
		'label' => __( 'Customize', 'kenta' ),
	],
	'starter-sites' => [
		'label' => __( 'Starter Sites', 'kenta' ),
	],
] );

$active_tab = $_GET['kenta-tab'] ?? 'customize';
$active_tab = in_array( $active_tab, array_keys( $tabs ) ) ? $active_tab : 'customize';
?>

<div class="wrap kenta-admin-page">
    <div class="page-header">
        <div class="container page-header-content">
            <img class="kenta-logo" src="<?php echo esc_url( kenta_image_url( 'kenta-logo-light.png' ) ) ?>"
                 alt="Kenta Logo">
            <div>
                <h1><?php esc_html_e( 'Kenta Theme', 'kenta' ); ?></h1>
                <p>
					<?php esc_html_e( 'Fast, Customizable & SEO Optimized Free WordPress Theme.', 'kenta' ); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="container page-body">
        <div class="page-tabs-container">
            <ul class="page-tabs">
				<?php foreach ( $tabs as $id => $tab ): ?>
					<?php
					if ( isset( $tab['skip'] ) && $tab['skip'] ) {
						continue;
					}
					?>

                    <li>
                        <a class="<?php Utils::the_clsx( [
							'active' => $active_tab === $id
						] ); ?>"
                           href="<?php echo esc_url( isset( $tab['url'] ) ? $tab['url'] : kenta_theme_admin_url( [ 'kenta-tab' => $id ] ) ) ?>"
                        >
							<?php echo esc_html( $tab['label'] ) ?>
                        </a>
                    </li>
				<?php endforeach; ?>
            </ul>
        </div>

        <div class="page-main">
            <div class="page-content">
				<?php get_template_part( 'template-parts/admin', $active_tab ); ?>
				<?php get_template_part( 'template-parts/admin', 'recommend-plugins' ); ?>
            </div>
            <div class="page-sidebar">
				<?php get_template_part( 'template-parts/admin', 'sidebar' ); ?>
            </div>
        </div>
    </div>

    <div class="page-footer"></div>
</div>
