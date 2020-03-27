<?php
	// view question

    $hdq_id =  intval($_POST['question_id']);
	$hdq_quiz_id =  intval($_POST['quiz_id']);

	if ($hdq_quiz_id == "" || $hdq_quiz_id == null){
		$hdq_quiz_id = $hdq_id;
		$hdq_id = null;
	}

    $hdq_selected = intval(get_post_meta($hdq_id, 'hdQue_post_class2', true));
    $hdq_image_as_answer = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class23', true));
    $hdq_question_as_title = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class24', true));
    $hdq_paginate = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class25', true));
    $hdq_tooltip = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class12', true));
    $hdq_after_answer = wp_kses_post(get_post_meta($hdq_id, 'hdQue_post_class26', true));
    $hdq_answers = hdq_get_answers($hdq_id);

	$hdq_featured_image_id = get_post_thumbnail_id($hdq_id );
	if ($hdq_featured_image_id != "" && $hdq_featured_image_id != null){
    	$hdq_featured_image_url = wp_get_attachment_image_src($hdq_featured_image_id, "full", false);
    	$hdq_featured_image_url = $hdq_featured_image_url[0];
	} else {
		$hdq_featured_image_url = "https://via.placeholder.com/500x300?text=SET+FEATURED+IMAGE";
	}
?>
<input type = "hidden" id = "question_id" value = "<?php echo $hdq_id; ?>"/>
<div id = "hdq_question_buttons">
	<div class = "hdq_button3" data-id = "<?php echo $hdq_quiz_id; ?>" id = "hdq_back_to_quiz">
		<span class="dashicons dashicons-arrow-left-alt"></span> BACK TO QUIZ
	</div>
	<div class = "hdq_button3" data-id = "<?php echo $hdq_quiz_id; ?>" id = "hdq_add_question">
		<span class="dashicons dashicons-plus"></span> ADD NEW QUESTION
	</div>

	<div style = "float:right;">
		
		<div class="hdq_button4" id = "hdq_delete_question" data-id = "<?php echo $hdq_id; ?>"><span class="dashicons dashicons-trash"></span></div>
		
		<div class = "hdq_button2" data-id = "<?php echo $hdq_id; ?>" id = "hdq_save_question">
			<span class="dashicons dashicons-sticky"></span> SAVE QUESTION
		</div>
	</div>
	<div class = "clear"></div>
</div>
<div class = "hdq_row" style = "margin-bottom:44px;">
	<input type = "text" id = "hdq_question_title" class = "hdq_input" value = "<?php echo get_the_title($hdq_id); ?>" placeholder = "Enter the question here..."/>
</div>


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
					<li data-hdq-content="hdq_tab_quizzes">Quizzes</li>
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
				<input type = "hidden" id = "hdQue-post-class2" value = "<?php echo $hdq_selected; ?>"/>
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
					<label>Question Featured Image</label>
					<div class="hdq_featured_image" id = "hdq_featured_image" data-id="<?php if($hdq_featured_image_id == "") {echo "0"; } else {echo $hdq_featured_image_id;} ?>">
						<?php
							echo '<img class = "hdq_question_featured" src = "'.$hdq_featured_image_url.'" alt = ""/>';
						?>
					</div>					
				</div>
				
				<div class = "hdq_row">
					<label for = "hdQue-post-class12">Tooltip Text &nbsp;&nbsp;<span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>This bubble is an example of a tool tip :) </span></span></span></label>
					<input class="hdq_input" type="text" name="hdQue-post-class12" id="hdQue-post-class12" value="<?php echo $hdq_tooltip; ?>" />
				</div>			
				
					<div class = "hdq_row">
					<label for ="hdQue-post-class26">Text that appears if answer was wrong</label>
					<?php
                        wp_editor($hdq_after_answer, "hdQue-post-class26", array('textarea_name' => 'hdQue-post-class26','teeny' => true, 'media_buttons' => true, 'textarea_rows' => 3, 'quicktags' => false)); ?>

				</div>
			</div>
			<div id = "hdq_tab_quizzes" class = "hdq_tab">
				<h3>Add this question to multiple quizzes</h3>
				
				
				
				
				
				
	<?php		

		$terms = get_the_terms( $hdq_id, 'quiz' );
		$product_categories = array();
		if(!empty($terms)){
			foreach ( $terms as $term ) {
				array_push($product_categories, $term->term_id);
			}
		}

		// first, grab a list of parent categories
		// and store in $parentsList
		$terms = get_terms(array(
			'taxonomy' => 'quiz',
			'hide_empty' => false,
			'parent' => 0
		));

		$parentsList = array();
		$termsList = array();

		if ($terms && ! is_wp_error($terms ) ) {
    		foreach ($terms as $term ) {
				 array_push($parentsList, array($term->name, $term->term_id, $term->parent, null));
    		}
		}

		// now that we have a list of the parents,
		// and their IDs, let's get the children
		// TODO: I reused this from HDCommerce, but HD Quiz does not used nested taxonomies
		// so I need to simplify this down since we no longer care about "children" taxes
		$counter = 0;
		foreach ($parentsList as $termId ) {
			$terms = get_terms(array(
				'taxonomy' => 'hdq_products',
				'hide_empty' => false,
				'parent' => $termId[1]
			));

			if ($terms && ! is_wp_error($terms ) ) {
				$counter2 = 0;
				foreach ($terms as $term ) {
					 $counter2 = $counter2 + 1;
					 if ($counter2 == 1){
						 // also push the original parent
						 array_push($termsList, array($parentsList[$counter][0],$parentsList[$counter][1],$parentsList[$counter][2]));
					 }
					 array_push($termsList, array($term->name, $term->term_id, $term->parent, null));
					// now we need to go another layer deep.
					// This will be three layers, or 2 children from a parent
					$terms2 = get_terms(array(
						'taxonomy' => 'hdq_products',
						'hide_empty' => false,
						'parent' => $term->term_id
					));
					if ($terms2 && ! is_wp_error($terms ) ) {
						foreach ($terms2 as $term2 ) {
							 array_push($termsList, array($term2->name, $term2->term_id, $term2->parent, "isSubChild"));
						}
					}
				}
				$counter = $counter + 1;
			} else {
				array_push($termsList, array($parentsList[$counter][0],$parentsList[$counter][1],$parentsList[$counter][2], null));
				$counter = $counter + 1;
			}
		}
?>

	<div id = "hdq_category_list">
	<?php
		// now print out the array into an ordered list
		$counter = 0;
		$totalCategories = count($termsList);
    	foreach ($termsList as $term ) {
			$counter = $counter + 1;
			$isChecked = "";
			foreach ($product_categories as $cat_id ) {
				if ($cat_id == $term[1] || $hdq_quiz_id == $term[1] ){					
					$isChecked = "checked";
				}
			}
			
			?>
	
					<div class="hdq_check_row hdq_checkbox" style = "margin-bottom:8px"><label for="term_<?php echo $term[1]; ?>" style = "padding-bottom: 4px;"><?php echo $term[0]; ?></label>
						<div class="hdq-options-check">
							<input type="checkbox" id="term_<?php echo $term[1]; ?>" value="yes" data-term = "<?php echo $term[1]; ?>" name="term_<?php echo $term[1]; ?>" <?php echo $isChecked;?>/>
							<label for="term_<?php echo $term[1]; ?>"></label>
						</div>
					</div>		
		
		
			<?php
    	}
	?>
	</div>				
				
				
				
				
				<p>
					NOTE: having the question added to multiple quizzes may create issues with custom question ordering
				</p>
			</div>
				
				
			</div>