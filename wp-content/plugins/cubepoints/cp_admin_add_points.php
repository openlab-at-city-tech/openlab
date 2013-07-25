<?php
/**
 * CubePoints admin page: add_points
 */

function cp_admin_add_points()
{

?>

	<div class="wrap">
		<h2>CubePoints - <?php _e('Add Points', 'cp'); ?></h2>
		<?php _e('Manually award points to your users.', 'cp'); ?><br /><br />

		<form method="post" name="cp_add_points_form" id="cp_add_points_form">
			<div id="cp_add_points_user_label"><?php _e('Search for user', 'cp'); ?>:</strong> <input type="text" name="cp_add_points_user" id="cp_add_points_user" value="" autocomplete="off" /> <input type="button" value="<?php _e('Search', 'cp'); ?> &raquo;" id="cp_add_points_search" /></div>
		</form>
			<br /><br />
			<div id="cp_add_points_results">
				<div id="cp_add_points_loading"><?php _e('Loading', 'cp'); ?>...</div>
				<div id="cp_add_points_error"><strong><?php _e('Error', 'cp'); ?>:</strong> <i><?php _e('The user you are looking for could not be found.', 'cp'); ?></i></div>
			
				<div id="cp_add_points_modify">
					<div style="background-image:url(https://secure.gravatar.com/avatar/?s=50);" id="cp_add_points_modify_details">
						<div id="cp_add_points_modify_userid"></div>
						<div id="cp_add_points_modify_email"></div>
					</div>
					
					<form name="cp_add_points_update_form" id="cp_add_points_update_form">
					
						
						<div id="cp_add_points_update_success">
							<strong><?php _e('Updated', 'cp'); ?>:</strong> <i><?php _e('The points for this user has been updated!', 'cp'); ?></i>
						</div>
						
						<div id="cp_add_points_update_error">
							<div id="cp_add_points_update_error_points"><strong><?php _e('Error', 'cp'); ?>:</strong> <i><?php _e('Please enter a valid number of points.', 'cp'); ?></i></div>
							<div id="cp_add_points_update_error_description"><strong><?php _e('Error', 'cp'); ?>:</strong> <i><?php _e('Please enter a description to be shown in the logs.', 'cp'); ?></i></div>
						</div>
						
					
						<input type="hidden" name="cp_add_points_update_form_id" id="cp_add_points_update_form_id" />
						
						<div class="cp_add_points_field_container"><span id="cp_add_points_modify_points_label"><?php _e('Current points', 'cp'); ?>:</span> <span id="cp_add_points_modify_points"></span></div>
						<div class="cp_add_points_field_container"><label for="cp_add_points_update_form_points"><?php _e('Points to add or subtract', 'cp'); ?>:</label> <input type="text" name="cp_add_points_update_form_points" id="cp_add_points_update_form_points" value="" /></div>
						<div class="cp_add_points_field_container"><label for="cp_add_points_update_form_description"><?php _e('Description to be shown in the log', 'cp'); ?>:</label> <input type="text" name="cp_add_points_update_form_description" id="cp_add_points_update_form_description" value="" /></div>
						<div class="cp_add_points_submit_container"><input type="submit" name="cp_add_points_update_form_submit" id="cp_add_points_update_form_submit" value="Update Points &raquo;" /></div>
					</form>
					
				</div>
			</div>
		
	</div>
	
	<script type="text/javascript">
		jQuery("#cp_add_points_form").submit(function(){
			cp_add_points_search();
			return false;
		});
		jQuery("#cp_add_points_user").autocomplete({
			url: ajaxurl,
			extraParams: { action: 'cp_add_points_user_suggest' },
			matchSubset: 0,
			showResult: function(value, data) {
				return '<div class="cp_add_points_user_suggest_result" style="background-image:url(https://secure.gravatar.com/avatar/'+data[3]+'?s=25);"><span class="cp_add_points_user_suggest_name">'+value+'</span><br /><span class="cp_add_points_user_suggest_email">'+data[2]+'</span></div>';
			},
			onItemSelect: function(item) {
				cp_add_points_search();
			}
		});
		function cp_add_points_search(){
			q = jQuery('#cp_add_points_user').val();
			if(q==''){
				jQuery('#cp_add_points_user').focus();
			}
			else{
				cp_add_points_search_go(q);
				location.href="#" + escape(q);
			}
		}
		function cp_add_points_search_go(q){
			jQuery('#cp_add_points_user').blur();
			jQuery('.acResults').hide();
			jQuery('#cp_add_points_update_success').hide();
			jQuery('#cp_add_points_update_error').hide();
			jQuery('#cp_add_points_loading').show(100);
			jQuery('#cp_add_points_error').hide(100);
			jQuery('#cp_add_points_modify').hide(100);
			if( this.cp_add_points_ajax_query != null ){
				this.cp_add_points_ajax_query.abort();
			}
			this.cp_add_points_ajax_query = jQuery.ajax({
                url: ajaxurl,
				data: { 'q': q, 'action': 'cp_add_points_user_query' },
				cache: false,
                success: function(data){
					jQuery('.acResults').hide();
					jQuery('#cp_add_points_loading').hide(100);
					if(data.id == null){
						// no users found
						jQuery('#cp_add_points_error').show(100);
					}
					else{
						// user found
						jQuery('#cp_add_points_modify_userid').html(data.user_login);
						jQuery('#cp_add_points_modify_email').html(data.email);
						jQuery('#cp_add_points_modify_points').html(data.points);
						jQuery('#cp_add_points_modify_details').css('background-image', 'url(https://secure.gravatar.com/avatar/'+data.hash+'?s=50)');
						jQuery('#cp_add_points_update_form_id').val(data.id);
						
						jQuery('#cp_add_points_modify').show(100);
						jQuery('#cp_add_points_error').hide(100);
					}
				}
            });
		}
		jQuery('#cp_add_points_search').click(function(){cp_add_points_search();});
		
		jQuery("#cp_add_points_update_form").submit(function(){
			cp_add_points_submit();
			return false;
		});
		
		function cp_add_points_submit(){
			jQuery('#cp_add_points_update_success').hide(100);
			id = jQuery('#cp_add_points_update_form_id').val();
			points = jQuery('#cp_add_points_update_form_points').val();
			description = jQuery('#cp_add_points_update_form_description').val();
			hasErrors = false;
			if(isNaN(points)||points==''||parseInt(points)!=points||points==0){
				jQuery('#cp_add_points_update_error_points').show();
				hasErrors = true;
			}
			else{
				jQuery('#cp_add_points_update_error_points').hide();
			}
			if(description==''){
				jQuery('#cp_add_points_update_error_description').show();
				hasErrors = true;
			}
			else{
				jQuery('#cp_add_points_update_error_description').hide();
			}
			if(hasErrors){
				jQuery('#cp_add_points_update_error').show(100);
			}
			else{
				jQuery('#cp_add_points_update_error').hide(100);
			}
			
			if(!hasErrors){
				// posting data to server
				jQuery('#cp_add_points_modify').hide(100);
				jQuery('#cp_add_points_loading').show(100);
				this.cp_add_points_ajax_query = jQuery.ajax({
					url: ajaxurl,
					type: "POST",
					data: { 'id': id, 'points': points, 'description': description, 'action': 'cp_add_points_user_update' },
					success: function(data){
						if(data.status != 'ok'){
							// something went wrong
							jQuery('#cp_add_points_loading').hide(100);
							alert('Something went wrong! Please try again later!');
							jQuery('#cp_add_points_modify').show(100);
						}
						else{
							jQuery('#cp_add_points_modify_points').html(data.newpoints);
							jQuery('#cp_add_points_modify').show(100);
							jQuery('#cp_add_points_update_success').show(100);
							jQuery('#cp_add_points_loading').hide(100);
							jQuery('#cp_add_points_update_form_points').val('');
							jQuery('#cp_add_points_update_form_description').val('');
						}
					}
				});
			}			
		}
		
	   if (location.href.indexOf("#") != -1) {
			cp_add_points_search_go(location.href.substr(location.href.indexOf("#")+1));
		}

	</script>
	
	<?php do_action('cp_admin_add_points'); ?>
	
<?php
}
?>