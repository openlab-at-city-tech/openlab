<div id='ngg_page_content'>

	<div id="errors">	
	</div>

	<div class="ngg_page_content_menu" class="ngg_advanced">

		<?php foreach ( $tabs as $tab ) : ?>
			<a href='javascript:void(0)' data-id='<?php echo esc_attr( $tab['id'] ); ?>'><?php echo esc_html( $tab['title'] ); ?></a>
		<?php endforeach ?>

	</div>

	<div class="ngg_page_content_main">

		<?php foreach ( $tabs as $tab ) : ?>
			<div data-id='<?php echo esc_attr( $tab['id'] ); ?>'>
				<h3 class="accordion_tab" id="<?php echo esc_html( $tab['id'] ); ?>"><?php echo esc_html( $tab['title'] ); ?></h3>
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Tab content is already escaped ?>
				<div id="<?php echo esc_attr( $tab['id'] ); ?>_content"><?php echo $tab['content']; ?></div>
			</div>
		<?php endforeach ?>

		<p class="wp-core-ui">
			<input type="button" class="button button-primary button-large ngg_display_tab_save" id="save_displayed_gallery" value="
			<?php
			if ( $displayed_gallery->id() ) {
				esc_html_e( 'Save Changes', 'nggallery' );
			} else {
				esc_html_e( 'Insert Gallery', 'nggallery' ); }
			?>
			"/>
		</p>
		<div class="ngg_igw_video">
			<p class="ngg_igw_video_open button-primary"><?php esc_html_e( 'Need a quick tutorial?', 'nggallery' ); ?></p>
			<div class="ngg_igw_video_inner">
				<span class="ngg_igw_video_close"><?php esc_html_e( 'Click to Close', 'nggallery' ); ?></span>
			</div>
		</div>
	</div>

</div>