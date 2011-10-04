		</div><!-- /content -->

		<div id="sidebar">
			<?php if (is_front_page()) get_template_part('sidebar','home');
			      else get_template_part('sidebar','default'); ?>	
		</div><!-- /sidebar -->

		<div class="clr"></div>
	</div><!-- /main -->
	
	<footer id="colophon">
		<div class="alignleft">&copy; <?php echo date('Y'); ?>, <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name','display')); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></div>
		<div class="alignright"><a href="http://www.citytech.cuny.edu/" target="_blank">New York City College of Technology</a></div>
		<div class="clr"></div>
	</footer>

</div><!-- /page -->

<div id="loginlink"><a href="<?php echo esc_url(home_url('/wp-admin')); ?>">Log In</a></div>

<?php wp_footer(); ?>
</body>
</html>
