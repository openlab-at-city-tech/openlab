<?php
// general HDQ functions

// Gutenberg
function hdq_register_block_box()
{
    if (!function_exists('register_block_type')) {
        // Gutenberg is not active.
        return;
    }
    wp_register_script(
        'hdq-block-quiz',
        plugin_dir_url(__FILE__) . '/js/hdq_block.js',
        array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'),
        plugin_dir_url(__FILE__) . '/js/hdq_block.js'
    );
    register_block_type('hdquiz/hdq-block-quiz', array(
        'style' => 'hdq-block-quiz',
        'editor_style' => 'hdq-block-quiz',
        'editor_script' => 'hdq-block-quiz',
    ));
}
add_action('init', 'hdq_register_block_box');

/* Get Quiz list
 * used for the gutenberg block
------------------------------------------------------- */
function hdq_get_quiz_list()
{
    $taxonomy = 'quiz';
    $term_args = array(
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    );
    $tax_terms = get_terms($taxonomy, $term_args);
    $quizzes = array();
    if (!empty($tax_terms) && !is_wp_error($tax_terms)) {
        foreach ($tax_terms as $tax_terms) {
            $quiz = new stdClass;
            $quiz->value = $tax_terms->term_id;
            $quiz->label = $tax_terms->name;
            array_push($quizzes, $quiz);
        }
    }
    echo json_encode($quizzes);
    die();
}
add_action('wp_ajax_hdq_get_quiz_list', 'hdq_get_quiz_list');

/* Check acccess level
 * check if authors should be granted access
------------------------------------------------------- */
function hdq_user_permission()
{
    $hasPermission = false;
    $authorsCan = sanitize_text_field(get_option("hd_qu_authors"));
    if ($authorsCan == "yes") {
        if (current_user_can('publish_posts')) {
            $hasPermission = true;
        }
    } else {
        if (current_user_can('edit_others_pages')) {
            $hasPermission = true;
        }
    }
    return $hasPermission;
}

/* Get Question Answer Meta
 * Returns array metaID, answer text, [featuredImageURL or ID]
------------------------------------------------------- */
function hdq_get_answers($hdq_id)
{

    $allowed_html = array(
        'strong' => array(),
        'em' => array(),
        'code' => array(),
        'sup' => array(),
        'sub' => array(),
    );

    $data = array();
    $hdq_1_answer = wp_kses(get_post_meta($hdq_id, 'hdQue_post_class1', true), $allowed_html);
    $hdq_1_image = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class13', true));
    array_push($data, array(1, $hdq_1_answer, $hdq_1_image));
    $hdq_2_answer = wp_kses(get_post_meta($hdq_id, 'hdQue_post_class3', true), $allowed_html);
    $hdq_2_image = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class14', true));
    array_push($data, array(3, $hdq_2_answer, $hdq_2_image));
    $hdq_3_answer = wp_kses(get_post_meta($hdq_id, 'hdQue_post_class4', true), $allowed_html);
    $hdq_3_image = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class15', true));
    array_push($data, array(4, $hdq_3_answer, $hdq_3_image));
    $hdq_4_answer = wp_kses(get_post_meta($hdq_id, 'hdQue_post_class5', true), $allowed_html);
    $hdq_4_image = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class16', true));
    array_push($data, array(5, $hdq_4_answer, $hdq_4_image));
    $hdq_5_answer = wp_kses(get_post_meta($hdq_id, 'hdQue_post_class6', true), $allowed_html);
    $hdq_5_image = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class17', true));
    array_push($data, array(6, $hdq_5_answer, $hdq_5_image));
    $hdq_6_answer = wp_kses(get_post_meta($hdq_id, 'hdQue_post_class7', true), $allowed_html);
    $hdq_6_image = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class18', true));
    array_push($data, array(7, $hdq_6_answer, $hdq_6_image));
    $hdq_7_answer = wp_kses(get_post_meta($hdq_id, 'hdQue_post_class8', true), $allowed_html);
    $hdq_7_image = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class19', true));
    array_push($data, array(8, $hdq_7_answer, $hdq_7_image));
    $hdq_8_answer = wp_kses(get_post_meta($hdq_id, 'hdQue_post_class9', true), $allowed_html);
    $hdq_8_image = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class20', true));
    array_push($data, array(9, $hdq_8_answer, $hdq_8_image));
    $hdq_9_answer = wp_kses(get_post_meta($hdq_id, 'hdQue_post_class10', true), $allowed_html);
    $hdq_9_image = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class21', true));
    array_push($data, array(10, $hdq_9_answer, $hdq_9_image));
    $hdq_10_answer = wp_kses(get_post_meta($hdq_id, 'hdQue_post_class11', true), $allowed_html);
    $hdq_10_image = sanitize_text_field(get_post_meta($hdq_id, 'hdQue_post_class22', true));
    array_push($data, array(11, $hdq_10_answer, $hdq_10_image));
    return $data;
}

function hdq_get_featured_image_container($image)
{
    $image_ar = hdq_get_featured_image($image);
    $data = '<div class = "hdq_featured_image" data-id = "' . $image_ar[0] . '"><img src = "' . $image_ar[1] . '" alt = ""/></div>';
    return $data;
}

function hdq_get_featured_image($image)
{
    $data = array();
    if (is_numeric($image)) {
        // if this uses image ID instead of URL
        $hdc_featured_image = wp_get_attachment_image_src($image, "thumbnail", false);
        $hdc_featured_image = $hdc_featured_image[0];
        $data = array($image, $hdc_featured_image);
    } else {
        // created with old version of HD Quiz
        if ($image != null && $image != "") {
            $data = array(0, $image);
        } else {
            $data = array(0, "https://via.placeholder.com/150x150?text=UPLOAD");
        }
    }
    return $data;
}

/* Returns object of all quiz options
------------------------------------------------------- */
function hdq_get_quiz_options($hdq_id)
{
    $term_meta = get_option("taxonomy_term_$hdq_id");
    return $term_meta;
}

function hdq_get_answer_image_url($image)
{
    if (is_numeric($image)) {
        // if this uses image ID instead of URL
        $image_url = wp_get_attachment_image_src($image, "hd_qu_size2", false);
        if ($image_url[0] == "" || $image_url[0] == null) {
            $image_url = wp_get_attachment_image_src($image, "thumbnail", false);
        }
        $image = $image_url[0];
        return $image;
    } else {
        // figure out what the original custom image size was
        // get the extention -400x400
        $image_parts = explode(".", $image);
        $image_extention = end($image_parts);
        unset($image_parts[count($image_parts) - 1]);
        $image_url = implode(".", $image_parts);
        $image_url = $image_url . '-400x400.' . $image_extention;
        return $image_url;
    }
}

/* Prints the result divs
------------------------------------------------------- */
function hdq_get_results($hdq_quiz_options)
{
    $resultsPercent = sanitize_text_field(get_option("hd_qu_percent"));
    $pass = stripslashes(wp_kses_post($hdq_quiz_options["passText"]));
    $pass = apply_filters('the_content', $pass);
    $fail = stripslashes(wp_kses_post($hdq_quiz_options["failText"]));
    $fail = apply_filters('the_content', $fail);
    $result_text = sanitize_text_field(get_option("hd_qu_results"));
    $fb_appId = sanitize_text_field(get_option("hd_qu_fb"));
    if ($result_text == null || $result_text == "") {
        $result_text = "Results";
    }
    $shareResults = sanitize_text_field($hdq_quiz_options["shareResults"]);?>


	<div class = "hdq_results_wrapper hdq_question">
		<div class = "hdq_results_inner">
			<h2><?php echo $result_text; ?></h2>
			<div class = "hdq_result"><?php if ($resultsPercent == "yes") {echo ' - <span class = "hdq_result_percent"></span>';}?></div>
			<div class = "hdq_result_pass"><?php echo $pass; ?></div>
			<div class = "hdq_result_fail"><?php echo $fail; ?></div>
			<?php
if ($shareResults === "yes") {
        ?>
					<div class = "hdq_share">
						<?php
if ($fb_appId == "" || $fb_appId == null) {
            ?>
						<div class = "hdq_social_icon">
							<a href="http://www.facebook.com/sharer/sharer.php?u=<?php echo the_permalink(); ?>&amp;title=Quiz" target="_blank" class = "hdq_facebook">
								<img src="<?php echo plugins_url('/images/fbshare.png', __FILE__); ?>" alt="Share your score!">
							</a>
						</div>
						<?php
} else {
            hd_get_fb_app_share($fb_appId);
        }
        ?>
						<div class = "hdq_social_icon">
							<a href="#" target="_blank" class = "hdq_twitter"><img src="<?php echo plugins_url('/images/twshare.png', __FILE__); ?>" alt="Tweet your score!"></a>
						</div>
					</div>
				<?php
}?>
		</div>
	</div>

	<?php
}

function hd_get_fb_app_share($fb_appId)
{
    ?>


	<script>
	  window.fbAsyncInit = function() {
		FB.init({
		  appId            : '<?php echo $fb_appId; ?>',
		  autoLogAppEvents : true,
		  xfbml            : true,
		  version          : 'v3.2'
		});
	  };

	  (function(d, s, id){
		 var js, fjs = d.getElementsByTagName(s)[0];
		 if (d.getElementById(id)) {return;}
		 js = d.createElement(s); js.id = id;
		 js.src = "https://connect.facebook.net/en_US/sdk.js";
		 fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));

	</script>


	<div class = "hdq_social_icon">
			<img id = "hdq_fb_sharer" src="<?php echo plugins_url('/images/fbshare.png', __FILE__); ?>" alt="Share your score!">
	</div>


<?php
}

function hdq_print_question_as_title($i, $hdq_q_id, $hdq_tooltip)
{
    $hdq_answers = hdq_get_answers($hdq_q_id);?>
				<div class = "hdq_question hdq_question_title">
					<?php
if (has_post_thumbnail()) {
        echo '<div class = "hdq_question_featured_image">';
        the_post_thumbnail();
        echo '</div>';
    }?>
					<h3><?php echo get_the_title($hdq_q_id); ?></h3>
				</div>
	<?php
}

function hdq_print_question_normal($i, $hdq_q_id, $hdq_tooltip, $hdq_after_answer, $hdq_selected, $hdq_random_answer_order)
{
    $hdq_answers = hdq_get_answers($hdq_q_id);
    // add the 'correct' to array in case we randomize
    @array_push($hdq_answers[$hdq_selected - 1], "checked");
    if ($hdq_random_answer_order === "yes") {
        shuffle($hdq_answers);
    }?>

				<div class = "hdq_question" id = "hdq_question_<?php echo $hdq_q_id; ?>">
					<?php
if (has_post_thumbnail()) {
        echo '<div class = "hdq_question_featured_image">';
        the_post_thumbnail();
        echo '</div>';
    }?>
					<h3>
						<?php
$question_number = hdq_get_paginate_question_number($i);
    echo '<span class = "hdq_question_number">#' . $question_number . "</span> " . get_the_title($hdq_q_id);
    if ($hdq_tooltip != "" && $hdq_tooltip != null) {
        echo '<span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>' . $hdq_tooltip . '</span></span></span>';
    }?>
					</h3>
					<?php
$x = 0;
    foreach ($hdq_answers as $answer) {
        if ($answer[1] != "" && $answer[1] != null) {
            $x = $x + 1;
            $hdq_is_correct = "";
            if (array_key_exists(3, $answer)) {
                $hdq_is_correct = "1";
            } else {
                $hdq_is_correct = "0";
            }?>
								<div class = "hdq_row">
									<label class="hdq_label_answer" data-type = "radio" data-id = "hdq_question_<?php echo $hdq_q_id; ?>" for="hdq_option_<?php echo $i . '_' . $x; ?>">
										<div class="hdq-options-check">
											<input type="checkbox" class="hdq_option hdq_check_input" value="<?php echo $hdq_is_correct; ?>" name="hdq_option_<?php echo $i . '_' . $x; ?>" id="hdq_option_<?php echo $i . '_' . $x; ?>">
											<label for="hdq_option_<?php echo $i . '_' . $x; ?>"></label>
										</div>
										<?php echo $answer[1]; ?>
									</label>
								</div>
							<?php
}
    }
    if ($hdq_after_answer != "" && $hdq_after_answer != null) {
        echo '<div class = "hdq_question_after_text">';
        echo apply_filters('the_content', $hdq_after_answer);
        echo '</div>';
    }?>
				</div>


	<?php
}

function hdq_print_question_image($i, $hdq_q_id, $hdq_tooltip, $hdq_after_answer, $hdq_selected, $hdq_random_answer_order)
{
    $hdq_answers = hdq_get_answers($hdq_q_id);
    // add the 'correct' to array in case we randomize
    array_push($hdq_answers[$hdq_selected - 1], "checked");
    if ($hdq_random_answer_order === "yes") {
        shuffle($hdq_answers);
    }?>

				<div class = "hdq_question hdq_question_images" id = "hdq_question_<?php echo $hdq_q_id; ?>">
					<?php
if (has_post_thumbnail()) {
        echo '<div class = "hdq_question_featured_image">';
        the_post_thumbnail();
        echo '</div>';
    }?>
					<h3>
						<?php
$question_number = hdq_get_paginate_question_number($i);
    echo '<span class = "hdq_question_number">#' . $question_number . "</span> " . get_the_title($hdq_q_id);
    if ($hdq_tooltip != "" && $hdq_tooltip != null) {
        echo '<span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>' . $hdq_tooltip . '</span></span></span>';
    }?>
					</h3>
					<?php
$x = 0;
    foreach ($hdq_answers as $answer) {
        if ($answer[1] != "" && $answer[1] != null) {
            $answer_image = hdq_get_answer_image_url($answer[2]);
            $x = $x + 1;
            $hdq_is_correct = "";
            if (array_key_exists(3, $answer)) {
                $hdq_is_correct = "1";
            } else {
                $hdq_is_correct = "0";
            }
            if ($x % 2 != 0) {
                echo '<div class = "hdq_one_half">';
            } else {
                echo '<div class = "hdq_one_half hdq_last">';
            }?>

								<div class = "hdq_row">
									<label class="hdq_label_answer" data-type = "image" data-id = "hdq_question_<?php echo $hdq_q_id; ?>" for="hdq_option_<?php echo $i . '_' . $x; ?>">
										<img src = "<?php echo $answer_image; ?>" alt = ""/>
										<div class="hdq-options-check">
											<input type="checkbox" class="hdq_option hdq_check_input" value="<?php echo $hdq_is_correct; ?>" name="hdq_option_<?php echo $i . '_' . $x; ?>" id="hdq_option_<?php echo $i . '_' . $x; ?>">
											<label for="hdq_option_<?php echo $i . '_' . $x; ?>"></label>
										</div>
										<?php echo $answer[1]; ?>
									</label>
								</div>
						</div>
							<?php
if ($x % 2 == 0) {
                echo '<div class = "clear"></div>';
            }
        }
    }
    if ($hdq_after_answer != "" && $hdq_after_answer != null) {
        echo '<div class = "hdq_question_after_text">';
        echo apply_filters('the_content', $hdq_after_answer);
        echo '</div>';
    }?>
				</div>


	<?php
}

function hdq_print_jPaginate($hdq_id)
{
    $next_text = sanitize_text_field(get_option("hd_qu_next"));
    if ($next_text == "" || $next_text == null) {
        $next_text = "next";
    }
    echo '<div class = "hdq_jPaginate"><div class = "hdq_next_button hdq_button" data-id = "' . $hdq_id . '">' . $next_text . '</div></div>';
}

function hdq_print_finish($hdq_id)
{
    $finish_text = sanitize_text_field(get_option("hd_qu_finish"));
    if ($finish_text == "" || $finish_text == null) {
        $finish_text = "finish";
    }
    echo '<div class = "hdq_finish hdq_jPaginate"><div class = "hdq_finsh_button hdq_button" data-id = "' . $hdq_id . '">' . $finish_text . '</div></div>';
}

function hdq_print_next($hdq_id, $page_num)
{
    $next_text = sanitize_text_field(get_option("hd_qu_next"));
    if ($next_text == "" || $next_text == null) {
        $next_text = "next";
    }
    $page_num = $page_num + 1;
    $next_page_data = get_the_permalink();
    $next_page_data = $next_page_data . 'page/' . $page_num . '?currentScore=';
    echo '<div class = "hdq_next_page"><a class = "hdq_next_page_button hdq_button" data-id = "' . $hdq_id . '" href = "' . $next_page_data . '">' . $next_text . '</a></div>';
}

function hdq_get_paginate_question_number($i)
{
    if (isset($_GET['totalQuestions'])) {
        return intval($_GET['totalQuestions'] + $i);
    } else {
        return $i;
    }
}

/* Admin Ajax Stuff
------------------------------------------------------- */
function hdq_view_quiz()
{
    if (hdq_user_permission()) {
        $hdq_nonce = $_POST['hdq_quizzes_nonce'];
        if (wp_verify_nonce($hdq_nonce, 'hdq_quizzes_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include dirname(__FILE__) . '/view_question_tax.php';
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdq_view_quiz', 'hdq_view_quiz');

function hdq_save_quiz()
{
    if (hdq_user_permission()) {
        $hdq_nonce = $_POST['hdq_quizzes_nonce'];
        if (wp_verify_nonce($hdq_nonce, 'hdq_quizzes_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include dirname(__FILE__) . '/save_quiz.php';
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdq_save_quiz', 'hdq_save_quiz');

function hdq_save_question()
{
    if (hdq_user_permission()) {
        $hdq_nonce = $_POST['hdq_quizzes_nonce'];
        if (wp_verify_nonce($hdq_nonce, 'hdq_quizzes_nonce') != false) {
            // permission granted
            $quiz_ids = $_POST['quiz_ids'];
            $quiz_ids2 = $quiz_ids;
            $quiz_ids = array();
            foreach ($quiz_ids2 as $q) {
                array_push($quiz_ids, intval($q));
            }
            $question_id = intval($_POST['question_id']);
            $title = sanitize_text_field($_POST['title']);
            $image_based_answers = sanitize_text_field($_POST['image_based_answers']);
            $question_as_title = sanitize_text_field($_POST['question_as_title']);
            $paginate = sanitize_text_field($_POST['paginate']);
            $answers = $_POST['answers'];
            $answer_correct = intval($_POST['answer_correct']);

            $featured_image = intval($_POST['featured_image']);
            $tooltip = sanitize_text_field($_POST['tooltip']);
            $extra_text = wp_kses_post($_POST['extra_text']);

            $allowed_html = array(
                'strong' => array(),
                'em' => array(),
                'code' => array(),
                'sup' => array(),
                'sub' => array(),
            );

            if ($question_id == 0 || $question_id == "" || $question_id == null) {
                // get total count to set initial menu_order
                $men_order = get_term($quiz_ids[0]);
                $men_order = $men_order->count + 1;

                $post_information = array(
                    'post_title' => $title,
                    'post_content' => '', // need to set as blank
                    'post_type' => 'post_type_questionna',
                    'post_status' => 'publish',
                    'menu_order' => intval($men_order),
                );
                $question_id = wp_insert_post($post_information);
            }

            $answers = json_decode(html_entity_decode(stripslashes($answers)), false);
            foreach ($answers as $answer) {
                $meta_key = sanitize_text_field($answer[0]);
                $meta_value = wp_kses($answer[1], $allowed_html);
                $meta_image_key = sanitize_text_field($answer[2]);
                $meta_image_value = sanitize_text_field($answer[3]);
                if ($meta_key != "" && $meta_key != null) {
                    $meta_key2 = str_replace("-", "_", $meta_key);
                    update_post_meta($question_id, $meta_key2, $meta_value, false);
                }
                if ($meta_image_key != "" && $meta_image_key != null) {
                    $meta_image_key2 = str_replace("-", "_", $meta_image_key);
                    update_post_meta($question_id, $meta_image_key2, $meta_image_value, false);
                }

            }
            update_post_meta($question_id, 'hdQue_post_class23', $image_based_answers, false);
            update_post_meta($question_id, 'hdQue_post_class24', $question_as_title, false);
            update_post_meta($question_id, 'hdQue_post_class25', $paginate, false);
            update_post_meta($question_id, 'hdQue_post_class2', $answer_correct, false);
            update_post_meta($question_id, 'hdQue_post_class12', $tooltip, false);
            update_post_meta($question_id, 'hdQue_post_class26', $extra_text, false);
            if ($featured_image > 0) {
                set_post_thumbnail($question_id, $featured_image);
            }

            // update post title too
            $hdq_post = array(
                'ID' => $question_id,
                'post_title' => $title,
            );
            wp_update_post($hdq_post);

            // set categoires
            $test = wp_set_post_terms($question_id, $quiz_ids, "quiz");
            echo 'updated|' . $question_id;

        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdq_save_question', 'hdq_save_question');

function hdq_delete_question()
{
    if (hdq_user_permission()) {
        $hdq_nonce = $_POST['hdq_quizzes_nonce'];
        if (wp_verify_nonce($hdq_nonce, 'hdq_quizzes_nonce') != false) {
            // permission granted
            $question_id = intval($_POST['question_id']);
            wp_delete_post($question_id); // will move to trash
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }

    die();
}
add_action('wp_ajax_hdq_delete_question', 'hdq_delete_question');

function hdq_add_new_quiz()
{
    if (hdq_user_permission()) {
        $hdq_nonce = $_POST['hdq_quizzes_nonce'];
        if (wp_verify_nonce($hdq_nonce, 'hdq_quizzes_nonce') != false) {
            $hdq_new_quiz = sanitize_text_field($_POST['hdq_new_quiz']);
            $hdq_new_quiz = wp_insert_term(
                $hdq_new_quiz, // the term
                'quiz' // the taxonomy
            );
            echo $hdq_new_quiz["term_id"];
        } else {
            echo 'permission denied';
        }
    } else {
        echo 'permission denied';
    }
    die();
}
add_action('wp_ajax_hdq_add_new_quiz', 'hdq_add_new_quiz');

function hdq_view_question()
{
    if (hdq_user_permission()) {
        $hdq_nonce = $_POST['hdq_quizzes_nonce'];
        if (wp_verify_nonce($hdq_nonce, 'hdq_quizzes_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include dirname(__FILE__) . '/view_question.php';
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdq_view_question', 'hdq_view_question');

function hdq_print_quiz_settings($quiz_id)
{
    $quiz_id = intval($quiz_id);
    $term_meta = get_option("taxonomy_term_$quiz_id");
    $term_meta = hdq_return_quiz_options($term_meta);
    ?>

		<h3>General Quiz Options</h3>
		<div class = "hdq_row">
			<label for = "hdq_quiz_pass_percent">Quiz Pass Percentage</label>
			<input type = "number" name = "hdq_quiz_pass_percent" id = "hdq_quiz_pass_percent" class = "hdq_input" min = "1" max = "100" value = "<?php echo $term_meta->passPercent; ?>"/>
		</div>

		<div class = "hdq_row">
			<label for = "hdq_quiz_pass_text">Quiz Pass Text</label>
			<?php wp_editor($term_meta->passText, "hd_quiz_term_meta_passText", array('textarea_name' => 'hd_quiz_term_meta_passText', 'teeny' => false, 'media_buttons' => true, 'textarea_rows' => 10, 'quicktags' => true));?>
		</div>

		<div class = "hdq_row">
			<label for = "hdq_quiz_fail_text">Quiz Fail Text</label>
			<?php wp_editor($term_meta->failText, "hd_quiz_term_meta_failText", array('textarea_name' => 'hd_quiz_term_meta_failText', 'teeny' => false, 'media_buttons' => true, 'textarea_rows' => 10, 'quicktags' => true));?>
		</div>

		<h3>Once Quiz Has Been Completed</h3>

		<div class = "hdq_one_half">
			<div class="hdq_row hdq_checkbox">
				<label class="hdq_label_title" for="hdq_share_results"> Share Quiz Results</label>
				<div class="hdq_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="hdq_share_results" value="yes" name="hdq_share_results" <?php if ($term_meta->shareResults === "yes") {echo 'checked';}?>>
						<label for="hdq_share_results"></label>
					</div>
				</div>
				<p>
					This option shows or hides the Facebook and Twitter share buttons that appears when a user completes the quiz.
				</p>
			</div>
		</div>
		<div class = "hdq_one_half hdq_last">
			<div class="hdq_row hdq_checkbox">
				<label class="hdq_label_title" for="hdq_results_position"> Show Results Above Quiz</label>
				<div class="hdq_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="hdq_results_position" value="yes" name="hdq_results_position" <?php if ($term_meta->resultPos === "yes") {echo 'checked';}?>>
						<label for="hdq_results_position"></label>
					</div>
				</div>
				<p>
					The site will automatically scroll to the position of the results.<br/>
					<small>if you notice any issues with your site not scrolling to the correct position, please let me know <a href = "https://harmonicdesign.ca/hd-quiz" target ="_blank">here</a>.</small>
				</p>
			</div>
		</div>
		<div class = "clear"></div>

		<hr/>
		<br/>

		<div class = "hdq_one_half">
			<div class="hdq_row hdq_checkbox">
				<label class="hdq_label_title" for="hdq_show_results"> Highlight correct / incorrect selected answers on completion</label>
				<div class="hdq_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="hdq_show_results" value="yes" name="hdq_show_results" <?php if ($term_meta->showResults === "yes") {echo 'checked';}?>>
						<label for="hdq_show_results"></label>
					</div>
				</div>
				<p>
					This will show the user which questions they got right, and which they got wrong.
				</p>
			</div>
		</div>
		<div class = "hdq_one_half hdq_last">
			<div class="hdq_row hdq_checkbox">
				<label class="hdq_label_title" for="hdq_show_results_correct"> Show the correct answers on completion</label>
				<div class="hdq_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="hdq_show_results_correct" value="yes" name="hdq_show_results_correct" <?php if ($term_meta->showResultsCorrect === "yes") {echo 'checked';}?>>
						<label for="hdq_show_results_correct"></label>
					</div>
				</div>
				<p>
					This feature goes the extra step and shows what the correct answer was, in the case that the user selected the wrong one.
				</p>
			</div>
		</div>
		<div class = "clear"></div>

		<hr/>
		<br/>

		<div class="hdq_row hdq_checkbox">
			<label class="hdq_label_title" for="hdq_show_extra_text"> Always Show Incorrect Answer Text</label>
			<div class="hdq_check_row">
				<div class="hdq-options-check">
					<input type="checkbox" id="hdq_show_extra_text" value="yes" name="hdq_show_extra_text" <?php if ($term_meta->showIncorrectAnswerText === "yes") {echo 'checked';}?>>
					<label for="hdq_show_extra_text"></label>
				</div>
			</div>
			<p>
				Each indivdual question can have accompanying text that will show if the user selects the wrong answer. Enabling this feature will force this text to show even if the selected answer was correct.
			</p>
		</div>

		<h3>Extra Quiz Options</h3>


		<div class = "hdq_one_half">
			<div class="hdq_row hdq_checkbox">
				<label class="hdq_label_title" for="hdq_immediate_mark"> Immediately mark answer as correct or incorrect</label>
				<div class="hdq_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="hdq_immediate_mark" value="yes" name="hdq_immediate_mark" <?php if ($term_meta->immediateMark === "yes") {echo 'checked';}?>>
						<label for="hdq_immediate_mark"></label>
					</div>
				</div>
				<p>
					Enabling this will show if the answer was right or wrong as soon as an answer has been selected.
				</p>
			</div>
		</div>

		<div class = "hdq_one_half hdq_last">
			<div class="hdq_row hdq_checkbox">
				<label class="hdq_label_title" for="hdq_stop_answer_reselect"> Stop users from changing their answers</label>
				<div class="hdq_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="hdq_stop_answer_reselect" value="yes" name="hdq_stop_answer_reselect" <?php if ($term_meta->stopAnswerReselect === "yes") {echo 'checked';}?>>
						<label for="hdq_stop_answer_reselect"></label>
					</div>
				</div>
				<p>
					Enabling this will stop users from being able to change their answer once one has been selected.
				</p>
			</div>
		</div>

		<div class = "clear"></div>

		<hr/>
		<br/>


		<div class="hdq_row">
			<label class="hdq_label_title" for="hdq_quiz_timer"> Timer / Countdown</label>
			<input type="number" id="hdq_quiz_timer" name="hdq_quiz_timer" class = "hdq_input" value = "<?php echo $term_meta->quizTimerS; ?>" min = "0" placeholder = "leave blank to disable"/>
			<p>
				Enter how many seconds total. So 3 minutes would be 180. Please note that the timer will NOT work if the below WP Pagination feature is being used.
			</p>
		</div>
		<hr/>
		<br/>

		<div class = "hdq_one_half">
			<div class="hdq_row hdq_checkbox">
				<label class="hdq_label_title" for="hdq_randomize_question_order"> Randomize <u>Question</u> Order</label>
				<div class="hdq_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="hdq_randomize_question_order" value="yes" name="hdq_randomize_question_order" <?php if ($term_meta->randomizeQuestions === "yes") {echo 'checked';}?>>
						<label for="hdq_randomize_question_order"></label>
					</div>
				</div>
				<p>
					Please note that randomizing the questions is NOT possible if the below WP Pagination feature is being used.<br/>
					<small>and also not a good idea to use this if you are using the "questions as title" option for any questions attached to this quiz</small>
				</p>
			</div>
		</div>
		<div class = "hdq_one_half hdq_last">
			<div class="hdq_row hdq_checkbox">
				<label class="hdq_label_title" for="hdq_randomize_answer_order"> Randomize <u>Answer</u> Order</label>
				<div class="hdq_check_row">
					<div class="hdq-options-check">
						<input type="checkbox" id="hdq_randomize_answer_order" value="yes" name="hdq_randomize_answer_order" <?php if ($term_meta->randomizeAnswers === "yes") {echo 'checked';}?>>
						<label for="hdq_randomize_answer_order"></label>
					</div>
				</div>
				<p>
					This feature will randomize the order that each answer is displayed and is compatible with WP Pagination.
				</p>
			</div>
		</div>
		<div class = "clear"></div>

		<hr/>
		<br/>

		<div class = "hdq_one_half">
			<div class="hdq_row">
				<label class="hdq_label_title" for="hdq_pool_of_questions"> Use Pool of Questions</label>
				<input type="number" min = "0" max = "100" class = "hdq_input" id="hdq_pool_of_questions" name="hdq_pool_of_questions" value = "<?php echo $term_meta->pool; ?>">
				<p>
					If you want each quiz to randomly grab a number of questions from the quiz, then enter that amount here. So, for example, you might have 100 questions attached to this quiz, but entering 20 here will make the quiz randomly grab 20 of the questions on each load.
				</p>
			</div>
		</div>
		<div class = "hdq_one_half hdq_last">
			<div class="hdq_row">
				<label class="hdq_label_title" for="hdq_wp_paginate"> WP Pagination</label>
				<input type="number" min = "0" max = "100" class = "hdq_input" id="hdq_wp_paginate" name="hdq_wp_paginate" value = "<?php echo $term_meta->paginate; ?>">
				<p>
					NOTE: It is recommended to not use this feature unless necessary.<br/>
					<small>WP Paginate will force this number of questions per page, and force new page loads for each new question group. The <em>only</em> benefit of this is for additional ad views. The downside is reduced compatibility of features. It is recommended to use the "paginate" option on each question instead.</small>
				</p>
			</div>
		</div>
		<div class = "clear"></div>
		<p style = "text-align:center">
			<strong>Save these settings by selecting "SAVE QUIZ" located at the top of this page</strong>
		</p>
	<?php
}

function hdq_return_quiz_options($term_meta)
{
    $hdq_settings = new \stdClass();

    $passPercent = intval($term_meta['passPercent']);

    if ($passPercent == 0 || $passPercent == null) {
        // since this isn't set, we know that the user has never saved the quiz before
        // set all values to default
        $passPercent = 70;
        $passText = "";
        $failText = "";
        $shareResults = "yes";
        $resultPos = "yes";
        $showResults = "yes";
        $showResultsCorrect = "no";
        $showIncorrectAnswerText = "no";
        $quizTimerS = 0;
        $randomizeQuestions = "menu_order";
        $randomizeAnswers = "no";
        $pool = 0;
        $paginate = 0;
		$immediateMark = "no";
		$stopAnswerReselect = "no";
    } else {
        // continue getting data
        $passText = stripslashes(wp_kses_post($term_meta['passText']));
        $failText = stripslashes(wp_kses_post($term_meta['failText']));
        $shareResults = sanitize_text_field($term_meta['shareResults']);
        $resultPos = sanitize_text_field($term_meta['resultPos']);
        $showResults = sanitize_text_field($term_meta['showResults']);
        $showResultsCorrect = sanitize_text_field($term_meta['showResultsCorrect']);
        $showIncorrectAnswerText = sanitize_text_field($term_meta['showIncorrectAnswerText']);
        $quizTimerS = intval($term_meta['quizTimerS']);
        $randomizeQuestions = sanitize_text_field($term_meta['randomizeQuestions']);
        $randomizeAnswers = sanitize_text_field($term_meta['randomizeAnswers']);
        $pool = intval($term_meta['pool']);
        $paginate = intval($term_meta['paginate']);
		$immediateMark = sanitize_text_field($term_meta['immediateMark']);
		$stopAnswerReselect = sanitize_text_field($term_meta['stopAnswerReselect']);		
    }

    $hdq_settings->passPercent = $passPercent;
    $hdq_settings->passText = $passText;
    $hdq_settings->failText = $failText;
    $hdq_settings->shareResults = $shareResults;
    $hdq_settings->resultPos = $resultPos;
    $hdq_settings->showResults = $showResults;
    $hdq_settings->showResultsCorrect = $showResultsCorrect;
    $hdq_settings->showIncorrectAnswerText = $showIncorrectAnswerText;
    $hdq_settings->quizTimerS = $quizTimerS;
    $hdq_settings->randomizeQuestions = $randomizeQuestions;
    $hdq_settings->randomizeAnswers = $randomizeAnswers;
    $hdq_settings->pool = $pool;
    $hdq_settings->paginate = $paginate;
	$hdq_settings->immediateMark = $immediateMark;
	$hdq_settings->stopAnswerReselect = $stopAnswerReselect;

    return $hdq_settings;
}