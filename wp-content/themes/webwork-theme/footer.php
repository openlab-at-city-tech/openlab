	<div class="footer section large-padding bg-dark" role="complementary">

		<div class="footer-inner section-inner">

			<?php if ( is_active_sidebar( 'footer-a' ) ) : ?>

				<div class="column column-1 left">

					<div class="widgets">

						<?php dynamic_sidebar( 'footer-a' ); ?>

					</div>

				</div>

			<?php endif; ?> <!-- /footer-a -->

			<div class="column column-2 left">

				<div class="widgets">

					<?php /* Empty */ ?>

				</div> <!-- /widgets -->

			</div>

			<div class="clear"></div>

		</div> <!-- /footer-inner -->

	</div> <!-- /footer -->

	<div class="credits section bg-dark no-padding" role="contentinfo">

		<div class="credits-inner section-inner">

			<p class="credits-left">

				&copy; <?php echo date("Y") ?> OPENLAB / CITY TECH / CUNY

			</p>

			<p class="credits-right">

				<span><a title="<?php _e('To the top', 'hemingway'); ?>" class="tothetop">Top <i class="fa fa-arrow-circle-up"></i></a>

			</p>

			<div class="clear"></div>

		</div> <!-- /credits-inner -->

	</div> <!-- /credits -->

</div> <!-- /big-wrapper -->

<?php wp_footer(); ?>

</body>
</html>
