<?php

add_action('init', array('GFDirectory_EditForm', 'initialize'));

class GFDirectory_EditForm {

	function initialize() {
		$GFDirectory_EditForm = new GFDirectory_EditForm();
	}

	function __construct() {

		add_action('admin_init',  array(&$this, 'process_exterior_pages'));

		if(self::is_gravity_page()) {

			add_filter('gform_tooltips', array(&$this, 'directory_tooltips')); //Filter to add a new tooltip
			add_action("gform_editor_js", array(&$this, "editor_script")); //Action to inject supporting script to the form editor page
			add_action("admin_head", array(&$this, "toolbar_links")); //Action to inject supporting script to the form editor page
			add_action("gform_field_advanced_settings", array(&$this,"use_as_entry_link_settings"), 10, 2);
			add_filter("gform_add_field_buttons", array(&$this,"add_field_buttons"));
			add_filter('admin_head', array(&$this,'directory_admin_head'));
			add_action('gform_editor_js_set_default_values', array(&$this,'directory_add_default_values'));

			// Allows for edit links to work with a link instead of a form (GET instead of POST)
			if(isset($_GET["screen_mode"])) { $_POST["screen_mode"] = $_GET["screen_mode"]; }
		}
	}

	// From gravityforms.php
	public static function process_exterior_pages(){
		if(rgempty("gf_page", $_GET))
            return;

        //ensure users are logged in
        if(!is_user_logged_in())
            auth_redirect();

        switch(rgget("gf_page")){
            case "directory_columns" :
            	require_once("select_directory_columns.php");
            break;
        }
        exit();
    }

	public function directory_add_default_values() {
		?>
		case "entrylink" :
				field.label = "<?php _e("Go to Entry", "gravity-forms-addons"); ?>";
		        field.adminOnly = true;
		        field.choices = null;
		        field.inputs = null;
		        field.hideInSingle = true;
		        field.useAsEntryLink = 'label';
		        field.type = 'hidden';
		        field.disableMargins = true;

		break;

		case 'usereditlink':
				field.label = "<?php _e("Edit", "gravity-forms-addons"); ?>";

		        field.adminOnly = true;
		        field.choices = null;
		        field.inputs = null;
		        field.hideInSingle = false;
		        field.useAsEntryLink = false;
		        field.type = 'hidden';
		        field.disableMargins = 2;

		break;

		case 'directoryapproved':
				field.label = "<?php _e("Approved? (Admin-only)", "gravity-forms-addons"); ?>";

				field.adminLabel = "<?php _e("Approved?", "gravity-forms-addons"); ?>";
				field.adminOnly = true;

				field.choices = null;
		        field.inputs = null;

		        if(!field.choices)
		            field.choices = new Array(new Choice("<?php _e("Approved", "gravity-forms-addons"); ?>"));

		        field.inputs = new Array();
		        for(var i=1; i<=field.choices.length; i++)
		            field.inputs.push(new Input(field.id + (i/10), field.choices[i-1].text));

		        field.hideInDirectory = true;
		        field.hideInSingle = true;
		        field.type = 'checkbox';

		break;
		<?php
	}

	public function directory_admin_head() {
			global $_gform_directory_approvedcolumn, $process_bulk_update_message;

			if(!(self::is_gravity_page('gf_entries') && isset($_GET['id']) && !self::is_gravity_page('gf_edit_forms'))) { return; }

			 ?>
		<style type="text/css">

		.lead_approved .toggleApproved {
			background: url(<?php echo GFCommon::get_base_url() ?>/images/tick.png) left top no-repeat;
		}
		.toggleApproved {
			background: url(<?php echo GFCommon::get_base_url() ?>/images/cross.png) left top no-repeat;
			width: 16px;
			height: 16px;
			display: block;
			text-indent: -9999px;
			overflow: hidden;
		}
		</style>
		<script type="text/javascript">

			<?php

			$formID = RGForms::get("id");
	        if(empty($formID)) {
		        $forms = RGFormsModel::get_forms(null, "title");
	            $formID = $forms[0]->id;
	        }

		   	$_gform_directory_approvedcolumn = empty($_gform_directory_approvedcolumn) ? GFDirectory::globals_get_approved_column($formID) : $_gform_directory_approvedcolumn;

			if(!empty($_gform_directory_approvedcolumn)) {
			    echo 'formID = '.$formID.';';
		       ?>

		    function UpdateApproved(lead_id, approved) {
		    	var mysack = new sack("<?php echo admin_url("admin-ajax.php")?>" );
		        mysack.execute = 1;
		        mysack.method = 'POST';
		        mysack.setVar( "action", "rg_update_approved" );
		        mysack.setVar( "rg_update_approved", "<?php echo wp_create_nonce("rg_update_approved") ?>" );
		        mysack.setVar( "lead_id", lead_id);
		        mysack.setVar( "form_id", formID);
		        mysack.setVar( "approved", approved);
		        mysack.encVar( "cookie", document.cookie, false );
		        mysack.onError = function() { console.log('<?php echo esc_js(__("Ajax error while setting lead approval", "gravity-forms-addons")) ?>' )};
		        mysack.runAJAX();

		        return true;
		    }

		 <?php

		 if(!function_exists('gform_get_meta')) { ?>

		    function displayMessage(message, messageClass, container){

                hideMessage(container, true);

                var messageBox = jQuery('<div class="message ' + messageClass + '" style="display:none;"><p>' + message + '</p></div>');
                jQuery(messageBox).prependTo(container).slideDown();

                if(messageClass == 'updated')
                    messageTimeout = setTimeout(function(){ hideMessage(container, false); }, 10000);

            }

            function hideMessage(container, messageQueued){

                var messageBox = jQuery(container).find('.message');

                if(messageQueued)
                    jQuery(messageBox).remove();
                else
                    jQuery(messageBox).slideUp(function(){ jQuery(this).remove(); });

            }

         <?php } // end meta check for 1.6         ?>

			jQuery(document).ready(function($) {

		    	<?php if(!empty($process_bulk_update_message)) { ?>
			    	displayMessage('<?php _e($process_bulk_update_message); ?>', 'updated', '#lead_form');
			    <?php } ?>

		    	$("#bulk_action,#bulk_action2").append('<optgroup label="Directory"><option value="approve-'+formID+'"><?php _e('Approve', 'gravity-forms-addons'); ?></option><option value="unapprove-'+formID+'"><?php _e('Disapprove', 'gravity-forms-addons'); ?></option></optgroup>');

		    	var approveTitle = '<?php _e('Entry not approved for directory viewing. Click to approve this entry.', 'gravity-forms-addons'); ?>';
		    	var unapproveTitle = '<?php _e('Entry approved for directory viewing. Click to disapprove this entry.', 'gravity-forms-addons'); ?>';

		    	$('.toggleApproved').live('click load', function(e) {
		    		e.preventDefault();

		    		var $tr = $(this).parents('tr');
					var is_approved = $tr.is(".lead_approved");

					if(e.type == 'click') {
				        $tr.toggleClass("lead_approved");
				    }

					// Update the title and screen-reader text
			        if(!is_approved) { $(this).text('X').attr('title', unapproveTitle); }
			        else { $(this).text('O').attr('title', approveTitle); }

					if(e.type == 'click') {
				        UpdateApproved($('th input[type="checkbox"]', $tr).val(), is_approved ? 0 : 'Approved');
				    }

					UpdateApprovedColumns($(this).parents('table'), false);

					return false;

		    	});

		    	// We want to make sure that the checkboxes go away even if the Approved column is showing.
		    	// They will be in sync when loaded, so only upon click will we process.
		    	function UpdateApprovedColumns($table, onLoad) {
					var colIndex = $('th:contains("Approved")', $table).index() - 1;

					$('tr', $table).each(function() {
						if($(this).is('.lead_approved') || (onLoad && $("input.lead_approved", $(this)).length > 0)) {
							if(onLoad && $(this).not('.lead_approved')) { $(this).addClass('lead_approved'); }
							$('td:visible:eq('+colIndex+'):has(.toggleApproved)', $(this)).html("<img src='<?php echo GFCommon::get_base_url(); ?>/images/tick.png'/>");
						} else {
							if(onLoad && $(this).is('.lead_approved')) { $(this).removeClass('lead_approved'); }
							$('td:visible:eq('+colIndex+'):has(.toggleApproved)', $(this)).html('');
						}
					});
		    	}

		    	$('td:has(img[src*="star"])').after('<td><a href="#" class="toggleApproved" title="'+approveTitle+'">X</a></td>');
		    	$('th.check-column:eq(1)').after('<th class="manage-column column-cb check-column"><a href="<?php echo add_query_arg(array('sort' => $_gform_directory_approvedcolumn)); ?>"><img src="<?php echo WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)); ?>/form-button-1.png" style="text-align:center; margin:0 auto; display:block;" title="<?php _e('Show entry in directory view?', 'gravity-forms-addons'); ?>" /></span></a></th>');

		    	$('tr:has(input.lead_approved)').addClass('lead_approved').find('a.toggleApproved').attr('title', unapproveTitle).text('O');

		    	UpdateApprovedColumns($('table'), true);

		    });
			<?php } // end if(!empty($_gform_directory_approvedcolumn)) check ?>
		</script><?php
	}

	public function use_as_entry_link_settings($position, $form_id){

	    //create settings on position 50 (right after Admin Label)
	    if($position === -1){
	        ?>
	        </ul>
	      </div>
	      <div id="gform_tab_3">
		        <ul>
			        <li class="use_as_entry_link gf_directory_setting field_setting">
			            <label for="field_use_as_entry_link">
			                <?php _e("Use As Link to Single Entry", "gravity-forms-addons"); ?>
			                <?php gform_tooltip("kws_gf_directory_use_as_link_to_single_entry") ?>
			            </label>
			            <label for="field_use_as_entry_link"><input type="checkbox" value="1" id="field_use_as_entry_link" /> <?php _e("Use this field as a link to single entry view", "gravity-forms-addons"); ?></label>
			        </li>
			        <li class="use_as_entry_link_value gf_directory_setting field_setting">
			            <label>
			                <?php _e("Single Entry Link Text", "gravity-forms-addons"); ?>
			                <span class="howto"><?php _e('Note: it is a good idea to use required fields for links to single entries so there are no blank links.', 'gravity-forms-addons'); ?></span>
			            </label>

			        	<label><input type="radio" name="field_use_as_entry_link_value" id="field_use_as_entry_link_value" value="on" /> <?php _e("Use field values from entry", "gravity-forms-addons"); ?></label>
			        	<label><input type="radio" name="field_use_as_entry_link_value" id="field_use_as_entry_link_label" value="label" /> <?php _e(sprintf("Use the Field Label %s as link text", '<span id="entry_link_label_text"></span>'), "gravity-forms-addons"); ?></label>
			        	<label><input type="radio" name="field_use_as_entry_link_value" id="field_use_as_entry_link_custom" value="custom" /> <?php _e("Use custom link text.", "gravity-forms-addons"); ?></label>
			        	<span class="hide-if-js" style="display:block;clear:both; margin-left:1.5em"><input type="text" class="widefat" id="field_use_as_entry_link_value_custom_text" value="" /><span class="howto"><?php _e(sprintf('%s%%value%%%s will be replaced with each entry\'s value.', "<code class='code'>", '</code>'), 'gravity-forms-addons'); ?></span></span>
			        </li>
			        <li class="hide_in_directory_view gf_directory_setting field_setting">
			            <label for="hide_in_directory_view">
			                <?php _e("Hide This Field in Directory View?", "gravity-forms-addons"); ?>
			                <?php gform_tooltip("kws_gf_directory_hide_in_directory_view") ?>
			            </label>
			        	<label><input type="checkbox" id="hide_in_directory_view" /> <?php _e("Hide this field in the directory view.", "gravity-forms-addons"); ?></label>
			        </li>
			        <li class="hide_in_single_entry_view gf_directory_setting field_setting">
			            <label for="hide_in_single_entry_view">
			                <?php _e("Hide This Field in Single Entry View?", "gravity-forms-addons"); ?>
			                <?php gform_tooltip("kws_gf_directory_hide_in_single_entry_view") ?>
			            </label>
			        	<label><input type="checkbox" id="hide_in_single_entry_view" /> <?php _e("Hide this field in the single entry view.", "gravity-forms-addons"); ?></label>
			        </li>
	        <?php
	   }
	}

	public function toolbar_links() {

		?>
	    <style type="text/css">
	    	li.gf_directory_setting, li.gf_directory_setting li {
	    		padding-bottom: 4px!important;
	    	}
	    	ul#gf_form_toolbar_links li#gf_form_toolbar_directory a { background: url(<?php echo WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)); ?>/editor-icon.gif) left top no-repeat; }
	    	ul#gf_form_toolbar_links li#gf_form_toolbar_directory a:hover { background-position: left -19px; }
	    </style>
	    <script type='text/javascript'>
	    	jQuery(document).ready(function($) {
	    		var url = '<?php echo add_query_arg(array('gf_page' => 'directory_columns', 'id' => @$_GET['id'], 'TB_iframe' => 'true', 'height' => 600, 'width' => 700), admin_url()); ?>';
	    		$link = $('<li class="gf_form_toolbar_preview gf_form_toolbar_directory" id="gf_form_toolbar_directory"><a href="'+url+'" class="thickbox" title="<?php echo esc_html(__('Modify Gravity Forms Directory Columns', 'gravity-forms-addons')); ?>"><?php _e('Directory Columns', 'gravity-forms-addons'); ?></a></li>');
	    		$('#gf_form_toolbar_links').append($link);
	    	});
	    </script>
	<?php
	}

	public function editor_script(){
	    ?>
	    <style type="text/css">
	    	li.gf_directory_setting, li.gf_directory_setting li {
	    		padding-bottom: 4px!important;
	    	}
	    </style>
	    <script type='text/javascript'>
	    	jQuery(document).ready(function($) {

	    		$( "#field_settings" ).tabs("add", "#gform_tab_3", "Directory");

	    		$('a[href="#gform_tab_3"]').parent('li').css({'width':'100px', 'padding':'0'});

		        for (var key in fieldSettings) {
		        	fieldSettings[key] += ", .gf_directory_setting";
		        }

		  		$('#field_use_as_entry_link_value_custom_text').change(function() {
		  			if($("#field_use_as_entry_link_custom").is(':checked')) {
		  				SetFieldProperty('useAsEntryLink', $(this).val());
		  			}
		  		});

				$("input:checkbox, input:radio",$('#gform_tab_3')).click(function() {
					var $li = $(this).parents('#field_settings');
					var entrylink = false;

					if($("#field_use_as_entry_link", $li).is(":checked")) {
						entrylink = '1';

						$('.use_as_entry_link_value').slideDown();

						if($('input[name="field_use_as_entry_link_value"]:checked').length) {
							entrylink = $('input[name="field_use_as_entry_link_value"]:checked').val();
						}
						if(entrylink == 'custom') {
							entrylink = $('#field_use_as_entry_link_value_custom_text').val();
							$('#field_use_as_entry_link_value_custom_text').parent('span').slideDown();
						} else {
							$('#field_use_as_entry_link_value_custom_text').parent('span').slideUp();
						}
					} else {
						$('.use_as_entry_link_value', $li).slideUp();
					}

					var hideInSingle = false;
					if($("#hide_in_single_entry_view", $li).is(':checked')) {
						hideInSingle = true;
					}

					var hideInDirectory = false;
					if($("#hide_in_directory_view", $li).is(':checked')) {
						hideInDirectory = true;
					}

					SetFieldProperty('hideInDirectory', hideInDirectory);
					SetFieldProperty('hideInSingle', hideInSingle);
					SetFieldProperty('useAsEntryLink', entrylink);
		        });

				$('#field_label').change(function() {
					kwsGFupdateEntryLinkLabel($(this).val());
				});

				function kwsGFupdateEntryLinkLabel(label) {
					$('#entry_link_label_text').html(' ("'+label+'")');
				}

		        //binding to the load field settings event to initialize the checkbox
		        $(document).bind("gform_load_field_settings", function(event, field, form){

		        	if(typeof(field["useAsEntryLink"]) !== "undefined" && field["useAsEntryLink"] !== false && field["useAsEntryLink"] !== 'false' && field["useAsEntryLink"] !== '') {
			            $("#field_use_as_entry_link").attr("checked", true);
			            $(".use_as_entry_link_value").show();
			            $('#field_use_as_entry_link_value_custom_text').parent('span').hide();
			            switch(field["useAsEntryLink"]) {
			            	case "on":
			            	case "":
			            	case false:
			            		$("#field_use_as_entry_link_value").attr('checked', true);
			            		break;
			            	case "label":
			            		$("#field_use_as_entry_link_label").attr('checked', true);
			            		break;
			            	default:
			            		$('#field_use_as_entry_link_value_custom_text').parent('span').show();
			            		$("#field_use_as_entry_link_custom").attr('checked', true);
			            		$("#field_use_as_entry_link_value_custom_text").val(field["useAsEntryLink"]);
			            }
			        } else {
			        	$(".use_as_entry_link_value").hide();
			        	$("#field_use_as_entry_link").attr("checked", false);
			        }

			        if($('input[name="field_use_as_entry_link_value"]:checked').length === 0) {
						$('#field_use_as_entry_link_value').attr('checked', true);
					}

			        kwsGFupdateEntryLinkLabel(field.label);


		            $("#field_use_as_entry_link_label").attr("checked", field["useAsEntryLink"] === 'label');

		            $("#hide_in_single_entry_view").attr("checked", (field["hideInSingle"] === true || field["hideInSingle"] === "on"));
		            $("#hide_in_directory_view").attr("checked", (field["hideInDirectory"] === true || field["hideInDirectory"] === "on"));


		        });
	       });
	    </script>
	    <?php
	}

	public function directory_tooltips($tooltips){
   		$tooltips["kws_gf_directory_use_as_link_to_single_entry"] = __(sprintf("%sLink to single entry using this field%sIf you would like to link to the single entry view using this link, check the box.", '<h6>', '</h6>'), 'gravity-forms-addons');
   		$tooltips['kws_gf_directory_hide_in_directory_view'] = __(sprintf('%sHide in Directory View%sIf checked, this field will not be shown in the directory view, even if it is visible in the %sDirectory Columns%s. If this field is Admin Only (set in the Advanced tab), it will be hidden in the directory view unless "Show Admin-Only columns" is enabled in the directory. Even if "Show Admin-Only columns" is enabled, checking this box will hide the column in the directory view.', '<h6>', '</h6>', sprintf('<a class="thickbox" title="%s" href="'.add_query_arg(array('gf_page' => 'directory_columns', 'id' => @$_GET['id'], 'TB_iframe' => 'true', 'height' => 600, 'width' => 700), admin_url()).'">', __('Modify Directory Columns', 'gravity-forms-addons')), '</a>'), 'gravity-forms-addons');
   		$tooltips['kws_gf_directory_hide_in_single_entry_view'] = __(sprintf('%sHide in Single Entry View%sIf checked, this field will not be shown in the single entry view of the directory.', '<h6>', '</h6>'), 'gravity-forms-addons');
   		return $tooltips;
	}

	//Returns true if the current page is one of Gravity Forms pages. Returns false if not
    private static function is_gravity_page($page = array()){
        return GFDirectory::is_gravity_page($page);
    }

	function add_field_buttons($field_groups){
		$directory_fields = array(
			'name' => 'directory_fields',
			'label' => 'Directory Fields',
			'fields' => array(
				array(
					'class' => 'button',
					'value' => __('Approved', 'gravity-forms-addons'),
					'onclick' => "StartAddField('directoryapproved');"
				),
				array(
					'class' => 'button',
					'value' => __('Entry Link', 'gravity-forms-addons'),
					'onclick' => "StartAddField('entrylink');"
				),
				array(
					'class' => 'button',
					'value' => __('User Edit Link', 'gravity-forms-addons'),
					'onclick' => "StartAddField('usereditlink');"
				)
			)
		);

		array_push($field_groups, $directory_fields);

		return $field_groups;
	}
}