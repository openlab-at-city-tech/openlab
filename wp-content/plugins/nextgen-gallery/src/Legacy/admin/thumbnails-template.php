<?php
$nextgen_thumb_size_custom_style = null;

// defaults for the later form input.
if ( empty( $thumbnails_template_name ) ) {
	$thumbnails_template_name = 'thumbsize';
}
if ( empty( $thumbnails_template_width_name ) ) {
	$thumbnails_template_width_name = 'thumbwidth';
}
if ( empty( $thumbnails_template_height_name ) ) {
	$thumbnails_template_height_name = 'thumbheight';
}
if ( empty( $thumbnails_template_id ) ) {
	$thumbnails_template_id = 'thumbsize';
}
if ( empty( $thumbnails_template_width_id ) ) {
	$thumbnails_template_width_id = '';
}
if ( empty( $thumbnails_template_height_id ) ) {
	$thumbnails_template_height_id = '';
}

$settings = \Imagely\NGG\Settings\Settings::get_instance();

if ( $settings != null ) {
	$thumb_sizes = $settings->get( 'thumbnail_dimensions' );

	if ( empty( $thumbnails_template_width_value ) ) {
		$thumbnails_template_width_value = $settings->get( 'thumbwidth' );
	}
	if ( empty( $thumbnails_template_height_value ) ) {
		$thumbnails_template_height_value = $settings->get( 'thumbheight' );
	}

	if ( is_array( $thumb_sizes ) ) {
		$size_selected    = null;
		$size_select_html = "<select name='{$thumbnails_template_name}' id='{$thumbnails_template_id}' onchange='"
			. 'var jt = jQuery(this);'
			. ' var szcust = jt.next(".nextgen-thumb-size-custom");'
			. ' if (jt.val() == "custom") {'
			. " szcust.find(\"[name=\\\"{$thumbnails_template_width_name}\\\"]\").val(\"{$thumbnails_template_width_value}\");"
			. " szcust.find(\"[name=\\\"{$thumbnails_template_height_name}\\\"]\").val(\"{$thumbnails_template_height_value}\");"
			. ' szcust.show();'
			. ' } else {'
			. ' var parts = jt.val().split("x");'
			. ' szcust.hide();'
			. " szcust.find(\"[name=\\\"{$thumbnails_template_width_name}\\\"]\").val(parts[0]);"
			. " szcust.find(\"[name=\\\"{$thumbnails_template_height_name}\\\"]\").val(parts[1]);"
			. " }'>";

		foreach ( $thumb_sizes as $thumb_size ) {
			$thumb_size_parts = explode( 'x', $thumb_size );
			$thumb_width      = $thumb_size_parts[0];
			$thumb_height     = $thumb_size_parts[1];

			$size_select_html .= "\n" . '<option value="' . $thumb_size . '"';

			if ( $thumbnails_template_width_value == $thumb_width && $thumbnails_template_height_value == $thumb_height ) {
				$size_selected     = $thumb_size;
				$size_select_html .= ' selected';
			}

			$size_select_html .= '>' . $thumb_size . '</option>';
		}

		$size_select_html .= "\n" . '<option value="custom"';

		if ( is_null( $size_selected ) ) {
			$size_select_html .= ' selected';
		} else {
			$nextgen_thumb_size_custom_style .= 'display: none;';
		}

		$size_select_html .= '>' . __( 'Custom', 'nggallery' ) . '</option>';
		$size_select_html .= '</select>';

		echo $size_select_html;
	}
}

if ( ! is_null( $nextgen_thumb_size_custom_style ) ) {
	$nextgen_thumb_size_custom_style = ' style="' . $nextgen_thumb_size_custom_style . '"';
}

?><span class="nextgen-thumb-size-custom" <?php echo $nextgen_thumb_size_custom_style; ?>>
	<input type="text"
			size="5"
			maxlength="5"
			id='<?php echo esc_attr( $thumbnails_template_width_id ); ?>'
			name="<?php echo esc_attr( $thumbnails_template_width_name ); ?>"
			value="<?php echo esc_attr( $thumbnails_template_width_value ); ?>"/>
	x
	<input type="text"
			size="5"
			maxlength="5"
			id='<?php echo esc_attr( $thumbnails_template_height_id ); ?>'
			name="<?php echo esc_attr( $thumbnails_template_height_name ); ?>"
			value="<?php echo esc_attr( $thumbnails_template_height_value ); ?>"/>
	<br/>
	<small><?php esc_html_e( 'These are maximum values', 'nggallery' ); ?></small>
</span>
