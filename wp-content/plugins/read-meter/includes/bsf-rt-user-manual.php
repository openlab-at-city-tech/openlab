<?php
/**
 * The Read meter User Manual tab
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */

wp_enqueue_style( 'bsfrt_dashboard' );

?>
<div class="bsf_rt_user_manual">
	<br><label class="bsf_rt_page_title" for="howtouse">
		<?php
		echo 'How to Use? </label><br><br>
	<b>Step 1</b> :Under the <i>General Settings</i> Tab, select post types to display the Read Time and Progress bar. Select Words Per Minute and set other options if required. <br><br>
	<b>Step 2</b> : Go to the <i>Read Time</i> Tab, and select target pages. Set position, prefix, postfix, and other styling options. <br><br>
	<b>Step 3</b> : Go to the <i>Progress Bar</i> Tab, and select the position, colors, and thickness of the progress bar as per your need. <br><br>
	<b>Step 4</b> :That' . "'" . 's it! Visit Post/Page to see results.  <br><br><br>
	<b> Shortcode : [read_meter]</b> <br><br>
		You can also display the reading time wherever you want by using this shortcode , You just need to copy it and paste it in any content of Post or Page.';
		?>
</div>
