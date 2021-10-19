<div class="wrap">
	<?php echo do_shortcode('[watupro '.intval($_GET['quiz']).' question_ids="'.$question_id.'" nosubmit="1" dont_load_inprogress=1]');?>
	
	<p align="center">
			<input type="button" value="Continue Editing" onclick="window.location='admin.php?page=watupro_question&question=<?php echo $question_id;?>&action=edit&quiz=<?php echo $exam->ID;?>'" class="button-primary">
			
			<input type="button" value="Done, Back to Questions" onclick="window.location='admin.php?page=watupro_questions&quiz=<?php echo $exam->ID;?>'" class="button-primary">
	</p>
</div>