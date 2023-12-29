<?php
/**
 * The Read meter readtime Settings tab
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */

wp_enqueue_style( 'bsfrt_dashboard' );
wp_enqueue_script( 'bsfrt_backend' );
wp_enqueue_script( 'colorpickerscript' );
$options = get_option( 'bsf_rt_read_time_settings' );

$bsf_rt_show_read_time = ( ! empty( $options['bsf_rt_show_read_time'] ) ? $options['bsf_rt_show_read_time'] : array() );

$bsf_rt_position_of_read_time = ( ! empty( $options['bsf_rt_position_of_read_time'] ) ? $options['bsf_rt_position_of_read_time'] : '' );

$bsf_rt_reading_time_label = ( ! empty( $options['bsf_rt_reading_time_label'] ) ? $options['bsf_rt_reading_time_label'] : '' );

$bsf_rt_reading_time_postfix_label = ( ! empty( $options['bsf_rt_reading_time_postfix_label'] ) ? $options['bsf_rt_reading_time_postfix_label'] : '' );

$bsf_rt_read_time_font_size = ( ! empty( $options['bsf_rt_read_time_font_size'] ) ? $options['bsf_rt_read_time_font_size'] : 10 );

$bsf_rt_read_time_bg_option = ( ! empty( $options['bsf_rt_read_time_bg_option'] ) ? $options['bsf_rt_read_time_bg_option'] : '' );

$bsf_rt_read_time_background_color = ( ! empty( $options['bsf_rt_read_time_background_color'] ) ? $options['bsf_rt_read_time_background_color'] : '' );

$bsf_rt_read_time_color = ( ! empty( $options['bsf_rt_read_time_color'] ) ? $options['bsf_rt_read_time_color'] : '' );

$bsf_rt_read_time_margin_top = ( ! empty( $options['bsf_rt_read_time_margin_top'] ) ? $options['bsf_rt_read_time_margin_top'] : 0 );

$bsf_rt_read_time_margin_right = ( ! empty( $options['bsf_rt_read_time_margin_right'] ) ? $options['bsf_rt_read_time_margin_right'] : 0 );

$bsf_rt_read_time_margin_bottom = ( ! empty( $options['bsf_rt_read_time_margin_bottom'] ) ? $options['bsf_rt_read_time_margin_bottom'] : 0 );

$bsf_rt_read_time_margin_left = ( ! empty( $options['bsf_rt_read_time_margin_left'] ) ? $options['bsf_rt_read_time_margin_left'] : 0 );

$bsf_rt_read_time_padding_top = ( ! empty( $options['bsf_rt_read_time_padding_top'] ) ? $options['bsf_rt_read_time_padding_top'] : 0 );

$bsf_rt_read_time_padding_right = ( ! empty( $options['bsf_rt_read_time_padding_right'] ) ? $options['bsf_rt_read_time_padding_right'] : 0 );

$bsf_rt_read_time_padding_bottom = ( ! empty( $options['bsf_rt_read_time_padding_bottom'] ) ? $options['bsf_rt_read_time_padding_bottom'] : 0 );

$bsf_rt_read_time_padding_left = ( ! empty( $options['bsf_rt_read_time_padding_left'] ) ? $options['bsf_rt_read_time_padding_left'] : 0 );

$bsf_rt_padding_unit = ( ! empty( $options['bsf_rt_padding_unit'] ) ? $options['bsf_rt_padding_unit'] : 'em' );

$bsf_rt_margin_unit = ( ! empty( $options['bsf_rt_margin_unit'] ) ? $options['bsf_rt_margin_unit'] : 'px' );

$bsf_rt_read_time_options_display = ( ( 'none' === $bsf_rt_position_of_read_time ) ? 'style="display:none"' : '' );

?>
<div class="bsf_rt_global_settings" id="bsf_rt_global_settings">
<form method="post" name="bsf_rt_settings_form">
<table class="form-table" >
<br>     
<p class="description">
<?php esc_attr_e( 'Control the position & appearance of the estimated read time of the post.', 'read-meter' ); ?>
</p> 
<tr>
<th scope="row">
<label for="ShowEstimatedReadTime"><?php esc_attr_e( 'Show Estimated Read Time On', 'read-meter' ); ?>:</label>
</th>
<td>
<label id="bsf_rt_single_checkbox_label" for="ForSinglePage" class="bsf_rt_show_readtime_label" >
<?php
if ( isset( $bsf_rt_show_read_time ) && is_array( $bsf_rt_show_read_time ) ) {

	if ( in_array( 'bsf_rt_single_page', $bsf_rt_show_read_time ) ) {//PHPCS:ignore:WordPress.PHP.StrictInArray.MissingTrueStrict
		echo ' <input id="bsf_rt_single_page" type="checkbox" checked name="bsf_rt_show_read_time[]"  value="bsf_rt_single_page">';
	} else {
		echo ' <input id="bsf_rt_single_page" type="checkbox" name="bsf_rt_show_read_time[]"  value="bsf_rt_single_page">';
	}
} else {
	echo '  <input id="bsf_rt_single_page" type="checkbox" checked name="bsf_rt_show_read_time[]"  value="bsf_rt_single_page">';
}
?>
<?php esc_attr_e( 'Single Post', 'read-meter' ); ?>
</label> 

<br>
<label for="ForHomeBlogPage" class="bsf_rt_show_readtime_label">
<?php
if ( isset( $bsf_rt_show_read_time ) && is_array( $bsf_rt_show_read_time ) && in_array( 'bsf_rt_home_blog_page', $bsf_rt_show_read_time ) ) { //PHPCS:ignore:WordPress.PHP.StrictInArray.MissingTrueStrict
	echo ' <input id="bsf_rt_home_blog_page" type="checkbox" checked name="bsf_rt_show_read_time[]" value="bsf_rt_home_blog_page" >';
} else {
	echo '  <input id="bsf_rt_home_blog_page" type="checkbox" name="bsf_rt_show_read_time[]" value="bsf_rt_home_blog_page" >';
}
?>
<?php esc_attr_e( 'Home / Blog Page', 'read-meter' ); ?>
</label> 

<br>
<label for="ForArchivePage" class="bsf_rt_show_readtime_label">
<?php
if ( isset( $bsf_rt_show_read_time ) && is_array( $bsf_rt_show_read_time ) && in_array( 'bsf_rt_archive_page', $bsf_rt_show_read_time ) ) {//PHPCS:ignore:WordPress.PHP.StrictInArray.MissingTrueStrict
	echo ' <input id="bsf_rt_archive_page" type="checkbox" checked name="bsf_rt_show_read_time[]" value="bsf_rt_archive_page" >';
} else {
	echo ' <input id="bsf_rt_archive_page"  type="checkbox" name="bsf_rt_show_read_time[]" value="bsf_rt_archive_page" >';
}
?>
<?php esc_attr_e( 'Archive Page', 'read-meter' ); ?>
</label> 


</td>

</tr>
<tr>
<th scope="row">
<label for="ShowReadTimePosition"> <?php esc_attr_e( 'Read Time Position', 'read-meter' ); ?> :</label>
</th>
<td>
<select id="bsf_rt_position_of_read_time" required name="bsf_rt_position_of_read_time" >
<?php
if ( isset( $bsf_rt_position_of_read_time ) ) {
	if ( 'above_the_content' === $bsf_rt_position_of_read_time ) {
		echo '<option selected value="above_the_content">';
		esc_attr_e( 'Above the Content', 'read-meter' );
		echo '</option>';
	} else {
		echo '<option value="above_the_content">';
		esc_attr_e( 'Above the Content', 'read-meter' );
		echo '</option>';                }
	if ( 'above_the_post_title' === $bsf_rt_position_of_read_time ) {

		echo '<option selected value="above_the_post_title">';
		esc_attr_e( 'Above the Post Title', 'read-meter' );
		echo '</option>';
	} else {
		echo '<option  value="above_the_post_title">';
		esc_attr_e( 'Above the Post Title', 'read-meter' );
		echo '</option>';
	}
	if ( 'below_the_post_title' === $bsf_rt_position_of_read_time ) {
		echo '<option selected value="below_the_post_title">';
		esc_attr_e( 'Below the Post Title', 'read-meter' );
		echo '</option>';
	} else {
		echo '<option  value="below_the_post_title">';
		esc_attr_e( 'Below the Post Title', 'read-meter' );
		echo '</option>';
	}
	if ( 'none' === $bsf_rt_position_of_read_time ) {
		echo '<option selected value="none">';
		esc_attr_e( 'None', 'read-meter' );
		echo '</option>';
	} else {
		echo '<option  value="none">';
		esc_attr_e( 'None', 'read-meter' );
		echo '</option>';
	}
} else {

	echo '<option value="above_the_content">';
	esc_attr_e( 'Above the Content', 'read-meter' );
	echo '</option>';
	echo '<option  value="above_the_post_title">';
	esc_attr_e( 'Above the Post Title', 'read-meter' );
	echo '</option>';
	echo '<option  value="below_the_post_title">';
	esc_attr_e( 'Below the Post Title', 'read-meter' );
	echo '</option>';
	echo '<option  value="none">';
	esc_attr_e( 'None', 'read-meter' );
	echo '</option>';

}

?>
</select> 
</td>
</tr>
</table>
<table class="form-table" id="bsf_rt_read_time_option" <?php echo wp_kses_post( $bsf_rt_read_time_options_display ); ?>>

<tr>
<th scope="row">
<label for="ReadingTimePrefixLabel"> <?php esc_attr_e( 'Reading Time Prefix', 'read-meter' ); ?> :</label>
</th>
<td>
<?php
if ( isset( $bsf_rt_reading_time_label ) ) {
	echo '<input type="text"  name="bsf_rt_reading_time_prefix_label"  value="' . esc_attr( $bsf_rt_reading_time_label ) . '" class="regular-text">';
} else {
	?>
<input type="text"  name="bsf_rt_reading_time_prefix_label" value="Reading Time" class="regular-text">
<?php } ?>

<p class="description">
<?php esc_attr_e( 'This text will display before the Reading Time.', 'read-meter' ); ?>


</p>  
</td>
</tr>
<tr>
<th scope="row">
<label for="ReadingTimePrefixLabel"><?php esc_attr_e( 'Reading Time Postfix', 'read-meter' ); ?> :</label>
</th>
<td>
<?php
if ( isset( $bsf_rt_reading_time_postfix_label ) ) {
	echo '<input type="text"  name="bsf_rt_reading_time_postfix_label"  value="' . esc_attr( $bsf_rt_reading_time_postfix_label ) . '" class="regular-text">';
} else {
	?>
<input type="text"  name="bsf_rt_reading_time_postfix_label" value="mins" class="regular-text">
<?php } ?>
<p class="description">  
<?php esc_attr_e( 'This text will display after the Reading Time.', 'read-meter' ); ?>                  

</p>  
</td>
</tr>
<tr >
<th scope="row">
<label for="ReadtimeFontSize"><?php esc_attr_e( 'Font Size', 'read-meter' ); ?>  :</label>
</th>
<td>
<?php
echo '<input type="number" name="bsf_rt_read_time_font_size" max="50" min="10" class="small-text" value="' . esc_attr( $bsf_rt_read_time_font_size ) . '"  >&nbsp px';
?>
<p class="description">
<?php esc_attr_e( 'Keep blank for default value.', 'read-meter' ); ?>                  

</p>  
</td>
</tr>
<tr>
<th scope="row">
<label for="ReadingTimeMargin"><?php esc_attr_e( 'Margin', 'read-meter' ); ?> :</label>
</th>
<td>
<?php
echo '<input step="any" id="bsf_rt_margin" type="number" name="bsf_rt_read_time_margin_top" class="small-text" value="' . esc_attr( $bsf_rt_read_time_margin_top ) . '" >';
echo '<input step="any" id="bsf_rt_margin" type="number" name="bsf_rt_read_time_margin_right" class="small-text" value="' . esc_attr( $bsf_rt_read_time_margin_right ) . '" >';
echo '<input step="any" id="bsf_rt_margin" type="number" name="bsf_rt_read_time_margin_bottom" class="small-text" value="' . esc_attr( $bsf_rt_read_time_margin_bottom ) . '" >';
echo '<input step="any" id="bsf_rt_margin" type="number" name="bsf_rt_read_time_margin_left" class="small-text" value="' . esc_attr( $bsf_rt_read_time_margin_left ) . '" >';
?>
<select name="bsf_rt_margin_unit">
<?php
if ( 'px' === $bsf_rt_margin_unit ) {

	echo '<option selected value="px">px</option>';
} else {

	echo '<option  value="px">px</option>';
}
if ( 'em' === $bsf_rt_margin_unit ) {

	echo '<option selected value="em">em</option>';
} else {

	echo '<option  value="em">em</option>';
}
?>
</select>
<p class="description bsf-rt-label-style">
<label class="bsf-rt-top">TOP</label>
<label class="bsf-rt-right">RIGHT</label>
<label class="bsf-rt-bottom">BOTTOM</label>
<label class="bsf-rt-left">LEFT</label>                  
</p> 
</td> 
</tr>
<tr>
<th scope="row">
<label for="ReadingTimePadding"><?php esc_attr_e( 'Padding', 'read-meter' ); ?> :</label>
</th>
<td>
<?php
echo '<input step="any" id="bsf_rt_padding" type="number" name="bsf_rt_read_time_padding_top" class="small-text" value="' . esc_attr( $bsf_rt_read_time_padding_top ) . '" >';
echo '<input step="any" id="bsf_rt_padding" type="number" name="bsf_rt_read_time_padding_right" class="small-text" value="' . esc_attr( $bsf_rt_read_time_padding_right ) . '" > ';
echo '<input step="any" id="bsf_rt_padding" type="number" name="bsf_rt_read_time_padding_bottom" class="small-text" value="' . esc_attr( $bsf_rt_read_time_padding_bottom ) . '" >';
echo '<input step="any" id="bsf_rt_padding" type="number" name="bsf_rt_read_time_padding_left" class="small-text" value="' . esc_attr( $bsf_rt_read_time_padding_left ) . '" >';
?>
<select name="bsf_rt_padding_unit">
<?php
if ( 'px' === $bsf_rt_padding_unit ) {

	echo '<option selected value="px">px</option>';
} else {

	echo '<option  value="px">px</option>';
}
if ( 'em' === $bsf_rt_padding_unit ) {

	echo '<option selected value="em">em</option>';
} else {

	echo '<option  value="em">em</option>';
}
?>
</select>
<p class="description bsf-rt-label-style">
<label class="bsf-rt-top">TOP</label>
<label class="bsf-rt-right">RIGHT</label>
<label class="bsf-rt-bottom">BOTTOM</label>
<label class="bsf-rt-left">LEFT</label>                  
</p> 
</td>
</tr> 
<tr>
<th scope="row"> 
<label for="ReadtimeBackgroundColor"> <?php esc_attr_e( 'Background Color', 'read-meter' ); ?> :</label>
</th>
<td>
<?php
echo '<div id="bsf_rt_bg">';
if ( isset( $bsf_rt_read_time_background_color ) ) {

	echo '<input  name="bsf_rt_read_time_background_color" class="my-color-field" value="' . esc_attr( $bsf_rt_read_time_background_color ) . '">';
} else {
	?>
<input  name="bsf_rt_read_time_background_color" class="my-color-field" value="#eeeeee">
	<?php
}
echo '</div>';
?>

</td>
</tr> 
<tr >
<th scope="row">
<label for="ReadTimeColor"> <?php esc_attr_e( 'Text Color', 'read-meter' ); ?> :</label>
</th>  
<td>
<?php
if ( isset( $bsf_rt_read_time_color ) ) {

	echo '<input name="bsf_rt_read_time_color" class="my-color-field" value="' . esc_attr( $bsf_rt_read_time_color ) . '">';
} else {
	?>
<input name="bsf_rt_read_time_color" class="my-color-field" value="#333333">
	<?php
}
?>

</td>
</tr>
</div>
</table>
<table class="form-table">
<tr>
<th>
<?php wp_nonce_field( 'bsf-rt-nonce-reading', 'bsf-rt-reading' ); ?>
<input type="submit" value="Save" class="bt button button-primary" name="submit">
</th>
</tr>
</table>
</form>
</div>


