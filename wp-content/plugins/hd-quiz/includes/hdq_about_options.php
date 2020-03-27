<?php
/*
HD Quiz About / options page
 */

$opt_name1 = 'hd_qu_fb';
$opt_name2 = 'hd_qu_tw';
$opt_name3 = 'hd_qu_next';
$opt_name4 = 'hd_qu_finish';
$opt_name5 = 'hd_qu_questionName';
$opt_name6 = 'hd_qu_results';
$opt_name7 = 'hd_qu_authors';
$opt_name8 = 'hd_qu_percent';
$opt_name9 = 'hd_qu_adcode';

$hidden_field_name = 'hd_submit_hidden';
$data_field_name1 = 'hd_qu_fb';
$data_field_name2 = 'hd_qu_tw';
$data_field_name3 = 'hd_qu_next';
$data_field_name4 = 'hd_qu_finish';
$data_field_name5 = 'hd_questionName';
$data_field_name6 = 'hd_results';
$data_field_name7 = 'hd_qu_authors';
$data_field_name8 = 'hd_qu_percent';
$data_field_name9 = 'hd_qu_adcode';

//$hd_questionName = sanitize_text_field(get_option( 'hd_questionName' )); // depricated

// Read in existing option value from database
$opt_val1 = sanitize_text_field(get_option($opt_name1));
$opt_val2 = sanitize_text_field(get_option($opt_name2));
$opt_val3 = sanitize_text_field(get_option($opt_name3));
$opt_val4 = sanitize_text_field(get_option($opt_name4));
$opt_val5 = sanitize_text_field(get_option($opt_name5));
$opt_val6 = sanitize_text_field(get_option($opt_name6));
$opt_val7 = sanitize_text_field(get_option($opt_name7));
$opt_val8 = sanitize_text_field(get_option($opt_name8));
$opt_val9 = get_option($opt_name9);

// See if the user has posted us some information
if (isset($_POST['hdq_about_options_nonce'])) {
    $hdq_nonce = $_POST['hdq_about_options_nonce'];
    if (wp_verify_nonce($hdq_nonce, 'hdq_about_options_nonce') != false) {
        // Read their posted value
        $opt_val1 = sanitize_text_field($_POST[$data_field_name1]);
        $opt_val2 = sanitize_text_field($_POST[$data_field_name2]);
        $opt_val3 = sanitize_text_field($_POST[$data_field_name3]);
        $opt_val4 = sanitize_text_field($_POST[$data_field_name4]);
        $opt_val5 = sanitize_text_field($_POST[$data_field_name5]);
        $opt_val6 = sanitize_text_field($_POST[$data_field_name6]);
        if (isset($_POST[$data_field_name7])) {
            $opt_val7 = sanitize_text_field($_POST[$data_field_name7]);
        } else {
            $opt_val7 = "no";
        }
        if (isset($_POST[$data_field_name8])) {
            $opt_val8 = sanitize_text_field($_POST[$data_field_name8]);
        } else {
            $opt_val8 = "no";
        }
        $opt_val9 = urlencode($_POST[$data_field_name9]);

        // Save the posted value in the database
        update_option($opt_name1, $opt_val1);
        update_option($opt_name2, $opt_val2);
        update_option($opt_name3, $opt_val3);
        update_option($opt_name4, $opt_val4);
        update_option($opt_name5, $opt_val5);
        update_option($opt_name6, $opt_val6);
        update_option($opt_name7, $opt_val7);
        update_option($opt_name8, $opt_val8);
        update_option($opt_name9, $opt_val9);
    }
}
?>
	<div id = "hdq_meta_forms">
		<div id = "hdq_message"></div>
		<div id = "hdq_wrapper">
			<div id="hdq_form_wrapper">
				<div class = "hdq_tab hdq_tab_active">
					<h1>
						HD Quiz
					</h1>
					<p>
						HD Quiz was designed and developed to be one of the easiest and hassle free quiz builders for WordPress. If you have any questions, or need support, please contact me at the <a href = "https://wordpress.org/support/plugin/hd-quiz" target = "_blank">official WordPress HD Quiz support forum</a>.
					</p>
					<p>
						As I continue to develop HD Quiz, more features, options, customizations, and settings will be introduced. If you have enjoyed HD Quiz, then I would sure appreciate it if you could <a href="https://wordpress.org/support/plugin/hd-quiz/reviews/#new-post" target = "_blank">leave an honest review</a>. It's the little things that make building systems like this worthwhile ❤.
					</p>

					<div class = "hdq_highlight">
						<p>
							HD Quiz is free and developed in my spare time. If you are enjoying HD Quiz and would like to show your support, please consider contributing to my <a href = "https://www.patreon.com/harmonic_design" target ="_blank">patreon page</a> to help continued development. Every little bit helps, and I am fuelled by coffee ☕.
						</p>
					</div>

					<br/>

					<form id = "hdq_settings" method="post">
						<h2 style = "display: inline-block">Settings</h2>
						<input type="submit" class="hdq_button2" id="hdq_save_settings" value="SAVE" style = "float:right;">
						<div class = "clear"></div>
						<input type="hidden" name="hdq_submit_hidden" value="Y">
						<?php wp_nonce_field('hdq_about_options_nonce', 'hdq_about_options_nonce');?>
						<h3>
							Social Sharing
						</h3>
						<div class = "hdq_one_half">
							<div class = "hdq_row">
								<label for = "<?php echo $data_field_name1; ?>" >Facebook APP ID <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>This is needed to allow Facebook to share dynamic content - the results of the quiz. If this is not used, then Facebook will share the page without the results. </span></span></span></label>
								<input type = "text" name="<?php echo $data_field_name1; ?>" id="<?php echo $data_field_name1; ?>1" value="<?php echo $opt_val1; ?>" class = "hdq_input" placeholder = "leave blank to use default sharing" />
							</div>
						</div>
						<div class = "hdq_one_half hdq_last">
							<div class = "hdq_row">
								<label for = "<?php echo $data_field_name2; ?>" >Twitter Handle <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>This is used if you have sharing results enabled. The sent tweet will contain a mention to your account for extra exposure. </span></span></span></label>
								<input type = "text" name="<?php echo $data_field_name2; ?>" id="<?php echo $data_field_name2; ?>1" value="<?php echo $opt_val2; ?>" class = "hdq_input" placeholder = "please do NOT include the @ symbol" />
							</div>
						</div>
						<div class = "clear"></div>
						<h3>
							Rename / Translate
						</h3>
						<div class = "hdq_one_half">
							<div class = "hdq_row">
								<label for = "<?php echo $data_field_name4; ?>" >Rename "Finish" Button</label>
								<input type = "text" name="<?php echo $data_field_name4; ?>" id="<?php echo $data_field_name4; ?>1" value="<?php echo $opt_val4; ?>" class = "hdq_input" placeholder = "leave blank to use default" />
							</div>
						</div>
						<div class = "hdq_one_half hdq_last">
							<div class = "hdq_row">
								<label for = "<?php echo $data_field_name3; ?>" >Rename "Next" Button</label>
								<input type = "text" name="<?php echo $data_field_name3; ?>" id="<?php echo $data_field_name3; ?>1" value="<?php echo $opt_val3; ?>" placeholder = "leave blank to use default" class = "hdq_input"/>
							</div>
						</div>
						<div class = "clear"></div>
						<div class = "hdq_one_half">
							<div class = "hdq_row">
								<label for = "<?php echo $data_field_name5; ?>" >Rename "Question"</label>
								<input type = "text" name="<?php echo $data_field_name5; ?>" id="<?php echo $data_field_name5; ?>1" value="<?php echo $opt_val5; ?>" placeholder = "leave blank to use default" class = "hdq_input"/>
							</div>
						</div>
						<div class = "hdq_one_half hdq_last">
							<div class = "hdq_row">
								<label for = "<?php echo $data_field_name6; ?>" >Rename "Results"</label>
								<input type = "text"  name="<?php echo $data_field_name6; ?>" id="<?php echo $data_field_name6; ?>1" value="<?php echo $opt_val6; ?>" placeholder = "leave blank to use default" class = "hdq_input"/>
							</div>
						</div>
						<div class = "clear"></div>


						<div class = "hdq_one_half">
							<div class = "hdq_row">
								<label for = "<?php echo $data_field_name7; ?>" >Allow Authors Role Access <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>By default, only Editors or Admins can add or edit questions. Enabling this will allow Authors to create quizzes as well.</span></span></span></label>

							<div class="hdq_check_row">
								<div class="hdq-options-check">
									<input type="checkbox" id="<?php echo $data_field_name7; ?>" value="yes" name="<?php echo $data_field_name7; ?>" <?php if ($opt_val7 == "yes") {echo 'checked = ""';}?>>
									<label for="<?php echo $data_field_name7; ?>"></label>
								</div>
							</div>


							</div>
						</div>
						<div class = "hdq_one_half hdq_last">
							<div class = "hdq_row">
								<label for = "<?php echo $data_field_name8; ?>" >Enable Percent Results <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>By default, HD Quiz will only show the score as a fraction (example: 9/10). Enabling this will also show the score as a percentage (example: 90%) </span></span></span></label>


							<div class="hdq_check_row">
								<div class="hdq-options-check">
									<input type="checkbox" id="<?php echo $data_field_name8; ?>" value="yes" name="<?php echo $data_field_name8; ?>" <?php if ($opt_val8 == "yes") {echo 'checked = ""';}?>>
									<label for="<?php echo $data_field_name8; ?>"></label>
								</div>
							</div>

							</div>
						</div>
						<div class = "clear"></div>
						<div class = "hdq_row">
							<label or = "<?php echo $data_field_name9; ?>">Adset code <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>If you are using Google Adsense or something similar, you can paste your ad code here. HD Quiz will display the ad after every 4th question.</span></span></span></label>
							<textarea class = "hdq_input" id = "<?php echo $data_field_name9; ?>" placeholder = "paste ad code here" name = "<?php echo $data_field_name9; ?>"><?php echo stripcslashes(urldecode($opt_val9)); ?></textarea>
						</div>
					</form>
					<br/>

					<h2>Upcoming Features</h2>
					<p>I am developing HD Quiz in my spare time, but still plan to add the following features at some point</p>
					<ul class = "hdq_list">
						<li>Quiz styler <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>This would allow you to change the fonts and colours of the quizzes across your site</span></span></span></li>
						<li>Logged in only <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>This would hide the quiz for non-registered users.</span></span></span></li>
						<li>Global / default quiz options <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>These would become the default for all quizzes, but could be overridden on a per-quiz basis.</span></span></span></li>
						<li>Translation ready (including the admin area)</li>
					</ul>
					<br/>

					<h2>Quick Documentation</h2>
					<p>
						HD Quiz was designed to be as easy and intuitive to use as possible. However, I understand that some guidance might still be needed. The following are the "quick steps" needed to create your first quiz!
					</p>
					<div class = "hdq_accordion">
						<h3>Adding A New Quiz</h3>
						<div>
							<ul class = "hdq_list">
								<li>Select HD Quiz from the left sidebar, then Quizzes.</li>
								<li>Enter the name of the quiz, then click on Add Quiz. This will add the new quiz to the list.</li>
								<li>You can now select your new quiz to edit quiz settings or add questions</li>
							</ul>
						</div>
					</div>
					<div class = "hdq_accordion">
						<h3>Using A Quiz - Adding Quiz To a Page</h3>
						<div>
							<p>
								HD Quiz uses shortcodes to render a quiz, so you can place a quiz almost anywhere on your site!
							</p>
							<ul class = "hdq_list">
								<li>To find the shortcode for a quiz, select HD Quiz -> Quizzes in the left menu.</li>
								<li>You will now see a list of all of your quizzes in a table, with the shortcode listed.</li>
								<li>Copy and paste the shortcode into any page or post you want to render that quiz!</li>
							</ul>
							<p>
								<strong>Gutenberg</strong>: HD Quiz is also fully Gutenberg compatible, and I even coded in a custom Gutenberg block to make adding quizzes easier than ever! If you are using Gutenberg, then you can add the HD Quiz block. A block will be added to your editor and will automatically populate a list of all of your quizzes. Simply select the quiz you wish to add and save.
							</p>
						</div>
					</div>
					<div class = "hdq_accordion">
						<h3>Changing Question Order</h3>
						<div>
							<p>
								The latest and greatest version of HD Quiz makes creating custom question order easier than ever! When editing a quiz, you can simply drag and drop to change the order of the questions. Just remember to save the quiz when done!
						</div>
					</div>
					<div class = "hdq_accordion">
						<h3>Need More Help?</h3>
						<div>
							<p>
								This is a free Premium WordPress plugin, so I just get pure unfiltered satisfaction knowing that you use and love HD Quiz.</p>
							<p>So, loyal HD Quiz lover, if you need help, please don't hesitate to leave us a message or question on the <a href = "https://wordpress.org/support/plugin/hd-quiz" target = "_blank">official WordPress HD Quiz Support Forum</a>, or on our own <a href = "http://harmonicdesign.ca/hd-quiz/" target = "_blank">support page at Harmonic Design</a>.</p>
						</div>
					</div>
					<br/>
					<h2>Other Harmonic Design Plugins</h2>


					<div id = "hdq_admin_plugins">


					<?php
$data = wp_remote_get("https://harmonicdesign.ca/plugins/additional_plugins.txt");
if (is_array($data)) {

    $data = $data["body"];
    $data = stripslashes(html_entity_decode($data));
    $data = json_decode($data);

    foreach ($data as $value) {
        $title = sanitize_text_field($value[0]);
        $subtitle = sanitize_text_field($value[1]);
        $image = sanitize_text_field($value[2]);
        $link = sanitize_text_field($value[3]);
        $description = sanitize_text_field($value[4]);
        ?>

						<div class="product">
							<h3><?php echo $title; ?></h3>
							<p class="tagline"><?php echo $subtitle; ?></p>
							<img src="<?php echo $image; ?>" alt="<?php echo $title; ?>">
							<p><?php echo $description; ?></p>
							<p><a href="<?php echo $link; ?>" class="btn">download</a></p>
						</div>


							<?php }
} else {
    echo '<h2>There was an error loading additional Harmonic Design Plugins.</h2>';
}
?>

					</div>
				</div>
			</div>
		</div>
	</div>
