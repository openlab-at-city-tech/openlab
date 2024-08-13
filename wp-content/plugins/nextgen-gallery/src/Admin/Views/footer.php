<?php
/**
 * Admin Footer Template.
 *
 * @since 3.5.0
 *
 * @package NextGEN Gallery
 */

?>
<div class="clear"></div>
<div class="nextgen-footer-promotion">

<p><?php esc_html_e( 'Made with ♥ by the Imagely Team', 'nggallery' ); ?></p>
	<ul class="nextgen-footer-promotion-links">
		<li>
		<a href="<?php echo esc_url( M_Marketing::get_utm_link( 'https://www.imagely.com/support', 'wpfooter', 'support' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Support', 'nggallery' ); ?></a><span>/</span>
		</li>
		<li>
		<a href="<?php echo esc_url( M_Marketing::get_utm_link( 'https://www.imagely.com/docs', 'wpfooter', 'docs' ) ); ?>" target="_blank" rel="noopener noreferrer">Docs</a><span>/</span>
		</li>
		<li>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=nextgen-gallery-about-us' ) ); ?>"><?php esc_html_e( 'Free Plugins', 'nggallery' ); ?></a>
		</li>
	</ul>
	<ul class="nextgen-footer-promotion-social">
		<li>
		<a href="https://www.facebook.com/imagely" target="_blank" rel="noopener noreferrer">
			<svg width="16" height="16" aria-hidden="true">
				<path fill="#A7AAAD" d="M16 8.05A8.02 8.02 0 0 0 8 0C3.58 0 0 3.6 0 8.05A8 8 0 0 0 6.74 16v-5.61H4.71V8.05h2.03V6.3c0-2.02 1.2-3.15 3-3.15.9 0 1.8.16 1.8.16v1.98h-1c-1 0-1.31.62-1.31 1.27v1.49h2.22l-.35 2.34H9.23V16A8.02 8.02 0 0 0 16 8.05Z"></path>
			</svg>
			<span class="screen-reader-text"><?php esc_html_e( 'Facebook', 'nggallery' ); ?></span>
		</a>
		</li>
		<li>
		<a href="https://www.instagram.com/imagely/" target="_blank" rel="noopener noreferrer">
			<svg width="16" height="16" aria-hidden="true">
				<path fill="#A7AAAD" d="M8.016 4.39c-2 0-3.594 1.626-3.594 3.594 0 2 1.594 3.594 3.594 3.594a3.594 3.594 0 0 0 3.593-3.594c0-1.968-1.625-3.593-3.593-3.593Zm0 5.938a2.34 2.34 0 0 1-2.344-2.344c0-1.28 1.031-2.312 2.344-2.312a2.307 2.307 0 0 1 2.312 2.312c0 1.313-1.031 2.344-2.312 2.344Zm4.562-6.062a.84.84 0 0 0-.844-.844.84.84 0 0 0-.843.844.84.84 0 0 0 .843.843.84.84 0 0 0 .844-.843Zm2.375.843c-.062-1.125-.312-2.125-1.125-2.937-.812-.813-1.812-1.063-2.937-1.125-1.157-.063-4.625-.063-5.782 0-1.125.062-2.093.312-2.937 1.125-.813.812-1.063 1.812-1.125 2.937-.063 1.157-.063 4.625 0 5.782.062 1.125.312 2.093 1.125 2.937.844.813 1.812 1.063 2.937 1.125 1.157.063 4.625.063 5.782 0 1.125-.062 2.125-.312 2.937-1.125.813-.844 1.063-1.812 1.125-2.937.063-1.157.063-4.625 0-5.782Zm-1.5 7c-.219.625-.719 1.094-1.312 1.344-.938.375-3.125.281-4.125.281-1.032 0-3.22.094-4.125-.28a2.37 2.37 0 0 1-1.344-1.345c-.375-.906-.281-3.093-.281-4.125 0-1-.094-3.187.28-4.125a2.41 2.41 0 0 1 1.345-1.312c.906-.375 3.093-.281 4.125-.281 1 0 3.187-.094 4.125.28.593.22 1.062.72 1.312 1.313.375.938.281 3.125.281 4.125 0 1.032.094 3.22-.28 4.125Z"></path>
			</svg>
			<span class="screen-reader-text"><?php esc_html_e( 'Instagram', 'nggallery' ); ?></span>
		</a>
		</li>
		<li>
		<a href="https://twitter.com/imagely" target="_blank" rel="noopener noreferrer">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" aria-hidden="true">
				<path fill="#A7AAAD" d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
			</svg>
			<span class="screen-reader-text"><?php esc_html_e( 'Twitter', 'nggallery' ); ?></span>
		</a>
		</li>
		<li>
		<a href="https://www.youtube.com/c/Imagely" target="_blank" rel="noopener noreferrer">
			<svg width="17" height="16" aria-hidden="true">
				<path fill="#A7AAAD" d="M16.63 3.9a2.12 2.12 0 0 0-1.5-1.52C13.8 2 8.53 2 8.53 2s-5.32 0-6.66.38c-.71.18-1.3.78-1.49 1.53C0 5.2 0 8.03 0 8.03s0 2.78.37 4.13c.19.75.78 1.3 1.5 1.5C3.2 14 8.51 14 8.51 14s5.28 0 6.62-.34c.71-.2 1.3-.75 1.49-1.5.37-1.35.37-4.13.37-4.13s0-2.81-.37-4.12Zm-9.85 6.66V5.5l4.4 2.53-4.4 2.53Z"></path>
			</svg>
			<span class="screen-reader-text"><?php esc_html_e( 'YouTube', 'nggallery' ); ?></span>
		</a>
		</li>
	</ul>
</div>
