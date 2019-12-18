<?php defined( 'ABSPATH' ) || exit; ?>

<?php $shortcodes = $this->get_available_shortcodes(); ?>

<div id="su_admin_shortcodes" class="wrap su-admin-shortcodes">

	<h1><?php $this->the_page_title(); ?></h1>

	<div class="wp-filter">
		<div class="filter-count">
			<span class="count"><?php echo count( $shortcodes ); ?></span>
		</div>
		<ul class="filter-links">

			<?php foreach ( $this->get_groups() as $group ) : ?>

				<?php if ( $group['active'] ) : ?>
					<li><a href="<?php echo esc_url( $group['url'] ); ?>" class="current"><?php echo esc_html( $group['title'] ); ?></a></li>
				<?php else : ?>
					<li><a href="<?php echo esc_url( $group['url'] ); ?>"><?php echo esc_html( $group['title'] ); ?></a></li>
				<?php endif; ?>

			<?php endforeach; ?>

		</ul>
	</div>

	<div class="su-admin-shortcodes-list wp-clearfix">

		<?php if ( count( $shortcodes ) ) : ?>

			<?php foreach ( $shortcodes as $shortcode_id => $shortcode ) : ?>
				<a href="<?php echo esc_attr( add_query_arg( 'shortcode', $shortcode_id, $this->get_component_url() ) ); ?>" class="su-admin-shortcodes-list-item">
					<span class="su-admin-shortcodes-list-item-image">
						<?php $this->shortcode_image( $shortcode, 120 ); ?>
					</span>
					<span class="su-admin-shortcodes-list-item-title"><?php echo esc_html( $shortcode['name'] ); ?></span>
				</a>
			<?php endforeach; ?>

		<?php else : ?>
			<p class="su-admin-shortcodes-list-not-found"><?php esc_html_e( 'No shortcodes found.', 'shortcodes-ultimate' ); ?></p>
		<?php endif; ?>

	</div>

</div>
