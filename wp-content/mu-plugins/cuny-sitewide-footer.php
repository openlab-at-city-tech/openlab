<?php

/**
 * Adds 'local environment' tab
 */
function cuny_local_env_flag() {
	if ( defined( 'IS_LOCAL_ENV' ) && IS_LOCAL_ENV ) {
		?>

		<style type="text/css">
			#local-env-flag {
				position: fixed;
				left: 0;
				top: 35px;
				width: 150px;
				padding: 10px 15px;
				text-align: center;
				background: #600;
				color: #fff;
				font-size: 1em;
				line-height: 1.8em;
				border: 2px solid #666;
				z-index: 1000;
				opacity: 0.7;
			}
		</style>

		<div id="local-env-flag">
			LOCAL ENVIRONMENT
		</div>

		<?php
	}
}
add_action( 'wp_footer', 'cuny_local_env_flag' );
add_action( 'admin_footer', 'cuny_local_env_flag' );

add_action('wp_enqueue_scripts','wds_jquery');
function wds_jquery() {
		wp_enqueue_script('jquery');
}

add_action('wp_print_styles', 'cuny_site_wide_navi_styles');
function cuny_site_wide_navi_styles() {
	global $blog_id;
	$sw_navi_styles = WPMU_PLUGIN_URL . '/css/sw-navi.css';

	if ( $blog_id == 1 )
		return;

	wp_register_style( 'SW_Navi_styles', $sw_navi_styles );
	wp_enqueue_style( 'SW_Navi_styles' );
}

//add_action('wp_head', 'cuny_login_popup_script');
function cuny_login_popup_script() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		var cpl = jQuery('#cuny-popup-login');
		jQuery("#popup-login-link").show();
		jQuery(cpl).hide();

		jQuery("#popup-login-link").click(function(){
			if ( 'none' == jQuery(cpl).css('display') ) {
				jQuery(cpl).show();
				jQuery("#sidebar-user-login").focus();
			} else {
				jQuery(cpl).hide();
			}

			return false;
		});

		jQuery(".close-popup-login").click(function(){
			jQuery(cpl).hide();
		});
	});
	</script>
	<?php

}

add_action('wp_footer', 'cuny_site_wide_footer');
function cuny_site_wide_footer() {
global $blog_id;
switch_to_blog(1);
$site=site_url();
restore_current_blog();
?>

<div id="cuny-sw-footer">
<div class="footer-widgets" id="footer-widgets"><div class="wrap"><div class="footer-widgets-1 widget-area"><div class="widget widget_text" id="text-4"><div class="widget-wrap">
	<div class="textwidget"><a href="http://www.citytech.cuny.edu/" target="_blank"><img src="<?php echo $site;?>/wp-content/themes/citytech/images/ctnyc-seal.png" alt="Ney York City College of Technology" border="0" /></a></div>
		</div></div>
</div><div class="footer-widgets-2 widget-area"><div class="widget widget_text" id="text-3"><div class="widget-wrap"><h4 class="widgettitle">About OpenLab</h4>
			<div class="textwidget"><p>OpenLab is an open-source, digital platform designed to support teaching and learning at New York City College of Technology (NYCCT), and to promote student and faculty engagement in the intellectual and social life of the college community.</p></div>
		</div></div>
</div><div class="footer-widgets-3 widget-area"><div class="widget menupages" id="menu-pages-4"><div class="widget-wrap"><h4 class="widgettitle">Support</h4>
<a href="<?php echo $site;?>/blog/help/openlab-help/">Help</a> | <a href="<?php echo $site;?>/about/contact-us/">Contact Us</a> | <a href="http://cuny.edu/website/privacy.html" target="_blank">Privacy Policy</a> | <a href="<?php echo $site;?>/about/terms-of-service/">Terms of Use</a> | <a href="<?php echo $site;?>/about/credits/">Credits</a></div></div>
</div><div class="footer-widgets-4 widget-area"><div class="widget widget_text" id="text-6"><div class="widget-wrap"><h4 class="widgettitle">Share</h4>
			<div class="textwidget"><ul class="nav"><li class="rss"><a href="<?php echo $site."/activity/feed/" ?>">RSS</a></li>
            <li>
            <!-- Place this tag in your head or just before your close body tag -->
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>

<!-- Place this tag where you want the +1 button to render -->
<g:plusone size="small"></g:plusone>
            </li>
            </ul></div>
		</div></div>
</div>
<div class="footer-widgets-5 widget-area"><div class="widget widget_text" id="text-7"><div class="widget-wrap"><div class="textwidget"><a href="http://www.cuny.edu/" target="_blank"><img alt="City University of New York" src="<?php echo $site;?>/wp-content/uploads/2011/05/cuny-box.png" /></a></div>
		</div></div>
</div></div><!-- end .wrap --></div>
<div class="footer" id="footer"><div class="wrap"><span class="alignleft">&copy; <a href="http://www.citytech.cuny.edu/" target="_blank">New York City College of Technology</a></span><span class="alignright"><a href="http://www.cuny.edu" target="_blank">City University of New York</a></span></div><!-- end .wrap --></div>
</div>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-24214531-1']);
  _gaq.push(['_setDomainName', 'openlab.citytech.cuny.edu']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php }

remove_action( 'init', 'maybe_add_existing_user_to_blog' );
add_action( 'init', 'maybe_add_existing_user_to_blog', 90 );
