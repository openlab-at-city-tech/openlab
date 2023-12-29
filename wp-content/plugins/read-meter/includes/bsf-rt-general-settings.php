<?php
/**
 * The Read meter general Settings tab
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */

wp_enqueue_style( 'bsfrt_dashboard' );

$options = get_option( 'bsf_rt_general_settings' );

$bsf_rt_words_per_minute = ( ! empty( $options['bsf_rt_words_per_minute'] ) ? $options['bsf_rt_words_per_minute'] : '' );

$bsf_rt_post_types = ( ! empty( $options['bsf_rt_post_types'] ) ? $options['bsf_rt_post_types'] : array() );

$bsf_rt_include_images = ( ! empty( $options['bsf_rt_include_images'] ) ? $options['bsf_rt_include_images'] : '' );

$bsf_rt_include_comments = ( ! empty( $options['bsf_rt_include_comments'] ) ? $options['bsf_rt_include_comments'] : '' );


$args = array(
	'public' => true,

);

$exclude = array( 'attachment', 'elementor_library', 'Media', 'My Templates' );
?>
<div class="bsf_rt_global_settings" id="bsf_rt_global_settings">
<form method="post" name="bsf_rt_settings_form">
	<table class="form-table" > 
		<br>
		<p class="description">
				<?php
				esc_attr_e( 'Control the core settings of a read meter, e.g. the average count of words that humans can read in a minute & allow a read meter on particular post types, etc.', 'read-meter' );
				?>
			   
		</p>  
		<tr>
	<th scope="row">
			<label for="SelectPostTypes"><?php esc_attr_e( 'Select Post Types', 'read-meter' ); ?> :</label>
			</th>
			<td class="post_type_name">
				   
					<?php

					foreach ( get_post_types( $args, 'objects' ) as $bsfrt_post_type ) {

						if ( in_array( $bsfrt_post_type->labels->name, $exclude ) ) {//PHPCS:ignore:WordPress.PHP.StrictInArray.MissingTrueStrict

							continue;
						}
						if ( 'post' !== $bsf_rt_post_types ) {
							if ( isset( $bsf_rt_post_types ) ) {
								if ( in_array( $bsfrt_post_type->name, $bsf_rt_post_types ) ) {//PHPCS:ignore:WordPress.PHP.StrictInArray.MissingTrueStrict
									echo '<label for="ForPostType">
                             <input type="checkbox" checked name="posts[]" value="' . esc_attr( $bsfrt_post_type->name ) . '">
                             ' . esc_attr( $bsfrt_post_type->labels->name ) . '</label><br> ';
								} else {
									echo '<label for="ForPostType">
                             <input type="checkbox"  name="posts[]" value="' . esc_attr( $bsfrt_post_type->name ) . '">
                             ' . esc_attr( $bsfrt_post_type->labels->name ) . '</label><br> ';
								}
							} else {
								echo '<label for="ForPostType">
                             <input type="checkbox"  name="posts[]" value="' . esc_attr( $bsfrt_post_type->name ) . '">
                             ' . esc_attr( $bsfrt_post_type->labels->name ) . '</label><br> ';
							}
						} else {
							if ( 'post' == $bsfrt_post_type->name ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
								echo '<label for="ForPostType">
                         <input type="checkbox" checked name="posts[]" value="' . esc_attr( $bsfrt_post_type->name ) . '">
                         ' . esc_attr( $bsfrt_post_type->labels->name ) . '</label><br> ';
							}
							echo '<label for="ForPostType">
                         <input type="checkbox"  name="posts[]" value="' . esc_attr( $bsfrt_post_type->name ) . '">
                         ' . esc_attr( $bsfrt_post_type->labels->name ) . '</label><br> ';
						}
					}
					?>
		</td>
		</tr>
		<tr>
		<tr>
		<th scope="row">
			<label for="WordsPerMinute"><?php esc_attr_e( 'Words Per Minute', 'read-meter' ); ?> :</label>
		</th>
		<td>
			<?php
				echo '<input type="number" min="0" required name="bsf_rt_words_per_minute" placeholder="275" value="' . esc_attr( $bsf_rt_words_per_minute ) . '" class="small-text">';
			?>
		</td>
		</tr>
		<th scope="row">

			<label for="IncludeComments"> <?php esc_attr_e( 'Include Comments', 'read-meter' ); ?> :</label>
		</th>
		<td>
				<?php
				if ( isset( $bsf_rt_include_comments ) && 'yes' == $bsf_rt_include_comments ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
					echo '<input type="checkbox" checked name="bsf_rt_include_comments" value="yes">';
				} else {
					echo '<input type="checkbox" name="bsf_rt_include_comments" value="yes">';
				}
				?>
			<p  class="description bsf_rt_description">
					<?php esc_attr_e( "Check this to include comment's text in reading time.", 'read-meter' ); ?>

			</p>  
		</td>
		</tr>
<tr>
		<th scope="row">

			<label for="IncludeImages"> <?php esc_attr_e( 'Include Images', 'read-meter' ); ?> :</label>
		</th>
		<td>
			<?php
			if ( isset( $bsf_rt_include_images ) && 'yes' == $bsf_rt_include_images ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison

				echo '<input type="checkbox" checked name="bsf_rt_include_images" value="yes">';
			} else {

				echo '<input type="checkbox" name="bsf_rt_include_images" value="yes">';
			}
			?>
			<p  class="description bsf_rt_description">   
				<?php esc_attr_e( ' Check this to include post images in reading time.', 'read-meter' ); ?>  
			</p>  
		</td>
		</tr>
	</table>
	<table class="form-table">
	<tr>
		<th>
			<?php wp_nonce_field( 'bsf-rt-nonce-general', 'bsf-rt-general' ); ?>
			<input type="submit" value="Save" class="bt button button-primary" name="submit">
		</th>
	</tr>
	</table>
</form>
</div>
