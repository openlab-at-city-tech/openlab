<div class="wrap watupro-wrap">
   <h1><?php printf(__('Manually Award Certificate "%s"', 'watupro'), stripslashes($certificate->title));?></h1>
   
   <p><a href="admin.php?page=watupro_certificates"><?php _e('Back to certificates', 'watupro');?></a>
   | <a href="admin.php?page=watupro_user_certificates&id=<?php echo $certificate->ID?>"><?php _e('See who earned this certificate', 'watupro');?></a></p>   
   
   <form method="post">
      <p><?php _e('Email address or WP user login:', 'watupro');?> <input type="text" name="user_name" value="<?php echo empty($_POST['user_name']) ? '' : $_POST['user_name']?>">
         <input type="submit" value="<?php _e('Fetch data', 'watupro');?>" class="button button-primary"></p>
      <?php if(!empty($_POST['user_name'])):
         if(count($taken_exams)):?>
            <p><?php printf(__('For taking %s:', 'watupro'), WATUPRO_QUIZ_WORD);?> <select name="exam_id" onclick="selectExam(this.value);">
               <?php foreach($taken_exams as $exam):?>
                  <option value="<?php echo $exam['ID']?>" <?php if(!empty($_POST['exam_id']) and $_POST['exam_id'] == $exam['ID']) echo 'selected'?>><?php
                     echo stripslashes($exam['name']);?></option>
               <?php endforeach;?>
            </select> <?php _e('on', 'watupro');?> <select name="taking_id" id="takingID">
               <?php foreach($taken_exams[0]['takings'] as $t):?>
                  <option value="<?php echo $t->ID?>"><?php printf(__('%s with %s points (%d%% correct answers)', 'watupro'), 
                     date_i18n($dateformat, strtotime($t->date)), $t->points, $t->percent_correct);?></option>
               <?php endforeach;?>
            </select></p>
            <?php if($certificate->is_multi_quiz):?>
               <h3><?php printf(__('Multiple %s certificate data:', 'watupro'), WATUPRO_QUIZ_WORD);?></h3>
               <p><?php printf(__('The user completed these %s:', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?>
                  <?php foreach($exams as $exam):?>
                     <input type="checkbox" name="quiz_ids[]" value="<?php echo $exam->ID?>"> <?php echo stripslashes($exam->name);?> &nbsp;
                  <?php endforeach;?><br />
                  <?php _e('with avg. points:', 'watupro');?> <input type="text" name="avg_points" size="6">
                  <?php _e('and avg. % correct answers:', 'watupro');?> <input type="text" name="avg_percent" size="6"></p>
            <?php endif;?>
            <p><input type="submit" name="award" value="<?php _e('Award Certificate', 'watupro');?>" class="button button-primary"></p>
         <?php else:
            echo "<p>".sprintf(__('This user did not take any %s.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL).'</p>';
         endif; // end if no takings
       endif;
       wp_nonce_field('watupro_award');?>   
   </form>
</div>

<script type="text/javascript">
var takenExams = {<?php foreach($taken_exams as $taken_exam):?>
   <?php echo $taken_exam['ID']?> :
    [<?php foreach($taken_exam['takings'] as $t):?>
      {id : <?php echo $t->ID?>, val: "<?php printf(__('%s with %s points (%d%% correct answers)', 'watupro'), 
                     date_i18n($dateformat, strtotime($t->date)), $t->points, $t->percent_correct);?>"},
   <?php endforeach;?>],
<?php endforeach;?>};

function selectExam(id) {
   var takings = takenExams[id];
   var opts = '';
   for(i=0; i<takings.length; i++) {
      opts += '<option value="' + takings[i]['id']+'">' + takings[i]['val'] + "</option>\n";
   }
   jQuery('#takingID').html(opts);
}
</script>