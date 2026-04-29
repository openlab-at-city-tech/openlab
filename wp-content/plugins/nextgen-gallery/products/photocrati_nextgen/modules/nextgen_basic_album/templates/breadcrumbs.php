<ul class="ngg-breadcrumbs">
	<?php
	$end = end( $breadcrumbs );
	reset( $breadcrumbs );
	foreach ( $breadcrumbs as $crumb ) { ?>
		<li class="ngg-breadcrumb">
			<?php if ( ! is_null( $crumb['url'] ) ) { ?>
				<a href="<?php echo esc_url( $crumb['url'] ); ?>">
									<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses() with allowed HTML tags provides safe output
									print wp_kses( $crumb['name'], \Imagely\NGG\Display\I18N::get_kses_allowed_html() );
									?>
				</a>
			<?php } else { ?>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses() with allowed HTML tags provides safe output
				print wp_kses( $crumb['name'], \Imagely\NGG\Display\I18N::get_kses_allowed_html() );
				?>
			<?php } ?>
			<?php if ( $crumb !== $end ) { ?>
				<span class="ngg-breadcrumb-divisor"><?php echo esc_html( $divisor ); ?></span>
			<?php } ?>
		</li>
	<?php } ?>
</ul>