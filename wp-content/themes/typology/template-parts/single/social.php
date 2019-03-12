<?php if( typology_get_option( 'single_share' ) ): ?>
	<?php $share_items = typology_get_social_share(); ?>

	<?php if ( !empty( $share_items ) ) : ?>

		<div class="typology-social-icons">
			<?php foreach ( $share_items as $item ): ?>
				<?php echo $item; ?>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>
<?php endif; ?>