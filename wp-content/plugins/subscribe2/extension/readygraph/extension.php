<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
// ReadyGraph Extension
//

function s2_readygraph_client_script_head() {
	if(!get_option('readygraph_application_id') && strlen(get_option('readygraph_application_id')) < 0) return;
	if (get_option('readygraph_enable_branding', '') == 'false') {
		echo '<style>/* FOR INLINE WIDGET */.rgw-text {display: none !important;}</style>';
	}
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	?>	
<script type='text/javascript'>
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
var d = top.document;
var h = d.getElementsByTagName('head')[0], script = d.createElement('script');
script.type = 'text/javascript';
script.src = '//cdn.readygraph.com/scripts/readygraph.js';
script.onload = function(e) {
  var settings = <?php echo str_replace("\\\"", "\"", get_option('readygraph_settings', '{}')) ?>;
  settings['applicationId'] = '<?php echo get_option('readygraph_application_id', '') ?>';
  settings['overrideFacebookSDK'] = true;
  settings['platform'] = 'others';
  settings['enableLoginWall'] = <?php echo get_option('readygraph_enable_popup', 'false') ?>;
  settings['enableSidebar'] = <?php echo get_option('readygraph_enable_sidebar', 'false') ?>;
	settings['inviteFlowDelay'] = <?php echo get_option('readygraph_delay', '5000') ?>;
	settings['enableNotification'] = <?php echo get_option('readygraph_enable_notification', 'false') ?>;
	settings['inviteAutoSelectAll'] = <?php echo get_option('readygraph_auto_select_all', 'true') ?>;
	top.readygraph.setup(settings);
	readygraph.ready(function() {
		readygraph.framework.require(['auth', 'invite', 'compact.sdk'], function() {
			function process(userInfo) {
				var rg_email = userInfo.get('email');
				var first_name = userInfo.get('first_name');
				var last_name = userInfo.get('last_name');
				<?php if ( is_plugin_active( 'subscribe2/subscribe2.php' ) ) { ?>
				jQuery.post(ajaxurl,
				{
					action : 's2-myajax-submit',
					email : rg_email
				},function(){});
				<?php } ?>
				<?php if ( is_plugin_active( 'email-newsletter/email-newsletter.php' ) ) { ?>
				jQuery.post(ajaxurl,
				{
					action : 'ee-myajax-submit',
					email : rg_email
				},function(){});
				<?php } ?>
				<?php if ( is_plugin_active( 'simple-subscribe/SimpleSubsribe.php' ) ) { ?>
				jQuery.post(ajaxurl,
				{
					action : 'ss-myajax-submit',
					email : rg_email,
					fname : first_name,
					lname : last_name
				},function(){});
				<?php } ?>
				<?php if ( is_plugin_active( 'simple-contact-form/simple-contact-form.php' ) ) { ?>
				jQuery.post(ajaxurl,
				{
					action : 'gCF-myajax-submit',
					email : rg_email,
					name : first_name
				},function(){});
				<?php } ?>
				}
			readygraph.framework.authentication.getUserInfo(function(userInfo) {
				if (userInfo.get('uid') != null) {
					process(userInfo);
				}
				else {
					userInfo.on('change:fb_access_token change:rg_access_token', function() {
						readygraph.framework.authentication.getUserInfo(function(userInfo) {
							process(userInfo);
						});
					});
				}
			}, true);
		});
	});
}
h.appendChild(script);
</script>
<?php } ?>