<?php
/**
** A base module for [acceptance]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_acceptance', 10, 0 );

function wpcf7_add_form_tag_acceptance() {
	wpcf7_add_form_tag( 'acceptance',
		'wpcf7_acceptance_form_tag_handler',
		array(
			'name-attr' => true,
			'selectable-values' => true,
		)
	);
}

function wpcf7_acceptance_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	if ( $tag->has_option( 'invert' ) ) {
		$class .= ' invert';
	}

	if ( $tag->has_option( 'optional' ) ) {
		$class .= ' optional';
	}

	$atts = array(
		'class' => trim( $class ),
	);

	$item_atts = array(
		'type' => 'checkbox',
		'name' => $tag->name,
		'value' => '1',
		'tabindex' => $tag->get_option( 'tabindex', 'signed_int', true ),
		'checked' => $tag->has_option( 'default:on' ),
		'class' => $tag->get_class_option() ? $tag->get_class_option() : null,
		'id' => $tag->get_id_option(),
	);

	if ( $validation_error ) {
		$item_atts['aria-invalid'] = 'true';
		$item_atts['aria-describedby'] = wpcf7_get_validation_error_reference(
			$tag->name
		);
	} else {
		$item_atts['aria-invalid'] = 'false';
	}

	$item_atts = wpcf7_format_atts( $item_atts );

	$content = empty( $tag->content )
		? (string) reset( $tag->values )
		: $tag->content;

	$content = trim( $content );

	if ( $content ) {
		if ( $tag->has_option( 'label_first' ) ) {
			$html = sprintf(
				'<span class="wpcf7-list-item-label">%2$s</span><input %1$s />',
				$item_atts,
				$content
			);
		} else {
			$html = sprintf(
				'<input %1$s /><span class="wpcf7-list-item-label">%2$s</span>',
				$item_atts,
				$content
			);
		}

		$html = sprintf(
			'<span class="wpcf7-list-item"><label>%s</label></span>',
			$html
		);

	} else {
		$html = sprintf(
			'<span class="wpcf7-list-item"><input %1$s /></span>',
			$item_atts
		);
	}

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap" data-name="%1$s"><span %2$s>%3$s</span>%4$s</span>',
		esc_attr( $tag->name ),
		wpcf7_format_atts( $atts ),
		$html,
		$validation_error
	);

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_acceptance',
	'wpcf7_acceptance_validation_filter', 10, 2 );

function wpcf7_acceptance_validation_filter( $result, $tag ) {
	if ( ! wpcf7_acceptance_as_validation() ) {
		return $result;
	}

	if ( $tag->has_option( 'optional' ) ) {
		return $result;
	}

	$name = $tag->name;
	$value = ( ! empty( $_POST[$name] ) ? 1 : 0 );

	$invert = $tag->has_option( 'invert' );

	if ( $invert and $value
	or ! $invert and ! $value ) {
		$result->invalidate( $tag, wpcf7_get_message( 'accept_terms' ) );
	}

	return $result;
}


/* Acceptance filter */

add_filter( 'wpcf7_acceptance', 'wpcf7_acceptance_filter', 10, 2 );

function wpcf7_acceptance_filter( $accepted, $submission ) {
	$tags = wpcf7_scan_form_tags( array( 'type' => 'acceptance' ) );

	foreach ( $tags as $tag ) {
		$name = $tag->name;

		if ( empty( $name ) ) {
			continue;
		}

		$value = ( ! empty( $_POST[$name] ) ? 1 : 0 );

		$content = empty( $tag->content )
			? (string) reset( $tag->values )
			: $tag->content;

		$content = trim( $content );

		if ( $value and $content ) {
			$submission->add_consent( $name, $content );
		}

		if ( $tag->has_option( 'optional' ) ) {
			continue;
		}

		$invert = $tag->has_option( 'invert' );

		if ( $invert and $value
		or ! $invert and ! $value ) {
			$accepted = false;
		}
	}

	return $accepted;
}

add_filter( 'wpcf7_form_class_attr',
	'wpcf7_acceptance_form_class_attr', 10, 1 );

function wpcf7_acceptance_form_class_attr( $class_attr ) {
	if ( wpcf7_acceptance_as_validation() ) {
		return $class_attr . ' wpcf7-acceptance-as-validation';
	}

	return $class_attr;
}

function wpcf7_acceptance_as_validation() {
	if ( ! $contact_form = wpcf7_get_current_contact_form() ) {
		return false;
	}

	return $contact_form->is_true( 'acceptance_as_validation' );
}

add_filter( 'wpcf7_mail_tag_replaced_acceptance',
	'wpcf7_acceptance_mail_tag', 10, 4 );

function wpcf7_acceptance_mail_tag( $replaced, $submitted, $html, $mail_tag ) {
	$form_tag = $mail_tag->corresponding_form_tag();

	if ( ! $form_tag ) {
		return $replaced;
	}

	if ( ! empty( $submitted ) ) {
		$replaced = __( 'Consented', 'contact-form-7' );
	} else {
		$replaced = __( 'Not consented', 'contact-form-7' );
	}

	$content = empty( $form_tag->content )
		? (string) reset( $form_tag->values )
		: $form_tag->content;

	if ( ! $html ) {
		$content = wp_strip_all_tags( $content );
	}

	$content = trim( $content );

	if ( $content ) {
		$replaced = sprintf(
			/* translators: 1: 'Consented' or 'Not consented', 2: conditions */
			_x( '%1$s: %2$s', 'mail output for acceptance checkboxes',
				'contact-form-7' ),
			$replaced,
			$content
		);
	}

	return $replaced;
}


/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_acceptance', 35, 0 );

function wpcf7_add_tag_generator_acceptance() {
	$tag_generator = WPCF7_TagGenerator::get_instance();

	$tag_generator->add( 'acceptance', __( 'acceptance', 'contact-form-7' ),
		'wpcf7_tag_generator_acceptance',
	 	array( 'version' => '2' )
	);
}

function wpcf7_tag_generator_acceptance( $contact_form, $options ) {
	$field_types = array(
		'acceptance' => array(
			'display_name' => __( 'Acceptance checkbox', 'contact-form-7' ),
			'heading' => __( 'Acceptance checkbox form-tag generator', 'contact-form-7' ),
			'description' => __( 'Generates a form-tag for an <a href="https://contactform7.com/acceptance-checkbox/">acceptance checkbox</a>.', 'contact-form-7' ),
		),
	);

	$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

?>
<header class="description-box">
	<h3><?php
		echo esc_html( $field_types['acceptance']['heading'] );
	?></h3>

	<p><?php
		$description = wp_kses(
			$field_types['acceptance']['description'],
			array(
				'a' => array( 'href' => true ),
				'strong' => array(),
			),
			array( 'http', 'https' )
		);

		echo $description;
	?></p>
</header>

<div class="control-box">
	<fieldset>
		<legend id="<?php echo esc_attr( $tgg->ref( 'type-legend' ) ); ?>"><?php
			echo esc_html( __( 'Field type', 'contact-form-7' ) );
		?></legend>

		<select data-tag-part="basetype" aria-labelledby="<?php echo esc_attr( $tgg->ref( 'type-legend' ) ); ?>"><?php
			echo sprintf(
				'<option %1$s>%2$s</option>',
				wpcf7_format_atts( array(
					'value' => 'acceptance',
				) ),
				esc_html( $field_types['acceptance']['display_name'] )
			);
		?></select>
		<br />
		<label>
			<input type="checkbox" data-tag-part="option" data-tag-option="optional" checked="checked" />
			<?php echo esc_html( __( "This checkbox is optional.", 'contact-form-7' ) ); ?>
		</label>
	</fieldset>

	<?php
		$tgg->print( 'field_name' );

		$tgg->print( 'class_attr' );
	?>

	<fieldset>
		<legend id="<?php echo esc_attr( $tgg->ref( 'value-legend' ) ); ?>"><?php
			echo esc_html( __( 'Condition', 'contact-form-7' ) );
		?></legend>
		<?php
			echo sprintf(
				'<input %s />',
				wpcf7_format_atts( array(
					'type' => 'text',
					'required' => true,
					'value' => __( 'Put the condition for consent here.', 'contact-form-7' ),
					'data-tag-part' => 'content',
					'aria-labelledby' => $tgg->ref( 'value-legend' ),
				) )
			);
		?>
	</fieldset>
</div>

<footer class="insert-box">
	<?php
		$tgg->print( 'insert_box_content' );

		$tgg->print( 'mail_tag_tip' );
	?>
</footer>
<?php
}
