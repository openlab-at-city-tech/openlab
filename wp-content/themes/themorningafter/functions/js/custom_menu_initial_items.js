//init functions
jQuery(function() 
	{
		
		jQuery( "#dialog-confirm").dialog({
			autoOpen: false,
			resizable: false,
			height: 210,
			width: 400,
			modal: true,
			buttons: {
				'Save': function() {
					
					titletosave = jQuery( '#edittitle').attr( 'value' );
					linktosave = jQuery( '#editlink').attr( 'value' );
					anchortitletosave = jQuery( '#editanchortitle').attr( 'value' );
					newwindowtosave = jQuery( '#editnewwindow').attr( 'value' );
					desctosave = jQuery( '#editdescription').attr( 'value' );
					
					jQuery( '#title' + jQuery(this).dialog( 'option', 'itemID')).attr( 'value',titletosave);
					jQuery( '#linkurl' + jQuery(this).dialog( 'option', 'itemID')).attr( 'value',linktosave);
					jQuery( '#anchortitle' + jQuery(this).dialog( 'option', 'itemID')).attr( 'value',anchortitletosave);
					jQuery( '#newwindow' + jQuery(this).dialog( 'option', 'itemID')).attr( 'value',newwindowtosave);
					jQuery( '#description' + jQuery(this).dialog( 'option', 'itemID')).attr( 'value',desctosave);
					
					jQuery( '#menu-' + jQuery(this).dialog( 'option', 'itemID') + ' > dl > dt > span.title').text(titletosave);
					
					jQuery( '#view' + + jQuery(this).dialog( 'option', 'itemID')).attr( 'href', linktosave);
					
					jQuery(this).dialog( 'close' );
					
				},
				Cancel: function() {
					jQuery(this).dialog( 'close' );
				}
			}
		});
		
		jQuery( '#message').animate({ opacity: 1.0 },2000).fadeOut(300, function(){ jQuery(this).remove();});
		
	    jQuery( '#custom-nav li').prepend( '<div class="dropzone"></div>' );	
		
		jQuery( '#custom-nav li').draggable({
			    handle: ' > dl',
			    opacity: .8,
			    addClasses: false,
			    helper: 'clone',
			    zIndex: 100
		});

		jQuery( '#custom-nav dl, #custom-nav .dropzone').droppable(
		{
	    	accept: '#custom-nav li',
		    tolerance: 'pointer',
	    	drop: function(e, ui) 
	    	{
	        	var li = jQuery(this).parent();
	        	var child = !jQuery(this).hasClass( 'dropzone' );
	        	//Add UL to first child
	        	if (child && li.children( 'ul').length == 0) 
	        	{
	            	li.append( '<ul id="sub-menu" />' );
	        	}
	        	//Make it draggable
	        	if (child) 
	        	{
	            	li.children( 'ul').append(ui.draggable);
	        	}
	        	else 
	        	{
	            	li.before(ui.draggable);
	        	}

	        	li.find( 'dl,.dropzone').css({ backgroundColor: '', borderColor: '' });
	        	
	        	var draggablevalue = ui.draggable.attr( 'value' );
	        	var droppablevalue = li.attr( 'value' );
	        	li.find( '#menu-' + draggablevalue).find( '#parent' + draggablevalue).val(droppablevalue); 
	        	jQuery(this).parent().find( "dt").removeAttr( 'style' );
	        	jQuery(this).parent().find( "div:first").removeAttr( 'style' );
	        	
	        	
	    	},
	    	over: function() 
	    	{
	    		//Add child
	    		if (jQuery(this).attr( 'class') == 'dropzone ui-droppable') 
	    		{
	    			jQuery(this).parent().find( "div:first").css( 'background', 'none').css( 'height', '50px' );
	    		}
	    		//Add above
	    		else if (jQuery(this).attr( 'class') == 'ui-droppable') 
	    		{
	    			jQuery(this).parent().find( "dt:first").css( 'background', '#d8d8d8' );
	    		}
	    		//do nothing
	    		else {
	    		
	    		}
	    		var parentid = jQuery(this).parent().attr( 'id' );
		        
	       	},
	    	out: function() 
	    	{
	        	jQuery(this).parent().find( "dt").removeAttr( 'style' );
	        	jQuery(this).parent().find( "div:first").removeAttr( 'style' );
	        	jQuery(this).filter( '.dropzone').css({ borderColor: '' });
	    	},
	    	deactivate: function()
	    	{	
	    		
					
	    	}
	    	
	    		
		});
				 
		
	
		jQuery( '#save_top').click(function()
		{
			updatepostdata();
		});
		jQuery( '#save_bottom').click(function()
		{
			updatepostdata();
		});
		

	});


