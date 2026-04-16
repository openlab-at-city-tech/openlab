<?php
/**
 * Unified drawer container.
 *
 * This container holds all flyout menus and drawers (navbar menus + mobile sidebars).
 * Only one drawer can be open at a time.
 *
 * This partial should be included once per page, typically in the footer or after the navbar.
 */
?>
<div class="openlab-drawer-container" inert>
	<!-- ARIA live region for announcing drawer state changes -->
	<div class="sr-only" aria-live="polite" aria-atomic="true" id="drawer-announcer"></div>

	<?php
	// Render navbar drawers (My OpenLab, Favorites, Login, Main Menu).
	if ( is_user_logged_in() ) :
		get_template_part( 'parts/navbar/favorites-flyout' );
		get_template_part( 'parts/navbar/my-openlab-flyout' );
	else :
		get_template_part( 'parts/navbar/login-flyout' );
	endif;

	get_template_part( 'parts/navbar/main-menu-flyout' );

	// Render any dynamically registered drawers (mobile sidebars, etc.).
	echo openlab_render_registered_drawers();
	?>
</div>
