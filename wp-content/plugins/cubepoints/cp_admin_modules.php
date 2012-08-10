<?php
/**
 * CubePoints admin page: modules
 */


/** Make sure plugins get activated before page loads */
function cp_admin_module_process(){
	if( isset($_POST['cp_module_activate']) && isset($_POST['cp_module_name']) ){
		cp_module_activation_set( $_POST['cp_module_activate'] , true );
	}
	if( isset($_POST['cp_module_deactivate']) && isset($_POST['cp_module_name'])){
		cp_module_activation_set( $_POST['cp_module_deactivate'] , false );
	}
}
add_action('plugins_loaded','cp_admin_module_process',1);

/** Do activation and deactivation hooks */
function cp_admin_module_hooks(){
	if( isset($_POST['cp_module_activate']) && isset($_POST['cp_module_name'])){
		do_action('cp_module_'.$_POST['cp_module_activate'].'_activate');
	}
	if( isset($_POST['cp_module_deactivate']) && isset($_POST['cp_module_name'])){
		do_action('cp_module_'.$_POST['cp_module_deactivate'].'_deactivate');
	}
}
add_action('init','cp_admin_module_hooks',1);

function cp_admin_modules()
{
?>

	<div class="wrap">
		<h2>CubePoints - <?php _e('Modules', 'cp'); ?></h2>
		<?php _e('View installed CubePoints modules!', 'cp'); ?><br /><br />
		
		<?php
			if( isset($_POST['cp_module_activate']) && isset($_POST['cp_module_name']) ){
				echo '<div class="updated"><p><strong>'.__('Module', 'cp').' "'.$_POST['cp_module_name'].'"'.__(' activated','cp').'!</strong></p></div>';
			}
			if( isset($_POST['cp_module_deactivate']) && isset($_POST['cp_module_name']) ){
				echo '<div class="updated"><p><strong>'.__('Module', 'cp').' "'.$_POST['cp_module_name'].'"'.__(' deactivated','cp').'!</strong></p></div>';
			}
		?>
		
		<table id="cp_modules_table" class="widefat datatables">
			<thead><tr><th scope="col" width="150"><?php _e('Module','cp'); ?></th><th scope="col"><?php _e('Description','cp'); ?></th><th scope="col" width="80"><?php _e('Version','cp'); ?></th><th scope="col" width="70"><?php _e('Action','cp'); ?></th></tr></thead>
			<tfoot><tr><th scope="col"><?php _e('Module','cp'); ?></th><th scope="col"><?php _e('Description','cp'); ?></th><th scope="col"><?php _e('Version','cp'); ?></th><th scope="col"><?php _e('Action','cp'); ?></th></tr></tfoot>
		
			<?php
			global $cp_module;
			if(count($cp_module)==0){ $cp_module = array(); }
			foreach($cp_module as $m){
			if($m['can_deactivate']){
				if(cp_module_activated($m['id'])){
					$action = '<a href="admin.php?page=cp_admin_modules&cp_module_deactivate='.$m['id'].'">'.__('Deactivate', 'cp').'</a>';
					$action = '<form method="post" name="cp_modules_form_'.$m['id'].'"><input type="hidden" name="cp_module_deactivate" value="'.$m['id'].'" /><input type="hidden" name="cp_module_name" value="'.$m['module'].'" /><a href="javascript:void(0)" onclick="document.cp_modules_form_'.$m['id'].'.submit();">'.__('Deactivate', 'cp').'</a></form>';
				}
				else{
					$action = '<a href="admin.php?page=cp_admin_modules&cp_module_activate='.$m['id'].'">'.__('Activate', 'cp').'</a>';
					$action = '<form method="post" name="cp_modules_form_'.$m['id'].'"><input type="hidden" name="cp_module_activate" value="'.$m['id'].'" /><input type="hidden" name="cp_module_name" value="'.$m['module'].'" /><a href="javascript:void(0)" onclick="document.cp_modules_form_'.$m['id'].'.submit();">'.__('Activate', 'cp').'</a></form>';
				}
			}
			else{
				$action ='<a href="javascript:void(0);" onclick="alert(\''.__('This module cannot be deactivated through this page.', 'cp').'\n'.__('To deactive this module, remove it manually', 'cp').'.\')">'.__('Deactivate', 'cp').'</a>';
			}
			if($m['plugin_url']!=''){
				$mname = '<a href="' . $m['plugin_url'] . '">' . $m['module'] . '</a>';
			}
			else{
				$mname = $m['module'];
			}
			if($m['author']!=''){
				if($m['author_url']!=''){
					$author = ' | '.__('By', 'cp').' <a href="' . $m['author_url'] . '">' . $m['author'] . '</a>';
				}
				else{
				$author = ' | '.__('By', 'cp') . ' ' . $m['author'];
				}
			}
			?>
				<tr>
					<td><?php echo $mname; ?></td>
					<td><?php echo $m['description'] . $author; ?></td>
					<td><?php echo $m['version']; ?></td>
					<td><?php echo $action; ?></td>
				</tr>
			<?php
			}
			?>
		</table>
		
	</div>
	
	<br /><br />
	
	<?php cp_donate_html(); ?>
	
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#cp_modules_table').dataTable({
				"bPaginate": false,
				"aoColumns": [	{ "bSortable": false },
								{ "bSortable": false },
								{ "bSortable": false },
								{ "bSortable": false, "bSearchable": false }
							 ]
				});
		} );
		</script>
	
	<?php do_action('cp_admin_modules'); ?>
	
<?php
}
?>