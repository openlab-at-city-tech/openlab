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

add_action( 'wp_head', 'cuny_site_wide_google_font');
function cuny_site_wide_google_font() {
	echo "<link href='http://fonts.googleapis.com/css?family=Arvo' rel='stylesheet' type='text/css'>";
}

add_action('init','wds_search_override',1);
function wds_search_override(){
    global $bp;
	if(isset($_POST['search-submit']) && $_POST['search-terms']){
		if($_POST['search-which']=="members"){
			wp_redirect($bp->root_domain.'/people/?search='.$_POST['search-terms']);
			exit();
		}elseif($_POST['search-which']=="courses"){
			wp_redirect($bp->root_domain.'/courses/?search='.$_POST['search-terms']);
			exit();
		}elseif($_POST['search-which']=="projects"){
			wp_redirect($bp->root_domain.'/projects/?search='.$_POST['search-terms']);
			exit();
		}elseif($_POST['search-which']=="clubs"){
			wp_redirect($bp->root_domain.'/clubs/?search='.$_POST['search-terms']);
			exit();
		}
	}
}

function cuny_site_wide_bp_search() { ?>
	<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
		<input type="text" id="search-terms" name="search-terms" value="" />
		<?php //echo bp_search_form_type_select() ?>
        <select style="width: auto" id="search-which" name="search-which">
        <option value="members">People</option>
        <option value="courses">Courses</option>
        <option value="projects">Projects</option>
        <option value="clubs">Clubs</option>
        </select>

		<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
		<?php wp_nonce_field( 'bp_search_form' ) ?>
	</form><!-- #search-form -->
<?php }


//add_action('wp_footer', 'cuny_site_wide_header');
function cuny_site_wide_header() {
	global $blog_id;

	if ( $blog_id == 1 )
		return;


?>

<div id="cuny-sw-header-wrap">
	<div id="cuny-sw-header">
		<div class="cuny-navi">
	<?php switch_to_blog(1) ?>
		<a href="<?php echo get_bloginfo('url') ?>" id="cuny-sw-logo"></a>
	<?php restore_current_blog() ?>

			<ul class="alignright">
				<?php cuny_bp_adminbar_menu(); ?>
			</ul>

		</div>
	</div>
</div>
<?php }

function cuny_bp_adminbar_menu(){ ?>
	<div id="wp-admin-bar">
    	<ul id="wp-admin-bar-menus">
        	<?php //the admin bar items are in "reverse" order due to the right float ?>
        	<li id="login-logout" class="sub-menu user-links admin-bar-last">
            	<?php if ( is_user_logged_in() ) { ?>
                	<a href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a>
                <?php } else { ?>
                	<a href="<?php echo wp_login_url( bp_get_root_domain() ) ?>"><?php _e( 'Log In', 'buddypress' ) ?></a>
                <?php } ?>
            </li>
            <?php cuny_myopenlab_menu(); ?>
        	<li id="openlab-menu" class="sub-menu"><span class="bold">Open</span>Lab
            <?php //switch to the root site to get the wp-nav menu
                  switch_to_blog(1) ?>
            <?php $args = array(
				'theme_location' => 'main',
				'container' => '',
				'menu_class' => 'nav',
			);
			//main menu for top bar
			wp_nav_menu( $args ); ?>
			<?php restore_current_blog();  ?>
            </li><!--openlab-menu-->
            <li class="clearfloat"></li>
        </ul><!--wp-admin-bar-menus-->
    </div><!--wp-admin-bar-->
<?php }//end cuny_adminbar_menu

//myopenlab menu function
function cuny_myopenlab_menu(){
    global $bp; ?>
        	<?php if ( is_user_logged_in() ) { ?>
        	<li id="myopenlab-menu" class="sub-menu">My OpenLab
			<ul id="my-bar">
            	<li><a href="<?php echo $bp->loggedin_user->domain; ?>">My Profile</a></li>
                <li><a href="<?php echo bp_get_root_domain(); ?>/my-courses">My Courses</a></li>
                <li><a href="<?php echo bp_get_root_domain(); ?>/my-projects">My Projects</a></li>
                <li><a href="<?php echo bp_get_root_domain(); ?>/my-clubs">My Clubs</a></li>
                <li><a href="<?php echo $bp->loggedin_user->domain; ?>/friends">My Friends</a></li>
                <li><a href="<?php echo $bp->loggedin_user->domain; ?>/messages">My Messages</a></li>
            </ul><!--my-bar-->
            </li><!--myopenlab-menu-->
            <?php } else { ?>
            	<li id="register" class="sub-menu user-links">
            		<a href="<?php site_url(); ?>/register/">Register</a>
           		</li>
            <?php } ?>

<?php }//header mods

//adds the profile sidebar to the add <group> pages

add_action('genesis_before_sidebar_widget_area', 'add_group_sidebar');
function add_group_sidebar()
{
  global $bp;
  $component =  $bp->current_component;
  $action =  $bp->current_action;

  if (($component == "groups" && $action == "create") || $component=="settings" || $component == "invite-anyone")

  { ?>
     <h2 class="sidebar-title">My OpenLab</h2>
     <div id="item-buttons"><?php do_action( 'cuny_bp_profile_menus' ); ?></div>
  <?php }
}

//we may be able to deprecate this function - need to look into it
function cuny_site_wide_navi($args = '') {
global $bp, $wpdb;

switch_to_blog(1);
	$site=site_url();
restore_current_blog();
$departments_tech=array('Advertising Design and Graphic Arts','Architectural Technology','Computer Engineering Technology','Computer Systems Technology','Construction Management and Civil Engineering Technology','Electrical and Telecommunications Engineering Technology','Entertainment Technology','Environmental Control Technology','Mechanical Engineering Technology');
$departments_studies=array('Business','Career and Technology Teacher Education','Dental Hygiene','Health Services Administration','Hospitality Management','Human Services','Law and Paralegal Studies','Nursing','Radiologic Technology and Medical Imaging','Restorative Dentistry','Vision Care Technology');
$departments_arts=array('African-American Studies','Biological Sciences','Chemistry','English','Humanities','Library','Mathematics','Physics','Social Science');

$pos = strpos($site,"openlabdev");
if (!($pos === false)) {
		echo "<div style='text-align:center;width:300px;background-color:red;color:white;font-weight:bold;'>T E S T&nbsp;&nbsp;&nbsp;&nbsp;S I T E</div>";
}
?>

<ul class="menu" id="menu-main-menu"><li class="menu-item<?php if ( is_home() ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>">Home</a></li>
	<li id="menu-item-people" class="menu-item<?php if ( is_page('people') ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>/people/">People</a>
	<ul class="sub-menu">
		<li class="menu-item"><a href="<?php echo $site;?>/people/faculty/">Faculty</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/people/students/">Students</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/people/staff/">Staff</a></li>
	</ul>
	</li>
	<li class="menu-item<?php if ( is_page('courses') ) { echo ' selected-page'; } ?>" id="menu-item-40"><a href="<?php echo $site;?>/courses/">Courses</a>
    	<ul class="sub-menu">
   			<li ><a href="<?php echo $site."/courses/?school=tech"; ?>">School of Technology &amp; Design</a>
   				<ul class="sub-menu">
    			<?php foreach ($departments_tech as $i => $value) {?>
					<li><a href="<?php echo $site."/courses/?school=tech"; ?>&department=<?php echo str_replace(" ","-",strtolower($value)); ?>"><?php echo $value; ?></a></li>
				<?php }?>
                </ul>
            </li>
            <li id="menu-item-91"><a href="<?php echo $site."/courses/?school=studies"; ?>">School of Professional Studies</a>
   				<ul class="sub-menu">
    			<?php foreach ($departments_studies as $i => $value) {?>
					<li><a href="<?php echo $site."/courses/?school=studies"; ?>&department=<?php echo str_replace(" ","-",strtolower($value)); ?>"><?php echo $value; ?></a></li>
				<?php }?>
                </ul>
            </li>
            <li id="menu-item-93"><a href="<?php echo $site."/courses/?school=arts"; ?>">School of Arts &amp; Sciences</a>
   				<ul class="sub-menu">
    			<?php foreach ($departments_arts as $i => $value) {?>
					<li><a href="<?php echo $site."/courses/?school=arts"; ?>&department=<?php echo str_replace(" ","-",strtolower($value)); ?>"><?php echo $value; ?></a></li>
				<?php }?>
                </ul>
            </li>
    	</ul>
    </li>
	<li  id="menu-item-projects" class="menu-item<?php if ( is_page('projects') ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>/projects/">Projects</a></li>
	<li id="menu-item-clubs" class="menu-item<?php if ( is_page('clubs') ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>/clubs/">Clubs</a></li>
	<li id="menu-item-sites" class="menu-item<?php if ( is_page('all-sites') ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>/all-sites/">Sites</a></li>
	<li id="menu-item-help" class="menu-item<?php if ( is_page('help') ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>/support/help">Help</a>
	<ul class="sub-menu">
		<li class="menu-item"><a href="<?php echo $site;?>/support/about-city-tech-elab/">About City Tech OpenLab</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/contact-us/">Contact Us</a></li>
		<li class="menu-item"><a href="http://cuny.edu/website/privacy.html" target="_blank">Privacy Policy</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/terms-of-service/">Terms of Use</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/credits/">Image Credits</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/help/">Help</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/faq/">FAQ</a></li>
	</ul>
	</li>
	<?php if($bp->loggedin_user->id){ ?>
		<li class="menu-item"><a href="<?php echo wp_logout_url( home_url() ) ?>">Log Out</a></li>
	<?php }else { ?>
		<li class="menu-item"><a id="popup-login-link" href="#">Log In</a>
			<div id="cuny-popup-login" class="popup-login-wrap" style="display:none">
				<div class="popup-login-content">

						<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
							<label><?php _e( 'Username', 'buddypress' ) ?>
							<?php $user_login = '' ?>
							<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="1" /></label>

							<label><?php _e( 'Password', 'buddypress' ) ?>
							<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="2" /></label>

							<div><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="3" /> <?php _e( 'Keep me logged in', 'buddypress' ) ?>
							<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="4" /></div>

							<?php do_action( 'bp_sidebar_login_form' ) ?>
							<input type="hidden" name="testcookie" value="1" />
						</form>
						<a class="forgot-password-link" href="<?php echo site_url('wp-login.php?action=lostpassword', 'login') ?>">Forgot Password?</a>
				</div>
			</div></li>
	<?php } ?>
</ul>

<?php }

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
<a href="<?php echo $site;?>/support/help/">Help</a> | <a href="<?php echo $site;?>/support/contact-us/">Contact Us</a> | <a href="http://cuny.edu/website/privacy.html" target="_blank">Privacy Policy</a> | <a href="<?php echo $site;?>/support/terms-of-service/">Terms of Use</a> | <a href="<?php echo $site;?>/about/credits/">Credits</a></div></div>
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
