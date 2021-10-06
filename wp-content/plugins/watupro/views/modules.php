<div class="wrap watupro-wrap">
	<h1><?php _e('Watu PRO Modules', 'watupro')?></h1>
	
	<?php if(!empty($_POST['upload'])):?>
		<p><?php _e('If you have just uploaded a module, please reactivate WatuPRO from the Wordpress plugins page.', 'watupro')?></p>
	<?php endif;?>
	
	<h2><?php _e("Currently available modules", 'watupro')?></h2>
	
	<ul>
		 <li><strong><?php _e('Intelligence module -', 'watupro')?></strong> <?php echo watupro_intel()?__("Installed", 'watupro'):__("Not installed", 'watupro')?></li>
		 <li><strong><?php _e('Reporting module -', 'watupro')?></strong> <?php echo watupro_module('reports')?__("Installed", 'watupro'):__("Not installed", 'watupro')?></li>
	</ul>
	
	<p><a href="http://calendarscripts.info/watupro/modules.html" target="_blank"><?php _e('For more info about the additional modules please click here.', 'watupro')?></a></p>
	
	<p><?php printf(__('Check also our <a href="%s" target="_blank">free add-ons and bridges</a>.', 'watupro'), 'http://calendarscripts.info/watupro/bridges.html');?></p>
	
	<?php if(!get_option('watupro_sandbox_mode')):?>
		<h2><?php _e('Upload new module', 'watupro')?></h2>
		
		<p><?php _e('Please upload only module files purchased from the', 'watupro')?> <a href="http://calendarscripts.info/watupro" target="_blank"><?php _e('official WatuPRO site.', 'watupro')?></a></p>
		
		<form method="post" enctype="multipart/form-data">
			<p><label>Module file:</label> <input type="file" name="module"></p>
			<input type="submit" value="<?php _e('Upload File', 'watupro');?>" class="button-primary">
			<input type="hidden" name="upload" value="1">
			<?php wp_nonce_field('watupro_modules_nonce');?>	
		</form>
	<?php else:?>
		<p><strong><?php _e("Uploading modules is disabled on this blog for security reasons", 'watupro')?></strong></p>	
	<?php endif;?>
	
	<p><?php _e("If you wish to disable the module upload feature for security reasons, please manually enter 'watupro_sandbox_mode' in your wp_options table. Although in general you shouldn't give your administrator's login details to any person you don't trust.", 'watupro')?></p>
</div>	