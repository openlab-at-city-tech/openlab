<?php 
// Don't forged this page is included from a function!
global $question_catids;
$single_page = $exam->single_page;
// force start if we are continuing on limited time exam     
if($exam->full_time_limit > 0 and !empty($timer_warning) and empty($_POST['watupro_start_timer'])): echo "<p class='watupro-warning' id='timerRuns'>".$timer_warning."</p>"; endif;
if($exam->full_time_limit > 0 and count($all_question)):?>
	    <div id="timeNag" <?php if(!empty($_POST['watupro_start_timer'])):?>style="display:none;"<?php endif;?>>
		    <?php $button = $_exam->maybe_show_description($exam, false, $cnt_questions);
		    printf(__('This %s must be completed in %s minutes.', 'watupro'), WATUPRO_QUIZ_WORD, round($exam->time_limit, 1));
		    if(empty($button)):?> <a href="#" onclick="WatuPRO.InitializeTimer(<?php echo $exam->time_limit*60?>, <?php echo $exam->ID?>, 1);return false;"><?php printf(__('Click here to start the %s', 'watupro'), WATUPRO_QUIZ_WORD)?></a><?php endif;?>		   
	    </div>
	    <div id="timerDiv" <?php if(empty($_POST['watupro_start_timer'])):?>style="display:none;"<?php endif;?>><?php _wtpt(__('Time left:', 'watupro'))?> <?php echo $exam->time_limit*60;?></div>
<?php endif;?>
<?php //echo watupro_onpage_css();?>
<script type="text/javascript" >
document.addEventListener("DOMContentLoaded", function(event) { 
if(!window.jQuery) alert("<?php _e('The important jQuery library is not properly loaded in your site. Your WordPress theme is probably missing the essential wp_head() call. You can switch to another theme and you will see that the plugin works fine and this notice disappears. If you are still not sure what to do you can contact us for help.', 'watupro');?>");
});
</script>  
  
<div <?php if($exam->time_limit > 0 and empty($_POST['watupro_start_timer']) and count($all_question)):?>style="display:none;"<?php endif;?> id="watupro_quiz" class="quiz-area <?php if($single_page) echo 'single-page-quiz'; ?>">
<p id="submittingExam<?php echo $exam->ID?>" style="display:none;text-align:center;"><img src="<?php echo plugins_url('watupro/img/loading.gif')?>" width="16" height="16"></p>

<?php $button = $_exam->maybe_show_description($exam, true, $cnt_questions);?>

<?php if(!count($all_question)):?>
	<p><?php _e('There are no questions to answer.', 'watupro');?>
	<?php if(is_user_logged_in() and $exam->require_login and (!empty($advanced_settings['dont_show_answered']) or !empty($advanced_settings['dont_show_correctly_answered']))):?>
		<?php _e('You already answered them all.', 'watupro');
		echo WatuPROTaking :: display_latest_result($exam); 
	endif;?></p>
<?php return false; 
endif;?>

<form action="" method="post" class="quiz-form" id="quiz-<?php echo $exam_id?>" <?php if(!empty($ui['autocomplete_off'])):?>autocomplete="off"<?php endif; if($exam->time_limit <= 0 and $button):?>style="display:none;"<?php endif;?> enctype="multipart/form-data" <?php if(!empty($exam->no_ajax)):?>onsubmit="return WatuPRO.submitResult(this)"<?php endif;?>>
<?php
if($exam->email_taker and !is_user_logged_in()) watupro_ask_for_email($exam);
// the exam is shown below
$question_count = $cat_count = $exam->page_count = $num_pages = 1;
$question_ids = '';
$total = count($all_question);
if($exam->single_page == WATUPRO_PAGINATE_CUSTOM_NUMBER) {
	if($exam->custom_per_page == 0) $exam->custom_per_page = 1; // this should never be zero 	
	$num_pages = ceil( $total / $exam->custom_per_page );	
}

if($exam->show_pagination and (empty($advanced_settings['paginator_position']) or $advanced_settings['paginator_position'] == 'top')) echo WTPExam::paginator($total, (empty($in_progress) ? null : $in_progress), false, (empty($advanced_settings['paginator_decade']) ? 10 : intval($advanced_settings['paginator_decade'])) );
if(!empty($advanced_settings['show_category_paginator'])) echo WTPExam :: category_paginator($all_question, $exam, @$in_progress);
if(!empty($advanced_settings['show_progress_bar'])) echo WatuPROExams :: progress_bar($all_question, $exam, @$in_progress);

$question_catids = array(); // used for category based pagination and category header
$qct = 0;
if($exam->time_limit <= 0 or !empty($_POST['watupro_start_timer'])): // on timed exams questions should not be shown before the timer starts
	foreach ($all_question as $ques):        
	   echo watupro_cat_header($exam, $qct, $ques, 'show', $current_page);
	   if(!empty($exam->is_likert_survey)): 
	   	if(@file_exists(get_stylesheet_directory().'/watupro/show-likert-question.html.php')): include(get_stylesheet_directory().'/watupro/show-likert-question.html.php');
	   	else: include(WATUPRO_PATH . '/views/show-likert-question.html.php');
	   	endif;
	   else: 
	   	if(@file_exists(get_stylesheet_directory().'/watupro/show-exam-question.html.php')): include(get_stylesheet_directory().'/watupro/show-exam-question.html.php');
	   	else: include(WATUPRO_PATH . '/views/show-exam-question.html.php');
	   	endif;
	   endif;
	endforeach; // end foreach question
	if(!empty($exam->is_likert_survey)) echo '</tbody></table>';
	if($single_page == WATUPRO_PAGINATE_PAGE_PER_CATEGORY and $exam->group_by_cat) echo "</div>"; // close last category div
	if($single_page == WATUPRO_PAGINATE_CUSTOM_NUMBER) echo "</div>"; // close last custom pagination div	
	$_exam->maybe_ask_for_contact($exam, 'end'); // maybe display div with contact details
endif; // end if hiding because of timer	

if($exam->single_page == WATUPRO_PAGINATE_CUSTOM_NUMBER) $num_cats = $num_pages;
// show we hide the submit button? by default yes which means $submit_button_style is empty
$submit_button_style = '';
if(($exam->single_page == WATUPRO_PAGINATE_ONE_PER_PAGE and !$exam->submit_always_visible and sizeof($all_question)>1)
	or ( ($exam->single_page == WATUPRO_PAGINATE_PAGE_PER_CATEGORY or $exam->single_page == WATUPRO_PAGINATE_CUSTOM_NUMBER) 
			and !$exam->submit_always_visible and $num_cats>1)) $submit_button_style="style='display:none;'"; ?>
<div style='display:none' id='question-<?php echo $question_count?>'>
	<div class='question-content'>
		<img src="<?php echo plugins_url('watupro/img/loading.gif')?>" width="16" height="16" alt="<?php _e('Loading...', 'watu', 'watupro')?>" title="<?php _e('Loading...', 'watu', 'watupro')?>" />&nbsp;<?php _e('Loading...', 'watu', 'watupro')?>
	</div>
</div>

<?php
if($exam->show_pagination and !empty($advanced_settings['paginator_position']) and $advanced_settings['paginator_position'] == 'bottom') {
	echo WTPExam::paginator($total, (empty($in_progress) ? null : $in_progres), false, (empty($advanced_settings['paginator_decade']) ? 10 : intval($advanced_settings['paginator_decade'])) );
}

$question_ids = preg_replace('/,$/', '', $question_ids );
if(!empty($recaptcha_html)) echo $recaptcha_html;
if(!empty($text_captcha_html)) echo $text_captcha_html;?><br />
	
	<?php if(!empty($ui['use_legacy_buttons_table'])):
		include(WATUPRO_PATH .'/views/legacy-buttons-table.html.php');
	else:?>
		<div class="watupro_buttons flex <?php if(!empty($advanced_settings['is_rtl'])):?>watupro-rtl<?php endif;?>" id="watuPROButtons<?php echo $exam->ID?>" <?php if(!empty($attr['nosubmit'])) echo 'style="display:none;"';?>>
		  <?php if(empty($exam->disallow_previous_button)):?><div id="prev-question" style="display:none;"><input type="button" value="&lt; <?php echo _wtpt(__('Previous', 'watupro')); ?>" onclick="WatuPRO.nextQuestion(event, 'previous');"/></div><?php endif;?>
		  <?php if($exam->single_page == WATUPRO_PAGINATE_ONE_PER_PAGE):?><div id="next-question"><input type="button" value="<?php echo  _wtpt(__('Next', 'watupro')) ?> &gt;" onclick="WatuPRO.nextQuestion(event);" /></div><?php endif;?>
		  <?php if($exam->live_result and $exam->single_page==WATUPRO_PAGINATE_ONE_PER_PAGE):?><div><input class="watupro-live-button" type="button" id="liveResultBtn" value="<?php _e('See Answer', 'watupro')?>" onclick="WatuPRO.liveResult();"></div><?php endif;?>		   
		   <?php if( ($single_page==WATUPRO_PAGINATE_PAGE_PER_CATEGORY and $num_cats>1 and $exam->group_by_cat)
	  	or ($single_page == WATUPRO_PAGINATE_CUSTOM_NUMBER and $num_pages>1)):?>
	  	<?php if(empty($exam->disallow_previous_button)):?><div style="display:none;" id="watuproPrevCatButton"><input type="button" onclick="WatuPRO.nextCategory(<?php echo $num_cats?>, false);" value="<?php echo _wtpt(__('Previous page', 'watupro'));?>"></div><?php endif;?><div id="watuproNextCatButton"><input type="button" onclick="WatuPRO.nextCategory(<?php echo $num_cats?>, true);" value="<?php echo _wtpt(__('Next page', 'watupro'));?>"></div> 
	  <?php endif; // endif paginate per category ?>
	  <?php if(is_user_logged_in() and $exam->enable_save_button):?>
	  	<div><input type="button" name="action" class="watupro-save-button" onclick="WatuPRO.saveResult(event)" id="save-button" value="<?php _e('Save', 'watupro') ?>" /></div>
	  <?php endif;?>
		<div><?php if(empty($exam->no_ajax)):?><input type="button" name="action" class="watupro-submit-button" onclick="WatuPRO.submitResult(event)" id="action-button" value="<?php echo empty($advanced_settings['submit_button_value']) ? _wtpt(__('View Results', 'watupro')) : $advanced_settings['submit_button_value']; ?>" <?php echo $submit_button_style?> />
		<?php else:?>
			<input type="submit" name="submit_no_ajax" id="action-button" class="watupro-submit-button" value="<?php echo empty($advanced_settings['submit_button_value']) ? _wtpt(__('View Results', 'watupro')) : $advanced_settings['submit_button_value']; ?>" <?php echo $submit_button_style?>/>
		<?php endif;?></div>
		</div>
	<?php endif; // end if using flex?>
	
	<input type="hidden" name="quiz_id" value="<?php echo  $exam_id ?>" id="watuPROExamID"/>
	<input type="hidden" name="start_time" id="startTime" value="<?php echo current_time('mysql');?>" />
	<input type="hidden" name="start_timestamp" id="startTimeStamp" value="<?php echo (empty($_COOKIE['start_time'.$exam->ID]) or !empty($exam->timer_adjusted_by_schedule)) ? current_time('timestamp') : $_COOKIE['start_time'.$exam->ID];?>" />
	<input type="hidden" name="question_ids" value="<?php echo empty($qidstr) ? '' : $qidstr?>" />
	<input type="hidden" name="watupro_questions" value="<?php echo watupro_serialize_questions($all_question);?>" />
	<input type="hidden" name="no_ajax" value="<?php echo $exam->no_ajax?>"><?php if(!empty($exam->no_ajax)):?>
	<input type="hidden" name="wtpuc_ok" value="<?php echo empty($_POST['wtpuc_ok']) ? 0 : 1?>">
	<input type="hidden" name="action" value="watupro_submit">
	<?php endif;?>
	<?php if(!empty($exam->no_ajax)  and !empty($advanced_settings['ask_for_contact_details']) and $advanced_settings['ask_for_contact_details'] == 'start'):
	// when no-ajax exam requires contact fields we have to place them here too?>	
	<input type="hidden" name="watupro_taker_email" value="<?php echo empty($_POST['watupro_taker_email']) ? '' : esc_attr($_POST['watupro_taker_email'])?>">
	<input type="hidden" name="watupro_taker_name" value="<?php echo empty($_POST['watupro_taker_name']) ? '' : esc_attr(htmlentities($_POST['watupro_taker_name']))?>">
	<input type="hidden" name="watupro_taker_phone" value="<?php echo empty($_POST['watupro_taker_phone']) ? '' : esc_attr(htmlentities($_POST['watupro_taker_phone']))?>">
	<input type="hidden" name="watupro_taker_company" value="<?php echo empty($_POST['watupro_taker_company']) ? '' : esc_attr(htmlentities($_POST['watupro_taker_company']))?>">
	<input type="hidden" name="watupro_taker_field1" value="<?php echo empty($_POST['watupro_taker_field1']) ? '' : esc_attr(htmlentities($_POST['watupro_taker_field1']))?>">
	<input type="hidden" name="watupro_taker_field2" value="<?php echo empty($_POST['watupro_taker_field2']) ? '' : esc_attr(htmlentities($_POST['watupro_taker_field2']))?>">
	<input type="hidden" name="watupro_taker_checkbox" value="<?php echo @$_POST['watupro_taker_checkbox']?>">
	<?php endif;?>
	<?php if($exam->single_page==WATUPRO_PAGINATE_ONE_PER_PAGE and (!empty($advanced_settings['premature_end_question']) or !empty($advanced_settings['prevent_forward_question']))):?>
		<input type="hidden" id="watuproEvaluateOnTheFly<?php echo $exam->ID?>" value="1" name="evaluate_on_the_fly">
	<?php endif;
	if(!empty($advanced_settings['use_honeypot'])):?>		
		<input class="watupro-beehive" type="text" value="_<?php echo md5('honeyforme' . $_SERVER['REMOTE_ADDR']) /* honeypot value */?>" id="watuPROAppSourceID<?php echo $exam->ID?>">
		<input class="watupro-beehive" name="h_app_id" type="text" value="_<?php echo microtime() /* honeypot value */?>" id="watuPROAppID<?php echo $exam->ID?>">
	<?php endif;?>
	</form>
	<p>&nbsp;</p>
</div>

<?php if($exam->time_limit > 0 and empty($_POST['watupro_start_timer'])): // start timer form?>
<form method="post" id="watuproTimerForm<?php echo $exam->ID?>">
	<!-- watupro-hidden-fields -->
	<input type="hidden" name="watupro_start_timer" value="0">
	<input type="hidden" name="watupro_taker_email" value="">
	<input type="hidden" name="watupro_taker_name" value="">
	<input type="hidden" name="watupro_taker_phone" value="">
	<input type="hidden" name="watupro_taker_company" value="">
	<input type="hidden" name="watupro_taker_field1" value="">
	<input type="hidden" name="watupro_taker_field2" value="">
	<input type="hidden" name="watupro_taker_checkbox" value="1">
	<input type="hidden" name="watupro_diff_level" value="">
</form>
<?php endif;?>
<script type="text/javascript">
//jQuery(document).ready(function(){
document.addEventListener("DOMContentLoaded", function(event) { 	
<?php do_action('watupro_show_exam_js', $exam);?>
var question_ids = "<?php print $question_ids ?>";
WatuPROSettings[<?php echo $exam->ID?>] = {};
WatuPRO.qArr = question_ids.split(',');
WatuPRO.exam_id = <?php echo $exam_id ?>;	    
WatuPRO.post_id = <?php echo empty($post) ? 0 : $post->ID ?>;
WatuPRO.store_progress = <?php echo $exam->store_progress ?>;
WatuPRO.curCatPage = <?php echo $current_page ?>;
WatuPRO.requiredIDs="<?php echo $required_ids_str?>".split(",");
WatuPRO.hAppID = "<?php echo microtime(); /* honeypot */ ?>";
var url = "<?php print plugins_url('watupro/'.basename(__FILE__) ) ?>";
WatuPRO.examMode = <?php echo $exam->single_page?>;
<?php if($single_page==2 and $num_cats>1 and $exam->group_by_cat): echo 'WatuPRO.numCats ='. $num_cats.";\n"; endif;?>
WatuPRO.siteURL="<?php echo admin_url( 'admin-ajax.php' ); ?>";
WatuPRO.emailIsNotRequired = <?php echo empty($advanced_settings['email_not_required']) ? 0 : 1;?>;
<?php if(!empty($advanced_settings['confirm_on_submit'])):?>WatuPRO.confirmOnSubmit = true;<?php echo "\n"; endif;
if(!empty($advanced_settings['dont_prompt_unanswered'])):?>WatuPRO.dontPromtUnanswered = true;<?php echo "\n"; endif;  
if(!empty($advanced_settings['dont_prompt_notlastpage'])):?>WatuPRO.dontPromtNotlastpage = true;<?php echo "\n"; endif;
if(!empty($advanced_settings['dont_scroll'])):?>WatuPRO.dontScroll = true;<?php echo "\n"; endif;
if(!empty($advanced_settings['dont_scroll_start'])):?>WatuPRO.dontScrollStart = true;<?php echo "\n"; endif;
if(!empty($advanced_settings['live_result_no_answer'])):?>WatuPRO.LiveResultNoAnswer = true;<?php echo "\n"; endif;
if(!empty($advanced_settings['takings_by_email'])):?>WatuPRO.takingsByEmail = <?php echo intval($advanced_settings['takings_by_email'])  . ";\n"; endif;
if(!empty($advanced_settings['paginator_decade'])):?>WatuPRO.perDecade = <?php echo intval($advanced_settings['paginator_decade'])  . ";\n"; endif;
if(!empty($advanced_settings['single_choice_action'])):?>WatuPROSettings[<?php echo $exam->ID?>].singleChoiceAction = <?php echo "'".$advanced_settings['single_choice_action']."'\n"; endif;?>
<?php if(!empty($advanced_settings['user_choice'])):?>WatuPRO.userChoice = <?php echo empty($_POST['wtpuc_ok']) ? 0 : 1?>;<?php echo "\n"; endif;
if(watupro_intel()):?>WatuPROIntel.init(<?php echo $exam->ID?>);<?php echo "\n";endif;
if(!empty($in_progress)): watupro_load_page($in_progress); endif;
if($exam->single_page != WATUPRO_PAGINATE_ONE_PER_PAGE):?>WatuPRO.inCategoryPages=1;<?php endif;
if(!empty($exam->timer_adjusted_by_schedule)):?>WatuPRO.timerAdjustedBySchedule=1;<?php endif;
if(!empty($exam->no_ajax)):?>WatuPRO.maxUpload = '<?php echo get_option('watupro_max_upload');?>';
WatuPRO.uploadExts = '<?php echo $allowed_uploads;?>';<?php endif;
if($exam->time_limit > 0):?>
WatuPRO.secs=0;
WatuPRO.timerID = null;
WatuPRO.timerRunning = false;		
WatuPRO.TimerTurnsRed = <?php echo empty($advanced_settings['timer_turns_red']) ? 0 : intval($advanced_settings['timer_turns_red'])?>;
WatuPRO.fullTimeLimit = <?php echo $exam->full_time_limit*60;?>;
WatuPRO.currentTime = <?php echo current_time('timestamp'); ?>;
<?php if(!empty($_POST['watupro_start_timer'])):
echo "WatuPRO.InitializeTimer(".(round($exam->time_limit*60)).",".$exam->ID.", 0);"; // auto-start timer
endif;
endif;?>});    	 
</script>