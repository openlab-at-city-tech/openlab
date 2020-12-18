<?php
/**
 * The template that contains editor script content.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes/views
 */
?>

<style>
	li.gf_directory_setting, li.gf_directory_setting li {
		padding-bottom: 4px!important;
	}
</style>
<script>
	jQuery(document).ready(function($) {

		// instead of simply .tabs('add')...
		$('<li><a href="#gform_tab_directory"><?php echo esc_js( __( 'Directory', 'gravity-forms-addons' ) ); ?></a></li>' ).appendTo('#field_settings .ui-tabs-nav');
		$('#gform_tab_directory').appendTo( "#field_settings" );
		$( '#field_settings' ).tabs( "refresh" );

		$('a[href="#gform_tab_directory"]').parent('li').css({'width':'100px', 'padding':'0'});

		for (var key in fieldSettings) {
			fieldSettings[key] += ", .gf_directory_setting";
		}

		  $('#field_use_as_entry_link_value_custom_text').change(function() {
			  if($("#field_use_as_entry_link_custom").is(':checked')) {
				  SetFieldProperty('useAsEntryLink', $(this).val());
			  }
		  });

		$("input:checkbox, input:radio",$('#gform_tab_directory')).click(function() {
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

			// since 3.5
			var visibleToLoggedIn = false;
			if($("#only_visible_to_logged_in", $li).is(':checked')) {
				visibleToLoggedIn = true;
			}
			SetFieldProperty('visibleToLoggedIn', visibleToLoggedIn);

			var isSearchFilter = false;
			if($("#use_field_as_search_filter", $li).is(':checked')) {
				isSearchFilter = true;
			}
			SetFieldProperty('isSearchFilter', isSearchFilter );

		});


		$('#field_label').change(function() {
			kwsGFupdateEntryLinkLabel($(this).val());
		});

		function kwsGFupdateEntryLinkLabel(label) {
			$('#entry_link_label_text').html(' ("'+label+'")');
		}


		$('#only_visible_to_logged_in_cap').change( function() {
			if( $("#only_visible_to_logged_in").is(':checked') ) {
				SetFieldProperty('visibleToLoggedInCap', $(this).val() );
			}

		});



		//binding to the load field settings event to initialize the checkbox
		$(document).bind("gform_load_field_settings", function(event, field, form){

			if(typeof(field["useAsEntryLink"]) !== "undefined" && field["useAsEntryLink"] !== false && field["useAsEntryLink"] !== 'false' && field["useAsEntryLink"] !== '') {
				$("#field_use_as_entry_link").prop("checked", true);
				$(".use_as_entry_link_value").show();
				$('#field_use_as_entry_link_value_custom_text').parent('span').hide();
				switch(field["useAsEntryLink"]) {
					case "on":
					case "":
					case false:
						$("#field_use_as_entry_link_value").prop('checked', true);
						break;
					case "label":
						$("#field_use_as_entry_link_label").prop('checked', true);
						break;
					default:
						$('#field_use_as_entry_link_value_custom_text').parent('span').show();
						$("#field_use_as_entry_link_custom").prop('checked', true);
						$("#field_use_as_entry_link_value_custom_text").val(field["useAsEntryLink"]);
				}
			} else {
				$(".use_as_entry_link_value").hide();
				$("#field_use_as_entry_link").prop("checked", false);
			}

			if($('input[name="field_use_as_entry_link_value"]:checked').length === 0) {
				$('#field_use_as_entry_link_value').prop('checked', true);
			}

			kwsGFupdateEntryLinkLabel(field.label);


			$("#field_use_as_entry_link_label").prop("checked", field["useAsEntryLink"] === 'label');

			$("#hide_in_single_entry_view").prop("checked", (field["hideInSingle"] === true || field["hideInSingle"] === "on"));
			$("#hide_in_directory_view").prop("checked", (field["hideInDirectory"] === true || field["hideInDirectory"] === "on"));

			//since 3.5
			$("#only_visible_to_logged_in").prop("checked", (field["visibleToLoggedIn"] === true || field["visibleToLoggedIn"] === "on"));
			$("#only_visible_to_logged_in_cap").val( field["visibleToLoggedInCap"] );
			//since 3.5
			$("#use_field_as_search_filter").prop("checked", (field["isSearchFilter"] === true || field["isSearchFilter"] === "on"));



		});
   });
</script>
