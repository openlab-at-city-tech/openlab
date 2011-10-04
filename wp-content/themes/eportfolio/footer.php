		</div><!-- /content -->

		<div id="sidebar">
			<?php if (is_front_page()) get_template_part('sidebar','home');
			      else dynamic_sidebar('main-widget-area'); ?>
		</div><!-- /sidebar -->

		<div class="clr"></div>
	</div><!-- /main -->

	<footer id="colophon">
		<div class="alignleft"><a href="mailto:Connect@citytech.cuny.edu">Connect@citytech.cuny.edu</a></div>
		<div class="alignright"><a href="http://www.citytech.cuny.edu/" target="_blank">New York City College of Technology</a></div>
		<div class="clr"></div>
	</footer>

</div><!-- /page -->

<?php wp_footer(); ?>
</body>
</html>
