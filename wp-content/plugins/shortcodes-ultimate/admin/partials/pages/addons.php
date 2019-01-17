<?php defined( 'ABSPATH' ) or exit; ?>
<?php $addons = $this->get_addons(); ?>

<div id="su_admin_addons" class="wrap su-admin-addons wp-clearfix">

	<h1><?php $this->the_page_title(); ?></h1>

	<div class="su-admin-addons-list">

		<?php if ( empty( $addons ) ) : ?>

			<p><a href="https://getshortcodes.com/add-ons/" target="_blank" class="button button-primary"><?php _e( 'Premium add-ons', 'shortcodes-ultimate' ); ?></a></p>

		<?php else : ?>

			<?php foreach( $addons as $addon ) : ?>
				<a href="<?php echo esc_attr( $addon['permalink'] ); ?>" class="su-admin-addons-item" target="_blank">
					<img src="<?php echo esc_attr( $addon['images']['medium'] ); ?>" srcset="<?php echo esc_attr( $addon['images']['medium'] ); ?> 1x, <?php echo esc_attr( $addon['images']['full'] ); ?> 2x" class="su-admin-addons-item-image">
					<span class="su-admin-addons-item-info">
						<span class="su-admin-addons-item-title"><?php echo esc_html( $addon['name'] ); ?></span>
						<span class="su-admin-addons-item-description"><?php echo esc_html( $addon['description'] ); ?></span>
						<span class="su-admin-addons-item-button button"><?php _e( 'Learn more', 'shortcodes-ultimate' ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>

		<?php endif; ?>

	</div>

</div>
