"use strict";

//Add spacing for CPT panels
jQuery( document ).ready(function($) {
	var panels = $('li[id^="accordion-panel-sydney_panel_cpt_"]');
	panels.first().css( 'margin-top', '10px' );
	panels.last().css( 'margin-bottom', '10px' );
} );

jQuery( document ).ready( function() {

    /* === Checkbox Multiple Control === */

    jQuery( '.customize-control-checkbox-multiple input[type="checkbox"]' ).on(
        'change',
        function() {

            checkbox_values = jQuery( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
                function() {
                    return this.value;
                }
            ).get().join( ',' );

            jQuery( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );
        }
    );
    

} ); // jQuery( document ).ready

/* Typography */
jQuery(document).ready(function ($) {
  "use strict";

  $('.google-fonts-list').each(function (i, obj) {
    if (!$(this).hasClass('select2-hidden-accessible')) {
      $(this).select2();
    }
  });
  $('.google-fonts-list').on('change', function () {
    var elementRegularWeight = $(this).parent().parent().find('.google-fonts-regularweight-style');
    var selectedFont = $(this).val();
    var customizerControlName = $(this).attr('control-name'); // Clear Weight/Style dropdowns

    elementRegularWeight.empty(); // Make sure Italic & Bold dropdowns are enabled
    // Get the Google Fonts control object

    var bodyfontcontrol = _wpCustomizeSettings.controls[customizerControlName]; // Find the index of the selected font

    var indexes = $.map(bodyfontcontrol.sydneyfontslist, function (obj, index) {
      if (obj.family === selectedFont) {
        return index;
      }
    });
    var index = indexes[0]; // For the selected Google font show the available weight/style variants

    $.each(bodyfontcontrol.sydneyfontslist[index].variants, function (val, text) {
      elementRegularWeight.append($('<option></option>').val(text).html(text)); //Set default value

      if ($(elementRegularWeight).find('option[value="regular"]').length > 0) {
        $(elementRegularWeight).val('regular');
      } else if ($(elementRegularWeight).find('option[value="400"]').length > 0) {
        $(elementRegularWeight).val('400');
      } else if ($(elementRegularWeight).find('option[value="300"]').length > 0) {
        $(elementRegularWeight).val('300');
      }
    }); // Update the font category based on the selected font

    $(this).parent().parent().find('.google-fonts-category').val(bodyfontcontrol.sydneyfontslist[index].category);
    sydneyGetAllSelects($(this).parent().parent().parent().parent());
  });
  $('.google_fonts_select_control select').on('change', function () {
    sydneyGetAllSelects($(this).parent().parent().parent().parent());
  });

  function sydneyGetAllSelects($element) {
    var selectedFont = {
      font: $element.find('.google-fonts-list').val(),
      regularweight: $element.find('.google-fonts-regularweight-style').val(),
      category: $element.find('.google-fonts-category').val()
    }; // Important! Make sure to trigger change event so Customizer knows it has to save the field

    $element.find('.customize-control-google-font-selection').val(JSON.stringify(selectedFont)).trigger('change');
  }
});
jQuery(document).ready(function ($) {
  "use strict";

  let clickFlag = false;

  $('.sydney-devices-preview').find('button').on('click', function (event) {
	if (clickFlag) {
		clickFlag = false;
		return false;
	}

	clickFlag = true;

	let device = '';
	if ($(this).hasClass('preview-desktop')) {
	  $('.sydney-devices-preview').find('.preview-desktop').addClass('active');
	  $('.sydney-devices-preview').find('.preview-tablet').removeClass('active');
	  $('.sydney-devices-preview').find('.preview-mobile').removeClass('active');
	  $('.font-size-desktop').addClass('active');
	  $('.font-size-tablet').removeClass('active');
	  $('.font-size-mobile').removeClass('active');
	  $('.wp-full-overlay-footer .devices button[data-device="desktop"]').trigger('click');
	} else if ($(this).hasClass('preview-tablet')) {
	  $('.sydney-devices-preview').find('.preview-tablet').addClass('active');
	  $('.sydney-devices-preview').find('.preview-desktop').removeClass('active');
	  $('.sydney-devices-preview').find('.preview-mobile').removeClass('active');
	  $('.font-size-desktop').removeClass('active');
	  $('.font-size-tablet').addClass('active');
	  $('.font-size-mobile').removeClass('active');
	  $('.wp-full-overlay-footer .devices button[data-device="tablet"]').trigger('click');
	} else {
	  $('.sydney-devices-preview').find('.preview-mobile').addClass('active');
	  $('.sydney-devices-preview').find('.preview-desktop').removeClass('active');
	  $('.sydney-devices-preview').find('.preview-tablet').removeClass('active');
	  $('.font-size-desktop').removeClass('active');
	  $('.font-size-tablet').removeClass('active');
	  $('.font-size-mobile').addClass('active');
	  $('.wp-full-overlay-footer .devices button[data-device="mobile"]').trigger('click');
	}
  });
  $(' .wp-full-overlay-footer .devices button ').on('click', function () {
	if (clickFlag) {
		clickFlag = false;
		return false;
	}

	var device = $(this).attr('data-device');
	$('.sydney-devices-preview').find('.preview-' + device).trigger('click');
  });
});
/**
 * Repeater
 */

jQuery(document).ready(function ($) {
  "use strict"; // Update the values for all our input fields and initialise the sortable repeater

  $('.sydney-sortable_repeater_control').each(function () {
    // If there is an existing customizer value, populate our rows
    var defaultValuesArray = $(this).find('.customize-control-sortable-repeater').val().split(',');
    var numRepeaterItems = defaultValuesArray.length;

    if (numRepeaterItems > 0) {
      // Add the first item to our existing input field
      $(this).find('.repeater-input').val(defaultValuesArray[0]); // Create a new row for each new value

      if (numRepeaterItems > 1) {
        var i;

        for (i = 1; i < numRepeaterItems; ++i) {
          sydneyAppendRow($(this), defaultValuesArray[i]);
        }
      }
    }
  }); // Make our Repeater fields sortable

  $(this).find('.sydney-sortable_repeater.sortable').sortable({
    update: function update(event, ui) {
      sydneyGetAllInputs($(this).parent());
    }
  }); // Remove item starting from it's parent element

  $('.sydney-sortable_repeater.sortable').on('click', '.customize-control-sortable-repeater-delete', function (event) {
    event.preventDefault();
    var numItems = $(this).parent().parent().find('.repeater').length;

    if (numItems > 1) {
      $(this).parent().slideUp('fast', function () {
        var parentContainer = $(this).parent().parent();
        $(this).remove();
        sydneyGetAllInputs(parentContainer);
      });
    } else {
      $(this).parent().find('.repeater-input').val('');
      sydneyGetAllInputs($(this).parent().parent().parent());
    }
  }); // Add new item

  $('.customize-control-sortable-repeater-add').click(function (event) {
    event.preventDefault();
    sydneyAppendRow($(this).parent());
    sydneyGetAllInputs($(this).parent());
  }); // Refresh our hidden field if any fields change

  $('.sydney-sortable_repeater.sortable').change(function () {
    sydneyGetAllInputs($(this).parent());
  }); // Add https:// to the start of the URL if it doesn't have it

  $('.sydney-sortable_repeater.sortable').on('blur', '.repeater-input', function () {
    var url = $(this);
    var val = url.val();

    if (val && !val.match(/^.+:\/\/.*/)) {
      // Important! Make sure to trigger change event so Customizer knows it has to save the field
      url.val('https://' + val).trigger('change');
    }
  }); // Append a new row to our list of elements

  function sydneyAppendRow($element) {
    var defaultValue = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
    var newRow = '<div class="repeater" style="display:none"><input type="text" value="' + defaultValue + '" class="repeater-input" placeholder="https://" /><span class="dashicons dashicons-menu"></span><a class="customize-control-sortable-repeater-delete" href="#"><span class="dashicons dashicons-no-alt"></span></a></div>';
    $element.find('.sortable').append(newRow);
    $element.find('.sortable').find('.repeater:last').slideDown('slow', function () {
      $(this).find('input').focus();
    });
  } // Get the values from the repeater input fields and add to our hidden field


  function sydneyGetAllInputs($element) {
    var inputValues = $element.find('.repeater-input').map(function () {
      return $(this).val();
    }).toArray(); // Add all the values from our repeater fields to the hidden field (which is the one that actually gets saved)

    $element.find('.customize-control-sortable-repeater').val(inputValues); // Important! Make sure to trigger change event so Customizer knows it has to save the field

    $element.find('.customize-control-sortable-repeater').trigger('change');
  }
});
/**
 * Alpha color picker
 */
/* Color Control */
jQuery(document).ready(function ($) {

	var $colorControls = $('.sydney-color-control');

	if ($colorControls.length && Pickr) {

		var getCurrentSwatches = function () {
			var colors = [];
			$('#customize-control-custom_palette .sydney-color-input').each(function () {
				colors.push($(this).val().toLowerCase());
			});
			return colors;
		}

		//add the swatches to the dropdown
		var swatch = $('.global-colors-dropdown');
		swatch.each(function () {
			var colors = getCurrentSwatches();
			var $this = $(this);
			
			var element = $this.data('element');

			var $globalInput = $this.next('.sydney-connected-global').val();

			$.each(colors, function (index, value) {
				$this.append('<div class="global-color" data-global-setting="global_color_' + (index+1) + '" data-color="' + value + '"><span style="display:flex;align-items:center;"><span class="color-circle" style="background-color:' + value + '"></span>Global color ' + (index+1) + '</span><span class="color-value">' + value + '</span></div>');
			
				if ( $globalInput == 'global_color_' + (index+1) ) {
					$this.find('.global-color').eq(index).addClass('active');

					$this.prev('.dashicons').addClass('active');
				}
			});

			$this.on('click', '.global-color', function () {
				var $this 	= $(this);
				var $globalInput = $this.parent().next('.sydney-connected-global');

				//add active class to dashicon
				$this.parent().prev('.dashicons').addClass('active');

				//add active class
				$this.toggleClass('active').siblings().removeClass('active');

				if ( $this.hasClass('active') ) {
					$globalInput.val($this.data('global-setting'));
					sydneyChangeElementColors(element, $this.data('color'));
				} else {
					$globalInput.val('');
					$this.parent().prev('.dashicons').removeClass('active');
				}
				
				$globalInput.trigger('change');
			} );
		});

		//toggle the swatches
		$('.sydney-global-control .dashicons').on('click', function () {
			
			$(this).next('.global-colors-dropdown').toggleClass('show');

			//close other swatches
			$('.global-colors-dropdown').not($(this).next('.global-colors-dropdown')).removeClass('show');
		});

		//close the swatches when clicking outside
		$(document).on('click', function (e) {
			if ( !$(e.target).closest('.sydney-global-control').length ) {
				$('.global-colors-dropdown').removeClass('show');
			}
		});

		$colorControls.each(function () {

			var $colorControl = $(this);
			var $colorPicker = $colorControl.find('.sydney-color-picker');
			var inited;
			
			$colorPicker.on('click', function () {

				if (!inited) {

					var $colorInput = $colorControl.find('.sydney-color-input');
					var customizeControl = wp.customize($colorInput.data('customize-setting-link'));					

					var pickr = new Pickr({
						el: $colorPicker.get(0),
						container: 'body',
						theme: 'sydney',
						default: $colorInput.val() || '',
						//swatches: [],
						position: 'bottom-end',
						appClass: 'sydney-pcr-app',
						sliders: 'h',
						useAsButton: true,
						components: {
							hue: true,
							preview: true,
							opacity: true,
							interaction: {
								input: true,
								clear: true,
							},
						},
						i18n: {
							'btn:clear': 'Default',
						},
					});

					pickr.on('change', function (color) {

						var colorCode;

						if (color.a === 1) {
							pickr.setColorRepresentation('HEX');
							colorCode = color.toHEXA().toString(0);
						} else {
							pickr.setColorRepresentation('RGBA');
							colorCode = color.toRGBA().toString(0);
						}

						$colorPicker.css({ 'background-color': colorCode });
						$colorInput.val(colorCode);
						customizeControl.set(colorCode);

					});

					// Disconnect global if manually changed
					pickr.on('change', function (color, source) {
						if ( 'slider' === source ) {
							$colorPicker.parent().prev('.sydney-global-control').find('.dashicons').removeClass('active');
							$colorPicker.parent().prev('.sydney-global-control').find('.sydney-connected-global').val('');
							$colorPicker.parent().prev('.sydney-global-control').find('.global-color').removeClass('active');
						}
					});

					pickr.on('clear', function () {

						var defaultColor = $colorPicker.data('default-color');

						if (defaultColor) {
							pickr.setColor(defaultColor);
						} else {
							$colorPicker.css({ 'background-color': 'white' });
							$colorInput.val('');
							customizeControl.set('');
						}

					});

					$colorPicker.data('pickr', pickr);

					setTimeout(function () {
						pickr.show();
					});

					inited = true;

				}

			});

		});

	}
});


/**
 * Tab control
 */

jQuery(document).ready(function ($) {
  "use strict";

  $('.customize-control-sydney-tab-control').each(function () {
    $(this).parent().find('li').not('.section-meta').not('.customize-control-sydney-tab-control').addClass('sydney-hide-control');
    var generals = $(this).find('.control-tab-general').data('connected');
    $.each(generals, function (i, v) {
      $(this).removeClass('sydney-hide-control'); //show
    });
    $(this).find('.control-tab').on('click', function () {
      var visibles = $(this).data('connected');
      $(this).addClass('active');
      $(this).siblings().removeClass('active');
      $(this).parent().parent().parent().find('li').not('.section-meta').not('.customize-control-sydney-tab-control').addClass('sydney-hide-control');
      $.each(visibles, function (i, v) {
        $(this).removeClass('sydney-hide-control'); //show
      });
    });
  });
});
/**
 * TinyMCE control
 */

jQuery(document).ready(function ($) {
  "use strict";

  $('.customize-control-tinymce-editor').each(function () {
    // Get the toolbar strings that were passed from the PHP Class
    var tinyMCEToolbar1String = _wpCustomizeSettings.controls[$(this).attr('id')].sydneytb1;

    var tinyMCEToolbar2String = _wpCustomizeSettings.controls[$(this).attr('id')].sydneytb2;

    var tinyMCEMediaButtons = _wpCustomizeSettings.controls[$(this).attr('id')].sydneytmb;

    wp.editor.initialize($(this).attr('id'), {
      tinymce: {
        wpautop: true,
        toolbar1: tinyMCEToolbar1String,
        toolbar2: tinyMCEToolbar2String
      },
      quicktags: true,
      mediaButtons: true
    });
  });
  $(document).on('tinymce-editor-init', function (event, editor) {
    editor.on('change', function (e) {
      tinyMCE.triggerSave();
      $('#' + editor.id).trigger('change');
    });
  });
});
/**
 * Footer widget areas links
 */

jQuery(document).ready(function ($) {
  var footerCols = $('#customize-control-footer_widget_areas').find('input:checked');
  toggleLinks(footerCols);
  $('#customize-control-footer_widget_areas').find('input').change(function () {
    toggleLinks($(this));
  });

  function toggleLinks(el) {
    if ('3' === $(el).val() || 'col3-bigleft' === $(el).val() || 'col3-bigright' === $(el).val()) {
      $('.footer-widget-area-link-1, .footer-widget-area-link-2, .footer-widget-area-link-3').show();
      $('.footer-widget-area-link-4').hide();
    } else if ('4' === $(el).val() || 'col4-bigleft' === $(el).val() || 'col4-bigright' === $(el).val()) {
      $('.footer-widget-area-link-1, .footer-widget-area-link-2, .footer-widget-area-link-3, .footer-widget-area-link-4').show();
    } else if ('2' === $(el).val() || 'col2-bigleft' === $(el).val() || 'col2-bigright' === $(el).val()) {
      $('.footer-widget-area-link-1, .footer-widget-area-link-2').show();
      $('.footer-widget-area-link-4, .footer-widget-area-link-3').hide();
    } else if ('1' === $(el).val()) {
      $('.footer-widget-area-link-1').show();
      $('.footer-widget-area-link-4, .footer-widget-area-link-2, .footer-widget-area-link-3').hide();
    } else {
      $('.footer-widget-area-link-1, .footer-widget-area-link-2, .footer-widget-area-link-3, .footer-widget-area-link-4').hide();
    }
  }
});
/**
 * Palettes
 */

wp.customize('color_palettes', function (control) {
  var palettes = jQuery('#customize-control-color_palettes').find('.radio-buttons').data('palettes');
  control.bind(function () {
    var palette = control.get(); //Color 1 Button color, Link color

    var elements1 = ['custom_color1', 'scrolltop_bg_color', 'button_background_color', 'button_border_color', 'color_link_default'];

    for (var _i = 0, _elements = elements1; _i < _elements.length; _i++) {
      var element = _elements[_i];
      wp.customize(element).set(palettes[palette][0]);
      jQuery('#customize-control-' + element).find('.wp-color-result').css('background-color', palettes[palette][0]);
    } //Color 2 Hover color for - Button, Headings, Titles, Text links, Nav links


    var elements2 = ['custom_color2', 'footer_widgets_links_hover_color', 'scrolltop_bg_color_hover', 'button_background_color_hover', 'button_border_color_hover', 'color_link_hover'];

    for (var _i2 = 0, _elements2 = elements2; _i2 < _elements2.length; _i2++) {
      var _element = _elements2[_i2];
      wp.customize(_element).set(palettes[palette][1]);
      jQuery('#customize-control-' + _element).find('.wp-color-result').css('background-color', palettes[palette][1]);
    } //Color 3 Heading (1-6), Small text, Nav links, Site title, 


    var elements3 = ['single_post_title_color', 'custom_color3', 'main_header_submenu_color', 'offcanvas_menu_color', 'mobile_header_color', 'footer_widgets_title_color', 'single_product_title_color', 'color_forms_text', 'shop_product_product_title', 'loop_post_meta_color', 'loop_post_title_color', 'main_header_color', 'site_title_color', 'site_description_color', 'color_heading_1', 'color_heading_2', 'color_heading_3', 'color_heading_4', 'color_heading_5', 'color_heading_6'];

    for (var _i3 = 0, _elements3 = elements3; _i3 < _elements3.length; _i3++) {
      var _element2 = _elements3[_i3];
      wp.customize(_element2).set(palettes[palette][2]);
      jQuery('#customize-control-' + _element2).find('.wp-color-result').css('background-color', palettes[palette][2]);
    } //Color 4 Paragraph, Paragraph small, Breadcrums, Icons


    var elements4 = ['custom_color4', 'footer_widgets_links_color', 'footer_widgets_text_color', 'color_body_text', 'footer_credits_text_color', 'color_forms_placeholder'];

    for (var _i4 = 0, _elements4 = elements4; _i4 < _elements4.length; _i4++) {
      var _element3 = _elements4[_i4];
      wp.customize(_element3).set(palettes[palette][3]);
      jQuery('#customize-control-' + _element3).find('.wp-color-result').css('background-color', palettes[palette][3]);
    } //Color 5 Input, tag borders


    var elements5 = ['custom_color5', 'color_forms_borders'];

    for (var _i5 = 0, _elements5 = elements5; _i5 < _elements5.length; _i5++) {
      var _element4 = _elements5[_i5];
      wp.customize(_element4).set(palettes[palette][4]);
      jQuery('#customize-control-' + _element4).find('.wp-color-result').css('background-color', palettes[palette][4]);
    } //Color 6 Footer background, Subtle backgrounds


    var elements6 = ['custom_color6', 'footer_widgets_background', 'footer_credits_background', 'content_cards_background'];

    for (var _i6 = 0, _elements6 = elements6; _i6 < _elements6.length; _i6++) {
      var _element5 = _elements6[_i6];
      wp.customize(_element5).set(palettes[palette][5]);
      jQuery('#customize-control-' + _element5).find('.wp-color-result').css('background-color', palettes[palette][5]);
    } //Color 7 Default background, Text on dark BG


    var elements7 = ['custom_color7', 'background_color', 'button_color', 'button_color_hover', 'scrolltop_color', 'scrolltop_color_hover', 'color_forms_background'];

    for (var _i7 = 0, _elements7 = elements7; _i7 < _elements7.length; _i7++) {
      var _element6 = _elements7[_i7];
      wp.customize(_element6).set(palettes[palette][6]);
      jQuery('#customize-control-' + _element6).find('.wp-color-result').css('background-color', palettes[palette][6]);
    } //Color 8 header background


    var elements8 = ['custom_color8', 'main_header_submenu_background', 'main_header_background', 'main_header_bottom_background', 'mobile_header_background', 'offcanvas_menu_background'];

    for (var _i8 = 0, _elements8 = elements8; _i8 < _elements8.length; _i8++) {
      var _element7 = _elements8[_i8];
      wp.customize(_element7).set(palettes[palette][7]);
      jQuery('#customize-control-' + _element7).find('.wp-color-result').css('background-color', palettes[palette][7]);
    }
  });
});
/**
 * Custom palette
 */

wp.customize.bind('ready', function () {
  wp.customize('custom_color1', function (control) {
    control.bind(function (value) {
      var elements1 = ['scrolltop_bg_color', 'button_background_color', 'button_border_color', 'color_link_default'];

      for (var _i9 = 0, _elements9 = elements1; _i9 < _elements9.length; _i9++) {
        var element = _elements9[_i9];
        wp.customize(element).set(value);
        jQuery('#customize-control-' + element).find('.wp-color-result').css('background-color', value);
      }
    });
  });
  wp.customize('custom_color2', function (control) {
    control.bind(function (value) {
      var elements2 = ['footer_widgets_links_hover_color', 'scrolltop_bg_color_hover', 'button_background_color_hover', 'button_border_color_hover', 'color_link_hover'];

      for (var _i10 = 0, _elements10 = elements2; _i10 < _elements10.length; _i10++) {
        var element = _elements10[_i10];
        wp.customize(element).set(value);
        jQuery('#customize-control-' + element).find('.wp-color-result').css('background-color', value);
      }
    });
  });
  wp.customize('custom_color3', function (control) {
    control.bind(function (value) {
      var elements3 = ['main_header_submenu_color', 'offcanvas_menu_color', 'mobile_header_color', 'footer_widgets_title_color', 'single_product_title_color', 'color_forms_text', 'shop_product_product_title', 'loop_post_meta_color', 'loop_post_title_color', 'main_header_color', 'site_title_color', 'site_description_color', 'color_heading_1', 'color_heading_2', 'color_heading_3', 'color_heading_4', 'color_heading_5', 'color_heading_6'];

      for (var _i11 = 0, _elements11 = elements3; _i11 < _elements11.length; _i11++) {
        var element = _elements11[_i11];
        wp.customize(element).set(value);
        jQuery('#customize-control-' + element).find('.wp-color-result').css('background-color', value);
      }
    });
  });
  wp.customize('custom_color4', function (control) {
    control.bind(function (value) {
      var elements4 = ['footer_widgets_links_color', 'footer_widgets_text_color', 'color_body_text', 'footer_credits_text_color', 'color_forms_placeholder'];

      for (var _i12 = 0, _elements12 = elements4; _i12 < _elements12.length; _i12++) {
        var element = _elements12[_i12];
        wp.customize(element).set(value);
        jQuery('#customize-control-' + element).find('.wp-color-result').css('background-color', value);
      }
    });
  });
  wp.customize('custom_color5', function (control) {
    control.bind(function (value) {
      var elements5 = ['color_forms_borders'];

      for (var _i13 = 0, _elements13 = elements5; _i13 < _elements13.length; _i13++) {
        var element = _elements13[_i13];
        wp.customize(element).set(value);
        jQuery('#customize-control-' + element).find('.wp-color-result').css('background-color', value);
      }
    });
  });
  wp.customize('custom_color6', function (control) {
    control.bind(function (value) {
      var elements6 = ['footer_widgets_background', 'footer_credits_background', 'content_cards_background'];

      for (var _i14 = 0, _elements14 = elements6; _i14 < _elements14.length; _i14++) {
        var element = _elements14[_i14];
        wp.customize(element).set(value);
        jQuery('#customize-control-' + element).find('.wp-color-result').css('background-color', value);
      }
    });
  });
  wp.customize('custom_color7', function (control) {
    control.bind(function (value) {
      var elements7 = ['background_color', 'button_color', 'button_color_hover', 'scrolltop_color', 'scrolltop_color_hover', 'color_forms_background'];

      for (var _i15 = 0, _elements15 = elements7; _i15 < _elements15.length; _i15++) {
        var element = _elements15[_i15];
        wp.customize(element).set(value);
        jQuery('#customize-control-' + element).find('.wp-color-result').css('background-color', value);
      }
    });
  });
  wp.customize('custom_color8', function (control) {
    control.bind(function (value) {
      var elements8 = ['main_header_submenu_background', 'main_header_background', 'main_header_bottom_background', 'mobile_header_background', 'offcanvas_menu_background'];

      for (var _i16 = 0, _elements16 = elements8; _i16 < _elements16.length; _i16++) {
        var element = _elements16[_i16];
        wp.customize(element).set(value);
        jQuery('#customize-control-' + element).find('.wp-color-result').css('background-color', value);
      }
    });
  });
});

/**
 * Move color picker text field in popup
 */

jQuery(document).ready(function ($) {
  $('.wp-picker-input-wrap').each(function () {
    $(this).prependTo($(this).next('.wp-picker-holder'));
  });
});
/**
 * Transform palettes radio into dropdown
 */

jQuery(document).ready(function ($) {
  var saved = $('.saved-palette');
  $('.saved-palette').on('click', function () {
    $('.palette-radio-buttons').toggleClass('open');
  });
  $('.palette-radio-buttons').find('.palette').on('click', function () {
    saved.empty();
    $('.palette-radio-buttons').removeClass('open');
    var clone = $(this).parent().clone();
    clone.unwrap().appendTo(saved).find('input').remove();
  });
});
/**
 * Accordion control
 */

jQuery(document).ready(function ($) {
  var Sydney_Accordion = {
    init: function init() {
      this.firstTime = true;

      if (!this.initialized) {
        this.events();
      }

      this.initialized = true;
    },
    events: function events() {
      var self = this; // Toggle accordion

      $(document).on('click', '.sydney-accordion-title', function () {
        var $this = $(this);

        if ($(this).hasClass('expanded')) {
          self.showOrHide($(this), 'hide');
          $(this).removeClass('expanded').addClass('collapse');
          setTimeout(function () {
            $this.removeClass('collapse');
          }, 300);
        }

        if (!$(this).hasClass('collapse')) {
          // Open one accordion item per time 
          $('.sydney-accordion-item').addClass('sydney-accordion-hide');
          $('.sydney-accordion-title').removeClass('expanded'); // Show accordion content

          self.showOrHide($(this), 'show');
          $this.addClass('expanded');
        }
      }); // Mount the accordion when enter in the section (with accordions inside)
      // Also used to collapse all accordions when navigating between others tabs

      $(document).on('click', '.control-section', function (e) {
        var $section = $('.control-section.open');

        if (self.firstTime && $section.find('.sydney-accordion-title').length) {
          $section.find('.sydney-accordion-title').each(function () {
            self.showOrHide($(this), 'hide');
            $(this).removeClass('expanded');
            self.firstTime = false;
          });
        }
      }); // Reset the first time

      $(document).on('click', '.customize-section-back', function () {
        self.firstTime = true;
      });
      return this;
    },
    showOrHide: function showOrHide($this, status) {
      var current = '';
      current = $this.closest('.customize-control').next();
      var elements = [];

      if (current.attr('id') == 'customize-control-' + $this.data('until')) {
        elements.push(current[0].id);
      } else {
        while (current.attr('id') != 'customize-control-' + $this.data('until')) {
          elements.push(current[0].id);
          current = current.next();
        }
      }

      if (elements.length >= 1) {
        elements.push(current[0].id);
      }

      for (var i = 0; i < elements.length; i++) {
        // Identify accordion items
        $('#' + elements[i]).addClass('sydney-accordion-item active'); // Hide or show the accordion content

        if (status == 'hide') {
          $('#' + elements[i]).addClass('sydney-accordion-hide');
        } else {
          $('#' + elements[i]).removeClass('sydney-accordion-hide');
        } // Identify first accordion item


        if (i == 0) {
          $('#' + elements[i]).addClass('sydney-accordion-first-item');
        } // Identify last accordion item


        if (i == elements.length - 1 && elements.length > 1 || elements.length == 1) {
          $('#' + elements[i]).addClass('sydney-accordion-last-item');
        }
      }

      return this;
    }
  };
  $(document).ready(function () {
    Sydney_Accordion.init();
  });
});


/**
 * Controls a11y
 */

 jQuery(document).ready(function ($) {
  "use strict";

  $('.customize-control-sydney-toggle-control').each(function () {
    
    $(this).find('label').on('keydown', function (event) {
      var enterPressed = (event.key === 'Enter' || event.keyCode === 13);

      if (!enterPressed) { 
        return; 
      }

      $( this ).click();
    });
  });

  $('.customize-control-sydney-radio-buttons').each(function () {
    
    $(this).find('label').on('keydown', function (event) {
      var enterPressed = (event.key === 'Enter' || event.keyCode === 13);

      if (!enterPressed) { 
        return; 
      }

      $( this ).click();
    });
  });

});

/**
 * Display Conditions Control
 */

 jQuery(document).ready(function ($) {
	$(document).on('sydney-display-conditions-select2-initalize', function (event, item) {
	  var $item = $(item);
	  var $control = $item.closest('.sydney-display-conditions-control');
	  var $typeSelectWrap = $item.find('.sydney-display-conditions-select2-type');
	  var $typeSelect = $typeSelectWrap.find('select');
	  var $conditionSelectWrap = $item.find('.sydney-display-conditions-select2-condition');
	  var $conditionSelect = $conditionSelectWrap.find('select');
	  var $idSelectWrap = $item.find('.sydney-display-conditions-select2-id');
	  var $idSelect = $idSelectWrap.find('select');
	  $typeSelect.select2({
		width: '100%',
		minimumResultsForSearch: -1
	  });
	  $typeSelect.on('select2:select', function (event) {
		$typeSelectWrap.attr('data-type', event.params.data.id);
	  });
	  $conditionSelect.select2({
		width: '100%'
	  });
	  $conditionSelect.on('select2:select', function (event) {
		var $element = $(event.params.data.element);

		if ($element.data('ajax')) {
		  $idSelectWrap.removeClass('hidden');
		} else {
		  $idSelectWrap.addClass('hidden');
		}

		$idSelect.val(null).trigger('change');
	  });
	  var isAjaxSelected = $conditionSelect.find(':selected').data('ajax');

	  if (isAjaxSelected) {
		$idSelectWrap.removeClass('hidden');
	  }

	  $idSelect.select2({
		width: '100%',
		placeholder: '',
		allowClear: true,
		minimumInputLength: 1,
		ajax: {
		  url: ajaxurl,
		  dataType: 'json',
		  delay: 250,
		  cache: true,
		  data: function data(params) {
			return {
			  action: 'sydney_display_conditions_select_ajax',
			  term: params.term,
			  nonce: syd_data.ajax_nonce,
			  source: $conditionSelect.val()
			};
		  },
		  processResults: function processResults(response, params) {
			if (response.success) {
			  return {
				results: response.data
			  };
			}

			return {};
		  }
		}
	  });
	});
	$(document).on('click', '.sydney-display-conditions-modal-toggle', function (event) {
	  event.preventDefault();
	  var $button = $(this);
	  var template = wp.template('sydney-display-conditions-template');
	  var $control = $button.closest('.sydney-display-conditions-control');
	  var $modal = $control.find('.sydney-display-conditions-modal');

	  if (!$modal.data('initialized')) {
		$control.append(template($control.data('condition-settings')));
		var $items = $control.find('.sydney-display-conditions-modal-content-list-item').not('.hidden');

		if ($items.length) {
		  $items.each(function () {
			$(document).trigger('sydney-display-conditions-select2-initalize', this);
		  });
		}

		$modal = $control.find('.sydney-display-conditions-modal');
		$modal.data('initialized', true);
		$modal.addClass('open');
	  } else {
		$modal.toggleClass('open');
	  }
	});
	$(document).on('click', '.sydney-display-conditions-modal', function (event) {
	  event.preventDefault();
	  var $modal = $(this);

	  if ($(event.target).is($modal)) {
		$modal.removeClass('open');
	  }
	});
	$(document).on('click', '.sydney-display-conditions-modal-add', function (event) {
	  event.preventDefault();
	  var $button = $(this);
	  var $control = $button.closest('.sydney-display-conditions-control');
	  var $modal = $control.find('.sydney-display-conditions-modal');
	  var $list = $modal.find('.sydney-display-conditions-modal-content-list');
	  var $item = $modal.find('.sydney-display-conditions-modal-content-list-item').first().clone();
	  var conditionGroup = $button.data('condition-group');
	  $item.removeClass('hidden');
	  $item.find('.sydney-display-conditions-select2-condition').not('[data-condition-group="' + conditionGroup + '"]').remove();
	  $list.append($item);
	  $(document).trigger('sydney-display-conditions-select2-initalize', $item);
	});
	$(document).on('click', '.sydney-display-conditions-modal-remove', function (event) {
	  event.preventDefault();
	  var $item = $(this).closest('.sydney-display-conditions-modal-content-list-item');
	  $item.remove();
	});
	$(document).on('click', '.sydney-display-conditions-modal-save', function (event) {
	  event.preventDefault();
	  var data = [];
	  var $button = $(this);
	  var $control = $button.closest('.sydney-display-conditions-control');
	  var $modal = $control.find('.sydney-display-conditions-modal');
	  var $textarea = $control.find('.sydney-display-conditions-textarea');
	  var $items = $modal.find('.sydney-display-conditions-modal-content-list-item').not('.hidden');
	  $items.each(function () {
		var $item = $(this);
		data.push({
		  type: $item.find('select[name="type"]').val(),
		  condition: $item.find('select[name="condition"]').val(),
		  id: $item.find('select[name="id"]').val()
		});
	  });
	  $textarea.val(JSON.stringify(data)).trigger('change');
	});
  });


//activate/deactivate header settings
jQuery(document).ready(function ($) {

	var sticky_controls = [ 'header_divider_1', 'main_header_settings_title', 'header_container', 'enable_sticky_header', 'sticky_header_type', 'transparent_header' ];
	var menu_pos 		= [ 'main_header_menu_position' ];

	wp.customize('header_layout_desktop', function (value) {

		value.bind(function (newval) {
			$.each(sticky_controls, function (index, setting) {
				var control = wp.customize.control(setting);
				if (control) {
					if (newval === 'header_layout_6' || newval === 'header_layout_7') {
						control.deactivate();
					} else {
						control.activate();
					}
				}
			});
		});

		value.bind(function (newval) {
			$.each(menu_pos, function (index, setting) {
				if (newval === 'header_layout_2' ) {
					wp.customize.control(setting).activate();
				} else {
					wp.customize.control(setting).deactivate();
				}
			});
		});

		value.bind(function (newval) {
			if (newval === 'header_layout_1' || newval === 'header_layout_2' ) {
				wp.customize.control('header_components_l1').activate();
			} else {
				wp.customize.control('header_components_l1').deactivate();
			}
		});

		value.bind(function (newval) {
			if (newval === 'header_layout_3' ) {
				wp.customize.control('header_components_l3left').activate();
				wp.customize.control('header_components_l3right').activate();
			} else {
				wp.customize.control('header_components_l3left').deactivate();
				wp.customize.control('header_components_l3right').deactivate();
			}
		} );

		value.bind(function (newval) {
			if (newval === 'header_layout_4' ) {
				wp.customize.control('header_components_l4top').activate();
				wp.customize.control('header_components_l4bottom').activate();
			} else {
				wp.customize.control('header_components_l4top').deactivate();
				wp.customize.control('header_components_l4bottom').deactivate();
			}
		} );

		value.bind(function (newval) {
			if (newval === 'header_layout_5' ) {
				wp.customize.control('header_components_l5topleft').activate();
				wp.customize.control('header_components_l5topright').activate();
				wp.customize.control('header_components_l5bottom').activate();
			} else {
				wp.customize.control('header_components_l5topleft').deactivate();
				wp.customize.control('header_components_l5topright').deactivate();
				wp.customize.control('header_components_l5bottom').deactivate();
			}
		} );
	});
} );

wp.customize('enable_sticky_header', function (value) {
	value.bind(function (newval) {
		if (newval === true) {
			wp.customize.control('sticky_header_type').activate();
		} else {
			wp.customize.control('sticky_header_type').deactivate();
		}
	});
} );

//Activate menu typography options without refresh
jQuery(document).ready(function ($) {
	wp.customize('enable_top_menu_typography', function (value) {
		var controls = ['sydney_menu_font', 'menu_items_text_transform', 'sydney_menu_font_size', 'sydney_header_menu_adobe_font'];
		value.bind(function (newval) {
			if (newval === true) {
				$.each(controls, function (index, setting) {
					wp.customize.control(setting).activate();
				});
			} else {
				$.each(controls, function (index, setting) {
					wp.customize.control(setting).deactivate();
				});
			}
		});
	} );
});

//Activate blog columns without refresh
jQuery(document).ready(function ($) {
	wp.customize('blog_layout', function (value) {
		value.bind(function (newval) {
			if (newval === 'layout3' || newval === 'layout5' || newval === 'layout7' ) {
				wp.customize.control('archives_grid_columns').activate();
			} else {
				wp.customize.control('archives_grid_columns').deactivate();
			}
		} );

		value.bind(function (newval) {
			if (newval === 'layout4' ) {
				wp.customize.control('archive_list_image_placement').activate();
			} else {
				wp.customize.control('archive_list_image_placement').deactivate();
			}
		} );

		value.bind(function (newval) {
			if (newval === 'layout4' || newval === 'layout6' ) {
				wp.customize.control('archives_list_vertical_alignment').activate();
				wp.customize.control('archive_featured_image_size').activate();
			} else {
				wp.customize.control('archives_list_vertical_alignment').deactivate();
				wp.customize.control('archive_featured_image_size').deactivate();
			}
		} );		
	} );
} );

//Upsell in the general panel
jQuery(document).ready(function ($) {

  var upsell = $('<div class="sydney-upsell-feature-wrapper" style="margin:5px 15px 15px;">' +
              '<h3 style="max-width:100%;"><em>Take your site to the next level with Sydney Pro!</em></h3><p>You’ll get access to:</p>' +
              '<ul class="sydney-upsell-features">' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Templates Builder</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Breadcrumbs</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Unlimited Sidebars</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Modal Popup</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Live Chat module</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Offcanvas Content</li>' +
                 '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Mailchimp support</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span><a href="https://athemes.com/sydney-upgrade/#features?utm_source=theme_customizer_deep&amp;utm_medium=sydney_customizer&amp;utm_campaign=Sydney" target="_blank">&hellip;and many more premium features</a></li>' +
              '</ul><p><a href="https://athemes.com/sydney-upgrade/?utm_source=theme_customizer_deep&amp;utm_medium=sydney_customizer&amp;utm_campaign=Sydney" role="button" class="button-secondary deep-upsell-button button" target="_blank">Upgrade to Sydney Pro</a></p></div>')

  upsell.appendTo('#sub-accordion-panel-sydney_panel_general');


  var upsellWoo = $('<div class="sydney-upsell-feature-wrapper" style="margin:5px 15px 15px;">' +
              '<h3><em>Take your store to the next level with Sydney Pro!</em></h3>' +
              '<p>You’ll get access to:</p>' +
              '<ul class="sydney-upsell-features">' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Wishlist</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Product Swatch</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Product Gallery Layouts</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Multistep checkout</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Single sticky add to cart</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Shop header styles</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Product tab styles</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>More Shop pagination types</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Advanced reviews</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Extra single product elements</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>Extra shop sidebar layouts</li>' +
                  '<li class="sydney-hide-control"><span class="dashicons dashicons-yes"></span>AJAX product search</li>' +
                  '<a target="_blank" href="https://athemes.com/sydney-upgrade/#features?utm_source=theme_customizer_deep&amp;utm_medium=sydney_customizer&amp;utm_campaign=Sydney">&hellip;and many more premium features</a>' +
              '</ul><p><a href="https://athemes.com/sydney-upgrade/?utm_source=theme_customizer_deep&amp;utm_medium=sydney_customizer&amp;utm_campaign=Sydney" role="button" class="button-secondary deep-upsell-button button" target="_blank">Upgrade Now</a></p></div>')

  upsellWoo.appendTo('#sub-accordion-panel-woocommerce');

} );

/**
 * Custom palette
 */
var sydneyChangeElementColors = function (element, color, palette) {

	var $setting = jQuery('[data-control-id="' + element + '"]');

	if ($setting.length) {

		if (palette) {
			var index = palette.indexOf(color);
			if (palette[index]) {
				color = palette[index];
			}
		}

		var $picker = $setting.find('.sydney-color-picker');

		if ($picker.data('pickr')) {
			$picker.data('pickr').setColor(color);
		} else {
			$picker.css('background-color', color);
			wp.customize(element).set(color);
		}

	} else {

		var $control = jQuery('#customize-control-' + element);

		if ($control.length && $control.hasClass('global-color-connected')) {

			var $picker = $control.find('.sydney-color-picker');

			if ($picker.data('pickr')) {
				$picker.data('pickr').setColor(color);
				wp.customize(element).set(color);
			} else {
				$picker.css('background-color', color);
				wp.customize(element).set(color);
			}

		}

	}

};

/**
 * Global colors
 */
wp.customize.bind('ready', function () {
	for (let i = 1; i <= 9; i++) {
		wp.customize('global_color_' + i, function (control) {
			control.bind(function (value) {
				let elements = [];
	
				jQuery('.sydney-connected-global').each(function () {
					if (jQuery(this).val() === 'global_color_' + i) {
						elements.push(jQuery(this).data('customize-setting-link').replace('global_', ''));
					}
				});
	
				for (const element of elements) {
					if (typeof wp.customize(element) !== 'undefined') {
						sydneyChangeElementColors(element, value);
					}
				}

				// Update global color dropdown
				jQuery('.global-colors-dropdown').each(function () {
					var $dropdown = jQuery(this);
					$dropdown.find('.global-color').each(function () {
						
						var $item = jQuery(this);

						if ($item.data('global-setting') === 'global_color_' + i) {

							$item.data('color', value);
							$item.find('.color-circle').css('background-color', value);
							$item.find('.color-value').text(value);
						}
					} );
				} );
			});
	  	});
	}
});

/**
 * Child controls for sortables
 */
jQuery(document).ready(function($) {
	
	var config = syd_data.sortable_config;

    // Show arrow icon, Hide all controls
    $.each(config, function(key, subConfig) {

		if ( !$('#customize-control-' + key).length ) {
			return;
		}

        $.each(subConfig, function(subKey, value) {
            $.each(value.controls, function(index, control) {
				$('#customize-control-' + key + ' .kirki-sortable-item[data-value="' + subKey + '"]').find('.toggle-options').show();

                var $control = $('#customize-control-' + control);
                if ($control.length) {
                    $control.addClass('sortable-child-hidden');
                    $control.css('position', 'absolute');
                }
            });
        });
    });

    // Toggle controls on click
    $('.kirki-sortable-item .toggle-options').click(function() {
        var $sortableItem = $(this).parent();
        var dataValue = $sortableItem.data('value');

		$sortableItem.toggleClass('sortable-opened');

        if ($sortableItem.hasClass('invisible')) {
            return;
        }

		//Get the distance from the top of the container
		var container 			= $sortableItem.closest('.customize-pane-child');
		var sortableItemHeight 	= $sortableItem.outerHeight();
		var sortableItemOffset 	= $sortableItem.get(0).offsetTop;
		var containerOffset 	= container.get(0).offsetTop;
		var distanceTop 		= sortableItemOffset - containerOffset + sortableItemHeight + 1;

        $(this).toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');

        $.each(config, function(key, subConfig) {

			if ( !$sortableItem.parents( '.customize-control' ).attr('id').includes(key) ) {
				return;
			}

            $.each(subConfig, function(subKey, value) {

				if (dataValue !== subKey) {
					return;
				}

				var $firstControl = $('#customize-control-' + value.controls[0]);
				var isToggled = $firstControl.hasClass('sortable-child-toggled');
	
				$('.customize-control.sortable-child-toggled').each(function() {
					$(this).removeClass('sortable-child-toggled');
					$(this).addClass('sortable-child-hidden');
				});

				var prevTotalHeight = 0; // Total height of previous controls

                $.each(value.controls, function(index, control) {
                    var $control = $('#customize-control-' + control);

                    var $prevControl 		= $control.prev();
					
                    if (!isToggled) {
                        $control.addClass('sortable-child-toggled');
                        $control.removeClass('sortable-child-hidden');

                        if (index === 0) {
                            $control.addClass('first-control');
							$control.css( {
								'top': distanceTop + 'px',
								'right': '25px',
							} );
                        } else {
							prevTotalHeight += $prevControl.outerHeight();

							$control.css( {
								'top': distanceTop + prevTotalHeight + 'px',
								'right': '25px',
							} );

							//exceptions
							if ( control === 'header_search_field_style' ) {
								$control.css( {
									'top': distanceTop + prevTotalHeight - $prevControl.outerHeight() + 'px',
								} );
							}
                        }

                        if (index === value.controls.length - 1) {
                            $control.addClass('last-control');
                        }
                    }
                });
            });
        });
    });

	$(document).on('click mousedown', function(e) {
		if ( $(e.target).hasClass('sortable-opened') ) {

			if ( $(e.target).hasClass('invisible') ) {
				return;
			}

			$(e.target).removeClass('sortable-opened');

			$(e.target).find('.toggle-options').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');

			$('.customize-control.sortable-child-toggled').each(function() {
				$(this).removeClass('sortable-child-toggled');
				$(this).addClass('sortable-child-hidden');
			});
		}
	});

});