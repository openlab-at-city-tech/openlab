<?php
/*
    HD Quiz custom post types
    Creates the post_type_questionna post type
    TODO: Restrict menus to only editors+
*/

/* Register Forms
------------------------------------------------------- */
function hdq_cpt_quizzes()
{
    $labels = array(
            'name'                => _x('Questions', 'Post Type General Name', 'text_domain'),
            'singular_name'       => _x('HD Quiz', 'Post Type Singular Name', 'text_domain'),
            'menu_name'           => __('HD Quiz', 'text_domain'),
            'name_admin_bar'      => __('HD Quiz', 'text_domain'),
            'parent_item_colon'   => __('Parent Question:', 'text_domain'),
            'all_items'           => __('All Questions', 'text_domain'),
            'add_new_item'        => __('Add New Question', 'text_domain'),
            'add_new'             => __('Add New Question', 'text_domain'),
            'new_item'            => __('New Question', 'text_domain'),
            'edit_item'           => __('Edit Question', 'text_domain'),
            'update_item'         => __('Update Question', 'text_domain'),
            'view_item'           => __('View Question', 'text_domain'),
            'search_items'        => __('Search Question', 'text_domain'),
            'not_found'           => __('Not found', 'text_domain'),
            'not_found_in_trash'  => __('Not found in Trash', 'text_domain'),
        );
    $args = array(
            'label'               => __('HD Quiz', 'text_domain'),
            'description'         => __('Post Type Description', 'text_domain'),
            'labels'              => $labels,
            'supports'            => array( 'title', 'thumbnail','quiz'),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-clipboard',
            'show_in_admin_bar'   => false,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capability_type'     => 'page',
        );
    register_post_type('post_type_questionna', $args);
}
add_action('init', 'hdq_cpt_quizzes', 0);

function hdq_tax_quizzes()
{
    $labels = array(
        'name'                       => _x('Quizzes', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Quiz', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Quizzes', 'text_domain'),
        'all_items'                  => __('All Quizzes', 'text_domain'),
        'parent_item'                => __('Parent Quiz', 'text_domain'),
        'parent_item_colon'          => __('Parent Quiz:', 'text_domain'),
        'new_item_name'              => __('New Quiz Name', 'text_domain'),
        'add_new_item'               => __('Add A New Quiz', 'text_domain'),
        'edit_item'                  => __('Edit Quiz', 'text_domain'),
        'update_item'                => __('Update Quiz', 'text_domain'),
        'view_item'                  => __('View Quiz', 'text_domain'),
        'separate_items_with_commas' => __('Separate Quizzes with commas', 'text_domain'),
        'add_or_remove_items'        => __('Add or remove Quizzes', 'text_domain'),
        'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
        'popular_items'              => __('Popular Quizzes', 'text_domain'),
        'search_items'               => __('Search Quizzes', 'text_domain'),
        'not_found'                  => __('Not Found', 'text_domain'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => false,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => false,
        'rewrite'                    => false,
    );
    register_taxonomy('quiz', array( 'post_type_questionna' ), $args);
}
add_action('init', 'hdq_tax_quizzes', 0);

/* Set custom title placeholder for questions
------------------------------------------------------- */
function hdq_change_default_title($title)
{
    $screen = get_current_screen();
    if ('post_type_questionna' == $screen->post_type) {
        $title = 'Enter Question Here';
    }
    return $title;
}
add_filter('enter_title_here', 'hdq_change_default_title');

/* Add meta to quizzes
 * NOTE: This was built before taxonomy_meta existed,
 * so this is saving to the _options table.
 * Will create upgrade tool for WP 5.0 release
------------------------------------------------------- */

function hdq_quiz_taxonomy_custom_fields($tag)
{

	
    // TODO: SANITIZE THIS DATA ON READ
    $t_id = $tag->term_id; // Get the ID of the term you're editing
    $term_meta = get_option("taxonomy_term_$t_id"); // Do the check
	
    ?>
	<tr class="form-field h3Highlight">
		<th scope="row" valign="top" colspan ="2">
			<h3><?php _e('General Quiz Options'); ?></h3>
			<p class ="description small">The basic options for this quiz</p>
		</th>

	</tr>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for ="term_meta[passPercent]"><?php _e('Quiz pass percentage'); ?></label>
		</th>
		<td>
	<input class="hdq_input" type="number" min="0" max="100" name="term_meta[passPercent]" id="term_meta[passPercent]" value="<?php echo $term_meta['passPercent'] ? $term_meta['passPercent'] : '70'; ?>" min = "0" max = "100" size="3" />
	<p class ="description small">Enter the percentage of questions a user needs to get correct to pass the quiz.</p>
		</td>
	</tr>


	<tr class="form-field">
		<th scope="row" valign="top">
			<label for ="term_meta[passText]"><?php _e('Quiz pass text'); ?></label>
		</th>
		<td>
			<?php wp_editor(stripslashes(wp_kses_post($term_meta['passText'])), "hd_quiz_term_meta_passText", array('textarea_name' => 'term_meta[passText]','teeny' => false, 'media_buttons' => true, 'textarea_rows' => 1, 'quicktags' => false)); ?>
	<p class ="description small">Customize the text that appears when a user completes the quiz and achieves the pass percentage or higher.</p>

		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label for ="term_meta[failText]"><?php _e('Quiz fail text'); ?></label>
		</th>
		<td>
				<?php wp_editor(stripslashes(wp_kses_post($term_meta['failText'])), "hd_quiz_term_meta_failText", array('textarea_name' => 'term_meta[failText]','teeny' => false, 'media_buttons' => true, 'textarea_rows' => 1, 'quicktags' => false)); ?>
	<p class ="description small">Customize the text that appears when a user completes the quiz and does not achieve the pass percentage.</p>
		</td>
	</tr>

	<tr class="form-field h3Highlight">
		<th scope="row" valign="top" colspan ="2">
			<h3><?php _e('Quiz Results'); ?></h3>
			<p class ="description small">What happens when a user finishes a quiz</p>
		</th>

	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label><?php _e('Share results'); ?></label>
		</th>
		<td>
	<input type="radio" name="term_meta[shareResults]" id="term_meta[shareResults]1" value="yes" <?php if ($term_meta['shareResults'] =="yes") {
        echo 'checked';
    }
    if (! $term_meta['shareResults']) {
        echo 'checked';
    } ?>><label for="term_meta[shareResults]1"><span></span> Show</label><br/>
	<input type="radio" name="term_meta[shareResults]" id="term_meta[shareResults]2" value="no" <?php if ($term_meta['shareResults'] == "no") {
        echo 'checked';
    } ?>><label for="term_meta[shareResults]2"><span></span> Hide</label>
	<p class ="description small"><br/>This option shows or hides the Facebook and Twitter share buttons that appears when a user completes the quiz.</p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label><?php _e('Results Position'); ?></label>
		</th>
		<td>
	<input type="radio" name="term_meta[resultPos]" id="term_meta[resultPos]1" value="yes" <?php if ($term_meta['resultPos'] =="yes") {
        echo 'checked';
    }
    if (! $term_meta['resultPos']) {
        echo 'checked';
    } ?>><label for="term_meta[resultPos]1"><span></span> Above Quiz</label><br/>
	<input type="radio" name="term_meta[resultPos]" id="term_meta[resultPos]2" value="no" <?php if ($term_meta['resultPos'] == "no") {
        echo 'checked';
    } ?>><label for="term_meta[resultPos]2"><span></span> Below Quiz</label>
	<p class ="description small">The site will automatically scroll to the position of the results.</p>
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label><?php _e('Highlight correct / incorrect <strong>selected</strong> answers on completion'); ?></label>
		</th>
		<td>
	<input type="radio" name="term_meta[showResults]" id="term_meta[showResults]1" value="yes" <?php if ($term_meta['showResults'] =="yes") {
        echo 'checked';
    }
    if (! $term_meta['showResults']) {
        echo 'checked';
    } ?>><label for="term_meta[showResults]1"><span></span> Yes</label><br/>
	<input type="radio" name="term_meta[showResults]" id="term_meta[showResults]2" value="no" <?php if ($term_meta['showResults'] == "no") {
        echo 'checked';
    } ?>><label for="term_meta[showResults]2"><span></span> No</label>
	<p class ="description small">This feature allows you to enable or disable showing what answers a user got right or wrong.</p>

		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label><?php _e('Highlight the correct answers on completion'); ?></label>
		</th>
		<td>
	<input type="radio" name="term_meta[showResultsCorrect]" id="term_meta[showResultsCorrect]1" value="yes" <?php if ($term_meta['showResultsCorrect'] =="yes") {
        echo 'checked';
    } ?>><label for="term_meta[showResultsCorrect]1"><span></span> Yes</label><br/>
	<input type="radio" name="term_meta[showResultsCorrect]" id="term_meta[showResultsCorrect]2" value="no" <?php if ($term_meta['showResultsCorrect'] == "no") {
        echo 'checked';
    }
    if (! $term_meta['showResultsCorrect']) {
        echo 'checked';
    } ?>><label for="term_meta[showResultsCorrect]2"><span></span> No</label>
	<p class ="description small">By default, HD Quiz will only show if a user's <strong>selected</strong> answer was right or wrong.</p>
	<p class ="description small">Enabling this feature will go the extra step and show what the correct answer was if the user got the question wrong.</p>
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label><?php _e('Show the "Text that appears if answer was wrong" even if the user got the question right.'); ?></label>
		</th>
		<td>
	<input type="radio" name="term_meta[showIncorrectAnswerText]" id="term_meta[showIncorrectAnswerText]1" value="yes" <?php if ($term_meta['showIncorrectAnswerText'] =="yes") {
        echo 'checked';
    } ?>><label for="term_meta[showIncorrectAnswerText]1"><span></span> Yes</label><br/>
	<input type="radio" name="term_meta[showIncorrectAnswerText]" id="term_meta[showIncorrectAnswerText]2" value="no" <?php if ($term_meta['showIncorrectAnswerText'] == "no") {
        echo 'checked';
    }
    if (! $term_meta['showIncorrectAnswerText']) {
        echo 'checked';
    } ?>><label for="term_meta[showIncorrectAnswerText]2"><span></span> No</label>
	<p class ="description small">Each indivdual question can have accompanying text that will show if the user selects the wrong answer.</p>
	<p class ="description small">Enabling this feature will go the extra step and show this text even if the selected answer was correct.</p>
		</td>
	</tr>

	<tr class="form-field h3Highlight">
		<th scope="row" valign="top" colspan ="2">
			<h3><?php _e('Advanced Quiz Options'); ?></h3>
			<p class ="description small">These are the advanced options for the quiz if you want that extra control.</p>
		</th>

	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label for ="term_meta[quizTimer]"><?php _e('Timer / Countdown'); ?></label>
			<br/>leave blank to disable
		</th>
		<td>
	<input class="hdq_input" type="number" min="0" max="9999" name="term_meta[quizTimerS]" id="term_meta[quizTimerS]" value="<?php echo $term_meta['quizTimerS'] ? $term_meta['quizTimerS'] : '0'; ?>" size="3" /><br/>
	<p class ="description">Enter how many seconds total. So 3 minutes would be 180. </p>
	<p class ="description small"><strong>Please note</strong> that the timer will NOT work if the below WP Pagination feature is being used (it will work for jQuery pagination).</p>
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label><?php _e('Randomize <u>Question</u> Order'); ?></label>
		</th>
		<td>
	<input type="radio" name="term_meta[randomizeQuestions]" id="term_meta[randomizeQuestions]1" value="rand" <?php if ($term_meta['randomizeQuestions'] =="rand") {
        echo 'checked';
    } ?>><label for="term_meta[randomizeQuestions]1"><span></span> Yes</label><br/>
	<input type="radio" name="term_meta[randomizeQuestions]" id="term_meta[randomizeQuestions]2" value="menu_order" <?php if ($term_meta['randomizeQuestions'] == "menu_order") {
        echo 'checked';
    }
    if (! $term_meta['randomizeQuestions']) {
        echo 'checked';
    } ?>><label for="term_meta[randomizeQuestions]2"><span></span> No</label>
	<p class ="description small"><strong>Please note</strong> that randomizing the questions is NOT possible if the below WP Pagination feature is being used (it will work for jQuery pagination).</p>
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label><?php _e('Randomize <u>Answer</u> Order'); ?></label>
		</th>
		<td>
	<input type="radio" name="term_meta[randomizeAnswers]" id="term_meta[randomizeAnswers]1" value="yes" <?php if ($term_meta['randomizeAnswers'] =="yes") {
        echo 'checked';
    } ?>><label for="term_meta[randomizeAnswers]1"><span></span> Yes</label><br/>
	<input type="radio" name="term_meta[randomizeAnswers]" id="term_meta[randomizeAnswers]2" value="no" <?php if ($term_meta['randomizeAnswers'] == "no") {
        echo 'checked';
    }
    if (! $term_meta['randomizeAnswers']) {
        echo 'checked';
    } ?>><label for="term_meta[randomizeAnswers]2"><span></span> No</label>
	<p class ="description small">This feature will randomize the order that each answer is displayed.</p>

		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label><?php _e('Use Pool of Questions'); ?></label><br/>
			leave blank to disable
		</th>
		<td>
	<input class="hdq_input" type="number" min="0" max="200" name="term_meta[pool]" id="term_meta[pool]" value="<?php echo $term_meta['pool'] ? $term_meta['pool'] : '0'; ?>" size="3" /><br/>
	<p class ="description">Enter how many questions to grab. </p>
	<p class ="description small"><strong>Please note</strong> that this feature CANNOT be used with WP Pagination. This is a limiation of WordPress.</p>
	<p class ="description small">If used, this feature will randomly grab the amount of questions entered from the total amount of questions in that quiz. <strong>Example:</strong> If your quiz has 100 questions but you want the quiz to only contain 20 questions chosen at random.</p>

		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label><?php _e('WP Pagination'); ?></label><br/>
			leave blank to disable
		</th>
		<td>
	<input class="hdq_input" type="number" min="0" max="30" name="term_meta[paginate]" id="term_meta[paginate]" value="<?php echo $term_meta['paginate'] ? $term_meta['paginate'] : '0'; ?>" size="3" /><br/>
	<p class ="description">Enter how many questions per page. </p>
	<p class ="description small"><strong>Please note</strong> that this feature should really only be used if you want to force page refreshes for ad revenue or similar.</p>
	<p class ="description small">Most of you should use the jQuery pagination (found on each individual Question) instead. <a href ="http://harmonicdesign.ca/hd-quiz-pagination/">Click here to learn more</a> about the difference between WordPress pagination and jQuery pagination.</p>

		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label><?php _e('Quiz Shortcode'); ?></label>
		</th>
	<td>
	<p>Use the following shortcode to render this quiz:<br/> <code>[HDquiz quiz = "<?php echo  $_GET["tag_ID"]; ?>"]</code></p>
	<p>The quiz will comprise of any questions attached to this quiz.</p>
	<hr/>
	</td>
	</tr>

	<?php
    // add meta nonce
    wp_nonce_field('hdq_tax_quiz_nonce', 'hdq_tax_quiz_nonce');
}
add_action('quiz_edit_form_fields', 'hdq_quiz_taxonomy_custom_fields', 10, 2);

/* Save taxonomy fields
------------------------------------------------------- */
// A callback function to save our extra taxonomy field(s)
function hdq_save_taxonomy_custom_fields($term_id)
{
    if (isset($_POST[ 'hdq_tax_quiz_nonce' ])) {
        $hdq_nonce = $_POST[ 'hdq_tax_quiz_nonce' ];
        if (wp_verify_nonce($hdq_nonce, 'hdq_tax_quiz_nonce') != false) {
            if (isset($_POST['term_meta'])) {
                $t_id = $term_id;
                $term_meta = get_option("taxonomy_term_$t_id");
                $cat_keys = array_keys($_POST['term_meta']);
                foreach ($cat_keys as $key) {
                    if (isset($_POST['term_meta'][$key])) {
                        if ($key == "passText" || $key == "failText") {
                            $term_meta[$key] = wp_kses_post($_POST['term_meta'][$key]);
                        } else {
                            $term_meta[$key] = sanitize_text_field($_POST['term_meta'][$key]);
                        }
                    }
                }
                //save the option array
                update_option("taxonomy_term_$t_id", $term_meta);
            }
        }
    }
}

add_action('edited_quiz', 'hdq_save_taxonomy_custom_fields', 10, 2);

/* Add shortcode to taxonomy list
------------------------------------------------------- */
function hdq_quiz_columns($defaults)
{
    unset($defaults['description']);
    unset($defaults['slug']);
    $defaults['hdq_quiz_ids'] = __('Shortcode');
    return $defaults;
}
add_filter('manage_edit-quiz_columns', 'hdq_quiz_columns', 5);

// create column data
function hdq_quiz_custom_columns($value, $column_name, $id)
{
    if ($column_name == 'hdq_quiz_ids') {
        return '[HDquiz quiz = "'.(int)$id.'"]';
    }
}
add_action('manage_quiz_custom_column', 'hdq_quiz_custom_columns', 5, 3);
