<?php
/**
 * Footer for the Soliloquy Lite plugin.
 *
 * @package Soliloquy
 * @since 2.5.0
 */

?>
<div class="clear"></div>
<div class="soliloquy-footer-promotion">

	<p><?php esc_html_e( 'Made with ♥ by the Soliloquy Team', 'soliloquy' ); ?></p>
	<ul class="soliloquy-footer-promotion-links">
		<li>
		<a href="<?php echo esc_url( Soliloquy_Common_Admin_Lite::get_instance()->get_upgrade_link( 'https://soliloquywp.com/support', 'wpfooter', 'support' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Support', 'soliloquy' ); ?></a><span>/</span>
		</li>
		<li>
		<a href="<?php echo esc_url( Soliloquy_Common_Admin_Lite::get_instance()->get_upgrade_link( 'https://soliloquywp.com/docs', 'wpfooter', 'docs' ) ); ?>" target="_blank" rel="noopener noreferrer">Docs</a><span>/</span>
		</li>
		<li>
		<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=soliloquy&page=soliloquy-lite-about-us' ) ); ?>"><?php esc_html_e( 'Free Plugins', 'soliloquy' ); ?></a>
		</li>
	</ul>
	<ul class="soliloquy-footer-promotion-social">
		<li>
		<a href="https://www.facebook.com/soliloquywp" target="_blank" rel="noopener noreferrer">
			<svg width="16" height="16" aria-hidden="true">
				<path fill="#A7AAAD" d="M16 8.05A8.02 8.02 0 0 0 8 0C3.58 0 0 3.6 0 8.05A8 8 0 0 0 6.74 16v-5.61H4.71V8.05h2.03V6.3c0-2.02 1.2-3.15 3-3.15.9 0 1.8.16 1.8.16v1.98h-1c-1 0-1.31.62-1.31 1.27v1.49h2.22l-.35 2.34H9.23V16A8.02 8.02 0 0 0 16 8.05Z"></path>
			</svg>
			<span class="screen-reader-text"><?php esc_html_e( 'Facebook', 'soliloquy' ); ?></span>
		</a>
		</li>
		<li>
		<a href="https://twitter.com/soliloquywp" target="_blank" rel="noopener noreferrer">
			<svg width="17" height="16" aria-hidden="true">
				<path fill="#A7AAAD" d="M15.27 4.43A7.4 7.4 0 0 0 17 2.63c-.6.27-1.3.47-2 .53a3.41 3.41 0 0 0 1.53-1.93c-.66.4-1.43.7-2.2.87a3.5 3.5 0 0 0-5.96 3.2 10.14 10.14 0 0 1-7.2-3.67C.86 2.13.7 2.73.7 3.4c0 1.2.6 2.26 1.56 2.89a3.68 3.68 0 0 1-1.6-.43v.03c0 1.7 1.2 3.1 2.8 3.43-.27.06-.6.13-.9.13a3.7 3.7 0 0 1-.66-.07 3.48 3.48 0 0 0 3.26 2.43A7.05 7.05 0 0 1 0 13.24a9.73 9.73 0 0 0 5.36 1.57c6.42 0 9.91-5.3 9.91-9.92v-.46Z"></path>
			</svg>
			<span class="screen-reader-text"><?php esc_html_e( 'Twitter', 'soliloquy' ); ?></span>
		</a>
		</li>
		<li>
		<a href="https://www.youtube.com/@SoliloquySlider" target="_blank" rel="noopener noreferrer">
			<svg width="17" height="16" aria-hidden="true">
				<path fill="#A7AAAD" d="M16.63 3.9a2.12 2.12 0 0 0-1.5-1.52C13.8 2 8.53 2 8.53 2s-5.32 0-6.66.38c-.71.18-1.3.78-1.49 1.53C0 5.2 0 8.03 0 8.03s0 2.78.37 4.13c.19.75.78 1.3 1.5 1.5C3.2 14 8.51 14 8.51 14s5.28 0 6.62-.34c.71-.2 1.3-.75 1.49-1.5.37-1.35.37-4.13.37-4.13s0-2.81-.37-4.12Zm-9.85 6.66V5.5l4.4 2.53-4.4 2.53Z"></path>
			</svg>
			<span class="screen-reader-text"><?php esc_html_e( 'YouTube', 'soliloquy' ); ?></span>
		</a>
		</li>
	</ul>
</div>
