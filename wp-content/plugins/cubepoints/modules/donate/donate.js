// Numeric only control handler
jQuery.fn.ForceNumericOnly =
function()
{
    return this.each(function()
    {
        jQuery(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
            return (
                key == 8 || 
                key == 9 ||
                key == 46 ||
                (key >= 37 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};

function cp_module_donate(s){
	showDropdownSearch(s);
}

function showDropdownSearch(s){
	var	html0 = '<div id="optionsearch">'+
				'<h3>' + cp_donate.title + '</h3>'+
				'<div class="textinput"><label for="cp_searchquery"> ' + cp_donate.searchText + ' <span id="optionsearchstatus">' + cp_donate.searchingText + '</span></label><input type="search" id="cp_searchquery" name="cp_searchquery" value="" placeholder="' + cp_donate.searchPlaceholder + '"></div>'+
				'<div id="searchoptionswrapper"><div id="searchnoresult">'+ cp_donate.nothingFound +'</div><table id="searchoptions" class="radioTable" cellpadding="0" cellspacing="0"><tbody></tbody></table></div>'+
			'</div>',
		html1 = '<h3>' + cp_donate.title + '</h3>' +
				'<div class="textinput"><label for="cp_donateAmount"> ' + cp_donate.amountToDonate + '</label><input type="search" id="cp_donateAmount" name="cp_donateAmount" value="" placeholder="' + cp_donate.donateAmountPlaceholder + '"></div>',
		html2 = '<h3>' + cp_donate.title + '</h3>' +
				'<div class="textinput"><label for="cp_donateComment"> ' + cp_donate.donateComment + '</label><input type="search" id="cp_donateComment" name="cp_donateComment" value="" placeholder="' + cp_donate.donateCommentPlaceholder + '"></div>',
		html3 = '<h3>' + cp_donate.title + '</h3>' +
				'<span id="cp_donateFeedback">Please wait...</span>';
	
	var donatePrompt = {
		state0: {
			html: html0,
			buttons: { Cancel:false,Next:true },
			submit: function(e,v,m,f){
				if(v){
					if(f.selectedUser == undefined){
						var e = jQuery.Event("keydown");
						e.which = 13;
						jQuery("#cp_searchquery").trigger(e);
						return false;
					}
					jQuery.prompt.goToState('state1');
					return false;
				}
			}
		},
		state1: {
			html: html1,
			buttons: { Cancel:false,Next:true },
			submit: function(e,v,m,f){
				if(v){
					if(f.cp_donateAmount == ''){
						$imp.find('input').focus();
						return false;
					}
					jQuery.prompt.goToState('state2');
					return false;
				}
			}
		},
		state2: {
			html: html2,
			buttons: { Cancel:false,Donate:true },
			submit: function(e,v,m,f){
				if(v){
					jQuery.ajax({
						url: cp_donate.ajax_url,
						type: "POST",
						cache: false,
						dataType: "json",
						data: {action: "cp_module_donate_do", recipient: f.selectedUser, points: f.cp_donateAmount, message: f.cp_donateComment},
						success: function(data){
							if(data.success==true){
								jQuery.prompt.getStateContent('state3').find('#cp_donateFeedback').text(data.message);
								jQuery('.cp_points_display').html(data.pointsd);
								return false;
							}
							else{
								jQuery.prompt.getStateContent('state3').find('#cp_donateFeedback').text(data.message);
								return false;
							}
						}
					});
					jQuery.prompt.goToState('state3');
					return false;
				}
			}
		},
		state3: {
			html: html3,
			buttons: { Ok:true },
			submit: function(e,v,m,f){
				if(v){
					return true;
				}
			}
		}
	};
	
	var $imp = jQuery.prompt(donatePrompt);

	if(cp_donate.logged_in != '1'){
		jQuery.prompt.getStateContent('state3').find('#cp_donateFeedback').text(cp_donate.notLoggedInText);
		jQuery.prompt.goToState('state3');
	}
	
	jQuery("#cp_donateAmount").ForceNumericOnly();
	
	var $so = jQuery('#searchoptions', $imp);
	
	$so.on('click', 'tr', function(){
		jQuery('tr.checked', $so).removeClass('checked');
		jQuery(this).addClass('checked');
		jQuery(this).find('input').attr('checked', true);
	});

	jQuery('#cp_searchquery', $imp).focus().keydown(function(event){
		var $i = jQuery(this),
			query = $i.val(),
			html = "",
			counter = 0,
			$sos = jQuery('#optionsearchstatus',$imp);
			
		if(event.which == 13 || event.keyCode == 13 && jQuery.trim(query) !== ''){

			$sos.css('display','inline');
			
			var request = jQuery.ajax({
				url: cp_donate.ajax_url,
				type: "POST",
				data: {action: 'cp_donate_search', q: query}
			});
			
			request.done(function(msg) {
				jQuery(msg).each(function(i,user){
					var currval = user.id,
						tval = null,
						currtxt = user.ul;
					html += '<tr class="row'+ (counter++ % 2) + (currval == tval? ' checked':'')+'">'+
							'<td class="td-radio"><input type="radio" name="selectedUser" id="selectedUser_'+ counter +'" value="'+ currval +'"'+ (currval == tval? ' checked':'') +'></td>'+
							'<td class="td-label"><label for="selectedUser_'+ counter +'">'+ currtxt +'</label></td>'+
						'</tr>';
				});
				$so.find('tbody').html(html);
				$so.find('tr:eq(0)').addClass('checked').find('input').attr('checked',true);
				if(counter == 0){
					jQuery('#searchnoresult',$imp).show();
					jQuery('#searchoptions',$imp).hide();
				}
				else{
					jQuery('#searchnoresult',$imp).hide();
					jQuery('#searchoptions',$imp).show();
				}
				jQuery('#searchoptionswrapper',$imp).slideDown('slow', function(){
					$sos.hide();
				});
			});

			request.fail(function(jqXHR, textStatus) {
				jQuery.prompt.getStateContent('state3').find('#cp_donateFeedback').text(cp_donate.somethingWentWrongText);
				jQuery.prompt.goToState('state3');
			});

			return false;
		}// end if enter key

	});

	if(s){
		jQuery.prompt.getStateContent('state0').find('#cp_searchquery').val(s);
		var e = jQuery.Event("keydown");
		e.which = 13;
		jQuery("#cp_searchquery").trigger(e);
	}
	
	return false;
}