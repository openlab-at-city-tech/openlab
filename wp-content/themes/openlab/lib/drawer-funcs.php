<?php
/**
 * Drawer system functions.
 *
 * Provides a unified drawer component that can be triggered from anywhere on the page.
 * Used for mobile navigation, sidebars, and flyout menus.
 */

/**
 * Render a drawer toggle button.
 *
 * @param string $drawer_id The ID of the drawer this button controls.
 * @param array  $options   {
 *     Optional. Button configuration.
 *
 *     @type string $text           Screen reader text for the button. Default 'Toggle menu'.
 *     @type string $icon           Icon markup to display. Default is hamburger toggle icon.
 *     @type string $class          Additional CSS classes. Default empty.
 *     @type string $id             Button ID attribute. Default auto-generated from drawer_id.
 *     @type bool   $show_close     Whether to show a close icon variant. Default true.
 * }
 * @return string Button HTML.
 */
function openlab_render_drawer_toggle( $drawer_id, $options = [] ) {
	$defaults = [
		'text'       => 'Toggle menu',
		'icon'       => '<span class="toggle-icon"></span>',
		'class'      => '',
		'id'         => 'toggle-' . sanitize_html_class( $drawer_id ),
		'show_close' => true,
	];

	$options = wp_parse_args( $options, $defaults );

	$classes = array_filter( [
		'drawer-toggle',
		'mobile-toggle',
		'visible-xs',
		$options['class'],
	] );

	$class_attr = implode( ' ', array_map( 'sanitize_html_class', $classes ) );

	ob_start();
	?>
	<button
		id="<?php echo esc_attr( $options['id'] ); ?>"
		class="<?php echo esc_attr( $class_attr ); ?>"
		type="button"
		aria-expanded="false"
		aria-controls="<?php echo esc_attr( $drawer_id ); ?>"
		data-drawer-toggle="<?php echo esc_attr( $drawer_id ); ?>"
	>
		<span class="icon-default"><?php echo $options['icon']; ?></span>
		<?php if ( $options['show_close'] ) : ?>
			<span class="icon-close"><?php get_template_part( 'parts/navbar/close-icon' ); ?></span>
		<?php endif; ?>
		<span class="sr-only"><?php echo esc_html( $options['text'] ); ?></span>
	</button>
	<?php
	return ob_get_clean();
}

/**
 * Render a drawer panel.
 *
 * A panel is a single "screen" within a drawer. Drawers can contain multiple panels
 * for drill-down navigation (e.g., main menu → submenu).
 *
 * @param array $config {
 *     Panel configuration.
 *
 *     @type string $id           Required. Panel ID.
 *     @type string $heading      Panel heading text.
 *     @type string $heading_url  Optional URL for heading link.
 *     @type string $heading_icon Optional icon markup for heading.
 *     @type array  $items        Array of menu items. Each item can have:
 *                                - text: Display text
 *                                - href: Link URL (for links)
 *                                - target: Target panel ID (for submenu triggers)
 *                                - class: Additional CSS class
 *                                - is_heading: If true, renders as non-interactive heading
 *     @type bool   $is_root      Whether this is the root panel. Default false.
 *     @type string $back_target  Panel ID to navigate back to. Required for non-root panels.
 *     @type string $back_text    Text for back button. Default 'Back'.
 *     @type string $content      Custom HTML content instead of items list.
 * }
 * @return string Panel HTML.
 */
function openlab_render_drawer_panel( $config ) {
	$defaults = [
		'id'           => '',
		'heading'      => '',
		'heading_url'  => '',
		'heading_icon' => '',
		'items'        => [],
		'is_root'      => false,
		'back_target'  => '',
		'back_text'    => 'Back',
		'content'      => '',
		'show_close'   => false, // Whether to show a visible close button in header.
	];

	$config = wp_parse_args( $config, $defaults );

	if ( empty( $config['id'] ) ) {
		return '';
	}

	$panel_classes = [ 'drawer-panel' ];
	if ( $config['is_root'] ) {
		$panel_classes[] = 'drawer-panel-root';
	} else {
		$panel_classes[] = 'drawer-panel-submenu';
	}

	$panel_class_attr = implode( ' ', array_map( 'sanitize_html_class', $panel_classes ) );

	$left_chevron_svg = '<svg width="10" height="17" viewBox="0 0 10 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 1.23077L0.999999 8.61539L9 16" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	$right_chevron_svg = '<svg width="10" height="18" viewBox="0 0 10 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 17L9 9L0.999999 1" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	$close_icon_svg = '<svg aria-hidden="true" class="navbar-icon navbar-icon-close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 27"><path class="fill-1" d="M18,4.95L2,20.95M2,4.95l16,16"/></svg>';

	ob_start();
	?>
	<div class="<?php echo esc_attr( $panel_class_attr ); ?>" id="<?php echo esc_attr( $config['id'] ); ?>" inert>
		<div class="flyout-heading<?php echo $config['show_close'] ? ' has-close-button' : ''; ?>">
			<?php if ( $config['is_root'] ) : ?>
				<?php if ( $config['heading_url'] ) : ?>
					<a href="<?php echo esc_url( $config['heading_url'] ); ?>">
						<?php if ( $config['heading_icon'] ) : ?>
							<?php echo $config['heading_icon']; ?>
						<?php endif; ?>
						<span><?php echo esc_html( $config['heading'] ); ?></span>
					</a>
				<?php else : ?>
					<?php if ( $config['heading_icon'] ) : ?>
						<?php echo $config['heading_icon']; ?>
					<?php endif; ?>
					<span><?php echo esc_html( $config['heading'] ); ?></span>
				<?php endif; ?>

				<?php if ( $config['show_close'] ) : ?>
					<button class="drawer-close-button" data-flyout-close aria-label="<?php echo esc_attr( sprintf( __( 'Close %s menu', 'flavor' ), $config['heading'] ) ); ?>">
						<?php echo $close_icon_svg; ?>
					</button>
				<?php endif; ?>
			<?php else : ?>
				<button class="nav-item flyout-action-button flyout-subnav-back" data-back="<?php echo esc_attr( $config['back_target'] ); ?>">
					<span class="chevron-left"><?php echo $left_chevron_svg; ?></span>
					<span class="back-button-text"><?php echo esc_html( $config['back_text'] ); ?></span>
				</button>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $config['content'] ) ) : ?>
			<div class="drawer-content">
				<?php echo $config['content']; ?>
			</div>
		<?php elseif ( ! empty( $config['items'] ) ) : ?>
			<ul class="drawer-list">
				<?php foreach ( $config['items'] as $item ) :
					$item_classes = [ 'drawer-item' ];
					if ( ! empty( $item['class'] ) ) {
						$item_classes[] = $item['class'];
					}
					if ( ! empty( $item['is_current'] ) ) {
						$item_classes[] = 'current-menu-item';
					}

					// Handle submenu as alias for target
					$target = ! empty( $item['target'] ) ? $item['target'] : ( ! empty( $item['submenu'] ) ? $item['submenu'] : '' );

					// Handle is_raw for unescaped HTML content
					$text = ! empty( $item['is_raw'] ) ? $item['text'] : esc_html( $item['text'] );

					$item_class_attr = implode( ' ', array_map( 'sanitize_html_class', $item_classes ) );

					if ( ! empty( $item['is_heading'] ) ) :
						?>
						<li class="<?php echo esc_attr( $item_class_attr ); ?> flyout-subnav-heading">
							<span class="nav-item"><?php echo esc_html( $item['text'] ); ?></span>
						</li>
					<?php elseif ( ! empty( $target ) ) : ?>
						<li class="<?php echo esc_attr( $item_class_attr ); ?>">
							<button class="nav-item has-submenu flyout-action-button flyout-submenu-toggle" data-target="<?php echo esc_attr( $target ); ?>" aria-expanded="false">
								<span><?php echo $text; ?></span>
								<span class="right-chevron"><?php echo $right_chevron_svg; ?></span>
							</button>
						</li>
					<?php elseif ( ! empty( $item['href'] ) ) : ?>
						<li class="<?php echo esc_attr( $item_class_attr ); ?>">
							<a class="nav-item" href="<?php echo esc_url( $item['href'] ); ?>">
								<?php echo $text; ?>
							</a>
						</li>
					<?php else : ?>
						<li class="<?php echo esc_attr( $item_class_attr ); ?>">
							<span class="nav-item"><?php echo $text; ?></span>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $config['is_root'] ) : ?>
			<button class="flyout-close-button sr-only sr-only-focusable" data-flyout-close="<?php echo esc_attr( $config['id'] ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Close %s menu', $config['heading'] ) ); ?>">
				Close
			</button>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Render a complete drawer (flyout menu).
 *
 * A drawer contains one or more panels and slides in from the right side of the screen.
 *
 * @param array $config {
 *     Drawer configuration.
 *
 *     @type string $id            Required. Drawer ID (used for aria-controls).
 *     @type string $default_panel Panel ID to show by default when drawer opens.
 *     @type array  $panels        Array of panel configs to pass to openlab_render_drawer_panel().
 *     @type string $class         Additional CSS classes for the drawer.
 * }
 * @return string Drawer HTML.
 */
function openlab_render_drawer( $config ) {
	$defaults = [
		'id'            => '',
		'default_panel' => '',
		'panels'        => [],
		'class'         => '',
	];

	$config = wp_parse_args( $config, $defaults );

	if ( empty( $config['id'] ) ) {
		return '';
	}

	$drawer_classes = array_filter( [
		'flyout-menu',
		$config['class'],
	] );

	$drawer_class_attr = implode( ' ', array_map( 'sanitize_html_class', $drawer_classes ) );

	ob_start();
	?>
	<div class="<?php echo esc_attr( $drawer_class_attr ); ?>" id="<?php echo esc_attr( $config['id'] ); ?>" data-default-panel="<?php echo esc_attr( $config['default_panel'] ); ?>">
		<?php foreach ( $config['panels'] as $panel_config ) : ?>
			<?php echo openlab_render_drawer_panel( $panel_config ); ?>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Register a drawer to be rendered in the drawer container.
 *
 * Drawers registered via this function will be output in the unified drawer container
 * that appears after the navbar. This ensures all drawers share the same container
 * and only one can be open at a time.
 *
 * @param string   $id       Unique drawer identifier.
 * @param callable $callback Function that returns the drawer HTML (panels only, no container).
 * @param int      $priority Optional. Priority for rendering order. Default 10.
 */
function openlab_register_drawer( $id, $callback, $priority = 10 ) {
	global $openlab_registered_drawers;

	if ( ! isset( $openlab_registered_drawers ) ) {
		$openlab_registered_drawers = [];
	}

	$openlab_registered_drawers[ $id ] = [
		'callback' => $callback,
		'priority' => $priority,
	];
}

/**
 * Get all registered drawers sorted by priority.
 *
 * @return array Registered drawer configurations.
 */
function openlab_get_registered_drawers() {
	global $openlab_registered_drawers;

	if ( empty( $openlab_registered_drawers ) ) {
		return [];
	}

	// Sort by priority.
	uasort( $openlab_registered_drawers, function( $a, $b ) {
		return $a['priority'] <=> $b['priority'];
	} );

	return $openlab_registered_drawers;
}

/**
 * Render all registered drawers.
 *
 * This should be called once per page, typically in the drawer container partial.
 *
 * @return string Combined HTML of all registered drawers.
 */
function openlab_render_registered_drawers() {
	$drawers = openlab_get_registered_drawers();
	if ( empty( $drawers ) ) {
		return '';
	}

	$output = '';
	foreach ( $drawers as $id => $drawer ) {
		if ( is_callable( $drawer['callback'] ) ) {
			$output .= call_user_func( $drawer['callback'] );
		}
	}

	return $output;
}

/**
 * Output registered drawers in the footer.
 *
 * Since page templates register their drawers after the header loads,
 * we need to render them in the footer. The drawers are appended to
 * the existing .openlab-drawer-container via JavaScript.
 */
function openlab_output_registered_drawers_in_footer() {
	$drawers_html = openlab_render_registered_drawers();
	if ( empty( $drawers_html ) ) {
		return;
	}

	// Output the drawers in a hidden container, then move them with JS.
	?>
	<div id="openlab-registered-drawers-output" style="display:none;">
		<?php echo $drawers_html; ?>
	</div>
	<script>
	(function() {
		var output = document.getElementById('openlab-registered-drawers-output');
		var container = document.querySelector('.openlab-drawer-container');
		if (output && container) {
			// Move all drawer children to the container.
			while (output.firstChild) {
				container.appendChild(output.firstChild);
			}
			// Remove the empty output container.
			output.parentNode.removeChild(output);
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'openlab_output_registered_drawers_in_footer', 5 );
