<?php if ( is_active_sidebar('footer-col-1') || is_active_sidebar('footer-col-2') || is_active_sidebar('footer-col-3') || is_active_sidebar('footer-col-4') ) {

	$i = 0; 
	$max = 4; 
	$sidebars_with_widgets = 0;
	
	while ($i < $max) { 
		$i++; 
		if (is_active_sidebar('footer-col-' . esc_attr($i) )) {
			$sidebars_with_widgets++;
		}
	}

?>

		<footer id="site-footer" class="site-section site-section-footer">
			<div class="site-section-wrapper site-section-wrapper-footer">

				<div class="site-columns site-columns-footer site-columns-footer--<?php echo esc_attr($sidebars_with_widgets); ?>">

					<?php
					$i = 0;
					while ($i < $max) { 
						$i++; 
						if (is_active_sidebar('footer-col-' . esc_attr($i) )) { ?>
						<div class="site-column site-column-<?php echo esc_attr($i); ?>">
							<div class="site-column-wrapper">
								<?php dynamic_sidebar( 'footer-col-' . esc_attr($i) ); ?>
							</div><!-- .site-column-wrapper -->
						</div><!-- .site-column .site-column-<?php echo esc_attr($i); ?> -->
						<?php } // if is_active_sidebar() ?>
					<?php } // while ?>

				</div><!-- .site-columns .site-columns-footer -->

			</div><!-- .site-section-wrapper .site-section-wrapper-footer -->

		</footer><!-- #site-footer .site-section-footer --><?php 
		}
		?>

		<div id="site-footer-credit">
			<div class="site-section-wrapper site-section-wrapper-footer-credit">
				<?php $copyright_default = __('Copyright &copy; ','bradbury') . date("Y",time()) . ' ' . get_bloginfo('name'); ?><p class="site-credit"><?php echo esc_html(get_theme_mod( 'bradbury_copyright_text', $copyright_default )); ?></p>
						<?php if ( get_theme_mod('theme-display-footer-credit', 1 ) == 1) { ?><p class="academia-credit"><?php esc_html_e('Theme by', 'bradbury'); ?> <a href="https://www.academiathemes.com/" rel="nofollow designer noopener" target="_blank">AcademiaThemes</a></p><?php } ?>
			</div><!-- .site-section-wrapper .site-section-wrapper-footer-credit -->
		</div><!-- #site-footer-credit -->

	</div><!-- .site-wrapper-all .site-wrapper-boxed -->

</div><!-- #container -->

<?php 
wp_footer(); 
?>
</body>
</html>