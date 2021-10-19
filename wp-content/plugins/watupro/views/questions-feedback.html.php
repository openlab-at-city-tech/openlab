<style type="text/css">
<?php watupro_resp_table_css(600);?>
</style>
<div class="wrap watupro-wrap">
	<?php if(!empty($quiz->ID)):?>
		<h1><?php printf(__('User feedback on questions from %s "%s"', 'watupro'), WATUPRO_QUIZ_WORD, stripslashes($quiz->name))?></h1>	
		<p><a href="admin.php?page=watupro_takings&exam_id=<?php echo $quiz->ID?>"><?php printf(__('Back to the results on this %s', 'watupro'), WATUPRO_QUIZ_WORD);?></a>
		| <a href="admin.php?page=watupro_questions_feedback"><?php printf(__('View feedback on all %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></a></p>
	<?php else: 
		// feedback on all quizzes ?>
		<h1><?php printf(__('User feedback on questions from all %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></h1>	
		<p><a href="admin.php?page=watupro_takings"><?php printf(__('Back to the results on all %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></a></p>
	<?php endif;?>
	
	<form method="get" action="admin.php">
		<input type="hidden" name="page" value="watupro_questions_feedback">
		<?php if(!empty($_GET['quiz_id'])):?><input type="hidden" name="quiz_id" value="<?php echo intval($_GET['quiz_id']);?>"><?php endif;?>
		<p>Search: <input type="text" name="search" value="<?php echo empty($_GET['search']) ? '' : esc_attr($_GET['search']);?>">
		<input type="submit" value="Search" class="button-primary"> 
		<?php if(!empty($_GET['search'])):?><input type="button" value="Clear" class="button" onclick="this.form.search.value='';this.form.submit();"><?php endif;?>
		<br> <?php _e('Searches in the question contents, the answer, or the feedback contents', 'watupro');?></p>
	</form>	
	
	<?php if(!$count):?>
		<p><?php _e('There is no feedback left for any of the questions yet.', 'watupro')?></p>
		</div>
	<?php return;
	endif;?>
	
	<table class="widefat watupro-table">
		<thead>
			<tr><?php if(empty($quiz->ID)):?>
				<th><?php printf(__('%s Name', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD));?></th>
			<?php endif;?>
			<th><?php _e('Question', 'watupro')?>
			<a href="admin.php?page=watupro_questions_feedback&quiz_id=<?php echo empty($_GET['quiz_id']) ? '' : intval($_GET['quiz_id']);?>&search=<?php echo $search_str?>&ob=question&dir=<?php echo $odir;?>"><?php echo ($dir == 'ASC') ? '&#9660;' : '&#9650;';?></a>	</th>
			<th><?php _e('Answer', 'watupro')?> 
				<a href="admin.php?page=watupro_questions_feedback&quiz_id=<?php echo empty($_GET['quiz_id']) ? '' : intval($_GET['quiz_id']);?>&search=<?php echo $search_str?>&ob=answer&dir=<?php echo $odir;?>"><?php echo ($dir == 'ASC') ? '&#9660;' : '&#9650;';?></a></th>
			<th><?php _e('Feedback', 'watupro')?> <?php _e('Feedback', 'watupro')?> <a href="admin.php?page=watupro_questions_feedback&quiz_id=<?php echo empty($_GET['quiz_id']) ? '' : intval($_GET['quiz_id']);?>&search=<?php echo $search_str?>&ob=feedback&dir=<?php echo $odir;?>"><?php echo ($dir == 'ASC') ? '&#9660;' : '&#9650;';?></a></th>
			<th><?php _e('Author', 'watupro')?></th>			
			<th><?php _e('Left on', 'watupro')?> 
			<a href="admin.php?page=watupro_questions_feedback&quiz_id=<?php echo empty($_GET['quiz_id']) ? '' : intval($_GET['quiz_id']);?>&search=<?php echo $search_str?>&ob=tA.ID&dir=<?php echo $odir;?>"><?php echo ($dir == 'ASC') ? '&#9660;' : '&#9650;';?></a></th>
			<th><?php _e('Quiz result', 'watupro')?></th><th><?php _e('View details', 'watupro')?></th><th><?php _e('Delete', 'watupro');?></th></tr>
		</thead>
		
		<tbody>
		<?php foreach($feedbacks as $feedback):
		   if(empty($feedback->author) and !empty($feedback->user_id)) {
		   	$author = get_userdata($feedback->user_id);
		   	$feedback->author = $author->display_name;
		   }
			if(empty($class)) $class = 'alternate';
			else $class = '';?>
			<tr class="<?php echo $class?>">
				<?php if(empty($quiz->ID)):?>
					<td><?php echo stripslashes($feedback->quiz_name);?></td>
				<?php endif;?>
				<td><?php echo apply_filters('watupro_content', stripslashes($feedback->question))?></td>
				<td><?php echo apply_filters('watupro_content', stripslashes($feedback->answer))?></td>
				<td><?php echo apply_filters('watupro_content', stripslashes($feedback->feedback))?></td>
				<td><?php echo $feedback->author;?></td>
				<td><?php echo date($dateformat, strtotime($feedback->taking_date))?></td>
				<td><?php echo stripslashes($feedback->taking_result)?></td>
				<td><a href="admin.php?page=watupro_takings&exam_id=<?php echo $quiz->ID?>&taking_id=<?php echo $feedback->taking_id?>" target="_blank"><?php _e('view', 'watupro')?></a></td>
				<td><a href="<?php echo wp_nonce_url('admin.php?page=watupro_questions_feedback&quiz_id='.(empty($_GET['quiz_id']) ? '' : intval($_GET['quiz_id'])).'&search='.$search_str.'&ob='.$ob.'&dir='.$dir.'&delete='.$feedback->answer_id, 'delete_feedback', 'watupro_feedback_nonce')?>" onclick="return confirm('Are you sure?');"><?php _e('Delete', 'watupro');?></a></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
	
	<p align="center">
		<?php if($offset>0):?>
			<a href="admin.php?page=watupro_questions_feedback&offset=<?php echo $offset-$limit;?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>&search=<?php echo $search_str?>&quiz_id=<?php echo $quiz_id?>"><?php _e('previous page', 'watupro')?></a>
		<?php endif;?>
		&nbsp;
		<?php if($limit != -1 and $count>($offset+$limit)):?>
			<a href="admin.php?page=watupro_questions_feedback&offset=<?php echo $offset+$limit;?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>&search=<?php echo $search_str?>&quiz_id=<?php echo $quiz_id?>"><?php _e('next page', 'watupro')?></a>
		<?php endif;?>
		</p>
</div>	

<script type="text/javascript" >
<?php watupro_resp_table_js();?>
</script>