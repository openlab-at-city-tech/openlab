<table class="watupro_buttons" id="watuPROButtons<?php echo $exam->ID?>"><tr>	
	<?php if(empty($exam->disallow_previous_button)):?>
		<td id="prev-question" style="display:none;"><input type="button" value="&lt; <?php echo _wtpt(__('Previous', 'watupro')); ?>" onclick="WatuPRO.nextQuestion(event, 'previous');"/></td>
	<?php else: // to prevent JS error just output empty hidden field?><input type="hidden" id="prev-question"><?php endif;?>
  <?php if($exam->single_page == WATUPRO_PAGINATE_ONE_PER_PAGE):?><td id="next-question"><input type="button" value="<?php echo  _wtpt(__('Next', 'watupro')) ?> &gt;" onclick="WatuPRO.nextQuestion(event);" /></td><?php endif;?>
  <?php if($exam->live_result and $exam->single_page==WATUPRO_PAGINATE_ONE_PER_PAGE):?> <td><input type="button" id="liveResultBtn" value="<?php _e('See Answer', 'watupro')?>" onclick="WatuPRO.liveResult();"></td><?php endif;?>
  <?php if( ($single_page==WATUPRO_PAGINATE_PAGE_PER_CATEGORY and $num_cats>1 and $exam->group_by_cat)
  	or ($single_page == WATUPRO_PAGINATE_CUSTOM_NUMBER and $num_pages>1)):?>
  	<?php if(empty($exam->disallow_previous_button)):?><td style="display:none;" id="watuproPrevCatButton"><input type="button" onclick="WatuPRO.nextCategory(<?php echo $num_cats?>, false);" value="<?php echo _wtpt(__('Previous page', 'watupro'));?>"></td><?php endif;?><td id="watuproNextCatButton"><input type="button" onclick="WatuPRO.nextCategory(<?php echo $num_cats?>, true);" value="<?php echo _wtpt(__('Next page', 'watupro'));?>"></td> 
  <?php endif; // endif paginate per category ?>
  <?php if(($exam->single_page or $exam->store_progress == 0) and is_user_logged_in() and $exam->enable_save_button):?>
  	<td><input type="button" name="action" onclick="WatuPRO.saveResult(event)" id="save-button" value="<?php _e('Save', 'watupro') ?>" /></td>
  <?php endif;?>
	<td><?php if(empty($exam->no_ajax)):?><input type="button" name="action" onclick="WatuPRO.submitResult(event)" id="action-button" value="<?php echo empty($advanced_settings['submit_button_value']) ? _wtpt(__('Submit', 'watupro')) : $advanced_settings['submit_button_value']; ?>" <?php echo $submit_button_style?> />
	<?php else:?>
		<input type="submit" name="submit_no_ajax" id="action-button" value="<?php echo empty($advanced_settings['submit_button_value']) ? _wtpt(__('Submit', 'watupro')) : $advanced_settings['submit_button_value']; ?>" <?php echo $submit_button_style?>/>
	<?php endif;?></td>
	</tr></table>