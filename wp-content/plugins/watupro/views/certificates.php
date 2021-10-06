<div class="wrap watupro-wrap">
	<h1><?php _e('Watu PRO Certificates', 'watupro')?></h1>

	<p><?php _e('These certificates are optional and can be assigned to grades. Then when user takes an exam and receives a grade which has assigned certificate, they will see a link to print this certificate, optionally personalized with their details.', 'watupro')?></p>
		
	<p><?php _e('Need help designing the certificates? Here are some <a href="http://blog.calendarscripts.info/free-certificate-templates-for-watupro/" target="_blank">free templates</a> made for you by our designer. If you need more individual design we can discuss it.', 'watupro')?></p>	
	
	<p><b><?php printf(__('Not sure what to do? See <a href="%s" target="_blank">this easy tutorial</a>.', 'watupro'), 'http://blog.calendarscripts.info/using-certificates-in-watupro/');?> </b></p>
	
	<p><a href="admin.php?page=watupro_certificates&do=add"><?php _e('Click here to add a new certificate', 'watupro')?></a></p>

	<?php if(count($certificates)):?>
		<table class="widefat">
			<thead>
		<tr><th><?php _e('ID','watupro');?></th><th><?php _e('Certificate Title', 'watupro')?></th><th><?php _e('Users earned', 'watupro')?></th><th><?php _e('Edit', 'watupro')?></th></tr>
		</thead>
		
		<tbody id="the-list">
		<?php foreach($certificates as $certificate):
		$class = ('alternate' == @$class) ? '' : 'alternate';?>
		<tr class="<?php echo $class?>">
		<td><?php echo $certificate->ID?></td>
		<td><a href="<?php echo site_url('?watupro_view_certificate=1&id='.$certificate->ID)?>" target="_blank"><?php echo apply_filters('watupro_content', $certificate->title);?></a></td>
		<td><a href="admin.php?page=watupro_user_certificates&id=<?php echo $certificate->ID?>"><?php _e('View/Manage', 'watupro')?></a>
		| <a href="admin.php?page=watupro_award_certificate&id=<?php echo $certificate->ID?>"><?php _e('Manually award', 'watupro');?></a>
		<?php if(watupro_intel() and $certificate->fee > 0):?>
		| <a href="admin.php?page=watupro_certificate_payments&certificate_id=<?php echo $certificate->ID?>"><?php _e('View payments', 'watupro');?></a>
		<?php endif;?></td>		
		<td><a href="admin.php?page=watupro_certificates&do=edit&id=<?php echo $certificate->ID?>"><?php _e('Edit', 'watupro')?></a></td></tr>
		<?php endforeach;?>
		</tbody>
		</table>
	<?php endif;?>
	
	<form method="post">
	<p><input type="checkbox" name="no_rtf" value="1" <?php if(get_option('watupro_certificates_no_rtf') == '1') echo 'checked'?>> <?php _e('Do not use rich text editor on certificates (to prevent it from messing my certificate HTML code).', 'watupro')?>  </p>		
	
	<p><input type="checkbox" name="multiple_certificates" value="1" <?php if(get_option('watupro_multiple_certificates') == '1') echo 'checked'?>> <?php _e('Keep all user certificates from multiple quiz attempts. When this option is NOT checked, a certificate earned on a quiz will overwrite any previous certificates from the same user on the same quiz.', 'watupro')?>  </p>	
	
	<p><input type="checkbox" name="public_certificates" value="1" <?php if(get_option('watupro_public_certificates') == '1') echo 'checked'?>> <?php _e('Allow public access to all certificates.', 'watupro')?>  </p>	
	
	<p><input type="checkbox" name="generate_pdf_certificates" value="1" <?php if($generate_pdf_certificates) echo 'checked'?> onclick="this.checked ? jQuery('#watuproPDFOptions, #watuproPDFBridge').show() : jQuery('#watuproPDFOptions, #watuproDocRaptor').hide();"> <?php _e('Generate PDF Certificates instead of HTML based ones. (You need a DocRaptor account or the free pdf-bridge plugin for this)', 'watupro')?> <input type="submit" name="save_pdf_settings" value="<?php _e('Save these settings', 'watupro')?>" class="button-primary"></p>
	
	<div id="watuproPDFOptions" style='padding:10px;display:<?php echo $generate_pdf_certificates ? 'block' : 'none';?> '>
		<p><input type="radio" name="pdf_engine" value="pdf-bridge" <?php if(!empty($pdf_engine) and $pdf_engine=='pdf-bridge') echo 'checked'?> onclick="watuPROChangeEngine('pdf-bridge');"> <?php printf(__('Use the free <a href="%s" target="_blank">pdf bridge</a> plugin (must be installed and activated).', 'watupro'), "http://blog.calendarscripts.info/using-the-free-pdf-bridge-plugin-in-watupro/")?></p>
		
			
		<div id="watuproPDFBridge" style='display:<?php echo ($generate_pdf_certificates and @$pdf_engine == 'pdf-bridge') ? 'block' : 'none'?>'>
			<p><input type="checkbox" name="attach_certificates" value="1" <?php if(get_option('watupro_attach_certificates') == 1) echo 'checked'?>> <?php _e('Send certificate as attachment if quiz completion email is sent to admin and / or user.', 'watupro');?></p>
		</div>
		
		<p><input type="radio" name="pdf_engine" value="docraptor" <?php if(empty($pdf_engine) or $pdf_engine=='docraptor') echo 'checked'?> onclick="watuPROChangeEngine('docraptor');"> <?php _e('Use DocRaptor.', 'watupro');?></p>
	
		
		<div id="watuproDocRaptor" style='display:<?php echo ($generate_pdf_certificates and @$pdf_engine != 'pdf-bridge') ? 'block' : 'none'?>'>
			<p><?php _e('Your DocRaptor API Key:', 'watupro')?> <input type="text" name="docraptor_key">  <?php if(!empty($docraptor_key)): _e("(The key has been saved. It's never shown here for security reasons! Enter something only if you want to add it or change it.)", 'watupro'); endif;?></p>
			<p><?php _e('DocRaptor Mode:', 'watupro')?> <select name="docraptor_test_mode">
				<option value="1" <?php if($docraptor_test_mode) echo 'selected'?>><?php _e('Test', 'watupro')?></option>		
				<option value="0" <?php if(!$docraptor_test_mode) echo 'selected'?>><?php _e('Production', 'watupro')?></option>
			</select></p>
			<p><?php _e('Note: to avoid wasting your document limit with DocRaptor each certificate is generated only once for each user. This means that if you make changes to the certificate will be applied only if the user is issued the certificate again. This can happen if they re-take the quiz or if they take another quiz that issues the same certificate.', 'watupro')?></p>
		</div>
	</div>
	</form>
</div>

<script type="text/javascript" >
function watuPROChangeEngine(engine) {
	if(engine == 'pdf-bridge') {
		jQuery('#watuproDocRaptor').hide();
		jQuery('#watuproPDFBridge').show();
	}
	else {
		jQuery('#watuproDocRaptor').show();
		jQuery('#watuproPDFBridge').hide();
	}
}
</script>