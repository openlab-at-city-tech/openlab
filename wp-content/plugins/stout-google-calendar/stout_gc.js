jQuery(document).ready(function($) {
	
	// function to get id of current calendar
	$.fn.getCalId = function() { 
		return $(this).parents('.sgc-form-wrapper').first().attr('id').substring(9);
	}
	
	
	//This is for the colorpicker
	$('.colorpicker0,.colorpicker1,.colorpicker2,.colorpicker3,.colorpicker4,.colorpicker5,.colorpicker6').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
			$(el).css('backgroundColor', '#' + hex);
			var calId = $(el).getCalId();
			var colorId = $(el).attr('name').substring(5);
			if (colorId == 0) { $('#button-image-bkgrd_'+ calId).css('backgroundColor', '#' + hex); }
			var colorRe = new RegExp("sgc"+colorId+"=.{1,6}",'i');
			var iframe = $('#sgc_iframe_'+ calId);
			iframe.attr('src',iframe.attr('src').replace(colorRe, "sgc"+colorId+"="+hex));
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
	
	//Change background image behind buttons when background value changes
	$('.bkgrdImage').change(function(){
		var calId = $(this).getCalId();
		var iframe = $('#sgc_iframe_'+ calId);
		iframe.attr('src',iframe.attr('src').replace(/sgcImage=\d/, "sgcImage="+$(this).val()));
	});
	
	// Function to set width/height in embed code and iframe
	$.fn.setWidthOrHeight = function() {
		var calId = $(this).getCalId();
		var name = $(this).attr('name');
		var textarea = $('#sgccode'+calId);
		//get width/height name and value
		var re = new RegExp('(' + name + '="\\d+\W?")');
		var result = re.exec($(textarea).val());
		
		//update width or height of embed code text area
		if (result != null) { 
			textarea.val(textarea.val().replace(result[1], name + '="'+$(this).val()+'"'));
			$('#sgccode'+calId).refreshPreview();
		}
	}
	
	//Function to get width/height value from embed and copy to input
	$.fn.getWidthOrHeight = function() {
		var calId = $(this).getCalId();
		var name = $(this).attr('name');
		var textarea = $('#sgccode'+calId);
		// just get value of width/height in text area
		var re = new RegExp(name + '="(\\d+\W?)"');
		var result = re.exec($(textarea).val());
		
		//update width or height of text input
		if (result != null) { 
			$(this).val(result[1]);
		}
	}
		
	//Change width or height of embed code and iframe when text input changed
	$('.sgcWidthOrHeight').change(function(){
		$(this).setWidthOrHeight();
	});
	
	//Populate all width/height inputs from embed on page load
	$('.sgcWidthOrHeight').each(function(){
		$(this).getWidthOrHeight();
	});
	
	// Function to set bubble width and use % or pixels in iframe
	$.fn.setBubbleWidth = function() {
		var calId = $(this).getCalId();
		var iframe = $('#sgc_iframe_'+ calId);
		var bubble_value = $(this).val().replace(/\s*%\s*/,'');
		bubble_value = bubble_value.replace(/\s*px\s*/,'');
		iframe.attr('src',iframe.attr('src').replace(/bubbleWidth=.*&bubbleUnit/, "bubbleWidth="+bubble_value+"&bubbleUnit"));
	}

	// Function to set bubble width and use % or pixels in iframe
	$.fn.setBubbleUnit = function() {
		var calId = $(this).getCalId();
		var iframe = $('#sgc_iframe_'+ calId);
		var re = new RegExp(/%/);
		var result = re.exec($(this).val());
		if(result != null){
			iframe.attr('src',iframe.attr('src').replace(/&bubbleUnit=[a-z]*&/, '&bubbleUnit=percentage&'));
			$('#sgccode'+calId).refreshPreview();
		} else {
			iframe.attr('src',iframe.attr('src').replace(/&bubbleUnit=[a-z]*&/, '&bubbleUnit=pixel&'));
			$('#sgccode'+calId).refreshPreview();
		}
	}


	//Change width or height of embed code and iframe when text input changed
	$('.sgcBubble').change(function(){
		$(this).setBubbleWidth();
		$(this).setBubbleUnit();
	});
	
	
	//Change calendar view mode
	$('.calMode').change(function(){
		var calId = $(this).getCalId();
		var re = new RegExp(/(mode=[a-zA-Z]+)/);
		var result = re.exec($('#sgccode'+calId).val());
		var textarea = $('#sgccode'+calId);
		if(result != null){
			textarea.val(textarea.val().replace(result[1], "mode="+$(this).val()));
		} else {
			textarea.val(textarea.val().replace(/\?/, "?mode="+$(this).val()+"&"));
		}
		//refresh preview
		$('#sgccode'+calId).refreshPreview();
	});
	
	$.fn.setViewMode = function(calId){
		var re = new RegExp(/mode=([a-zA-Z]+)/);
		var result = re.exec($('#sgccode'+calId).val());
		if(result != null){
			switch(result[1]){
				case 'WEEK':
					$('#mode-week'+calId).attr('selected', true);
				  break;
				case 'AGENDA':
					$('#mode-agenda'+calId).attr('selected', true);
				  break;
				default:
					$('#mode-month'+calId).attr('selected', true);					
			}
		}	else {
			$('#mode-month'+calId).attr('selected', true);
		}
	}
	
	//Change calendar language
	$('.calLanguage').change(function(){
		var calId = $(this).getCalId();
		var re = new RegExp(/(hl=[a-zA-Z]+_?[a-zA-Z]+)/);
		var result = re.exec($('#sgccode'+calId).val());
		var textarea = $('#sgccode'+calId);
		if(result != null){
			textarea.val(textarea.val().replace(result[1], "hl="+$(this).val()));
		} else {
			textarea.val(textarea.val().replace(/\?/, "?hl="+$(this).val()+"&"));
		}
		//refresh preview
		$('#sgccode'+calId).refreshPreview();
	});
	
	$.fn.setLanguage = function(calId){
		var re = new RegExp(/hl=([a-zA-Z]+_?[a-zA-Z]+)/);
		var result = re.exec($('#sgccode'+calId).val());
		if(result != null){
			$('#hl'+ calId + ' option[selected]').removeAttr("selected");
			$('#hl'+ calId + ' option[value='+result[1]+']').attr("selected", "selected");
		} else {
			$('#hl'+ calId + " option[value='']").attr("selected", "selected");
		}
	}
	
  // "Select" the view mode if in embed code
  $('.sgc-pickers').each(function() {
  	var calId = $(this).getCalId();
  	$(this).setViewMode(calId);
		$(this).setLanguage(calId);
  });
	
	//Toggle tabs option
	$('.sgc-toggle-options').click(function(){
		var option = $(this).attr('name');
		var calId = $(this).getCalId();
		$(this).changeOptions(calId,option);
	});
		
	// function for toggling options
	$.fn.changeOptions = function(calId,option) { 
		var re = new RegExp('('+option+'=0&)','g');
		var textarea = $('#sgccode'+calId);
		var result = re.exec($('#sgccode'+calId).val());
		if($(this).attr('checked')) { 
			if(result != null){
				textarea.val(textarea.val().replace(result[1],''));
			}
		//not checked
		} else {
			textarea.val(textarea.val().replace(/\?/, '?'+option+'=0&'));
		}
		//refresh preview
		$('#sgccode'+calId).refreshPreview();
	}
	
	// function for setting checkboxes based upon embed code
	$.fn.tickCheckboxes = function(calId,option) { 
		var re = new RegExp('('+option+'=0&)','g');
		var textarea = $('#sgccode'+calId);
		var result = re.exec($('#sgccode'+calId).val());
		if(result == null){
			$(this).attr('checked', true);
		} else {
			$(this).attr('checked', false);
		}
	}
	
	//Set selected options on load
	$('.sgc-toggle-options').each(function(){
		var option = $(this).attr('name');
		var calId = $(this).getCalId();
		// let's get to ticking some boxes
		$(this).tickCheckboxes(calId,option);
	});
	
	//Admin calendar preview
	$('a.sgc_preview').each(function() {
			var $dialog = $(this).next('.sgc_iframe_wrapper');
			var $link = $(this).one('click', function() {
				$dialog
					.load()
					.dialog({
						title: 'Calendar Preview',
						width: 'auto',
						height: 'auto',
						resizable : true
					});

				$link.click(function() {
					$dialog.dialog('open');
					return false;
				});
				return false;
			});
		});
		
		//Toggle calendar form
		$('.sgc-form-toggle').click(function(){
			$(this).parent().next('.sgc-form-wrapper').slideToggle();
			$(this).text($(this).text() == 'Show Calendar Editor' ? 'Hide Calendar Editor' : 'Show Calendar Editor');
			return false;
		});
		
		// function for toggling transparency of calendar background
		$.fn.colorpickerBkgrd = function(generate_iframe) { 
			var calId = $(this).getCalId();
			var bkgrdInput = $('#color0'+calId);
			var iframe = $('#sgc_iframe_'+calId);
			if($(this).attr('checked')) { 
				 bkgrdInput.hide();
				$('#button-image-bkgrd_'+ calId).css('background-color', 'transparent');
				if (generate_iframe) { iframe.attr('src',iframe.attr('src').replace(/sgcBkgrdTrans=\d/, "sgcBkgrdTrans=1")); }
			 } else {
				bkgrdInput.show();
				$('#button-image-bkgrd_'+ calId).css('background-color', '#'+bkgrdInput.val());
				bkgrdInput.css('background-color', '#'+bkgrdInput.val());
				if (generate_iframe) { iframe.attr('src',iframe.attr('src').replace(/sgcBkgrdTrans=\d/, "sgcBkgrdTrans=0")); }
			}
		}
				
		//set background as transparent on click
		$('.bkgrdTransparent').click(function(){
			$(this).colorpickerBkgrd(true);
		});
		
		//set background as transparent on load
		$('.bkgrdTransparent').each(function(){
			$(this).colorpickerBkgrd(false);
		});
				
		//Form validation
		$(".button-primary").click(function(){
			$(this).parents('form').validate();
		});
		
		// function to update preview
		$.fn.refreshPreview = function() {
			// get current calendar name
			var idName = $(this).attr('id');
			
			//strip &amp; from embed code - hope I don't regret this later
			$('#'+idName).val($('#'+idName).val().replace(/(&amp;)/g, '&'));
			
			// modify and update query
			var reCode = new RegExp(/(\?\S+)/);
			var resultCode = reCode.exec($('#'+idName).val());
			
			// check for change in iframe height
			var reHeight = new RegExp(/height="(\d+)"/);
			var resultHeight = reHeight.exec($('#'+idName).val());
			
			//check for change in iframe width
			var reWidth = new RegExp(/width="(\d+)"/);
			var resultWidth = reWidth.exec($('#'+idName).val());
			
			var calId = $(this).getCalId();
			var iframe = $('#sgc_iframe_'+calId);
			
		  if (resultCode == null) {
					if (calId == "0" ) {
						//if new calendar, hide preview link and show error
						$("#new-preview").hide();
						$("#new-preview-msg").html('Your Google Calendar code appears incorrect.').css('color','red');
	  			}
			} else {

				var calCode = resultCode[0].slice(0,-1);
	
				//if new calendar, show preview link and hide error if displayed
				if (calId == "0" ) {
					$("#new-preview").show();
					$("#new-preview-msg").html('');
				}
		    
				//update iframe src; trim trailing quote
				iframe.attr('src', iframe.attr('src').replace(/(\?\S+&sgc0)/, calCode+"&sgc0"));
				
				//update height of iframe and text input
				if (resultHeight != null) { 
					iframe.attr('height', resultHeight[1]);
					$('#height'+calId).val(resultHeight[1]);
				}
				
				//update width of iframe and text input
				if (resultWidth != null) { 
					iframe.attr('width', resultWidth[1]);
					$('#width'+calId).val(resultWidth[1]); 
				}
				
				// update checkboxes
				$('#sgc-form'+calId+' .sgc-toggle-options').each(function(){
					var option = $(this).attr('name');
					var calId = $(this).getCalId();
					// let's get to ticking some boxes
					$(this).tickCheckboxes(calId,option);
				});	
				
				//update view	mode
				$(this).setViewMode(calId);
				
				//update language
				$(this).setLanguage(calId);
				
		  }
		}
		
		//update preview whenever embed code is changed
		$('.sgccode').change(function(){
			$(this).refreshPreview();
		});
		
		// Delete calendar confirmation
		$('.sgcdelete').submit(function(e){
			e.preventDefault();
			var self = this;
			var calId = $(this).getCalId();
			// confirm delete
			$("#delete-confirm"+calId).dialog({
					autoOpen: false,
					resizable: false,
					height:200,
					width:300,
					modal: true,
					buttons: {
						"Delete Calendar": function() {
							$( this ).dialog( "close" );
							self.submit();
						},
						Cancel: function() {
							$( this ).dialog( "close" );
						}
					}
				});
				$("#delete-confirm"+calId).dialog("open");
			});
		
		
			//update bubble width in all iframe string
			$('.sgcBubbleSaved').each(function(){
				$(this).setBubbleWidth();
				$(this).setBubbleUnit();
			});
						
		
});

