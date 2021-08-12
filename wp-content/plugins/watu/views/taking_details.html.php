<link type="text/css" rel="stylesheet" href="<?php echo WATU_URL.'/style.css' ?>" />
<div class="wrap">
	<h2><?php _e('Details for taken exam ', 'watu')?>"<?php echo $exam->name?>"</h2>
	<?php if(current_user_can('manage_options')):?>
		<p><?php _e('User:', 'watu')?> <?php echo $taking->user_id?"<a href='user-edit.php?user_id=".$taking->user_id."&wp_http_referer=".urlencode("admin.php?page=watu_takings&exam_id=".$exam->ID)."' target='_blank'>".$student->display_name."</a>":__("<b>N/A</b>", 'watu')?></p>
		<?php if(!empty($taking->email)):?>
			<p><?php printf(__('Email: %s', 'watu'), $taking->email);?></p>
	<?php endif; // end if email not empty 
	endif; // end if manager?>
	<p><?php _e('Date:', 'watu')?> <?php echo date(get_option('date_format'), strtotime($taking->date)) ?></p>
	<p><?php _e('Total points collected:', 'watu')?> <b><?php echo $taking->points;?></b></p>
	<p><?php _e('Achieved grade:', 'watu')?> <b><?php echo $taking->result;?></b></p>
	
	<?php if(empty($_GET['export'])):?>
		<p><?php _e('The textual details below show exact snapshot of the questions in the way that student have seen them when taking the exam. If you have added, edited or deleted questions since then you will not see these changes here.', 'watu')?></p>		
	<?php endif; // end if not export?>	
	
	<?php if(empty($_GET['export'])):?>
	<div id="detailsText" style="background:#EEE;padding:5px;">	
	<p><?php echo stripslashes($taking->snapshot); ?></p>
	</div>
	<?php endif;?>	
</div>