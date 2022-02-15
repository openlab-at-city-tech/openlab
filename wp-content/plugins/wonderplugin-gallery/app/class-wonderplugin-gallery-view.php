<?php 

if ( ! defined( 'ABSPATH' ) )
	exit;
	
require_once 'class-wonderplugin-gallery-list-table.php';
require_once 'class-wonderplugin-gallery-creator.php';

class WonderPlugin_Gallery_View {

	private $controller;
	private $list_table;
	private $creator;
	
	function __construct($controller) {
		
		$this->controller = $controller;
	}
	
	function add_metaboxes() {
		add_meta_box('overview_features', __('Wonder Gallery Features', 'wonderplugin_gallery'), array($this, 'show_features'), 'wonderplugin_gallery_overview', 'features', '');
		add_meta_box('overview_upgrade', __('Upgrade to Commercial Version', 'wonderplugin_gallery'), array($this, 'show_upgrade_to_commercial'), 'wonderplugin_gallery_overview', 'upgrade', '');
		add_meta_box('overview_news', __('WonderPlugin News', 'wonderplugin_gallery'), array($this, 'show_news'), 'wonderplugin_gallery_overview', 'news', '');
		add_meta_box('overview_contact', __('Contact Us', 'wonderplugin_gallery'), array($this, 'show_contact'), 'wonderplugin_gallery_overview', 'contact', '');
	}
	
	function show_upgrade_to_commercial() {
		?>
		<ul class="wonderplugin-feature-list">
			<li>Use on commercial websites</li>
			<li>Remove the wonderplugin.com watermark</li>
			<li>Priority techincal support</li>
			<li><a href="http://www.wonderplugin.com/wordpress-gallery/order/" target="_blank">Buy Commercial Version</a></li>
		</ul>
		<?php
	}
	
	function show_news() {
		
		include_once( ABSPATH . WPINC . '/feed.php' );
		
		$rss = fetch_feed( 'http://www.wonderplugin.com/feed/' );
		
		$maxitems = 0;
		if ( ! is_wp_error( $rss ) )
		{
			$maxitems = $rss->get_item_quantity( 5 );
			$rss_items = $rss->get_items( 0, $maxitems );
		}
		?>
		
		<ul class="wonderplugin-feature-list">
		    <?php if ( $maxitems > 0 ) {
		        foreach ( $rss_items as $item )
		        {
		        	?>
		        	<li>
		                <a href="<?php echo esc_url( $item->get_permalink() ); ?>" target="_blank" 
		                    title="<?php printf( __( 'Posted %s', 'wonderplugin_gallery' ), $item->get_date('j F Y | g:i a') ); ?>">
		                    <?php echo esc_html( $item->get_title() ); ?>
		                </a>
		                <p><?php echo esc_html( $item->get_description() ); ?></p>
		            </li>
		        	<?php 
		        }
		    } ?>
		</ul>
		<?php
	}
	
	function show_features() {
		?>
		<ul class="wonderplugin-feature-list">
			<li>Support images, YouTube, Vimeo, Dailymotion and MP4/WebM videos</li>
			<li>Support extra high quality video files to create an HD video player</li>
			<li>Works on mobile, tablets and all major web browsers, including iPhone, iPad, Android, Firefox, Safari, Chrome, Opera, Internet Explorer and Microsoft Edge</li>
			<li>Pre-defined professional skins</li>
			<li>Fully responsive</li>
			<li>Easy-to-use wizard style user interface</li>
			<li>Instantly preview</li>
			<li>Provide shortcode and PHP code to insert the gallery to pages, posts or templates</li>
		</ul>
		<?php
	}
	
	function show_contact() {
		?>
		<p>Technical support is available for Commercial Version users at support@wonderplugin.com. Please include your license information, WordPress version, link to your gallery, all related error messages in your email.</p> 
		<?php
	}
	
	function print_overview() {
		
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-gallery" class="icon32"><br /></div>
			
		<h2><?php echo __( 'Wonder Gallery', 'wonderplugin_gallery' ) . ( (WONDERPLUGIN_GALLERY_VERSION_TYPE == "C") ? " Pro" : ( (WONDERPLUGIN_GALLERY_VERSION_TYPE == "L") ? " Lite" : " Trial")) . " Version " . WONDERPLUGIN_GALLERY_VERSION; ?> </h2>
		 
		<div id="welcome-panel" class="welcome-panel">
			<div class="welcome-panel-content">
				<h3>WordPress Photo and Video Gallery Plugin</h3>
				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
						<h4>Get Started</h4>
						<a class="button button-primary button-hero" href="<?php echo admin_url('admin.php?page=wonderplugin_gallery_add_new'); ?>">Create A New Gallery</a>
					</div>
					<div class="welcome-panel-column">
						<h4>More Actions</h4>
						<ul>
							<li><a href="<?php echo admin_url('admin.php?page=wonderplugin_gallery_show_items'); ?>" class="welcome-icon welcome-widgets-menus">Manage Existing Galleries</a></li>
							<?php  if (WONDERPLUGIN_GALLERY_VERSION_TYPE !== "C") { ?>
							<li><a href="http://www.wonderplugin.com/wordpress-gallery/order/" target="_blank" class="welcome-icon welcome-view-site">Buy Commercial Version</a></li>
							<?php } ?>
						</ul>
					</div>
					<div class="welcome-panel-column welcome-panel-last">
						<h4>Help Document</h4>
						<ul>
							<li><a href="http://www.wonderplugin.com/wordpress-gallery/help/" target="_blank" class="welcome-icon welcome-learn-more">Quick Start</a></li>
							<li><a href="http://www.wonderplugin.com/wordpress-tutorials/how-to-upgrade-to-wonderplugin-commercial-version/" target="_blank" class="welcome-icon welcome-learn-more">How to Upgrade to Commercial or New Version</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder columns-2">
	 
	                 <div class="postbox-container">
	                    <?php 
	                    do_meta_boxes( 'wonderplugin_gallery_overview', 'features', '' ); 
	                    do_meta_boxes( 'wonderplugin_gallery_overview', 'contact', '' ); 
	                    ?>
	                </div>
	 
	                <div class="postbox-container">
	                    <?php 
	                    if (WONDERPLUGIN_GALLERY_VERSION_TYPE != "C")
	                    	do_meta_boxes( 'wonderplugin_gallery_overview', 'upgrade', ''); 
	                    do_meta_boxes( 'wonderplugin_gallery_overview', 'news', ''); 
	                    ?>
	                </div>
	 
	        </div>
        </div>
            
		<?php
	}
	
	function print_edit_settings() {
		?>
				<div class="wrap">
				<div id="icon-wonderplugin-gallery" class="icon32"><br /></div>
					
				<h2><?php _e( 'Settings', 'wonderplugin_gallery' ); ?></h2>
				<?php

				if ( isset($_POST['save-gallery-options']) && check_admin_referer('wonderplugin-gallery', 'wonderplugin-gallery-settings') )
				{		
					unset($_POST['save-gallery-options']);
					
					$this->controller->save_settings($_POST);
					
					echo '<div class="updated"><p>Settings saved.</p></div>';
				}
								
				$settings = $this->controller->get_settings();
				$userrole = $settings['userrole'];
				$imagesize = $settings['imagesize'];
				$thumbnailsize = $settings['thumbnailsize'];
				$keepdata = $settings['keepdata'];
				$disableupdate = $settings['disableupdate'];
				$supportwidget = $settings['supportwidget'];
				$addjstofooter = $settings['addjstofooter'];
				$jsonstripcslash = $settings['jsonstripcslash'];
				$jetpackdisablelazyload = $settings['jetpackdisablelazyload'];
				$supportmultilingual = $settings['supportmultilingual'];

				?>
				
				<h3>The settings page is only available for users of Administrator role.</h3>						
		        <form method="post">
		        
		        <?php wp_nonce_field('wonderplugin-gallery', 'wonderplugin-gallery-settings'); ?>
		        
		        <table class="wonderplugin-settings-form-table">
		        
		        <tr valign="top">
					<th scope="row">Set minimum user role of plugin administration</th>
					<td>
						<select name="userrole">
						  <option value="Administrator" <?php echo ($userrole == 'manage_options') ? 'selected="selected"' : ''; ?>>Administrator</option>
						  <option value="Editor" <?php echo ($userrole == 'moderate_comments') ? 'selected="selected"' : ''; ?>>Editor</option>
						  <option value="Author" <?php echo ($userrole == 'upload_files') ? 'selected="selected"' : ''; ?>>Author</option>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Select the default image size from Media Library for gallery images</th>
					<td>
						<select name="imagesize">
						<option value="thumbnail" <?php echo ($imagesize == 'thumbnail') ? 'selected="selected"' : ''; ?>>Thumbnail size</option>
						<option value="medium" <?php echo ($imagesize == 'medium') ? 'selected="selected"' : ''; ?>>Medium size</option>
						<option value="large" <?php echo ($imagesize == 'large') ? 'selected="selected"' : ''; ?>>Large size</option>
						<option value="full" <?php echo ($imagesize == 'full') ? 'selected="selected"' : ''; ?>>Full size</option>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Select the default image size from Media Library for gallery thumbnails</th>
					<td>
						<select name="thumbnailsize">
						<option value="thumbnail" <?php echo ($thumbnailsize == 'thumbnail') ? 'selected="selected"' : ''; ?>>Thumbnail size</option>
						<option value="medium" <?php echo ($thumbnailsize == 'medium') ? 'selected="selected"' : ''; ?>>Medium size</option>
						<option value="large" <?php echo ($thumbnailsize == 'large') ? 'selected="selected"' : ''; ?>>Large size</option>
						<option value="full" <?php echo ($thumbnailsize == 'full') ? 'selected="selected"' : ''; ?>>Full size</option>
						</select>
					</td>
				</tr>

				<tr>
					<th>Data option</th>
					<td><label><input name='keepdata' type='checkbox' id='keepdata' <?php echo ($keepdata == 1) ? 'checked' : ''; ?> /> Keep data when deleting the plugin</label>
					</td>
				</tr>
				
				<tr>
					<th>Update option</th>
					<td><label><input name='disableupdate' type='checkbox' id='disableupdate' <?php echo ($disableupdate == 1) ? 'checked' : ''; ?> /> Disable plugin version check and update</label>
					</td>
				</tr>
			
				<tr>
					<th>Display gallery in widget</th>
					<td><label><input name='supportwidget' type='checkbox' id='supportwidget' <?php echo ($supportwidget == 1) ? 'checked' : ''; ?> /> Support shortcode in text widget</label>
					</td>
				</tr>
				
				<tr>
					<th>Scripts position</th>
					<td><label><input name='addjstofooter' type='checkbox' id='addjstofooter' <?php echo ($addjstofooter == 1) ? 'checked' : ''; ?> /> Add plugin js scripts to the footer (wp_footer hook must be implemented by the WordPress theme)</label>
					</td>
				</tr>
				
				<tr>
					<th>JSON options</th>
					<td><label><input name='jsonstripcslash' type='checkbox' id='jsonstripcslash' <?php echo ($jsonstripcslash == 1) ? 'checked' : ''; ?> /> Remove backslashes in JSON string</label>
					</td>
				</tr>
				
				<tr>
					<th>Jetpack image lazy load</th>
					<td><label><input name='jetpackdisablelazyload' type='checkbox' id='jetpackdisablelazyload' <?php echo ($jetpackdisablelazyload == 1) ? 'checked' : ''; ?> /> Disable Jetpack lazy load for gallery images</label>
					</td>
				</tr>

				<tr>
					<th>Multilingual</th>
					<td><label><input name='supportmultilingual' type='checkbox' id='supportmultilingual' <?php echo ($supportmultilingual == 1) ? 'checked' : ''; ?> /> Support WPML (Version 3.2 and above)</label></td>
					</td>
				</tr>

				</table>
		        
		        <p class="submit"><input type="submit" name="save-gallery-options" id="save-gallery-options" class="button button-primary" value="Save Changes"  /></p>
		        
		        </form>
		        
				</div>
				<?php
	}
	
	function print_register() {
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-gallery" class="icon32"><br /></div>
			
		<script>	
		function validateLicenseForm() {
			
			if (jQuery.trim(jQuery("#wonderplugin-gallery-key").val()).length <= 0)
			{
				jQuery("#license-form-message").html("<p>Please enter your license key</p>").show();
				return false;
			}
			
			return true;
		}
		</script>
		
		<h2><?php _e( 'Register', 'wonderplugin_gallery' ); ?></h2>
		<?php
				
		if (isset($_POST['save-gallery-license']) && check_admin_referer('wonderplugin-gallery', 'wonderplugin-gallery-register') )
		{		
			unset($_POST['save-gallery-license']);

			$ret = $this->controller->check_license($_POST);
			
			if ($ret['status'] == 'valid')
				echo '<div class="updated"><p>The key has been saved.</p><p>WordPress caches the update information. If you still see the message "Automatic update is unavailable for this plugin", please wait for some time, then click the below button "Force WordPress To Check For Plugin Updates".</p></div>';
			else if ($ret['status'] == 'expired')
				echo '<div class="error"><p>Your free upgrade period has expired, please renew your license.</p></div>';
			else if ($ret['status'] == 'invalid')
				echo '<div class="error"><p>The key is invalid.</p></div>';
			else if ($ret['status'] == 'abnormal')
				echo '<div class="error"><p>You have reached the maximum website limit of your license key. Please log into the membership area and upgrade to a higher license.</p></div>';
			else if ($ret['status'] == 'misuse')
				echo '<div class="error"><p>There is a possible misuse of your license key, please contact support@wonderplugin.com for more information.</p></div>';
			else if ($ret['status'] == 'timeout')
				echo '<div class="error"><p>The license server can not be reached, please try again later.</p></div>';
			else if ($ret['status'] == 'empty')
				echo '<div class="error"><p>Please enter your license key.</p></div>';
			else if (isset($ret['message']))
				echo '<div class="error"><p>' . $ret['message'] . '</p></div>';
		}
		else if (isset($_POST['deregister-gallery-license']) && check_admin_referer('wonderplugin-gallery', 'wonderplugin-gallery-register') )
		{	
			$ret = $this->controller->deregister_license($_POST);
			
			if ($ret['status'] == 'success')
				echo '<div class="updated"><p>The key has been deregistered.</p></div>';
			else if ($ret['status'] == 'timeout')
				echo '<div class="error"><p>The license server can not be reached, please try again later.</p></div>';
			else if ($ret['status'] == 'empty')
				echo '<div class="error"><p>The license key is empty.</p></div>';
		}
		
		$settings = $this->controller->get_settings();
		$disableupdate = $settings['disableupdate'];
		
		$key = '';
		$info = $this->controller->get_plugin_info();
		if (!empty($info->key) && ($info->key_status == 'valid' || $info->key_status == 'expired'))
			$key = $info->key;
		
		?>
		
		<?php 
		if ($disableupdate == 1)
		{
			echo "<h3 style='padding-left:10px;'>The plugin version check and update is currently disabled. You can enable it in the Settings menu.</h3>";
		}
		else
		{
		?> <div style="padding-left:10px;padding-top:12px;"> <?php
			if (empty($key)) { ?>
				<form method="post" onsubmit="return validateLicenseForm()">
				<?php wp_nonce_field('wonderplugin-gallery', 'wonderplugin-gallery-register'); ?>
				<div class="error" style="display:none;" id="license-form-message"></div>
				<table class="form-table">
				<tr>
					<th>Enter Your License Key:</th>
					<td><input name="wonderplugin-gallery-key" type="text" id="wonderplugin-gallery-key" value="" class="regular-text" /> <input type="submit" name="save-gallery-license" id="save-gallery-license" class="button button-primary" value="Register"  />
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
					<p><strong>By entering your license key and registering your website, you agree to the following terms:</strong></p>
					<ul style="list-style-type:square;margin-left:20px;">
						<li>The key is unique to your account. You may not distribute, give away, lend or re-sell it. We reserve the right to monitor levels of your key usage activity and take any necessary action in the event of abnormal usage being detected.</li>
						<li>By entering your license key and clicking the button "Register", your domain name, the plugin name and the key will be sent to the plugin website <a href="https://www.wonderplugin.com" target="_blank">https://www.wonderplugin.com</a> for verification and registration.</li>
						<li>You can view all your registered domain name(s) and plugin(s) by logging into <a href="https://www.wonderplugin.com/members/" target="_blank">WonderPlugin Members Area</a>, left menu "License Key and Register".</li>
						<li>For more information, please view <a href="https://www.wonderplugin.com/terms-of-use/" target="_blank">Terms of Use</a>.</li>
					</ul>
					<p style="margin:8px 0;">To find your license key, please log into <a href="https://www.wonderplugin.com/members/" target="_blank">WonderPlugin Members Area</a>, then click "License Key and Register" on the left menu.</p>
					<p style="margin:8px 0;">After registration, when there is a new version available and you are in the free upgrade period, you can directly upgrade the plugin in your WordPress dashboard. If you do not register, you can still upgrade the plugin manually: <a href="https://www.wonderplugin.com/wordpress-carousel-plugin/how-to-upgrade-to-a-new-version-without-losing-existing-work/" target="_blank">How to upgrade to a new version without losing existing work</a>.</p>
					</td>
				</tr>
				</table>
				</form>
			<?php } else { ?>
				<form method="post">
				<?php wp_nonce_field('wonderplugin-gallery', 'wonderplugin-gallery-register'); ?>
				<p>You have entered your license key and this domain has been successfully registered. &nbsp;&nbsp;<input name="wonderplugin-gallery-key" type="hidden" id="wonderplugin-gallery-key" value="<?php echo esc_html($key); ?>" class="regular-text" /><input type="submit" name="deregister-gallery-license" id="deregister-gallery-license" class="button button-primary" value="Deregister"  /></p>
				</form>
				<?php if ($info->key_status == 'expired') { ?>
				<p><strong>Your free upgrade period has expired.</strong> To get upgrades, please <a href="https://www.wonderplugin.com/renew/" target="_blank">renew your license</a>.</p>
				<?php } ?>
			<?php } ?>
			</div>
		<?php } ?>
		
		<div style="padding-left:10px;padding-top:30px;">
		<a href="<?php echo admin_url('update-core.php?force-check=1'); ?>"><button class="button-primary">Force WordPress To Check For Plugin Updates</button></a>
		</div>
					
		<div style="padding-left:10px;padding-top:20px;">
        <ul style="list-style-type:square;font-size:16px;line-height:28px;margin-left:24px;">
		<li><a href="https://www.wonderplugin.com/how-to-upgrade-a-commercial-version-plugin-to-the-latest-version/" target="_blank">How to upgrade to the latest version</a></li>
	    <li><a href="https://www.wonderplugin.com/register-faq/" target="_blank">Where can I find my license key and other frequently asked questions</a></li>
	    </ul>
        </div>
        
		</div>
		
		<?php
	}
	
	function print_items() {
		
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-gallery" class="icon32"><br /></div>
			
		<h2><?php _e( 'Manage Galleries', 'wonderplugin_gallery' ); ?> <a href="<?php echo admin_url('admin.php?page=wonderplugin_gallery_add_new'); ?>" class="add-new-h2"> <?php _e( 'New Gallery', 'wonderplugin_gallery' ); ?></a> </h2>
				
		<form id="slider-list-table" method="post">
		<input type="hidden" name="page" value="<?php echo esc_html($_REQUEST['page']); ?>" />
		<?php 
		
		if ( !is_object($this->list_table) )
			$this->list_table = new WonderPlugin_Gallery_List_Table($this);
		
		$this->process_actions();
		
		$this->list_table->list_data = $this->controller->get_list_data();
		$this->list_table->prepare_items();
		$this->list_table->views();
		$this->list_table->display();		
		?>								
        </form>
        
		</div>
		<?php
	}
	
	function print_item()
	{
		if ( !isset( $_REQUEST['itemid'] ) || !is_numeric( $_REQUEST['itemid'] ) )
			return;
		
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-gallery" class="icon32"><br /></div>
					
		<h2><?php _e( 'View Gallery', 'wonderplugin_gallery' ); ?> <a href="<?php echo admin_url('admin.php?page=wonderplugin_gallery_edit_item') . '&itemid=' . $_REQUEST['itemid']; ?>" class="add-new-h2"> <?php _e( 'Edit Gallery', 'wonderplugin_gallery' ); ?>  </a> </h2>
		
		<div class="updated"><p style="text-align:center;">  <?php _e( 'To embed the gallery into your page, use shortcode: ', 'wonderplugin_gallery' ); ?> <?php echo esc_attr('[wonderplugin_gallery id=' . $_REQUEST['itemid'] . ']'); ?></p></div>

		<div class="updated"><p style="text-align:center;">  <?php _e( 'To embed the gallery into your template, use php code: ', 'wonderplugin_gallery' ); ?> <?php echo esc_attr('<?php echo do_shortcode(\'[wonderplugin_gallery id=' . $_REQUEST['itemid'] . ']\'); ?>'); ?></p></div>
		
		<?php
		if (WONDERPLUGIN_GALLERY_VERSION_TYPE !== "C")
			echo '<div class="updated"><p style="text-align:center;">To remove the Free Version watermark, please <a href="https://www.wonderplugin.com/wordpress-gallery/order/" target="_blank">Upgrade to Commercial Version</a>.</p></div>';
		
		echo $this->controller->generate_body_code( $_REQUEST['itemid'], null, null, true ); 
		?>	 
		
		</div>
		<?php
	}
	
	function process_actions()
	{
		if (!isset($_REQUEST['_wpnonce']) || (!wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . $this->list_table->_args['plural']) && !wp_verify_nonce($_REQUEST['_wpnonce'], 'wonderplugin-list-table-nonce')))
			return;
			
		if ( ((isset($_REQUEST['action']) && ($_REQUEST['action'] == 'trash')) || (isset($_REQUEST['action2']) && ($_REQUEST['action2'] == 'trash'))) && isset( $_REQUEST['itemid'] ) )
		{
			$trashed = 0;
	
			if ( is_array( $_REQUEST['itemid'] ) )
			{
				foreach( $_REQUEST['itemid'] as $id)
				{
					if ( is_numeric($id) )
					{
						$ret = $this->controller->trash_item($id);
						if ($ret > 0)
							$trashed += $ret;
					}
				}
			}
			else if ( is_numeric($_REQUEST['itemid']) )
			{
				$trashed = $this->controller->trash_item( $_REQUEST['itemid'] );
			}
	
			if ($trashed > 0)
			{
				echo '<div class="updated"><p>';
				printf( _n('%d gallery moved to the trash.', '%d galleries moved to the trash.', $trashed), $trashed );
				echo '</p></div>';
			}
		}
	
		if ( ((isset($_REQUEST['action']) && ($_REQUEST['action'] == 'restore')) || (isset($_REQUEST['action2']) && ($_REQUEST['action2'] == 'restore'))) && isset( $_REQUEST['itemid'] ) )
		{
			$restored = 0;
	
			if ( is_array( $_REQUEST['itemid'] ) )
			{
				foreach( $_REQUEST['itemid'] as $id)
				{
					if ( is_numeric($id) )
					{
						$ret = $this->controller->restore_item($id);
						if ($ret > 0)
							$restored += $ret;
					}
				}
			}
			else if ( is_numeric($_REQUEST['itemid']) )
			{
				$restored = $this->controller->restore_item( $_REQUEST['itemid'] );
			}
	
			if ($restored > 0)
			{
				echo '<div class="updated"><p>';
				printf( _n('%d gallery restored.', '%d galleries restored.', $restored), $restored );
				echo '</p></div>';
			}
		}
	
		if ( ((isset($_REQUEST['action']) && ($_REQUEST['action'] == 'delete')) || (isset($_REQUEST['action2']) && ($_REQUEST['action2'] == 'delete'))) && isset( $_REQUEST['itemid'] ) )
		{
			$deleted = 0;
				
			if ( is_array( $_REQUEST['itemid'] ) )
			{
				foreach( $_REQUEST['itemid'] as $id)
				{
					if ( is_numeric($id) )
					{
						$ret = $this->controller->delete_item($id);
						if ($ret > 0)
							$deleted += $ret;
					}
				}
			}
			else if ( is_numeric($_REQUEST['itemid']) )
			{
				$deleted = $this->controller->delete_item( $_REQUEST['itemid'] );
			}
				
			if ($deleted > 0)
			{
				echo '<div class="updated"><p>';
				printf( _n('%d gallery deleted.', '%d galleries deleted.', $deleted), $deleted );
				echo '</p></div>';
			}
		}
	
		if ( ((isset($_REQUEST['action']) && ($_REQUEST['action'] == 'clone')) || (isset($_REQUEST['action2']) && ($_REQUEST['action2'] == 'clone'))) && isset( $_REQUEST['itemid'] ) && is_numeric( $_REQUEST['itemid'] ))
		{
			$cloned_id = $this->controller->clone_item( $_REQUEST['itemid'] );
			if ($cloned_id > 0)
			{
				echo '<div class="updated"><p>';
				printf( 'New gallery created with ID: %d', $cloned_id );
				echo '</p></div>';
			}
			else
			{
				echo '<div class="error"><p>';
				printf( 'The gallery cannot be cloned.' );
				echo '</p></div>';
			}
		}
	}

	function print_add_new() {
		
		if ( !empty($_POST['wonderplugin-gallery-save-item-post-value']) && !empty($_POST['wonderplugin-gallery-save-item-post']) && check_admin_referer('wonderplugin-gallery', 'wonderplugin-gallery-saveform') )
		{
			$this->save_item_post($_POST['wonderplugin-gallery-save-item-post-value']);
			return;
		}
		
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-gallery" class="icon32"><br /></div>
			
		<h2><?php _e( 'New Gallery', 'wonderplugin_gallery' ); ?> <a href="<?php echo admin_url('admin.php?page=wonderplugin_gallery_show_items'); ?>" class="add-new-h2"> <?php _e( 'Manage Galleries', 'wonderplugin_gallery' ); ?>  </a> </h2>
		
		<?php 
		$this->creator = new WonderPlugin_Gallery_Creator($this);	
		
		$settings = $this->controller->get_settings();
		echo $this->creator->render( -1, null, $settings['imagesize'], $settings['thumbnailsize']);
	}
	
	function print_edit_item()
	{
		if ( !empty($_POST['wonderplugin-gallery-save-item-post-value']) && !empty($_POST['wonderplugin-gallery-save-item-post']) && check_admin_referer('wonderplugin-gallery', 'wonderplugin-gallery-saveform') )
		{
			$this->save_item_post($_POST['wonderplugin-gallery-save-item-post-value']);
			return;
		}
		
		if ( !isset( $_REQUEST['itemid'] ) || !is_numeric( $_REQUEST['itemid'] ) )
			return;
	
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-gallery" class="icon32"><br /></div>
			
		<h2><?php _e( 'Edit Gallery', 'wonderplugin_gallery' ); ?> <a href="<?php echo admin_url('admin.php?page=wonderplugin_gallery_show_item') . '&itemid=' . $_REQUEST['itemid']; ?>" class="add-new-h2"> <?php _e( 'View Gallery', 'wonderplugin_gallery' ); ?>  </a> </h2>
		
		<?php 
		$this->creator = new WonderPlugin_Gallery_Creator($this);

		$settings = $this->controller->get_settings();
		echo $this->creator->render( $_REQUEST['itemid'], $this->controller->get_item_data($_REQUEST['itemid']), $settings['imagesize'], $settings['thumbnailsize'] );
	}
	
	function save_item_post($item_post) {
	
		$jsonstripcslash = get_option( 'wonderplugin_gallery_jsonstripcslash', 1 );
		if ($jsonstripcslash == 1)
			$json_post = trim(stripcslashes($item_post));
		else
			$json_post = trim($item_post);
		
		$items = json_decode($json_post, true);
			
		if ( empty($items) )
		{
			$json_error = "json_decode error";
			if ( function_exists('json_last_error_msg') )
				$json_error .= ' - ' . json_last_error_msg();
			else if ( function_exists('json_last_error') )
				$json_error .= 'code - ' . json_last_error();
		
			$ret = array(
					"success" => false,
					"id" => -1,
					"message" => $json_error . ". <b>To fix the problem, in the Plugin Settings menu, please uncheck the option Remove backslashes in JSON string and try again.</b>",
					"errorcontent"	=> $json_post
			);
		}
		else
		{
			if (!current_user_can('manage_options'))
			{
				unset($items['customjs']);
			}
			
			add_filter('safe_style_css', 'wonderplugin_gallery_css_allow');
			add_filter('wp_kses_allowed_html', 'wonderplugin_gallery_tags_allow', 'post');
			foreach ($items as $key => &$value)
			{
				if ($key == 'customjs' && current_user_can('manage_options'))
					continue;
				
				if ($value === true)
					$value = "true";
				else if ($value === false)
					$value = "false";
				else if ( is_string($value) )
					$value = wp_kses_post($value);
			}
		
			if (isset($items["slides"]) && count($items["slides"]) > 0)
			{
				foreach ($items["slides"] as $key => &$slide)
				{
					if (!empty($slide['langs']))
						$slide['langs'] = str_replace(array('<', '>'), array('&lt;', '&gt;'), $slide['langs']);
						
					foreach ($slide as $key => &$value)
					{
						if ($value === true)
							$value = "true";
						else if ($value === false)
							$value = "false";
						else if ( is_string($value) )
							$value = wp_kses_post($value);
					}
				}
			}
			remove_filter('wp_kses_allowed_html', 'wonderplugin_gallery_tags_allow', 'post');
			remove_filter('safe_style_css', 'wonderplugin_gallery_css_allow');
			
			$ret = $this->controller->save_item($items);
		}
		?>
				
		<div class="wrap">
		<div id="icon-wonderplugin-gallery" class="icon32"><br /></div>
		
		<?php 
		if (isset($ret['success']) && $ret['success'] && isset($ret['id']) && $ret['id'] >= 0) 
		{
			echo "<h2>Gallery Saved.";
			echo "<a href='" . admin_url('admin.php?page=wonderplugin_gallery_edit_item') . '&itemid=' . $ret['id'] . "' class='add-new-h2'>Edit Gallery</a>";
			echo "<a href='" . admin_url('admin.php?page=wonderplugin_gallery_show_item') . '&itemid=' . $ret['id'] . "' class='add-new-h2'>View Gallery</a>";
			echo "</h2>";
					
			echo "<div class='updated'><p>The gallery has been saved and published.</p></div>";
			echo "<div class='updated'><p>To embed the gallery into your page or post, use shortcode:  [wonderplugin_gallery id=" . $ret['id'] . "]</p></div>";
			echo "<div class='updated'><p>To embed the gallery into your template, use php code:  &lt;?php echo do_shortcode('[wonderplugin_gallery id=" . $ret['id'] . "]'); ?&gt;</p></div>"; 
		}
		else
		{
			echo "<h2>Wonder Gallery</h2>";
			echo "<div class='error'><p>The gallery can not be saved.</p></div>";
			echo "<div class='error'><p>Error Message: " . ((isset($ret['message'])) ? $ret['message'] : "") . "</p></div>";
			echo "<div class='error'><p>Error Content: " . ((isset($ret['errorcontent'])) ? $ret['errorcontent'] : "") . "</p></div>";
		}	
	}
	
	function export_phpinfo() {
		ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_end_clean();
		$phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo);
		echo "
			<style type='text/css'>
				#phpinfo {}
				#phpinfo pre {margin: 0; font-family: monospace;}
				#phpinfo a:link {color: #009; text-decoration: none; background-color: #fff;}
				#phpinfo a:hover {text-decoration: underline;}
				#phpinfo table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
				#phpinfo .center {text-align: center;}
				#phpinfo .center table {margin: 1em auto; text-align: left;}
				#phpinfo .center th {text-align: center !important;}
				#phpinfo td, th {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
				#phpinfo h1 {font-size: 150%;}
				#phpinfo h2 {font-size: 125%;}
				#phpinfo .p {text-align: left;}
				#phpinfo .e {background-color: #ccf; width: 300px; font-weight: bold;}
				#phpinfo .h {background-color: #99c; font-weight: bold;}
				#phpinfo .v {background-color: #ddd; max-width: 300px; overflow-x: auto; word-wrap: break-word;}
				#phpinfo .v i {color: #999;}
				#phpinfo img {float: right; border: 0;}
				#phpinfo hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
			</style>
			<div id='phpinfo'>
				$phpinfo
			</div>
			";
	}

	function import_export()
	{
		?>
		<div class="wrap">
			<div id="icon-wonderplugin-gallery" class="icon32">
				<br />
			</div>

			<h2>
				<?php _e( 'Tools', 'wonderplugin_gallery' ); ?>
			</h2>

			<p>
				<b>This function only imports/exports gallery configurations.
					It does not import/export the media files.</b>
			</p>

			<p>The plugin uses WordPress Media Library to manage media files.
				Please transfer your WordPress Media Library to the new site after
				importing/exporting the gallery.</p>

			<ul class="wonderplugin-tab-buttons-horizontal"
				id="wp-tools-toolbar"
				data-panelsid="wonderplugin-popup-display-panels">
				<li
					class="wonderplugin-tab-button-horizontal wonderplugin-tab-button-horizontal-selected"><span
					class="dashicons dashicons-download" style="margin-right: 8px;"></span>
					<?php _e( 'Import', 'wonderplugin_gallery' ); ?></li>
				<li class="wonderplugin-tab-button-horizontal"><span
					class="dashicons dashicons-upload" style="margin-right: 8px;"></span>
					<?php _e( 'Export', 'wonderplugin_gallery' ); ?></li>
					
				<li class="wonderplugin-tab-button-horizontal"><span class="dashicons dashicons-search" style="margin-right:8px;"></span><?php _e( 'Search and Replace', 'wonderplugin_gallery' ); ?></li>
				<li class="wonderplugin-tab-button-horizontal"><span class="dashicons dashicons-desktop" style="margin-right:8px;"></span><?php _e( 'PHP Info', 'wonderplugin_gallery' ); ?></li>
			</ul>

			<?php 
			$data = $this->controller->get_list_data(true);
			?>
			<ul class="wonderplugin-tabs-horizontal"
				id="wonderplugin-popup-display-panels">
				<li
					class="wonderplugin-tab wonderplugin-tab-horizontal wonderplugin-tab-horizontal-selected">

					<?php 
					if (isset($_POST['wp-import']) && isset($_FILES['importxml']) && check_admin_referer('wonderplugin-gallery', 'wonderplugin-gallery-import'))
						$import_return = $this->controller->import_gallery($_POST, $_FILES);
					?>

					<form method="post" enctype="multipart/form-data">
						<?php wp_nonce_field('wonderplugin-gallery', 'wonderplugin-gallery-import'); ?>
						<?php 
						if (isset($import_return))
							echo '<div class="' . ($import_return['success'] ? 'wonderplugin-updated' : 'wonderplugin-error') . '"><p>' . $import_return['message'] . '</p></div>';
						$users = get_users();
						?>
						<h2>Choose an exported .xml file to upload, then click Upload
							file and import.</h2>
						<div class='wonderplugin-error wonderplugin-error-message'
							id="wp-import-error"></div>
						<input type="file" name="importxml" id="wp-importxml" />
						<p>
							<label><input type="radio" name="keepid" value=1 checked>Keep
								the same gallery ID</label>
						</p>
						<p>
							<label><input type="radio" name="keepid" value=0>Append to the
								existing gallery list </label>
						</p>
						<p>
							Assign to the user: <select name="authorid">
								<?php foreach ( $users as $user ) { ?>
								<option value="<?php echo $user->ID; ?>">
									<?php echo $user->user_login; ?>
								</option>
								<?php } ?>
							</select>
						</p>
						<h3>Search and replace</h3>
						<div class='wonderplugin-error wonderplugin-error-message'
							id="wp-replace-error"></div>
						<div id='wp-search-replace'></div>
						<div id="wp-site-url" style="display: none;"><?php echo get_site_url(); ?></div>
						<button class="button-secondary" id="wp-add-replace-list">Add
							Row</button>
						<p class="submit">
							<input type="submit" name="wp-import" id="wp-import-submit"
								class="button button-primary" value="Upload file and import" />
					
					</form>
				</li>

				<li class="wonderplugin-tab wonderplugin-tab-horizontal"><?php 
				if (empty($data)) {
					echo '<p>No gallery found!</p>';
				} else {
					?>
					<h2>Export to an .xml file.</h2>
					<form method="post"
						action="<?php echo admin_url('admin-post.php?action=wonderplugin_gallery_export'); ?>">
						<?php wp_nonce_field('wonderplugin-gallery', 'wonderplugin-gallery-export'); ?>

						<p>
							<label><input type="radio" name="allgallery" value=1 checked>Export
								all galleries</label>
						</p>
						<p>
							<label><input type="radio" name="allgallery" value=0>Select a
								gallery: </label> <select name="galleryid">
								<?php foreach ($data as $export_item) { ?>
								<option value="<?php echo $export_item['id']; ?>">
									<?php echo 'ID ' . $export_item['id'] . ' : ' . $export_item['name']; ?>
								</option>
								<?php } ?>
							</select>
						</p>
						<p class="submit">
							<input type="submit" name="wp-export"
								class="button button-primary" value="Export" />
							<?php if ( WP_DEBUG ) { ?>
							<span style="margin-left: 12px;">Warning: WP_DEBUG is enabled,
								the function "Export" may not work correctly. Please check
								your WordPress configuration file wp-config.php and change the
								WP_DEBUG to false.</span>
							<?php } ?>
						</p>
					</form> <?php } ?>
				</li>
				
				<li class="wonderplugin-tab wonderplugin-tab-horizontal">
			
				<?php 
	        	if (empty($data)) {
	        		echo '<p>No gallery found!</p>';
	        	} else {
	        	?>
	        	<h2>Search and Replace</h2>
				<form method="post">
	        	<?php wp_nonce_field('wonderplugin-gallery', 'wonderplugin-gallery-search-replace'); ?>
	        	<?php
	        	if (isset($_POST['wp-search-replace-submit']) && check_admin_referer('wonderplugin-gallery', 'wonderplugin-gallery-search-replace'))
					$search_return = $this->controller->search_replace_items($_POST);
				
	        	if (isset($search_return))
	        		echo '<div class="' . ($search_return['success'] ? 'wonderplugin-updated' : 'wonderplugin-error') . '"><p>' . $search_return['message'] . '</p></div>';
	        	?>
	        	<p><label><input type="radio" name="allitems" value=1 checked>Apply to all galleries</label></p>
	        	<p><label><input type="radio" name="allitems" value=0>Select a gallery: </label>
	        	<select name="itemid">
	        	<?php foreach ($data as $item) { ?>
	  				<option value="<?php echo $item['id']; ?>"><?php echo 'ID ' . $item['id'] . ' : ' . $item['name']; ?></option>
	  			<?php } ?>
	  			</select>
	        	</p>
	        	
	        	<h3>Search and replace</h3>
	        	<div class='wonderplugin-error wonderplugin-error-message' id="wp-standalone-replace-error"></div>
	        	<div id='wp-standalone-search-replace'></div>
	        	<button class="button-secondary" id="wp-add-standalone-replace-list">Add Row</button>
	        	<p class="submit"><input type="submit" name="wp-search-replace-submit" id="wp-search-replace-submit" class="button button-primary" value="Search and Replace"  />
	        	</p>
				</form>	
				<?php } ?>
				</li>

				<li class="wonderplugin-tab wonderplugin-tab-horizontal">
				<h2>PHP Info</k2>
				<div id="phpinfo">
				<?php $this->export_phpinfo(); ?>
				</div>
				</li>
			</ul>

		</div>
		<?php
	}
}