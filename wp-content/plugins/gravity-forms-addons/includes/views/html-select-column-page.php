<?php
/**
 * The template for select column page.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes/views
 */
?>

<html>
	<head>
		<?php
		wp_print_styles( array( 'wp-admin', 'colors-fresh' ) );
		wp_print_scripts( array( 'jquery', 'sack', 'jquery-ui-sortable' ) );

		//adds touchscreen support on mobile devices
		if ( wp_is_mobile() ) {
			wp_print_scripts( array( 'jquery-touch-punch' ) );
		}
		?>
		<style type="text/css">
			body {
				font-family: "Lucida Grande", Verdana, Arial, sans-serif;
			}

			#sortable_available, #sortable_selected {
				list-style-type: none;
				margin: 0;
				padding: 2px;
				height: 250px;
				border: 1px solid #eaeaea;
				-moz-border-radius: 4px;
				-webkit-border-radius: 4px;
				-khtml-border-radius: 4px;
				border-radius: 4px;
				background-color: #FFF;
				overflow: auto;
			}
			#sortable_available li, #sortable_selected li {
				margin: 0 2px 2px 2px;
				padding: 2px;
				width: 96%;
				border: 1px solid white;
				cursor: pointer;
				font-size: 13px;
			}

			.field_hover {
				border: 1px dashed #2175A9 !important;
			}

			.placeholder {
				background-color: #FFF0A5;
				height: 20px;
			}

			.gcolumn_wrapper {
				height: 290px;
				padding: 0 20px;
			}

			.gcolumn_container_left, .gcolumn_container_right {
				width: 46%;
			}

			.gcolumn_container_left {
				float: left;
			}

			.gcolumn_container_right {
				float: right;
			}

			.gform_select_column_heading {
				font-weight: bold;
				padding-bottom: 7px;
				font-size: 13px;
			}

			.column-arrow-mid {
				float: left;
				width: 45px;
				height: 250px;
				background-image: url(<?php echo GFCommon::get_base_url(); ?>/images/arrow-rightleft.png);
				background-repeat: no-repeat;
				background-position: center center;
				margin-top: 26px;
			}

			.panel-instructions {
				border-bottom: 1px solid #dfdfdf;
				color: #555;
				font-size: 11px;
				padding: 10px 20px;
				margin-bottom: 6px
			}

			div.panel-buttons {
				margin-top: 8px;
				padding: 0 20px;
			}

			div.panel-buttons {
				*margin-top: 0
			}

			/* ie specific */
		</style>

		<script type="text/javascript">
			jQuery(document).ready(function () {

				jQuery("#sortable_available, #sortable_selected").sortable({connectWith: '.sortable_connected', placeholder: 'placeholder'});

				jQuery(".sortable_connected li").hover(
					function () {
						jQuery(this).addClass("field_hover");
					},
					function () {
						jQuery(this).removeClass("field_hover");
					}
				);

			});

			function ChangeColumns( columns ) {
				var json_columns = JSON.stringify( columns );
				UpdateColumns( jQuery( "body" ).data( 'formid' ), json_columns );
			}

			function UpdateColumns( form_id, columns ) {
				var mysack = new sack( "<?php echo admin_url( 'admin-ajax.php' ); ?>" );
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar( "action", "change_directory_columns" );
				mysack.setVar( "gforms_directory_columns", "<?php echo wp_create_nonce( 'gforms_directory_columns' ); ?>" );
				mysack.setVar( "form_id", form_id );
				mysack.setVar( "directory_columns", columns );
				mysack.onCompletion = function () {
					if( self.parent.hasOwnProperty('tb_remove')){
						self.parent.tb_remove();
					}
				};
				mysack.onError = function () {
					alert( '<?php echo esc_js( __( 'Ajax error while setting lead property', 'gravity-forms-addons' ) ); ?>' )
				};
				mysack.runAJAX();

				return true;
			}

			var columns = new Array();

			function SelectColumns() {
				jQuery("#sortable_selected li").each(function () {
					columns.push(this.id);
				});
				self.ChangeColumns(columns);
			}
		</script>

	</head>

	<body data-formid="<?php echo $form_id; ?>">
		<div id="wrapper">
			<div class="panel-instructions">
				<p><?php esc_html_e( 'Drag & drop to order and select which columns are displayed in the Gravity Forms Directory.', 'gravity-forms-addons' ); ?></p>
				<p><?php echo sprintf( esc_html__( 'Embed the Directory on a post or a page using %s ', 'gravity-forms-addons' ), '<code>[directory form="' . $form_id . '"]</code>' ); ?></p>
			</div>
			<div class="gcolumn_wrapper">
				<div class="gcolumn_container_left">
					<div class="gform_select_column_heading"><?php esc_html_e( 'Active Columns', 'gravity-forms-addons' ); ?></div>
					<ul id="sortable_selected" class="sortable_connected">
						<?php
						foreach ( $columns as $field_id => $field_info ) {
							?>
							<li id="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field_info['label'] ); ?></li>
							<?php
						}
						?>
					</ul>
				</div>

				<div class="column-arrow-mid"></div>

				<div class="gcolumn_container_right" id="available_column">
					<div class="gform_select_column_heading"> <?php esc_html_e( 'Inactive Columns', 'gravity-forms-addons' ); ?></div>
					<ul id="sortable_available" class="sortable_connected">
						<?php
						foreach ( $form['fields'] as $field ) {
							/* @var GF_Field $field */
							if ( RGFormsModel::get_input_type( $field ) == 'checkbox' && ! in_array( $field->id, $field_ids ) ) {
								?>
								<li id="<?php echo esc_attr( $field->id ); ?>"><?php echo esc_html( GFCommon::get_label( $field ) ); ?></li>
								<?php
							}

							$inputs = $field->get_entry_inputs();

							if ( is_array( $inputs ) ) {
								foreach ( $inputs as $input ) {
									if ( rgar( $input, 'isHidden' ) ) {
										continue;
									}

									if ( ! in_array( $input['id'], $field_ids )
										&& ! ( 'creditcard' == $field->type
											&& in_array( $input['id'], array( floatval( "{$field->id}.2" ), floatval( "{$field->id}.3" ), floatval( "{$field->id}.5" ) ) ) )
									) {
										?>
										<li id="<?php echo esc_attr( $input['id'] ); ?>"><?php echo esc_html( GFCommon::get_label( $field, $input['id'] ) ); ?></li>
										<?php
									}
								}
							} else if ( ! $field->displayOnly && ! in_array( $field->id, $field_ids ) && RGFormsModel::get_input_type( $field ) != 'list' ) {
								?>
								<li id="<?php echo $field->id; ?>"><?php echo esc_html( GFCommon::get_label( $field ) ); ?></li>
								<?php
							}
						}
						?>
					</ul>
				</div>
		</div>

		<div class="panel-buttons">
			<input type="button" value="  <?php esc_attr_e( 'Save', 'gravity-forms-addons' ); ?>  " class="button-primary" onclick="SelectColumns();" onkeypress="SelectColumns();" />&nbsp;
			<input type="button" value="<?php esc_attr_e( 'Cancel', 'gravity-forms-addons' ); ?>" class="button" onclick="self.parent.tb_remove();" onkeypress="self.parent.tb_remove();" />
		</div>

	</body>
</html>
