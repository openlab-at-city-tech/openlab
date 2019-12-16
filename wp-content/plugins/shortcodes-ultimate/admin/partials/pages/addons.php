<?php defined( 'ABSPATH' ) || exit; ?>

<?php $addons = $this->get_addons(); ?>

<div id="su_admin_addons" class="wrap su-admin-addons wp-clearfix">

	<h1><?php $this->the_page_title(); ?></h1>

	<div class="su-admin-addons-list">

		<?php if ( empty( $addons ) ) : ?>

			<p>
				<a href="https://getshortcodes.com/add-ons/" target="_blank" class="button button-primary"><?php esc_html_e( 'Premium Add-ons', 'shortcodes-ultimate' ); ?> →</a>
			</p>

		<?php else : ?>

			<?php foreach ( $addons as $addon ) : ?>

				<div class="su-admin-addons-item">
					<div class="su-admin-addons-item-content">
						<img src="<?php echo esc_attr( $addon['image'] ); ?>" alt="" class="su-admin-addons-item-image">
						<h2 class="su-admin-addons-item-title"><?php echo esc_html( $addon['title'] ); ?></h2>
						<p class="su-admin-addons-item-description"><?php echo esc_html( $addon['description'] ); ?></p>
						<div class="su-admin-addons-item-action" aria-hidden="true">
							<span class="button"><?php esc_html_e( 'Learn more', 'shortcodes-ultimate' ); ?></span>
						</div>
					</div>
					<a href="<?php echo esc_attr( $this->get_addon_permalink( $addon ) ); ?>" target="_blank" rel="noopener" class="su-admin-addons-item-overlay"><?php esc_html_e( 'Learn more about', 'shortcodes-ultimate' ); ?> <?php echo esc_html( $addon['title'] ); ?></a>
				</div>

			<?php endforeach; ?>

		<?php endif; ?>

	</div>

</div>
