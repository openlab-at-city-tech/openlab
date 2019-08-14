			<footer id="site-footer" role="contentinfo">

				<?php if ( is_active_sidebar( 'footer-one' ) || is_active_sidebar( 'footer-two' ) || is_active_sidebar( 'footer-three' ) ) : ?>

					<div class="footer-widgets-outer-wrapper section-inner">

						<div class="footer-widgets-wrapper">

							<div class="footer-widgets">
								<?php dynamic_sidebar( 'footer-one' ); ?>
							</div>

							<div class="footer-widgets">
								<?php dynamic_sidebar( 'footer-two' ); ?>
							</div>

							<div class="footer-widgets">
								<?php dynamic_sidebar( 'footer-three' ); ?>
							</div>

						</div><!-- .footer-widgets-wrapper -->

					</div><!-- .footer-widgets-outer-wrapper.section-inner -->

				<?php endif; ?>

				<p class="credits">
					<?php
					/* Translators: $s = name of the theme developer */
					printf( _x( 'Theme by %s', 'Translators: $s = name of the theme developer', 'koji' ), '<a href="https://www.andersnoren.se">' . __( 'Anders Norén', 'koji' ) . '</a>' ); ?>
				</p><!-- .credits -->

			</footer><!-- #site-footer -->
			
			<?php wp_footer(); ?>

		</div><!-- #site-wrapper -->

	</body>
</html>
