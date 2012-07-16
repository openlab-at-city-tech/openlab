<?php
/**
 * The template used to display the footer
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets
 *
 */
?>
    </div><!-- #main -->
<?php if (weaver_getopt_checked('ttw_footer_last')) echo("</div><!-- #wrapper -->\n"); ?>
    <?php
    if (!weaver_is_checked_page_opt('ttw-hide-footer')) {
    weaver_put_area('prefooter');
    if (!weaver_getopt('ttw_hide_footer')) {
    ?>
	<div id="footer">
		<div id="colophon">
<?php 	get_sidebar( 'footer' );
	echo(do_shortcode(weaver_getopt('ttw_footer_opts')));	/* here is where the footer options get inserted */
	do_action('wvrx_extended_footer');			/* anything in the extended footer */
	do_action('wvrx_plus_footer');				/* after ttw_footer_opts added */
	$date = getdate();
	$year = $date['year'];
?>
<table id='ttw_ftable'><tr>
 <td id='ttw_ftdl'><div id="site-info">
<?php $cp = weaver_getopt('ttw_copyright');
	if (strlen($cp) > 0) { echo(do_shortcode($cp) . '</div></td>'); }
	else { ?>
 &copy; <?php echo($year); ?> - <a href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
 </div></td> <?php }
	if (! weaver_getopt('ttw_hide_poweredby')) { ?>
 <td id='ttw_ftdr'><div id="site-generator">
 <?php do_action('weaver_credits' ); ?>
 <a href="http://wordpress.org/" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', WEAVER_TRANS ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s.', WEAVER_TRANS ), 'WordPress' ); ?></a>
 <?php echo(WEAVER_THEMENAME); ?> by WeaverTheme.com
 </div></td> <?php } ?>
</tr></table>
		</div><!-- #colophon -->
	</div><!-- #footer -->
    <?php
    }
    weaver_put_area('postfooter');
    } /* end of ttw-hide-footer */ ?>
<?php if (!weaver_getopt_checked('ttw_footer_last')) echo("</div><!-- #wrapper -->\n"); ?>

<?php echo(do_shortcode(weaver_getopt('ttw_end_opts')) ."\n"); /* and this is the end options insertion */
      wp_footer();  ?>
</body>
</html>
