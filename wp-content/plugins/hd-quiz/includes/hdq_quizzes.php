<?php
/*
HD Quiz Quizzes Page
 */
wp_nonce_field('hdq_quizzes_nonce', 'hdq_quizzes_nonce');
?>

<div id = "hdq_loading">
	<div id = "hdq_loading_inner">
		<p>
			...
		</p>
	</div>

</div>
<div id = "hdq_meta_forms">
	<div id = "hdq_wrapper" class = "hdq_quizzes_admin">
		<div id = "hdq_message">
			<p></p>
		</div>
		<div id="hdq_form_wrapper">
			<h1 id = "hdq_h1_title">
				HD Quiz - Quizzes
			</h1>

			<a href = "./edit-tags.php?taxonomy=quiz&post_type=post_type_questionna" class = "hdq_button4"><span class="dashicons dashicons-trash"></span> DELETE QUIZZES</a>
			<a href = "./edit.php?post_type=post_type_questionna" class = "hdq_button4"></span> BULK MODIFY QUESTIONS</a>

			<input type = "text" name = "hdq_new_quiz_name" id = "hdq_new_quiz_name" class="hdq_input" placeholder = "add new quiz"/>
			<div class="hdq_input_notification"><span></span>Press "ENTER" to add the quiz</div>

			<p>
				Add a new quiz, or select a quiz below to add / edit questions, or change quiz settings.
			</p>

			<div id = "hdq_list_quizzes">
<?php
$taxonomy = 'quiz';
$term_args = array(
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC',
);
$tax_terms = get_terms($taxonomy, $term_args);

if (!empty($tax_terms) && !is_wp_error($tax_terms)) {
    foreach ($tax_terms as $tax_terms) {
        ?>
				<div class = "hdq_quiz_item hdq_quiz_term" data-id = "<?php echo $tax_terms->term_id; ?>">
					<?php 
						if (function_exists('mb_strimwidth')) {
							echo mb_strimwidth($tax_terms->name, 0, 50, "..."); 	
						} else {
							echo $tax_terms->name;
						}
						
					?> 
					<code>[HDquiz quiz = "<?php echo $tax_terms->term_id; ?>"]</code>
				</div>
<?php
	}
}
?>
			</div>

			<p>
				Hello, and welcome to the new HD Quiz admin area! I spent a great deal of time designing and coding this new backend to make managing your quizzes and questions as easy and intuitive as possible. However, I understand that like with any major change, you may need help, or bugs may arise. If you have any questions or need support, please do not hesitate to ask on the <a href="https://wordpress.org/support/plugin/hd-quiz">official WordPress support page</a> or on our own <a href="http://harmonicdesign.ca/hd-quiz/" target="_blank" rel="noopener">support page at Harmonic Design</a>.
			</p>
			<div class = "hdq_highlight">
				<p>
					HD Quiz is a 100% free plugin developed in my spare time, and as such, I get paid in nothing but good will and positive reviews. If you are enjoying HD Quiz and would like to show your support, please consider contributing to my <a href = "https://www.patreon.com/harmonic_design" target ="_blank">patreon page</a> to help continued development. Every little bit helps, and I am fuelled by ☕.
				</p>
			</div>

		</div>
	</div>
</div>
<div style = "display:none;">
<?php
// load editor so that tinymce is loaded
wp_editor("", "hdq_enqued_editor", array('textarea_name' => 'hdq_enqued_editor', 'teeny' => true, 'media_buttons' => false, 'textarea_rows' => 3, 'quicktags' => false));
// scripts and styles needed to use WP uploader
wp_enqueue_media();
?>
</div>