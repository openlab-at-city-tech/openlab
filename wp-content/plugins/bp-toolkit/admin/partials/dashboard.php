<?php

/**
 * Displays the dashboard main metabox.
 *
 * @since 2.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

?>

<div id="bptk-dashboard-welcome" class="bptk-box">
	<h3 class="bptk-box-header"><?php 
_e( 'Welcome to Block, Suspend, Report', 'bp-toolkit' );
?></h3>
	<div class="bptk-box-inner">
		<div class="bptk-dashboard-welcome-columns">
			<div class="bptk-dashboard-welcome-column">
				<?php 

if ( class_exists( 'BuddyPress' ) ) {
    ?>

					<h3><?php 
    _e( 'Initial Setup', 'bp-toolkit' );
    ?></h3>
					<ul>
						<li>
							<a href="<?php 
    echo  admin_url( '/admin.php?page=bp-toolkit-block' ) ;
    ?>"><i
										class="fa fa-ban fa-fw"></i><span>
										<?php 
    _e( ' Block Settings', 'bp-toolkit' );
    ?>
										</span></a>
						</li>

						<li>
							<a href="<?php 
    echo  admin_url( 'admin.php?page=bp-toolkit-suspend' ) ;
    ?>"><i
										class="fa fa-lock fa-fw"></i><span>
										<?php 
    _e( ' Suspend Settings', 'bp-toolkit' );
    ?>
										</span></a>
						</li>

						<li>
							<a href="<?php 
    echo  admin_url( 'admin.php?page=bp-toolkit-report' ) ;
    ?>"><i
										class="fa fa-flag fa-fw"></i><span>
										<?php 
    _e( ' Report Settings', 'bp-toolkit' );
    ?>
										</span></a>
						</li>
					</ul>
					<h3><?php 
    _e( 'Reports', 'bp-toolkit' );
    ?></h3>
					<ul>
						<li><a href="<?php 
    echo  admin_url( 'edit.php?post_type=report' ) ;
    ?>"><i
										class="fa fa-folder fa-fw"></i><span>
										<?php 
    _e( ' View Reports', 'bp-toolkit' );
    ?>
										</span></a></li>

						<?php 
    ?>

							<li><a class="isDisabled"><i
											class="fa fa-check fa-fw"></i><span>
											<?php 
    _e( ' Add New Report Types <span>- Pro Feature</span>', 'bp-toolkit' );
    ?>
											</span></a></li>

						<?php 
    ?>
					</ul>
					<hr>
					<p class="text-center">
					<?php 
    $docs = BP_TOOLKIT_SUPPORT;
    printf( __( 'For guidance as your begin these steps, <a href="%s" target="_blank">view the Initial Setup Documentation</a>.', 'bp-toolkit' ), $docs );
    ?>
								</a>
					</p>

					<?php 
} else {
    echo  '<p style="color: red; font-size: 22px; text-transform: uppercase; text-align: center; font-weight: bold;">This plugin requires BuddyPress to be installed and activated. Please return to this page once complete.</p>' ;
}

?>
			</div> <!-- end bptk-dashboard-welcome-column -->
			<div class="bptk-dashboard-welcome-column">
				<h3><?php 
echo  esc_attr_e( 'Professional Edition License', 'bp-toolkit' ) ;
?></h3>
				<?php 
?>
					<p class="bptk_message bptk_error">
						<strong><?php 
echo  esc_html_e( 'No license key found.', 'bp-toolkit' ) ;
?></strong><br/>
						<?php 
printf( __( '<a href="%s">Upgrade here &raquo;</a>', 'bp-toolkit' ), bptk_fs()->get_upgrade_url() );
?>
					</p>
				<?php 
?>
				<?php 

if ( bptk_fs()->is_not_paying() ) {
    ?>
				<p>
					<?php 
    esc_html_e( 'The Professional Edition adds heaps of extras and smart functions to Block, Suspend, Report for BuddyPress.', 'bp-toolkit' );
    ?>
						<br/><a href="<?php 
    echo  BP_TOOLKIT_HOMEPAGE ;
    ?>"
												  target="_blank">
												  <?php 
    esc_html_e( 'View all pro features &raquo;', 'bp-toolkit' );
    ?>
							</a></p>
				<p><a href="<?php 
    echo  bptk_fs()->get_upgrade_url() ;
    ?>" target="_blank"
					  class="button button-action button-hero"><?php 
    esc_attr_e( 'Upgrade', 'bp-toolkit' );
    ?></a>
					<?php 
}

?>
				<hr>
				<h3><?php 
esc_html_e( 'Latest News', 'bp-toolkit' );
?></h3>
				<?php 
$this->latest_news();
?>
				<?php 
?>
			</div> <!-- end bptk-dashboard-welcome-column -->
			<div class="bptk-dashboard-welcome-column">
				<h3><?php 
esc_html_e( 'Get Involved', 'bp-toolkit' );
?></h3>
				<p>
				<?php 
esc_html_e( 'There are many ways you can help support Block, Suspend, Report for BuddyPress.', 'bp-toolkit' );
?>
						</p>
				<p><a href="https://wordpress.org/plugins/bp-toolkit/#reviews" target="_blank"><i
								class="dashicons dashicons-wordpress"></i> 
								<?php 
esc_html_e( 'Share an honest review at WordPress.org.', 'bp-toolkit' );
?>
							</a></p>
				<p><a href="https://www.bouncingsprout.com/support#contact" target="_blank"><i
								class="dashicons dashicons-format-status"></i> 
								<?php 
esc_html_e( 'Suggest a new feature.', 'bp-toolkit' );
?>
							</a></p>
				<hr/>
				<p>
				<?php 
esc_html_e( 'Help translate Block, Suspend, Report for BuddyPress into your language.', 'bp-toolkit' );
?>
						 <a href="https://translate.wordpress.org/projects/wp-plugins/bp-toolkit"
											  target="_blank">
											  <?php 
esc_html_e( 'Translation Dashboard', 'bp-toolkit' );
?>
							</a></p>
			</div> <!-- end bptk-dashboard-welcome-column -->
		</div>
	</div>
</div>

<?php 

if ( bptk_fs()->is_not_paying() ) {
    ?>
	<div id="bptk-dashboard-upgrade" class="bptk-box">
		<div class="bptk-box-inner">
			<h3><?php 
    esc_html_e( 'Why Upgrade?', 'bp-toolkit' );
    ?></h3>
			<div class="bptk-dashboard-upgrade-columns">
				<div class="upgrade-column pro">
					<h3><?php 
    esc_html_e( 'Professional Edition', 'bp-toolkit' );
    ?></h3>
					<p>
					<?php 
    esc_html_e( 'If you want to show you listen to  your user\'s concerns, you need the Professional Version.', 'bp-toolkit' );
    ?>
							</p>
					<strong><?php 
    esc_html_e( 'Let your users report different activities:', 'bp-toolkit' );
    ?></strong>
					<ul>
						<li><?php 
    esc_html_e( 'Individual posts', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Forum Topics', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Forum Posts', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Media Uploads', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Messages', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Groups', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Post Comments', 'bp-toolkit' );
    ?></li>
					</ul>
					<p><strong><?php 
    esc_html_e( 'Send emails to multiple administrators', 'bp-toolkit' );
    ?></strong></p>
					<p><strong><?php 
    esc_html_e( 'Quickly moderate activity from this dashboard', 'bp-toolkit' );
    ?></strong></p>
					<p><strong><?php 
    esc_html_e( 'Integration with other plugins', 'bp-toolkit' );
    ?></strong></p>
					<p><strong><?php 
    esc_html_e( 'All your moderation information on the dashboard', 'bp-toolkit' );
    ?></strong></p>
					<p><strong><?php 
    esc_html_e( 'Premium Support', 'bp-toolkit' );
    ?></strong></p>
					<p><strong><?php 
    esc_html_e( 'Hooks and custom CSS to extend the plugin further', 'bp-toolkit' );
    ?></strong></p>
					<p><strong><?php 
    esc_html_e( 'Handle malicious complainers by removing their ability to report items', 'bp-toolkit' );
    ?></strong></p>
					<p><strong><?php 
    esc_html_e( 'Create your own report categories', 'bp-toolkit' );
    ?></strong></p>
				</div>
				<div class="upgrade-column enterprise">
					<h3><?php 
    esc_html_e( 'Enterprise Edition', 'bp-toolkit' );
    ?></h3>
					<p>
					<?php 
    esc_html_e( 'Once your community has grown to hundreds, use the Enterprise Edition to save you time.', 'bp-toolkit' );
    ?>
							</p>
					<img src="<?php 
    echo  plugin_dir_url( __DIR__ ) . 'assets/images/report_screen.jpg' ;
    ?>">
					<p><strong><?php 
    esc_html_e( 'Create Site Moderators', 'bp-toolkit' );
    ?></strong></p>
					<ul>
						<li><?php 
    esc_html_e( 'Give them access to an easy to use frontend dashboard', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'No access to your backend', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Can suspend users', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Can delete posts', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Can hold posts for moderation', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Can process and respond to reports', 'bp-toolkit' );
    ?></li>
						<li><?php 
    esc_html_e( 'Take some or all of the moderation workload from you, so you can go back to managing your community', 'bp-toolkit' );
    ?></li>
					</ul>
					<p><strong><?php 
    esc_html_e( 'Add notes to reports', 'bp-toolkit' );
    ?></strong></p>
					<p><strong><?php 
    esc_html_e( 'Assign reports to specific moderators', 'bp-toolkit' );
    ?></strong></p>
					<p><strong><?php 
    esc_html_e( 'Full moderation actions logging and audit trail', 'bp-toolkit' );
    ?></strong></p>
					<p><strong><?php 
    esc_html_e( 'Send messages to your users and moderators about reports and moderation issues', 'bp-toolkit' );
    ?></strong></p>
				</div>
			</div>
		</div>
	</div>
<?php 
}

?>

