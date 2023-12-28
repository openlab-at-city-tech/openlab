<?php
/**
 * Primary menu template.
 *
 * Accessibility markup applied (ARIA).
 * @link  http://a11yproject.com/patterns/
 *
 * SVG icon from Genericons Neue.
 * @link  https://github.com/Automattic/genericons-neue/blob/master/svg/user.svg
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.2
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$is_mobile_nav_enabled = Customize\Mod::get( 'navigation_mobile' );

?>

<nav id="site-navigation" class="main-navigation" aria-label="<?php echo esc_attr_x( 'Main menu', 'Navigational menu.', 'michelle' ); ?>">

	<?php

	if ( $is_mobile_nav_enabled ) :
		?>
		<button
			id="menu-toggle"
			class="menu-toggle"
			aria-controls="menu-primary"
			aria-expanded="false"
		>
			<svg class="svg-icon menu-open" width="1em" aria-hidden="true" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d=" M0,2v2h16V2H0z M0,9h12V7H0V9z M0,14h14v-2H0V14z"/></svg>
			<svg class="svg-icon menu-close" width="1em" aria-hidden="true" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><polygon points="14.7,2.7 13.3,1.3 8,6.6 2.7,1.3 1.3,2.7 6.6,8 1.3,13.3 2.7,14.7 8,9.4 13.3,14.7 14.7,13.3 9.4,8"/></svg>
			<span class="screen-reader-text"><?php echo esc_html_x( 'Menu', 'Mobile navigation toggle button title.', 'michelle' ); ?></span>
		</button>
		<?php
	endif;

	?>
	<div id="site-navigation-container" class="main-navigation-container">
		<?php

		/**
		 * For `theme_location` see `Menu\Component::get_menu_args_primary()` method
		 * in `includes/Menu/Component.php` file.
		 */
		wp_nav_menu( Menu\Component::get_menu_args_primary( $is_mobile_nav_enabled ) );

		?>
	</div>

</nav>
