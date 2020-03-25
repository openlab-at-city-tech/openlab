<?php
/*
    HD Quiz Meta Data and Pages
    This creates all of the custom meta for quizzes,
    and creates the post_type_questionna pages
*/

/* Add custom metabox to post_type_questionna pages
------------------------------------------------------- */
function hdq_create_hdq_form_page()
{
    function hdq_meta_quiz_setup()
    {
        add_action('add_meta_boxes', 'hdq_add_meta_quiz');
        add_action('save_post', 'hdq_save_quiz_meta', 10, 2);
    }
    add_action('load-post.php', 'hdq_meta_quiz_setup');
    add_action('load-post-new.php', 'hdq_meta_quiz_setup');

    function hdq_add_meta_quiz()
    {
        add_meta_box(
            'hdq_meta_quiz',
            esc_html__('HD Quiz', 'example'),
            'hdq_meta_quiz',
            'post_type_questionna',
            'normal',
            'default'
        );
    }
}
hdq_create_hdq_form_page();

/* Quiz page meta
------------------------------------------------------- */
function hdq_meta_quiz($object, $box)
{
    wp_nonce_field('hdq_meta_quiz_nonce', 'hdq_meta_quiz_nonce');
    $hdq_id =  get_the_ID();
    $hdq_selected = intval(get_post_meta($hdq_id, 'hdQue_post_class2', true));
    $hdq_image_as_answer = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class23', true));
    $hdq_question_as_title = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class24', true));
    $hdq_paginate = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class25', true));
    $hdq_tooltip = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class12', true));
    $hdq_after_answer = wp_kses_post(get_post_meta($hdq_id, 'hdQue_post_class26', true));
    $hdq_answers = hdq_get_answers($hdq_id); 
?>
<input type = "hidden" name="hdQue-post-class2" id="hdQue-post-class2" value="<?php echo $hdq_selected; ?>" />
<div id = "hdq_message"></div>
	<div id = "hdq_wrapper">
		<div id="hdq_form_wrapper">
			<div class = "hdq_one_third">
				<div class="hdq_row hdq_checkbox">
					<label class="hdq_label_title" for="hdQue-post-class23"> Image Based Answers &nbsp;&nbsp;<span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>Enable this if you want a user to select an image as their answer.</span></span></span></label>
					<div class="hdq_check_row"><label class="non-block" for="hdQue-post-class23"></label>
						<div class="hdq-options-check">
							<input type="checkbox" id="hdQue-post-class23" value="yes" name="hdQue-post-class23" <?php if ($hdq_image_as_answer == "yes") {
        echo 'checked';
    } ?>/>
							<label for="hdQue-post-class23"></label>
						</div>
					</div>
				</div>
			</div>
			<div class = "hdq_one_third">
				<div class="hdq_row hdq_checkbox">
					<label class="hdq_label_title" for="hdQue-post-class24"> Question as Title &nbsp;&nbsp;<span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>Enable this if you want to use this question as a title or heading.</span></span></span></label>
					<div class="hdq_check_row"><label class="non-block" for="hdQue-post-class24"></label>
						<div class="hdq-options-check">
							<input type="checkbox" id="hdQue-post-class24" value="yes" name="hdQue-post-class24" <?php if ($hdq_question_as_title == "yes") {
        echo 'checked';
    } ?>>
							<label for="hdQue-post-class24"></label>
						</div>
					</div>
				</div>
			</div>
			<div class = "hdq_one_third hdq_last">
				<div class="hdq_row hdq_checkbox">
					<label class="hdq_label_title" for="hdQue-post-class25"> Paginate &nbsp;&nbsp;<span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>Start a new page with this question (jQuery pagination) (user will need to select "next" to see this question or ones below it)</span></span></span></label>
					<div class="hdq_check_row"><label class="non-block" for="hdQue-post-class25"></label>
						<div class="hdq-options-check">
							<input type="checkbox" id="hdQue-post-class25" value="yes" name="hdQue-post-class25" <?php if ($hdq_paginate == "yes") {
        echo 'checked';
    } ?>>
							<label for="hdQue-post-class25"></label>
						</div>
					</div>
				</div>
			</div>
			<div class = "clear"></div>

			<br/>
			<div id = "hdq_tab_wrapper" style = "<?php if ($hdq_question_as_title == "yes") {
        echo 'display: none;';
    } ?>">


			<div id="hdq_tabs">
				<ul>
					<li class="hdq_active_tab" data-hdq-content="hdq_tab_content">Main</li>
					<li data-hdq-content="hdq_tab_extra">Extra</li>
				</ul>
				<div class="clear"></div>
			</div>
			<div id = "hdq_tab_content" class = "hdq_tab hdq_tab_active">
				<?php
                    if ($hdq_image_as_answer === "yes") {
                        $hdq_image_as_answer = "hdq_use_image_as_answer";
                    } else {
                        $hdq_image_as_answer = "";
                    } ?>
				<table class="hdq_table">
					<thead>
						<tr>
							<th>#</th>
							<th>Options</th>
							<th width = "150" class = "hdq_answer_as_image <?php echo $hdq_image_as_answer; ?>">Featured Image</th>
							<th width="30">Correct</th>
						</tr>
					</thead>
					<tbody>
						<?php
                            // print the rows we have data for
                            $x = 0;
    foreach ($hdq_answers as $answer) {
        $x = $x + 1; ?>
						<tr>
							<td width = "1"><?php echo $x; ?></td>
							<td>
								<input class="hdq_input" type="text" name="hdQue-post-class<?php echo $answer[0]; ?>" id="hdQue-post-class<?php echo $answer[0]; ?>" value="<?php echo $answer[1]; ?>" />
							</td>
							<td class="textCenter hdq_answer_as_image <?php echo $hdq_image_as_answer; ?>">
								<?php echo hdq_get_featured_image_container($answer[2]); ?>
								<input type = "hidden" id = "hdQue-post-class<?php echo $x + 12; ?>" name = "hdQue-post-class<?php echo $x + 12; ?>" value = "<?php echo $answer[2]; ?>"/>
							</td>
							<td class="textCenter">
								<div class="hdq-options-check">
									<input type="checkbox" class="hdq_correct" value="yes" name="hdq_correct_<?php echo $x; ?>" id = "hdq_correct_<?php echo $x; ?>" <?php if ($hdq_selected === $x) {
            echo 'checked';
        } ?>/>
									<label for="hdq_correct_<?php echo $x; ?>"></label>
								</div>
							</td>
						</tr>
						<?php
    } ?>

					</tbody>
				</table>

			</div>
			<div id = "hdq_tab_extra" class = "hdq_tab">
				<h3>Extra Question Options</h3>
				<div class = "hdq_row">
					<label for = "hdQue-post-class12">Tooltip Text &nbsp;&nbsp;<span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>This bubble is an example of a tool tip :) </span></span></span></label>
					<input class="hdq_input" type="text" name="hdQue-post-class12" id="hdQue-post-class12" value="<?php echo $hdq_tooltip; ?>" />
				</div>

				<div class = "hdq_row">
					<label for ="hdQue-post-class26">Text that appears if answer was wrong</label>
					<?php
                        wp_editor($hdq_after_answer, "hdQue-post-class26", array('textarea_name' => 'hdQue-post-class26','teeny' => true, 'media_buttons' => false, 'textarea_rows' => 3, 'quicktags' => false)); ?>

				</div>
			</div>
		</div>
			</div>
	</div>
<?php
}



function hdq_save_quiz_meta($post_id, $post)
{
    if (isset($_POST[ 'hdq_meta_quiz_nonce' ])) {
        $hdq_nonce = $_POST[ 'hdq_meta_quiz_nonce' ];
        if (wp_verify_nonce($hdq_nonce, 'hdq_meta_quiz_nonce') != false) {
            $post_type = get_post_type_object($post->post_type);
            if (current_user_can($post_type->cap->edit_post, $post_id)) {
                $new_meta_value = array();
                $meta_key = array();
                $new_meta_value = array();
                for ($i=1; $i<=26; $i++) {
                    $new_meta_value[$i] = $_POST['hdQue-post-class'.$i];
                    if ($i < 26) {
                        $new_meta_value[$i] = sanitize_text_field($new_meta_value[$i]);
                    } else {
                        // for hdQue-post-class26 -> the editor
                        $new_meta_value[$i] = wp_kses_post($new_meta_value[$i]);
                    }
                    $meta_key[$i] = 'hdQue_post_class'.$i;
                    $meta_value[$i] = get_post_meta($post_id, $meta_key[$i], true);
                    if ($new_meta_value[$i] && '' == $meta_value[$i]) {
                        add_post_meta($post_id, $meta_key[$i], $new_meta_value[$i], true);
                    } elseif ($new_meta_value[$i] && $new_meta_value[$i] != $meta_value[$i]) {
                        update_post_meta($post_id, $meta_key[$i], $new_meta_value[$i]);
                    } elseif ('' == $new_meta_value[$i] && $meta_value[$i]) {
                        delete_post_meta($post_id, $meta_key[$i], $meta_value[$i]);
                    }
                }
            }
        }
    }
}
?>
