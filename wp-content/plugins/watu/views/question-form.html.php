<div class="wrap">
<h2><?php echo ucfirst($action) . ' '. __("Question", 'watu'); ?></h2>

<p><a href="admin.php?page=watu_questions&quiz=<?php echo intval($_GET['quiz'])?>"><?php _e('Back to questions', 'watu')?></a>
&nbsp; <a href="admin.php?page=watu_exams"><?php printf(__('Back to %s', 'watu'), WATU_QUIZ_WORD_PLURAL)?></a></p>

<div id="titlediv">
<input type="hidden" id="title" name="ignore_me" value="This is here for a workaround for a editor bug" />
</div>


<style type="text/css">
.qtrans_title, .qtrans_title_wrap {display:none;}
</style>
<script type="text/javascript">
var answer_count = <?php echo $answer_count?>;
var ans_type = "<?php print $ans_type?>";
var exactType = ans_type;
function newAnswer() {
	answer_count++;
	var para = document.createElement("p");
	var rtfLink = "<a href=\"#\" onclick=\"watuMCE('watuAnswer"+answer_count+"', this);return false;\"><?php _e('Rich Text Editor', 'watu');?></a><br />";
	para.innerHTML += rtfLink;
	var textarea = document.createElement("textarea");
	textarea.setAttribute("name", "answer[]");
	textarea.setAttribute("rows", "3");
	textarea.setAttribute("cols", "50");
	textarea.setAttribute("id", "watuAnswer"+answer_count);
	para.appendChild(textarea);
	para.appendChild(document.createTextNode(' ') );
	var label = document.createElement("label");
	label.setAttribute("for", "correct_answer_" + answer_count);
	label.appendChild(document.createTextNode("<?php _e('Correct Answer ', 'watu'); ?>"));
	para.appendChild(label);
	var input = document.createElement("input");
	input.setAttribute("type", ans_type);
	input.setAttribute("name", "correct_answer[]");
	input.className = "correct_answer";
	input.setAttribute("value", answer_count);
	input.setAttribute("id", "correct_answer_" + answer_count);
	para.appendChild(input);
	var label2 = document.createElement("label");
	label2.setAttribute("style", 'margin-left:10px');
	label2.appendChild(document.createTextNode("<?php _e('Points: ', 'watu'); ?>"));
	var point = document.createElement('input');
	point.setAttribute("name", "point[]");
	point.className = 'numeric';
	point.setAttribute("type", "text");
	point.setAttribute("size", "4");
	label2.appendChild(point);
	para.appendChild(label2);
	//$("extra-answers").innerHTML += code.replace(/%%NUMBER%%/g, answer_count);
	document.getElementById("extra-answers").appendChild(para);
}
function init() {
	jQuery("#post").submit(function(e) {
		// Make sure question is suplied
		var contents;
		if(window.tinyMCE && document.getElementById("content").style.display=="none") { // If visual mode is activated.
			contents = tinyMCE.get("content").getContent();
		} else {
			contents = document.getElementById("content").value;
		}

		if(!contents) {
			alert("<?php _e('Please enter the question', 'watu'); ?>");
			e.preventDefault();
			e.stopPropagation();
			return true;
		}

		// We must have at least 2 answers.
		if(exactType!='textarea') {
			var answer_count = 0;
			jQuery(".answer").each(function() {
				if(this.value) answer_count++;
			});
			if(answer_count < 2) {
				alert("<?php _e('Please enter atleast two answers', 'watu'); ?>");
				e.preventDefault();
				e.stopPropagation();
				return true;
			}
		}
	});
	
	jQuery('input[name=answer_type]').click(function(){
		// this defines what "correct" input to display
		if(this.value=='radio') ans_type='radio';
		else ans_type='checkbox';
		
		// and this stores the real answer type 		
		exactType = this.value; 
		
		 jQuery('.correct_answer').each(function(){
			this.removeAttribute('type');
			this.setAttribute('type', ans_type);
		});
	});
}
jQuery(document).ready(init);
</script>

<form name="post" action="admin.php?page=watu_questions&amp;quiz=<?php echo intval($_GET['quiz']); ?>&amp;action=<?php echo (empty($question->ID) or @$action=='new') ? 'new':'edit'?>" method="post" id="post">
<div id="poststuff">

<div class="postarea">

<div class="postbox">
<h3 class="hndle"><?php _e('Question', 'watu') ?></span></h3>
<div class="inside">
<p><input type="checkbox" name="is_inactive" <?php if(!empty($question->ID) and $question->is_inactive) echo 'checked'?> value="1"> <?php printf(__('Deactivate this question. This will exclude it from showing on the %s, counting it, including it in reports etc.', 'watu'), WATU_QUIZ_WORD)?></p>
<?php wp_editor((empty($question->question) ? '' : stripslashes($question->question)), 'content'); ?>
</div></div>

<div class="postbox" id="atdiv">
<h3 class="hndle"><span><?php _e('Answer Type', 'watu') ?></span></h3>
<div class="inside" style="padding:8px">
<?php 
	$single = $multi = $essay ='';
	switch($ans_type) {
		case 'radio': $single='checked="checked"'; break;
		case 'textarea': $essay='checked="checked"'; break;
		case 'checkbox': $multi='checked="checked"'; break;
	}
?>
<label>&nbsp;<input type='radio' name='answer_type' <?php print $single?> id="answer_type_r" value='radio' onclick="jQuery('#watuOpenEndAnswers').hide();" /> <?php _e('Single Answer', 'watu')?> </label>
&nbsp;&nbsp;&nbsp;
<label>&nbsp;<input type='radio' name='answer_type' <?php print $multi?> id="answer_type_c" value='checkbox' onclick="jQuery('#watuOpenEndAnswers').hide();" /> <?php _e('Multiple Answers', 'watu')?></label>
&nbsp;&nbsp;&nbsp;
<label>&nbsp;<input type='radio' name='answer_type' <?php print $essay?> id="answer_type_t" value='textarea' onclick="jQuery('#watuOpenEndAnswers').show();" /> <?php _e('Open End (Essay)', 'watu')?></label>
<p><input type="checkbox" name="is_required" value="1" <?php if(!empty($question->is_required)) echo 'checked'?>> <?php _e('This is a required question', 'watu')?></p>
<p><input type="checkbox" name="is_survey" value="1" <?php if(!empty($question->is_survey)) echo 'checked'?>> <?php _e('This is a survey question. This means that it will not display correct or wrong checkmark at the end and will not be counted in the number of correct, wrong, or unanswered questions.', 'watu')?></p>
 <?php if($ans_type == 'radio' or $ans_type == 'checkbox'):?>
						<p><?php _e('Display possible answers in:', 'watu');?> <select name="num_columns">
		<option value="1" <?php if(empty($question->num_columns) or $question->num_columns == 1) echo 'selected'?>><?php _e('1 column (default)', 'watu');?></option>
		<option value="2" <?php if(!empty($question->num_columns) and $question->num_columns == 2) echo 'selected'?>><?php _e('2 columns', 'watu');?></option>
		<option value="3" <?php if(!empty($question->num_columns) and $question->num_columns == 3) echo 'selected'?>><?php _e('3 columns', 'watu');?></option>
		<option value="4" <?php if(!empty($question->num_columns) and $question->num_columns == 4) echo 'selected'?>><?php _e('4 columns', 'watu');?></option>
		<option value="5" <?php if(!empty($question->num_columns) and $question->num_columns == 5) echo 'selected'?>><?php _e('5 columns', 'watu');?></option>
	</select> </p>
<?php endif;?>	
</div></div>

<div class="postbox" id="questionAnswers">
	<h3 class="hndle"><span><?php _e('Answers', 'watu') ?></span></h3>	
	<div class="inside">	
		<p id="watuOpenEndAnswers" style='display:<?php echo ($ans_type == 'textarea')? 'block' : 'none';?>'><?php printf(__('Answers to open-end questions will be considered matched when there is exact case-insensitive match. For more flexibility check <a href="%s" target="_blank">WatuPRO</a>', 'watu'), 'http://calendarscripts.info/watupro');?></p>		
		<?php
		for($i=1; $i<=$answer_count; $i++): ?>
		<p style="border-bottom:1px dotted #ccc">
		<a href="#" onclick="watuMCE('watuAnswer<?php echo $i?>', this);return false;"><?php _e('Rich Text Editor', 'watu');?></a><br />			
		<textarea name="answer[]" class="answer" rows="3" cols="50" id="watuAnswer<?php echo $i?>"><?php if($action == 'edit' or !empty($_GET['question'])) echo stripslashes(@$all_answers[$i-1]->answer); ?></textarea>
		<label for="correct_answer_<?php echo $i?>"><?php _e("Correct Answer", 'watu'); ?></label>
		<input type="<?php print ($ans_type == 'radio') ? 'radio' : 'checkbox'?>" class="correct_answer" id="correct_answer_<?php echo $i?>" <?php if(@$all_answers[$i-1]->correct == 1) echo 'checked="checked"';?> name="correct_answer[]" value="<?php echo $i?>" />
		<label style="margin-left:10px"><?php _e('Points:', 'watu')?> <input type="text" class="numeric" size="4" name="point[]" value="<?php if($action == 'edit' or !empty($_GET['question'])) echo stripslashes(@$all_answers[$i-1]->point); ?>"></label>
		</p>
		<?php endfor; // end looping through answers ?>
		<style>#extra-answers p{border-bottom:1px dotted #ccc;}</style>
		<div id="extra-answers"></div>
		<a href="javascript:newAnswer();"><?php _e("Add New Answer", 'watu'); ?></a>
	
	</div>
</div>

<div class="postbox">
<h3 class="hndle"><span><?php _e('Explanation / feedback') ?></span></h3>
<div class="inside">

<?php wp_editor(stripslashes(@$question->feedback), 'feedback')?> 

<p><?php printf(__('You can use this field to explain the correct answer. This will be shown only at the end of the %s if you have selected the option to display the correct answers. You can use the %s tag to specify different feedback for correctly or incorrectly answered question. Place the explanation that will be shown for correct answer before the %s tag, and the one that will be shown for wrong answer after the %s tag. Using the split tag will NOT work in "Show the answer of a question immediately after the user have selected an answer." mode.<br> This feedback option is a lot more flexible in <a href="%s" target="_blank">WatuPRO</a>.', 'watu'), WATU_QUIZ_WORD, '{{{split}}}', '{{{split}}}', '{{{split}}}', 'http://calendarscripts.info/watupro/'); ?></p>
</div>
</div>
</div>


<p class="submit">
<input type="hidden" name="quiz" value="<?php echo intval($_REQUEST['quiz'])?>" />
<input type="hidden" name="question" value="<?php echo stripslashes(@$_REQUEST['question'])?>" />
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) @$user_ID ?>" />
<input type="hidden" name="action" value="<?php echo $action ?>" />
<input type="hidden" name="submit" value="1" />
<span id="autosave"></span>
<input type="submit" value="<?php _e('Save', 'watu') ?>" class="button button-primary" />
<input type="submit" name="reuse" value="<?php _e('Save &amp; Reuse as New', 'watu') ?>" class="button button-primary" />
<?php if(empty($question->ID)):?>
  <input type="submit" name="add_blank" value="<?php _e('Save &amp; Add New (Blank)', 'watu') ?>" class="button button-primary" />
<?php endif;?>
</p>
<a href="admin.php?page=watu_questions&amp;quiz=<?php echo intval($_REQUEST['quiz'])?>"><?php _e("Go to Questions Page", 'watu') ?></a>
</div>
<?php wp_nonce_field('watu_questions'); ?>
</form>
</div>

<script type="text/javascript">
// center the floating div
jQuery.fn.watuCenter = function () {
	 this.show();
    this.css("position","absolute");
    this.css("top", Math.max(0, ((jQuery(window).height() - jQuery(this).outerHeight()) / 2) + 
                                                jQuery(window).scrollTop()) + "px");
    this.css("left", Math.max(0, ((jQuery(window).width() - jQuery(this).outerWidth()) / 2) + 
                                                jQuery(window).scrollLeft()) + "px");
    return this;
}

function watuMCE(textareaID, lnk) {
	//tinymce.init({selector:'textarea#' + textareaID});
	settings = {	
	    tinymce: {
	        wpautop  : true,
	        theme    : 'modern',
	        skin     : 'lightgray',
	        language : 'en',
	        formats  : {
	            alignleft  : [
	                { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'left' } },
	                { selector: 'img,table,dl.wp-caption', classes: 'alignleft' }
	            ],
	            aligncenter: [
	                { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'center' } },
	                { selector: 'img,table,dl.wp-caption', classes: 'aligncenter' }
	            ],
	            alignright : [
	                { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'right' } },
	                { selector: 'img,table,dl.wp-caption', classes: 'alignright' }
	            ],
	            strikethrough: { inline: 'del' }
	        },
	        relative_urls       : false,
	        remove_script_host  : false,
	        convert_urls        : false,
	        browser_spellcheck  : true,
	        fix_list_elements   : true,
	        entities            : '38,amp,60,lt,62,gt',
	        entity_encoding     : 'raw',
	        keep_styles         : false,
	        paste_webkit_styles : 'font-weight font-style color',
	        preview_styles      : 'font-family font-size font-weight font-style text-decoration text-transform',
	        tabfocus_elements   : ':prev,:next',
	        plugins    : 'charmap,hr,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview',
	        resize     : 'vertical',
	        menubar    : false,
	        indent     : false,
	        toolbar1   : 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv',
	        toolbar2   : 'formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
	        toolbar3   : '',
	        toolbar4   : '',
	        body_class : 'id post-type-post post-status-publish post-format-standard',
	        wpeditimage_disable_captions: false,
	        wpeditimage_html5_captions  : true
	
	    },
	    quicktags   : true,
	    mediaButtons: true
	
	}
	wp.editor.initialize(textareaID, settings);
	lnk.style.display='none';
} // end watuMCE
</script>