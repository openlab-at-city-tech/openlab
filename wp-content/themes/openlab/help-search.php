<?php
$help_search = isset( $_GET['help-search'] ) ? urldecode( $_GET['help-search'] ) : '';
$pag_page = isset( $_GET['hs-page'] ) ? intval( $_GET['hs-page'] ) : 1;
?>

<?php get_header(); ?>
	<div id="content" class="hfeed row">
		<h1>Search Help</h1>

		<?php openlab_bp_mobile_sidebar( 'help' ); ?>

		<div class="col-sm-18 col-xs-24 col-help">
			<?php $hq = new WP_Query( array(
				'post_type' => 'help',
				's' => $help_search,
				'posts_per_page' => 10,
				'paged' => $pag_page,
			) ); ?>

			<?php if ( $hq->have_posts() ) : ?>
				<p>The following documents match the query <strong>"<?php echo esc_html( $help_search ); ?>"</strong>:</p>

				<ul>
				<?php while ( $hq->have_posts() ) : ?>
					<?php $hq->the_post(); ?>
					<li>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<div class="help-search-excerpt">
							<?php the_excerpt(); ?>
						</div>
					</li>
				<?php endwhile; ?>
				</ul>

				<div class="help-search-pagination">
				<?php
				$add_args = array();
				if ( ! empty( $help_search ) ) {
					$add_args['help-search'] = urlencode( $help_search );
				}

				echo paginate_links( array(
					'base' => add_query_arg( 'hs-page', '%#%', openlab_get_help_search_url() ),
					'format' => '',
					'current' => $pag_page,
					'total' => $hq->max_num_pages,
				) );
				?>
				</div>

			<?php else : ?>
				<p>Sorry, no help documents were found matching the query <strong>"<?php echo esc_html( $help_search ); ?>"</strong>.</p>
			<?php endif; ?>
		</div>

		<?php openlab_bp_sidebar('help'); ?>
	</div>
<?php get_footer();
