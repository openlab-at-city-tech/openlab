<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="recommend-plugins">
    <h2><?php esc_html_e( 'Recommend Plugins', 'kenta' ); ?></h2>

	<?php foreach ( kenta_recommend_plugins() as $slug => $plugin ): ?>
        <div class="recommend-plugin">
            <img src="<?php echo esc_url( $plugin['icon'] ) ?>">
            <div class="plugin-content">
                <h4 class="plugin-title">
                    <a href="<?php echo esc_url( $plugin['home'] ) ?>"
                       target="_blank">
						<?php echo esc_html( $plugin['title'] ); ?>
                    </a>
                </h4>
                <p>
					<?php echo esc_html( $plugin['desc'] ) ?>
                </p>

                <div class="plugin-actions">
                    <a class="kenta-button kenta-button-solid" href="<?php echo esc_url(
						add_query_arg( array(
							'action'   => 'kenta_install_recommend_plugin',
							'slug'     => $slug,
							'_wpnonce' => wp_create_nonce( 'kenta_install_recommend_plugin' )
						), admin_url( 'admin.php' ) )
					) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>

                        <span><?php esc_html_e( 'Install Plugin', 'kenta' ); ?></span>
                    </a>
                    <a class="kenta-button kenta-button-outline"
                       target="_blank"
                       href="<?php echo esc_url( 'https://wordpress.org/plugins/' . $slug ) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                            <path d="M96 0C78.3 0 64 14.3 64 32l0 96 64 0 0-96c0-17.7-14.3-32-32-32zM288 0c-17.7 0-32 14.3-32 32l0 96 64 0 0-96c0-17.7-14.3-32-32-32zM32 160c-17.7 0-32 14.3-32 32s14.3 32 32 32l0 32c0 77.4 55 142 128 156.8l0 67.2c0 17.7 14.3 32 32 32s32-14.3 32-32l0-67.2C297 398 352 333.4 352 256l0-32c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 160z"/>
                        </svg>

                        <span><?php esc_html_e( 'Plugin Page', 'kenta' ); ?></span>
                    </a>
                </div>
            </div>
        </div>
	<?php endforeach; ?>
</div>
