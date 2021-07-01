<?php 

$sidebar_count = count( array_filter( array( is_active_sidebar( 'footer-a' ), is_active_sidebar( 'footer-b' ), is_active_sidebar( 'footer-c' ) ) ) );

if ( $sidebar_count ) : 
	?>

	<footer class="footer section" id="site-footer">
		
		<div class="footer-inner section-inner group sidebar-count-<?php echo $sidebar_count; ?>">

			<?php

			$widget_areas = array( 'footer-a', 'footer-b', 'footer-c' );

			foreach ( $widget_areas as $widget_area ) :
				if ( is_active_sidebar( $widget_area ) ) :
					?>

					<div class="<?php echo esc_attr( $widget_area ); ?> widgets">
						<?php dynamic_sidebar( $widget_area ); ?>
					</div><!-- .widgets -->

					<?php
				endif;
			endforeach;
			?>
		
		</div><!-- .footer-inner -->
	
	</footer><!-- #site-footer -->

	<?php 
endif;
?>

<div class="credits section">

	<div class="credits-inner section-inner">

		<p class="credits-left">
			<span><?php _e( 'Copyright', 'lingonberry' ); ?></span> &copy; <?php echo date( 'Y' ) ?> <a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
		</p>
		
		<p class="credits-right">
			<span><?php printf( __( 'Theme by <a href="%s">Anders Noren</a>', 'lingonberry' ), 'https://www.andersnoren.se' ); ?> &mdash; </span><a class="tothetop"><?php _e( 'Up', 'lingonberry' ); ?> &uarr;</a>
		</p>
	
	</div><!-- .credits-inner -->
	
</div><!-- .credits -->

<?php wp_footer(); ?>

</body>
</html>