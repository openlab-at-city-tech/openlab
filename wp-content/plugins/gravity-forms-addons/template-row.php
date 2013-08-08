<?php


	if(is_array($leads) && !empty($leads) && sizeof($leads) > 0 && $lead_count > 0){

		$field_ids = array_keys($columns);

		$evenodd = '';
		foreach($leads as $lead){
			echo "\n\t\t\t\t\t\t";

			$address = array(); $celltitle = '';

			if($approved) { $leadapproved = self::check_approval($lead, $approvedcolumn); }

			if((isset($leadapproved) && $leadapproved && $approved) || !$approved) {

				$target = ($linknewwindow && empty($lightboxsettings['images'])) ? ' target="_blank"' : '';
				$valignattr = ($valign && $directoryview == 'table') ? ' valign="'.$valign.'"' : '';
				$nofollow = $nofollowlinks ? ' rel="nofollow"' : '';
				$evenodd = apply_filters('kws_gf_directory_evenodd', ($evenodd == ' odd') ? ' even' : ' odd');
				$rowstyle = !empty($rowstyle) ? ' style="'.$rowstyle.'"' : '';

			?><tr id="lead_row_<?php echo $lead["id"] ?>" class='<?php echo trim($rowclass.$evenodd); echo $lead["is_starred"] ? " featured" : "" ?>'<?php echo $rowstyle . $valignattr; ?>><?php
				$class = "";
				$is_first_column = true;

				foreach($field_ids as $field_id) {
					$field = $columns[$field_id];
					$lightboxclass = '';
					if(!empty($lightboxsettings['images'])) { $lightboxclass = ' class="thickbox colorbox lightbox"';  }
					$value = isset($lead[$field_id]) ? $lead[$field_id] : '';

					if(GFCommon::is_post_field($columns[$field_id])) {
						$input_type = $field['type'];
					} else {
						$input_type = RGFormsModel::get_input_type($field);
					}

					switch($input_type){

						case "address" :
						case "radio" :
						case "checkbox" :
						case "name":
							$value = "";

							// If you want to display all checked responses
							if(floatval($field_id) === floor(floatval($field_id))) {
								$field = RGFormsModel::get_field($form, $field_id);
								$value = RGFormsModel::get_lead_field_value($lead, $field);
								$value = GFCommon::get_lead_field_display($field, $value, $lead["currency"]);
							} else {
								// We're appending this to the end.
								if($input_type === 'address' && $appendaddress) {
									$address['id'] = floor((int)$field_id);
									$address[$field_id] = $value;
									if($hideaddresspieces) { $value = NULL; }
								} else {

									//looping through lead detail values trying to find an item identical to the column label. Mark with a tick if found.
									$lead_field_keys = array_keys($lead);
									foreach($lead_field_keys as $input_id){
										//mark as a tick if input label (from form meta) is equal to submitted value (from lead)
										if(is_numeric($input_id) && floatval($input_id) === floatval($field_id)) {
											if($lead[$input_id] == $field["label"]){
												$value = apply_filters( 'kws_gf_directory_tick', sprintf("<img src='" . GFCommon::get_base_url() . "/images/tick.png' alt='%s' />", esc_html( $lead[$input_id] )), $lead, $field);
											} else {
												$value = $lead[$input_id];
											}
										}
									}
								}
							}
						break;

						case "fileupload" :
						case "post_image" :
							$valueArray = explode("|:|", $value);

							@list($url, $title, $caption, $description) = $valueArray;
							$size = '';
							if(!empty($url)){
								//displaying thumbnail (if file is an image) or an icon based on the extension
								 $icon = self::get_icon_url($url);
								 if(!preg_match('/icon\_image\.gif/ism', $icon)) {
								 	$src = $icon;
								 	if(!empty($getimagesize)) {
										$size = @getimagesize($src);
										$img = "<img src='$src' {$size[3]}/>";
									} else {
										$size = false;
										$img = "<img src='$src' />";
									}
								 } else { // No thickbox for non-images please
								 	switch(strtolower(trim($postimage))) {
								 		case 'image':
								 			$src = $url;
								 			break;
								 		case 'icon':
								 		default:
								 			$src = $icon;
								 			break;
								 	}
								 	if(!empty($getimagesize)) {
										$size = @getimagesize($src);
									} else {
										$size = false;
									}
								 }
								 $img = array(
								 	'src' => $src,
								 	'size' => $size,
								 	'title' => $title,
								 	'caption' => $caption,
								 	'description' => $description,
								 	'url' => esc_attr($url),
								 	'code' => isset($size[3]) ? "<img src='$src' {$size[3]} />" : "<img src='$src' />"
								 );
								 $img = apply_filters('kws_gf_directory_lead_image', apply_filters('kws_gf_directory_lead_image_'.$postimage, apply_filters('kws_gf_directory_lead_image_'.$lead['id'], $img)));

								if(in_array('images', $lightboxsettings) || !empty($lightboxsettings['images'])) {
									$lightboxclass .= ' rel="directory_all directory_images"';
								}
								$value = "<a href='{$url}'{$target}{$lightboxclass}>{$img['code']}</a>";
							}
						break;

						case "source_url" :
							if(in_array('urls', $lightboxsettings) || !empty($lightboxsettings['urls'])) {
								$lightboxclass .= ' rel="directory_all directory_urls"';
							}
							if($linkwebsite) {
								$value = "<a href='" . esc_attr($lead["source_url"]) . "'{$target}{$lightboxclass} title='" . esc_attr($lead["source_url"]) . "'$nofollow>.../" .esc_attr(GFCommon::truncate_url($lead["source_url"])) . "</a>";
							} else {
								$value = esc_attr(GFCommon::truncate_url($lead["source_url"]));
							}
						break;

						case "textarea" :
						case "post_content" :
						case "post_excerpt" :
							if($fulltext) {
								$long_text = $value = "";

								if(isset($lead[$field_id]) && strlen($lead[$field_id]) >= GFORMS_MAX_FIELD_LENGTH) {
								   $long_text = RGFormsModel::get_lead_field_value($lead, RGFormsModel::get_field($form, $field_id));
								}
								if(isset($lead[$field_id])) {
									$value = !empty($long_text) ? $long_text : $lead[$field_id];
								}

							} else {
								$value = esc_html($value);
							}
							if($wpautop) { $value = wpautop($value); };
						break;

						case "post_category":
							$value = GFCommon::get_lead_field_display($field, $lead[$field_id]);
							$value = GFCommon::prepare_post_category_value($lead[$field_id], $field);
						break;

						case "date_created" :
							$value = GFCommon::format_date($lead['date_created'], true, apply_filters( 'kws_gf_date_format', '' ));
						break;

						case "date" :
							$field = RGFormsModel::get_field($form, $field_id);
							if($dateformat) {
								 $value = GFCommon::date_display($value, $dateformat);
							 } else {
							 	$value = GFCommon::date_display($value, $field["dateFormat"]);
							 }
						break;

						case "id" :
							$linkClass = '';
						break;

						case "list":
							$field = RGFormsModel::get_field($form, $field_id);
							$value = GFCommon::get_lead_field_display($field, $value);
						break;

						default:

							$input_type = 'text';
							if(is_email($value) && $linkemail) {$value = "<a href='mailto:$value'$nofollow>$value</a>"; }
							elseif(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $value) && $linkwebsite) {
								$href = $value;
								if(!empty($lightboxsettings['images'])) {
									if(in_array('urls', $lightboxsettings) || !empty($lightboxsettings['urls'])) {
										$lightboxclass .= ' rel="directory_all directory_urls"';
									}
									$linkClass = $lightboxclass;
								} else {
									$linkClass = isset($linkClass) ? $linkClass : '';
								}
								if($truncatelink) {
									$value = apply_filters('kws_gf_directory_anchor_text', $value);
								}
								$value = "<a href='{$href}'{$nofollow}{$target}{$linkClass}>{$value}</a>";
							}
							else { $value = esc_html($value); }
					}
					if($is_first_column) { echo "\n"; }
					if($value !== NULL) {
						if(isset($columns["{$field_id}"]['label']) && $hovertitle || $directoryview !== 'table') {
							$celltitle = ' title="'.esc_html(apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_'.$field_id, apply_filters('kws_gf_directory_th_'.sanitize_title($columns["{$field_id}"]['label']), $columns["{$field_id}"]['label'])))).'"';
						} else {
							$celltitle = '';
						}
					 	echo "\t\t\t\t\t\t\t"; ?><td<?php echo ' class="'.$input_type.'"'; echo $valignattr; echo $celltitle; ?>><?php


					 	$value = empty($value) ? '&nbsp;' : $value;

						if(isset($entrylinkcolumns[floor($field_id)]) || isset($entrylinkcolumns['id']) && $input_type == 'id') {

							if($input_type == 'id' && $entry) {
								$linkvalue = $entrylink;
							} else{
								$type = $entrylinkcolumns[floor($field_id)];
								if($type === 'label') {
									$linkvalue = $columns["{$field_id}"]['label'];
								} elseif(!empty($type) && $type !== 'on') {
									$linkvalue = str_replace('%value%', $value, $type);
								} else {
									$linkvalue = $value;
								}
							}
					 		$value = self::make_entry_link($options, $linkvalue, $lead['id'], $form_id, $field_id);
						}

					 	$value = apply_filters('kws_gf_directory_value', apply_filters('kws_gf_directory_value_'.$input_type, apply_filters('kws_gf_directory_value_'.$field_id, $value)));
					 echo $value;

					?></td><?php
						echo "\n";
						$is_first_column = false;
					}
				}

				if(is_array($address) && !empty($address) && $appendaddress) {
					$address = apply_filters('kws_gf_directory_td_address', $address, $linknewwindow);
					if(!is_array($address)) {
						echo "\t\t\t\t\t\t\t".'<td class="address" title="'.esc_html(apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_address', 'Address'))).'">'.$address.'</td>';
					}
				}

			   	?>
		</tr>
			<?php }
		}
	}
	else{
		?>
		<tr>
			<td colspan="<?php echo sizeof($columns); ?>" class="noresults" style="padding:20px;"><?php

				if($search_query) {
					_e("This search returned no results.", "gravity-forms-addons");
				} elseif($limituser) {
					_e("This form does not have any visible entries.", "gravity-forms-addons");
				} else {
					_e("This form does not have any entries yet.", "gravity-forms-addons");
				}

			?></td>
		</tr>
		<?php
	}
?>