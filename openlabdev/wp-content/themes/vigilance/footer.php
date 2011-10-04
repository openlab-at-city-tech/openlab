<?php global $vigilance; ?>
	<div id="footer">
		<p class="right"><?php _e( 'Copyright', 'vigilance' ); ?> <?php echo date( 'Y' ); ?> <?php echo $vigilance->copyrightName(); ?></p>
		<p><a href="<?php echo $vigilance->themeurl; ?>">Vigilance Theme</a> by <a href="http://thethemefoundry.com">The Theme Foundry</a></p>
	</div><!--end footer-->
</div><!--end wrapper-->
<?php wp_footer(); ?>
<?php
	if ($vigilance->statsCode() != '' ) {
		echo $vigilance->statsCode();
	}
?>
</body>
</html>
