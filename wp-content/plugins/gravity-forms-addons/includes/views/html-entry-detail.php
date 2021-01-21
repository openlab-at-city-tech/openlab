<?php
/**
 * The template that loads entry detail.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes/views
 */
?>

<table cellspacing="0" class="widefat fixed entry-detail-view">
	<?php
	$title = str_replace( '%%formtitle%%', $Form['title'], str_replace( '%%leadid%%', $lead['id'], $entrydetailtitle ) );
	if ( ! empty( $title ) && $inline ) {
		?>
		<thead>
		<tr>
			<th id="details" colspan="2" scope="col">
				<?php
				$title = apply_filters(
					'kws_gf_directory_detail_title',
					apply_filters(
						'kws_gf_directory_detail_title_' . (int) $lead['id'],
						array(
							$title,
							$lead,
						),
						true
					),
					true
				);
				if ( is_array( $title ) ) {
					echo $title[0];
				} else {
					echo $title;
				}
				?>
			</th>
		</tr>
		</thead>
		<?php
	}
	?>
	<tbody>
	<?php
	$count              = 0;
	$has_product_fields = false;
	$field_count        = count( $Form['fields'] );
	$display_value      = '';
	foreach ( $Form['fields'] as $field ) {

		// Don't show fields defined as hide in single.
		if ( ! empty( $field->hideInSingle ) ) {
			if ( self::has_access( 'gravityforms_directory' ) ) {
				echo "\n\t\t\t\t\t\t\t\t\t" . '<!-- ' . sprintf( esc_html__( '(Admin-only notice) Field #%d not shown: "Hide This Field in Single Entry View" was selected.', 'gravity-forms-addons' ), $field->id ) . ' -->' . "\n\n";
			}
			continue;
		}

		$count ++;
		$is_last = $count >= $field_count ? true : false;

		switch ( RGFormsModel::get_input_type( $field ) ) {
			case 'section':
				if ( ! GFCommon::is_section_empty( $field, $Form, $lead ) || $display_empty_fields ) {
					$count ++;
					$is_last = $count >= $field_count ? true : false;
					?>
					<tr>
						<td colspan="2"
							class="entry-view-section-break<?php echo $is_last ? ' lastrow' : ''; ?>"><?php echo esc_html( GFCommon::get_label( $field ) ); ?></td>
					</tr>
					<?php
				}
				break;

			case 'captcha':
			case 'html':
			case 'password':
			case 'page':
				//ignore captcha, html, password, page field
				break;

			case 'post_image':
				$value      = RGFormsModel::get_lead_field_value( $lead, $field );
				$valueArray = explode( '|:|', $value );

				@list( $url, $title, $caption, $description ) = $valueArray;

				if ( ! empty( $url ) ) {
					$value = $display_value = self::render_image_link( $url, $lead, $options, $title, $caption, $description );
				}
				break;

			default:
				//ignore product fields as they will be grouped together at the end of the grid
				if ( GFCommon::is_product_field( $field->type ) ) {
					$has_product_fields = true;
					break;
				}

				$value         = RGFormsModel::get_lead_field_value( $lead, $field );
				$display_value = GFCommon::get_lead_field_display( $field, $value, $lead['currency'] );
				break;

		} // end switch

		$display_value = apply_filters( 'gform_entry_field_value', $display_value, $field, $lead, $Form );
		if ( $display_empty_fields || ! empty( $display_value ) || $display_value === '0' ) {
			$count ++;
			$is_last  = $count >= $field_count && ! $has_product_fields ? true : false;
			$last_row = $is_last ? ' lastrow' : '';

			$display_value = empty( $display_value ) && $display_value !== '0' ? '&nbsp;' : $display_value;

			$content = '
                    <tr>
                        <th colspan="2" class="entry-view-field-name">' . esc_html( GFCommon::get_label( $field ) ) . '</th>
                    </tr>
                    <tr>
                        <td colspan="2" class="entry-view-field-value' . $last_row . '">' . $display_value . '</td>
                    </tr>';

			$content = apply_filters( 'gform_field_content', $content, $field, $value, $lead['id'], $Form['id'] );

			echo $content;

		}
	} // End foreach

	$products = array();
	if ( $has_product_fields ) {
		$products = GFCommon::get_product_fields( $Form, $lead );
		if ( ! empty( $products['products'] ) ) {
			?>
			<tr>
				<td colspan="2"
					class="entry-view-field-name"><?php echo apply_filters( "gform_order_label_{$Form["id"]}", apply_filters( 'gform_order_label', __( 'Order', 'gravityforms' ), $Form['id'] ), $Form['id'] ); ?></td>
			</tr>
			<tr>
				<td colspan="2" class="entry-view-field-value lastrow">
					<table class="entry-products" cellspacing="0" width="97%">
						<colgroup>
							<col class="entry-products-col1">
							<col class="entry-products-col2">
							<col class="entry-products-col3">
							<col class="entry-products-col4">
						</colgroup>
						<thead>
						<th scope="col"><?php echo apply_filters( "gform_product_{$Form['id']}", apply_filters( 'gform_product', __( 'Product', 'gravityforms' ), $Form['id'] ), $Form['id'] ); ?></th>
						<th scope="col"
							class="textcenter"><?php echo apply_filters( "gform_product_qty_{$Form['id']}", apply_filters( 'gform_product_qty', __( 'Qty', 'gravityforms' ), $Form['id'] ), $Form['id'] ); ?></th>
						<th scope="col"><?php echo apply_filters( "gform_product_unitprice_{$Form['id']}", apply_filters( 'gform_product_unitprice', __( 'Unit Price', 'gravityforms' ), $Form['id'] ), $Form['id'] ); ?></th>
						<th scope="col"><?php echo apply_filters( "gform_product_price_{$Form['id']}", apply_filters( 'gform_product_price', __( 'Price', 'gravityforms' ), $Form['id'] ), $Form['id'] ); ?></th>
						</thead>
						<tbody>
						<?php

						$total = 0;
						foreach ( $products['products'] as $product ) {
							?>
							<tr>
								<td>
									<div class="product_name"><?php echo esc_html( $product['name'] ); ?></div>
									<ul class="product_options">
										<?php
										$price = GFCommon::to_number( $product['price'] );
										if ( is_array( rgar( $product, 'options' ) ) ) {
											$count = count( $product['options'] );
											$index = 1;
											foreach ( $product['options'] as $option ) {
												$price += GFCommon::to_number( $option['price'] );
												$class = $index == $count ? " class='lastitem'" : '';
												$index ++;
												?>
												<li<?php echo $class; ?>><?php echo $option['option_label']; ?></li>
												<?php
											}
										}
										$subtotal = floatval( $product['quantity'] ) * $price;
										$total += $subtotal;
										?>
									</ul>
								</td>
								<td class="textcenter"><?php echo $product['quantity']; ?></td>
								<td><?php echo GFCommon::to_money( $price, $lead['currency'] ); ?></td>
								<td><?php echo GFCommon::to_money( $subtotal, $lead['currency'] ); ?></td>
							</tr>
							<?php
						}
						$total += floatval( $products['shipping']['price'] );
						?>
						</tbody>
						<tfoot>
						<?php
						if ( ! empty( $products['shipping']['name'] ) ) {
							?>
							<tr>
								<td colspan="2" rowspan="2" class="emptycell">&nbsp;</td>
								<td class="textright shipping"><?php echo $products['shipping']['name']; ?></td>
								<td class="shipping_amount"><?php echo GFCommon::to_money( $products['shipping']['price'], $lead['currency'] ); ?>
									&nbsp;</td>
							</tr>
							<?php
						}
						?>
						<tr>
							<?php
							if ( empty( $products['shipping']['name'] ) ) {
								?>
								<td colspan="2" class="emptycell">&nbsp;</td>
								<?php
							}
							?>
							<td class="textright grandtotal"><?php esc_html_e( 'Total', 'gravityforms' ); ?></td>
							<td class="grandtotal_amount"><?php echo GFCommon::to_money( $total, $lead['currency'] ); ?></td>
						</tr>
						</tfoot>
					</table>
				</td>
			</tr>

			<?php
		}
	}

	// Edit link
	if (
		! empty( $options['useredit'] ) && is_user_logged_in() && intval( $current_user->ID ) === intval( $lead['created_by'] ) || // Is user who created the entry
		! empty( $options['adminedit'] ) && self::has_access( 'gravityforms_directory' ) // Or is an administrator
	) {

		if ( ! empty( $options['adminedit'] ) && self::has_access( 'gravityforms_directory' ) ) {
			$editbuttontext = apply_filters( 'kws_gf_directory_edit_entry_text_admin', __( 'Edit Entry', 'gravity-forms-addons' ) );
		} else {
			$editbuttontext = apply_filters( 'kws_gf_directory_edit_entry_text_user', __( 'Edit Your Entry', 'gravity-forms-addons' ) );
		}

		?>
		<tr>
			<th scope="row"
				class="entry-view-field-name"><?php echo esc_html( apply_filters( 'kws_gf_directory_edit_entry_th', __( 'Edit', 'gravity-forms-addons' ) ) ); ?></th>
			<td class="entry-view-field-value useredit"><a
					href="<?php echo esc_url( add_query_arg( array( 'edit' => wp_create_nonce( 'edit' . $lead['id'] . $Form['id'] ) ) ) ); ?>"><?php echo $editbuttontext; ?></a>
			</td>
		</tr>
		<?php
	}

	?>
	</tbody>
</table>
