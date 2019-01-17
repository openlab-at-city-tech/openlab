<?php defined( 'ABSPATH' ) or exit; ?>

<?php $shortcodes = $this->get_shortcodes_list(); ?>
<h1><?php $this->the_page_title(); ?></h1>

<div class="wp-filter">
	<div class="filter-count">
		<span class="count"><?php echo count( $shortcodes ); ?></span>
	</div>
	<ul class="filter-links">

		<?php foreach( $this->get_groups() as $group ) : ?>

			<?php if ( $group['active'] ) : ?>
				<li><a href="<?php echo esc_url( $group['url'] ); ?>" class="current"><?php echo esc_html( $group['title'] ); ?></a></li>
			<?php else : ?>
				<li><a href="<?php echo esc_url( $group['url'] ); ?>"><?php echo esc_html( $group['title'] ); ?></a></li>
			<?php endif; ?>

		<?php endforeach; ?>

	</ul>

	<?php /*
	<form class="search-form" action="<?php echo $this->component_url; ?>">
		<label class="screen-reader-text" for="wp-filter-search-input"><?php _e( 'Search shortcodes', 'shortcodes-ultimate' ); ?></label>
		<input placeholder="<?php _e( 'Search shortcodes', 'shortcodes-ultimate' ); ?>&hellip;" type="search" id="wp-filter-search-input" class="wp-filter-search">
	</form>
	*/ ?>
</div>

<div class="su-admin-shortcodes-list wp-clearfix">

	<?php if ( count( $shortcodes ) ) : ?>

		<?php foreach ( $shortcodes as $id => $shortcode ) : ?>
			<a href="<?php echo add_query_arg( 'shortcode', $id, $this->get_component_url() ); ?>" class="su-admin-shortcodes-list-item">
				<span class="su-admin-shortcodes-list-item-image">
					<?php $this->shortcode_image( $shortcode, 120 ); ?>
				</span>
				<span class="su-admin-shortcodes-list-item-title"><?php echo $shortcode['name']; ?></span>
			</a>
		<?php endforeach; ?>

	<?php else : ?>
		<p class="su-admin-shortcodes-list-not-found"><?php _e( 'No shortcodes found.', 'shortcodes-ultimate' ); ?></p>
	<?php endif; ?>

</div>
