<?php

if ( ! defined( 'ABSPATH' ) ) {	exit; }

if (!current_user_can('manage_options')) {
	wp_die('Not authorized');
}

global $gdeoptions;

// which form are we submitting (uses nonce for security and identification)
if ( isset( $_POST['_general_default'] ) ) {
	check_admin_referer('update-default-opts', '_general_default');

	// updating default profile
	$tabid = "gentab";
	
	if ( gde_form_to_profile( 1, $_POST ) ) {
		// update successful
		gde_show_msg( __('Default profile <strong>updated</strong>.', 'google-document-embedder') );
	} else {
		gde_show_msg( __('Unable to update profile.', 'google-document-embedder'), true );
	}

} elseif ( isset( $_POST['_advanced'] ) ) {

	check_admin_referer('update-adv-opts', '_advanced');

	// updated advanced options (global)
	$tabid = "advtab";
	
	// keep old options for a moment
	$oldoptions = $gdeoptions;
	
	// initialize checkbox values (values if options unchecked)
	$gdeoptions['ed_disable'] = "no";
	$gdeoptions['ed_embed_sc'] = "no";
	$gdeoptions['ed_extend_upload'] = "no";
	$gdeoptions['error_display'] = "no";
	$gdeoptions['error_check'] = "no";
	$gdeoptions['error_log'] = "no";
	
	foreach ( $_POST as $k => $v ) {
		if ( $k == "ed_disable" ) {
			$gdeoptions[$k] = "yes";
		} elseif ( $k == "ed_embed_sc" ) {
			$gdeoptions[$k] = "yes";
		} elseif ( $k == "ed_extend_upload" ) {
			$gdeoptions[$k] = "yes";
		} elseif ( $k == "error_display" ) {
			$gdeoptions[$k] = "yes";
		} elseif ( $k == "error_check" ) {
			$gdeoptions[$k] = "yes";
		} elseif ( $k == "error_log" ) {
			$gdeoptions[$k] = "yes";
			if ( ! isset( $oldoptions['error_log'] ) || $oldoptions['error_log'] == "no" ) {
				if ( ! gde_dx_log("Diagnostic logging enabled") ) {
					// can't write to db - don't enable logging
					gde_show_msg( __('Unable to enable diagnostic logging.', 'google-document-embedder'), true );
					$gdeoptions[$k] = "no";
				}
			}
		}
		elseif ( $k == "file_maxsize") {
			$gdeoptions[$k] = intval($v);
        }
        elseif ( $k == "ga_enable" && in_array($v, array("yes", "compat", "no"))) {
			$gdeoptions[$k] = $v;
		}
        elseif ( $k == "ga_label" && in_array($v, array("file", "url"))) {
			$gdeoptions[$k] = $v;
		}
        elseif ( $k == "ga_category" && preg_match("|^[A-Za-z0-9 ()\-_\%]+$|", $v)) {
			$gdeoptions[$k] = $v;
		}

	}
	
	if ( update_option( 'gde_options', $gdeoptions ) ) {
		// update successful
		gde_show_msg( __('Settings <strong>updated</strong>.', 'google-document-embedder') );
	} else {
		gde_show_msg( __('Settings <strong>updated</strong>.', 'google-document-embedder') );	// not true, but avoids confusion in case where no changes were made
		gde_dx_log('Settings update failed - maybe no changes');
	}
}

?>

<div class="wrap">
	<div class="icon32" id="icon-options-general"></div>
	<h2>Google Doc Embedder <?php _e('Settings', 'google-document-embedder'); ?></h2>

    <p><b>This plugin is due to be retired.</b> Changes to WordPress and Google's free doc viewer mean that this plugin should be switched for an alternative.</p>

    <p>Existing gview shortcodes should still work for now.</p>

    <p>Please see the <a href="https://wordpress.org/plugins/google-document-embedder/" target="_blank">plugin homepage</a> and support forums for suggestions.</p>

	<div id="gde-tabcontent">

		<div id="gencontent" class="gde-tab gde-tab-active">
			<?php gde_show_tab('general'); ?>
		</div>

		<div id="advcontent" class="gde-tab gde-tab-active">
			<?php gde_show_tab('advanced'); ?>
		</div>

	</div>
	
</div>

<?php
function gde_opts_checkbox( $field, $label, $wrap = '', $br = '', $disabled = false ) {
	global $gdeoptions;
	
	if ( ! empty( $wrap ) ) {
		echo '<span id="'.esc_attr($wrap).'">';
	}
	echo '<input type="checkbox" id="'.esc_attr($field).'" name="'.esc_attr($field).'"';
	if ( ( isset( $gdeoptions[$field] ) && $gdeoptions[$field] == "yes" ) || ( $disabled ) ) {
		echo ' checked="checked"';
	}
	if ( $disabled ) {
		// used only for dx logging option due to global override in functions.php
		echo ' disabled="disabled"';
	}
	
	echo ' value="'.esc_attr($field).'"> <label for="'.esc_attr($field).'">'.htmlentities($label).'</label>';
	if ( ! empty( $br ) ) {
		echo '<br/>';
	}
	if ( ! empty( $wrap ) ) {
		echo '</span>';
	}
}

function gde_profile_option( $option, $value, $label, $helptext = '' ) {
	echo "<option value=\"".esc_attr($value)."\"";
	if ( ! empty( $helptext ) ) {
		echo " title=\"".esc_attr($helptext)."\"";
	}
	if ( $option == $value ) {
		echo ' selected="selected"';
	}
	echo ">$label &nbsp;</option>\n";
}

function gde_profile_checkbox( $option, $field, $label, $wrap = '', $br = '' ) {
	if ( ! empty( $wrap ) ) {
		echo '<span id="'.esc_attr($wrap).'">';
	}
	echo '<input type="checkbox" id="'.esc_attr($field).'" name="'.esc_attr($field).'"';
	
	// toolbar items
	if ( substr( $field, 0, 5 ) == "gdet_" ) {
		if ( $field == "gdet_h" && strstr( $option, str_replace( "gdet_", "", $field ) ) ) {
			echo ' checked="checked"';
		} elseif ( $field !== "gdet_h" && ! strstr( $option, str_replace( "gdet_", "", $field ) ) ) {
			echo ' checked="checked"';
		}
	// open in new window
	} elseif ( $field == "fs_win" && $option !== "same" ) {
		echo ' checked="checked"';
	// logged-in users only
	} elseif ( $field == "fs_user" && $option == "yes" ) {
		echo ' checked="checked"';
	// allow print
	} elseif ( $field == "fs_print" && $option !== "no" ) {
		echo ' checked="checked"';
	// content area options
	} elseif  ( substr( $field, 0, 5 ) == "gdev_" ) {
		if ( strstr( $option, str_replace( "gdev_", "", $field ) ) ) {
			echo ' checked="checked"';
		}
	// doc security options
	} elseif ( $field == "force" && $option !== "no" ) {
		echo ' checked="checked"';
	} elseif ( $field == "mask" && $option !== "no" ) {
		echo ' checked="checked"';
	} elseif ( $field == "block" && $option !== "no" ) {
		echo ' checked="checked"';
	}
	
	echo ' value="'.esc_attr($field).'"> <label for="'.esc_attr($field).'">'.htmlentities($label).'</label>';
	if ( ! empty( $br ) ) {
		echo '<br/>';
	}
	if ( ! empty( $wrap ) ) {
		echo '</span>';
	}
}

function gde_profile_text( $option, $field, $class = '', $size = '', $enabled = true ) {
	echo '<input type="text" id="'.esc_attr($field).'" name="'.esc_attr($field).'" value="'.esc_attr($option).'"';
	if ( ! empty( $class ) ) {
		echo ' class="'.esc_attr($class).'"';
	}
	if ( ! empty( $size ) ) {
		echo ' size="'.esc_attr($size).'"';
	}
	if ( $enabled === false ) {
		echo ' disabled="disabled"';
		echo ' style="color:#aaa;background-color:#eee;"';
	}
	echo ">";
}

function gde_help_link( $url, $float = '' ) {
	$title = __('Help', 'google-document-embedder');
	$img = GDE_PLUGIN_URL . "img/help.png";
	
	if ( ! empty( $float ) ) {
		echo '<div style="float:'.esc_attr($float).';">';
	}
	
	echo '<a href="'.esc_attr($url).'" target="_blank" title="'.esc_attr($title).'"><img src="'.esc_attr($img).'" alt="?"></a>';
	
	if ( ! empty( $float ) ) {
		echo "</div>\n";
	}
}

function gde_row_cb( $pid ) {
	// default profile
	if ( $pid == 1 ) {
		return " ";
	} else {
		return '<input type="checkbox" value="'.esc_attr($pid).'" name="delete_tags[]">';
	}
}

?>
