<?php
$help_search = isset( $_GET['help-search'] ) ? urldecode( $_GET['help-search'] ) : '';
$pag_page = isset( $_GET['hs-page'] ) ? intval( $_GET['hs-page'] ) : 1;
?>

<?php get_header(); ?>
	<div id="content" class="hfeed row">

		<div class="col-sm-18 col-xs-24 col-help">
			<div id="openlab-main-content" class="content-wrapper">

			<h1 class="entry-title help-entry-title">Search Help</h1>

			<div id="help-title">
				<h2 class="page-title clearfix submenu">
					<div class="subenu-text pull-left bold">Results: </div>
				</h2>
			</div>

			<div class="entry-content archive">
				<?php $hq = new WP_Query( array(
					'post_type' => 'help',
					's' => $help_search,
					'posts_per_page' => 10,
					'paged' => $pag_page,
				) ); ?>

				<?php if ( $hq->have_posts() ) : ?>
					<p>The following match the search term <strong>"<?php echo esc_html( $help_search ); ?>"</strong>:</p>

					<div class="child-cat-container help-cat-block">
						<ul>
						<?php while ( $hq->have_posts() ) : ?>
							<?php $hq->the_post(); ?>
							<li>
								<h3 class="help-title no-margin no-margin-bottom"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<div class="help-search-excerpt">
									<?php the_excerpt(); ?>
								</div>
							</li>
						<?php endwhile; ?>
						</ul>

					</div><!-- .child-cat-container help-cat-block -->

					<div class="pagination-links help-search-pagination">
					<?php
					$add_args = array();
					if ( ! empty( $help_search ) ) {
						$add_args['help-search'] = urlencode( $help_search );
					}

					$pag_links = paginate_links( array(
						'base' => add_query_arg( 'hs-page', '%#%', openlab_get_help_search_url() ),
						'format' => '',
						'current' => $pag_page,
						'total' => $hq->max_num_pages,
						'type' => 'array',
						'prev_text' => _x( '<i class="fa fa-angle-left"></i>', 'Group pagination previous text', 'buddypress' ),
						'next_text' => _x( '<i class="fa fa-angle-right"></i>', 'Group pagination next text', 'buddypress' ),
						'mid_size' => 3,
					) );

					echo '<ul class="pagination page-numbers">';
					foreach ( $pag_links as $pag_link ) {
						printf(
							'<li>%s</li>',
							$pag_link
						);
					}
					echo '</ul>';
					?>
					</div>

				<?php else : ?>
					<p>Sorry, no help documents were found matching the query <strong>"<?php echo esc_html( $help_search ); ?>"</strong>.</p>
				<?php endif; ?>
			</div><!-- .entry-content -->

			</div><!-- .content-wrapper -->
		</div>

		<?php openlab_bp_sidebar('help', false, ' mobile-enabled'); ?>
	</div>
<?php get_footer();
