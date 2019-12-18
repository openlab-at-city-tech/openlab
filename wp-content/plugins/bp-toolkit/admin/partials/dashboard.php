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
    ?>"><i class="fa fa-ban fa-fw"></i><span><?php 
    _e( ' Block Settings', 'bp-toolkit' );
    ?></span></a>
            </li>

            <li>
              <a href="<?php 
    echo  admin_url( 'admin.php?page=bp-toolkit-suspend' ) ;
    ?>"><i class="fa fa-lock fa-fw"></i><span><?php 
    _e( ' Suspend Settings', 'bp-toolkit' );
    ?></span></a>
            </li>

            <li>
              <a href="<?php 
    echo  admin_url( 'admin.php?page=bp-toolkit-report' ) ;
    ?>"><i class="fa fa-flag fa-fw"></i><span><?php 
    _e( ' Report Settings', 'bp-toolkit' );
    ?></span></a>
            </li>
          </ul>
          <h3><?php 
    _e( 'Reports', 'bp-toolkit' );
    ?></h3>
          <ul>
            <li><a href="<?php 
    echo  admin_url( 'edit.php?post_type=report' ) ;
    ?>"><i class="fa fa-folder fa-fw"></i><span><?php 
    _e( ' View Reports', 'bp-toolkit' );
    ?></span></a></li>

            <?php 
    ?>

              <li><a class="isDisabled"><i class="fa fa-check fa-fw"></i><span><?php 
    _e( ' Add New Report Types <span>- Pro Feature</span>', 'bp-toolkit' );
    ?></span></a></li>

            <?php 
    ?>
          </ul>
          <hr>
          <p class="text-center"><?php 
    $docs = BP_TOOLKIT_SUPPORT . 'bsr/';
    printf( __( 'For guidance as your begin these steps, <a href="%s" target="_blank">view the Initial Setup Documentation</a>.', 'bp-toolkit' ), $docs );
    ?></a>
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
?></strong><br />
            <?php 
printf( __( '<a href="%s">Enter your key here &raquo;</a>', 'bp-toolkit' ), bptk_fs()->get_account_url() );
?>
          </p>
        <?php 
?>
        <?php 
?>
          <p><?php 
esc_html_e( 'The Professional Edition adds heaps of extras and smart functions to Block, Suspend, Report for BuddyPress.', 'bp-toolkit' );
?><br /><a href="<?php 
echo  BP_TOOLKIT_HOMEPAGE ;
?>" target="_blank"><?php 
esc_html_e( 'View all pro features &raquo;', 'bp-toolkit' );
?></a></p>
          <p><a href="<?php 
echo  bptk_fs()->get_upgrade_url() ;
?>" target="_blank" class="button button-action button-hero"><?php 
esc_attr_e( 'Upgrade', 'bp-toolkit' );
?></a>
          <?php 
?>
          <hr>
          <h3><?php 
esc_html_e( 'Latest News', 'bp-toolkit' );
?></h3>
          <?php 
$this->latest_news();
?>
        </div> <!-- end bptk-dashboard-welcome-column -->
        <div class="bptk-dashboard-welcome-column">
          <h3><?php 
esc_html_e( 'Get Involved', 'bp-toolkit' );
?></h3>
          <p><?php 
esc_html_e( 'There are many ways you can help support Block, Suspend, Report for BuddyPress.', 'bp-toolkit' );
?></p>
          <p><?php 
esc_html_e( 'If you like the plugin, but haven\'t needed to purchase the Pro Edition, we would love it if you could buy us some chicken wings.', 'bp-toolkit' );
?> <a href="https://www.therealbenroberts.com/plugins/#donate" target="_blank"><?php 
esc_html_e( 'Buy us food.', 'bp-toolkit' );
?></a></p>
            <p><a href="https://wordpress.org/plugins/bp-toolkit/#reviews" target="_blank"><i class="dashicons dashicons-wordpress"></i> <?php 
esc_html_e( 'Share an honest review at WordPress.org.', 'bp-toolkit' );
?></a></p>
            <p><a href="https://www.therealbenroberts.com/contact" target="_blank"><i class="dashicons dashicons-format-status"></i> <?php 
esc_html_e( 'Suggest a new feature.', 'bp-toolkit' );
?></a></p>
            <hr />
            <p><?php 
esc_html_e( 'Help translate Block, Suspend, Report for BuddyPress into your language.', 'bp-toolkit' );
?> <a href="https://translate.wordpress.org/projects/wp-plugins/bp-toolkit" target="_blank"><?php 
esc_html_e( 'Translation Dashboard', 'bp-toolkit' );
?></a></p>
            </div> <!-- end bptk-dashboard-welcome-column -->
          </div>
        </div>
      </div>
      <?php 
$this->featured_news();
?>
      