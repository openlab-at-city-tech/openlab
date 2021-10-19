<div class="wrap">
	<h1><?php printf(__('Schedules for %1$s %2$s', 'watupro'), WATUPRO_QUIZ_WORD, stripslashes($quiz->name));?></h1>
	
	<?php if(!empty($error)):?>
		<p class="error watupro-error"><?php echo $error;?></p>
	<?php endif;?>
	
	<h2><?php _e('Add New Schedule', 'watupro');?></h2>
	<form method="post" onsubmit="return validateScheduleForm(this);">
		<p>
			<label><?php _e('User login or email address:', 'watupro');?> <input type="text" name="login"></label>			
			<label><?php _e('Schedule from:', 'watupro')?></label> &nbsp;
	                <input type="text" name="schedule_from" class="watuproDatePicker">
	                &nbsp;
	                <select name="schedule_from_hour">
	                    <?php $i=0;
	                    while ($i<24): ?>
	                        <option value="<?php echo $i?>"><?php printf("%02d", $i); ?></option>
	                    <?php  $i++;
	                    endwhile; ?>
	                    
	                </select>:
	                
	                <select name="schedule_from_minute">
	                    <?php $i=0;
	                    while ($i<60):  ?>
	                        <option value="<?php echo $i?>"><?php printf("%02d", $i)?></option>
	                    <?php $i++;
	                    endwhile; ?>
	                    
	                </select>
							
						 &nbsp;&nbsp;&nbsp;	
	                
	                <label><?php _e('Schedule to:', 'watupro')?></label> &emsp;
	                <input type="text" name="schedule_to" class="watuproDatePicker">
	                &nbsp;
	                <select name="schedule_to_hour">
	                    <?php $i=0;
	                    while ($i<24):?>
	                        <option value="<?php echo $i?>"><?php printf("%02d", $i); ?></option>
	                    <?php $i++;
	                    endwhile; ?>
	                    
	                </select>:
	                
	                <select name="schedule_to_minute">
	                    <?php $i=0;
	                    while ($i<60): ?>
	                        <option value="<?php echo $i?>"><?php printf("%02d", $i)?></option>
	                    <?php $i++;
	                    endwhile; ?>                    
	                </select>
	                
	              <input type="submit" value="<?php _e('Add this schedule', 'watupro');?>" class="button button-primary"> 
	              <input type="hidden" name="ok" value="1">
	              <?php wp_nonce_field('watupro_schedules');?>
		</p>
	</form>
	
	<?php if(!empty($schedules) and count($schedules)):
		echo '<h2>'.__('Existing schedules', 'watupro').'</h2>';
		foreach($schedules as $schedule):
		$from_parts = explode(' ', $schedule['from']);
		$to_parts = explode(' ', $schedule['to']);?>
		<form method="post">
		<p>
			<label><?php echo $schedule['userdata'];?></label>			
			<label><?php _e('Schedule from:', 'watupro')?></label> &nbsp;
	                <input type="text" name="schedule_from" class="watuproDatePicker" value="<?php echo $from_parts[0]?>">
	                &nbsp;
	                <select name="schedule_from_hour">
	                    <?php $i=0;
	                    while ($i<24): ?>
	                        <option value="<?php echo $i?>" <?php if(date("G",strtotime($schedule['from']))==$i) echo "selected"?>><?php printf("%02d", $i); ?></option>
	                    <?php  $i++;
	                    endwhile; ?>
	                    
	                </select>:
	                
	                <select name="schedule_from_minute">
	                    <?php $i=0;
	                    while ($i<60):  ?>
	                        <option value="<?php echo $i?>" <?php if(date("i",strtotime($schedule['from']))==$i) echo "selected"?>><?php printf("%02d", $i)?></option>
	                    <?php $i++;
	                    endwhile; ?>
	                    
	                </select>
							
						 &nbsp;&nbsp;&nbsp;	
	                
	                <label><?php _e('Schedule to:', 'watupro')?></label> &emsp;
	                <input type="text" name="schedule_to" class="watuproDatePicker" value="<?php echo $to_parts[0]?>">
	                &nbsp;
	                <select name="schedule_to_hour">
	                    <?php $i=0;
	                    while ($i<24):?>
	                        <option value="<?php echo $i?>" <?php if(date("G",strtotime($schedule['to']))==$i) echo "selected"?>><?php printf("%02d", $i); ?></option>
	                    <?php $i++;
	                    endwhile; ?>
	                    
	                </select>:
	                
	                <select name="schedule_to_minute">
	                    <?php $i=0;
	                    while ($i<60): ?>
	                        <option value="<?php echo $i?>" <?php if(date("i",strtotime($schedule['to']))==$i) echo "selected"?>><?php printf("%02d", $i)?></option>
	                    <?php $i++;
	                    endwhile; ?>                    
	                </select>
	                
	              <input type="submit" value="<?php _e('Save schedule', 'watupro');?>" class="button button-primary">
	              <input type="button" value="<?php _e('Delete schedule', 'watupro');?>" class="button" onclick="confirmDelSchedule(this.form);">  
	              <input type="hidden" name="ok" value="1">
	              <input type="hidden" name="del" value="0">
	              <input type="hidden" name="user_id" value="<?php echo $schedule['user_id']?>">
	              <?php wp_nonce_field('watupro_schedules');?>
		</p>
	</form>
		<?php endforeach;
	 endif;?>
</div>

<script type="text/javascript" >
jQuery(document).ready(function() {
    jQuery('.watuproDatePicker').datepicker({
        dateFormat : 'yy-mm-dd'
    });
});

function confirmDelSchedule(frm) {
	if(confirm("<?php _e('Are you sure?', 'watupro');?>")) {
		frm.del.value=1;
		frm.submit();
	}
}

function validateScheduleForm(frm) {
	// if adding, user login should not be empty
	if(frm.elements['login'].length > 0 && frm.login.value == '') {
		alert("<?php _e('Please enter valid user login or email address of an existing user.', 'watupro');?>");
		frm.login.focus();
		return false;
	}
	
	if(frm.schedule_from.value == '') {
		alert("<?php _e('Please enter start date', 'watupro');?>");
		frm.schedule_from.focus();
		return false;
	}
	
	if(frm.schedule_to.value == '') {
		alert("<?php _e('Please enter end date', 'watupro');?>");
		frm.schedule_to.focus();
		return false;
	}

	return true;
}
</script>