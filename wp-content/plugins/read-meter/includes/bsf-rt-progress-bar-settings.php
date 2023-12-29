<?php
/**
 * The Read meter progressbar Settings tab
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */

wp_enqueue_style( 'bsfrt_dashboard' );
wp_enqueue_script( 'bsfrt_backend' );
wp_enqueue_script( 'colorpickerscript' );
$options = get_option( 'bsf_rt_progress_bar_settings' );


$bsf_rt_position_of_progress_bar = ( ! empty( $options['bsf_rt_position_of_progress_bar'] ) ? $options['bsf_rt_position_of_progress_bar'] : '' );

$bsf_rt_progress_bar_styles = ( ! empty( $options['bsf_rt_progress_bar_styles'] ) ? $options['bsf_rt_progress_bar_styles'] : '' );

$bsf_rt_progress_bar_background_color = ( ! empty( $options['bsf_rt_progress_bar_background_color'] ) ? $options['bsf_rt_progress_bar_background_color'] : '' );

$bsf_rt_progress_bar_gradiant_one = ( ! empty( $options['bsf_rt_progress_bar_gradiant_one'] ) ? $options['bsf_rt_progress_bar_gradiant_one'] : '' );

$bsf_rt_progress_bar_gradiant_two = ( ! empty( $options['bsf_rt_progress_bar_gradiant_two'] ) ? $options['bsf_rt_progress_bar_gradiant_two'] : '' );

$bsf_rt_progress_bar_thickness = ( ! empty( $options['bsf_rt_progress_bar_thickness'] ) ? $options['bsf_rt_progress_bar_thickness'] : 12 );

$bsf_rt_progress_display = ( ( 'none' === $bsf_rt_position_of_progress_bar ) ? 'style="display:none"' : '' );

$bsf_rt_progress_color_display = ( ( 'Normal' === $bsf_rt_progress_bar_styles ) ? 'style="display:none"' : '' );

?>
<div class="bsf_rt_global_settings" id="bsf_rt_global_settings">
<form action="" method="post" name="bsf_rt_settings_form">
	<table class="form-table">
		<br>
		<p class="description">
			<?php esc_attr_e( 'Control the position & appearance of the progress bar. Progress bar acts with the content that the user has read. (Note : The Progress Bar will only display on Singular Posts or Pages)', 'bsf_rt_textdomain' ); ?>
		</p> 
		<tr>
		<th scope="row"> 

			<label for="PositionofDisplayProgressBar"><?php esc_attr_e( 'Display Position', 'bsf_rt_textdomain' ); ?> :</label>

		</th>
		<td>
			<select required id="bsf_rt_position_of_progress_bar" name="bsf_rt_position_of_progress_bar" >
				<?php
				if ( isset( $bsf_rt_position_of_progress_bar ) ) {

					if ( 'top_of_the_page' === $bsf_rt_position_of_progress_bar ) {

						echo '<option selected value="top_of_the_page">';
						esc_attr_e( 'Top of the Page', 'bsf_rt_textdomain' );
						echo '</option>';
					} else {

						echo '<option  value="top_of_the_page">';
						esc_attr_e( 'Top of the Page', 'bsf_rt_textdomain' );
						echo '</option>';
					}
					if ( 'bottom_of_the_page' === $bsf_rt_position_of_progress_bar ) {

						echo '<option selected value="bottom_of_the_page">';
						esc_attr_e( 'Bottom of the Page', 'bsf_rt_textdomain' );
						echo '</option>';
					} else {

						echo '<option  value="bottom_of_the_page">';
						esc_attr_e( 'Bottom of the Page', 'bsf_rt_textdomain' );
						echo '</option>';
					}
					if ( 'none' === $bsf_rt_position_of_progress_bar ) {

						echo '<option selected value="none">';
						esc_attr_e( 'None', 'bsf_rt_textdomain' );
						echo '</option>';
					} else {

						echo '<option  value="none">';
						esc_attr_e( 'None', 'bsf_rt_textdomain' );
						echo '</option>';
					}
				} else {
					echo '<option  value="none">';
						esc_attr_e( 'None', 'bsf_rt_textdomain' );
						echo '</option>';
					echo '<option  value="top_of_the_page">';
						esc_attr_e( 'Top of the Page', 'bsf_rt_textdomain' );
						echo '</option>';
					echo '<option  value="bottom_of_the_page">';
						esc_attr_e( 'Bottom of the Page', 'bsf_rt_textdomain' );
						echo '</option>';
				}

				?>
			</select>
		</td>
		</tr>
	</table>
	<table class="form-table" id="bsf-rt-progress-bar-options" <?php echo wp_kses_post( $bsf_rt_progress_display ); ?>>  
		<tr>
	<th scope="row"> 
			<label for="ProgressBarStyle"><?php esc_attr_e( 'Styles', 'bsf_rt_textdomain' ); ?> :</label>
	</th>
			<td>
			<select  name="bsf_rt_progress_bar_styles" id="bsf_rt_progress_bar_styles">
					<?php
					if ( isset( $bsf_rt_progress_bar_styles ) ) {

						if ( 'Normal' === $bsf_rt_progress_bar_styles ) {

							echo '<option id="normalcolor" selected value="Normal">';
							esc_attr_e( 'Normal', 'bsf_rt_textdomain' );
							echo '</option>';

						} else {

							echo '<option id="normalcolor"  value="Normal">';
							esc_attr_e( 'Normal', 'bsf_rt_textdomain' );
							echo '</option>';
						}
						if ( 'Gradient' === $bsf_rt_progress_bar_styles ) {

							echo '<option selected id="gradiantcolor" value="Gradient">';
							esc_attr_e( 'Gradient', 'bsf_rt_textdomain' );
							echo '</option>';

						} else {

							echo '<option  id="gradiantcolor" value="Gradient">';
							esc_attr_e( 'Gradient', 'bsf_rt_textdomain' );
							echo '</option>';
						}
					} else {

							echo '<option id="normalcolor"  value="Normal">';
							esc_attr_e( 'Normal', 'bsf_rt_textdomain' );
							echo '</option>';
							echo '<option  id="gradiantcolor" value="Gradient">';
							esc_attr_e( 'Gradient', 'bsf_rt_textdomain' );
							echo '</option>';
					}

					?>
			</select>
			</td>
		</tr>
		<tr id="normal-back-wrap">
			<th scope="row">

			<label for="ProgressBarBackgroundColor"><?php esc_attr_e( 'Background Color', 'bsf_rt_textdomain' ); ?> :</label>

			</th>
			<td>
				<?php
				if ( isset( $bsf_rt_progress_bar_background_color ) ) {


					echo '<input name="bsf_rt_progress_bar_background_color" class="my-color-field" value="' . esc_attr( $bsf_rt_progress_bar_background_color ) . '">';
				} else {
					?>

						<input name="bsf_rt_progress_bar_background_color" class="my-color-field" value="#e8d5ff">

					<?php
				}
				?>
			   
			</td>
		</tr> 
		<tr id="normal-color-wrap">
			<th scope="row">
			<label for="ProgressBarColor"> <?php esc_attr_e( 'Primary Color', 'bsf_rt_textdomain' ); ?> :</label>
			</th>  
			<td>
				<?php
				if ( isset( $bsf_rt_progress_bar_gradiant_one ) ) {


					echo '<input name="bsf_rt_progress_bar_color_g1" class="my-color-field" value="' . esc_attr( $bsf_rt_progress_bar_gradiant_one ) . '">';

				} else {
					?>

				<input name="bsf_rt_progress_bar_color_g1" class="my-color-field" value="#5540D9">

					<?php
				}
				?>
			</td>
		</tr>
		</tr>
		<tr id="gradiant-wrap2" <?php echo wp_kses_post( $bsf_rt_progress_color_display ); ?>>
			<th scope="row">
				<label for="ProgressBarColor"> <?php esc_attr_e( 'Secondary Color', 'bsf_rt_textdomain' ); ?> :</label>
			</th>
			<td>
					<?php
					if ( isset( $bsf_rt_progress_bar_gradiant_two ) ) {


						echo '<input name="bsf_rt_progress_bar_color_g2" class="my-color-field" value="' . esc_attr( $bsf_rt_progress_bar_gradiant_two ) . '">';
					} else {
						?>

					<input name="bsf_rt_progress_bar_color_g2" class="my-color-field" value="#ee7fff">
						<?php
					}
					?>
			</td>
		</tr>
		<tr>
			<th scope="row">

			<label for="Thickness"><?php esc_attr_e( 'Bar Thickness', 'bsf_rt_textdomain' ); ?> :</label>

			</th>
			<td>
					<?php

					if ( isset( $bsf_rt_progress_bar_thickness ) ) {


						echo '<input type="number" required name="bsf_rt_progress_bar_thickness" class="small-text" value="' . esc_attr( $bsf_rt_progress_bar_thickness ) . '">&nbsppx';

					} else {
						?>

					<input type="number"  name="bsf_rt_progress_bar_thickness" class="small-text" value="12">&nbsppx

						<?php
					}
					?>

			</td>
		</tr>
	</table>
	<table class="form-table">
	<tr>
			<th>
				<?php wp_nonce_field( 'bsf-rt-nonce-progress', 'bsf-rt-progress' ); ?>
			<input type="submit" value="Save" class="bt button button-primary" name="submit">

			</th>
		</tr>
	</table>
</form>
</div>
