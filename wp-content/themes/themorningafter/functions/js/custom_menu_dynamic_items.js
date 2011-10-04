function removeitem(o) 
{
	   		
	var todelete = document.getElementById( 'menu-' + o);
	
	if (todelete)
	{		
		var parenttodelete = document.getElementById( 'menu-' + o).parentNode;
        throwaway_node = parenttodelete.removeChild(todelete); 
	}	
			
	updatepostdata();
};

function edititem(o) 
{
	   		
		itemTitle = jQuery( '#title' + o).attr( 'value' );
		itemURL = jQuery( '#linkurl' + o).attr( 'value' );
		itemAnchorTitle = jQuery( '#anchortitle' + o).attr( 'value' );
		itemNewWindow = jQuery( '#newwindow' + o).attr( 'value' );
		itemDesc = jQuery( '#description' + o).attr( 'value' );
		
		jQuery( '#dialog-confirm').dialog( 'option' , 'itemID' , o )
				
		jQuery( '#dialog-confirm').dialog( 'open' );
		
		jQuery( '#edittitle').attr( 'value', itemTitle);
		jQuery( '#editlink').attr( 'value', itemURL);
		jQuery( '#editanchortitle').attr( 'value', itemAnchorTitle);
		jQuery( "#editnewwindow option[value='" + itemNewWindow  + "']").attr( 'selected', 'selected' );
		jQuery( '#editdescription').attr( 'value', itemDesc);
	
};

function updatepostdata() 
{	       	
	
	var i = 0;
	 jQuery( "#custom-nav").find( "li").each(function(i) {
		i = i + 1;
     	var j = jQuery(this).attr( 'value' );
		
     	jQuery(this).find( '#position' + j).attr( 'value', i);
     	jQuery(this).attr( 'id','menu-' + i);
     	jQuery(this).attr( 'value', i);
     	
     	jQuery(this).find( '#dbid' + j).attr( 'name','dbid' + i);
     	jQuery(this).find( '#dbid' + j).attr( 'id','dbid' + i);
     	
		jQuery(this).find( '#postmenu' + j).attr( 'name','postmenu' + i);
     	jQuery(this).find( '#postmenu' + j).attr( 'id','postmenu' + i);
     	
     	var p = jQuery(this).find( '#parent' + j).parent().parent().parent().attr( 'value' );
     	
     	jQuery(this).find( '#parent' + j).attr( 'name','parent' + i);
     	jQuery(this).find( '#parent' + j).attr( 'id','parent' + i);
     	if (p) {
     		//Do nothing
     	}
     	else {
     		//reset p to be top level
     		p = 0;
     	}
     	     	
     	jQuery(this).find( '#parent' + j).attr( 'value', p);
     	     	
     	jQuery(this).find( '#title' + j).attr( 'name','title' + i);
     	jQuery(this).find( '#title' + j).attr( 'id','title' + i);
     	
     	jQuery(this).find( '#linkurl' + j).attr( 'name','linkurl' + i);
     	jQuery(this).find( '#linkurl' + j).attr( 'id','linkurl' + i);
     		
     	jQuery(this).find( '#description' + j).attr( 'name','description' + i);
     	jQuery(this).find( '#description' + j).attr( 'id','description' + i);
     	
     	jQuery(this).find( '#icon' + j).attr( 'name','icon' + i);
     	jQuery(this).find( '#icon' + j).attr( 'id','icon' + i);
     	
     	jQuery(this).find( '#position' + j).attr( 'name','position' + i);
     	jQuery(this).find( '#position' + j).attr( 'id','position' + i);
     	
     	jQuery(this).find( '#linktype' + j).attr( 'name','linktype' + i);
     	jQuery(this).find( '#linktype' + j).attr( 'id','linktype' + i);
     	
     	jQuery(this).find( '#anchortitle' + j).attr( 'name','anchortitle' + i);
     	jQuery(this).find( '#anchortitle' + j).attr( 'id','anchortitle' + i);
     	
     	jQuery(this).find( '#newwindow' + j).attr( 'name','newwindow' + i);
     	jQuery(this).find( '#newwindow' + j).attr( 'id','newwindow' + i);
     	
     	jQuery(this).find( 'dl > dt > span > #remove' + j).attr( 'value', i);
     	jQuery(this).find( 'dl > dt > span > #remove' + j).attr( 'onClick', 'removeitem( ' + i + ')' );
     	jQuery(this).find( 'dl > dt > span > #remove' + j).attr( 'id','remove' + i);

     	
     	jQuery( '#licount').attr( 'value',i);

   });
   
   
	
};	

function get_html_translation_table (table, quote_style) {
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: noname
    // +   bugfixed by: Alex
    // +   bugfixed by: Marco
    // +   bugfixed by: madipta
    // +   improved by: KELAN
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Frank Forte
    // +   bugfixed by: T.Wild
    // +      input by: Ratheous
    // %          note: It has been decided that we're not going to add global
    // %          note: dependencies to php.js, meaning the constants are not
    // %          note: real constants, but strings instead. Integers are also supported if someone
    // %          note: chooses to create the constants themselves.
    // *     example 1: get_html_translation_table( 'HTML_SPECIALCHARS' );
    // *     returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}
    
    var entities = {}, hash_map = {}, decimal = 0, symbol = '';
    var constMappingTable = {}, constMappingQuoteStyle = {};
    var useTable = {}, useQuoteStyle = {};
    
    // Translate arguments
    constMappingTable[0]      = 'HTML_SPECIALCHARS';
    constMappingTable[1]      = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';

    useTable       = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

    if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
        throw new Error( "Table: "+useTable+' not supported' );
        // return false;
    }

    entities['38'] = '&amp;';
    if (useTable === 'HTML_ENTITIES') {
        entities['160'] = '&nbsp;';
        entities['161'] = '&iexcl;';
        entities['162'] = '&cent;';
        entities['163'] = '&pound;';
        entities['164'] = '&curren;';
        entities['165'] = '&yen;';
        entities['166'] = '&brvbar;';
        entities['167'] = '&sect;';
        entities['168'] = '&uml;';
        entities['169'] = '&copy;';
        entities['170'] = '&ordf;';
        entities['171'] = '&laquo;';
        entities['172'] = '&not;';
        entities['173'] = '&shy;';
        entities['174'] = '&reg;';
        entities['175'] = '&macr;';
        entities['176'] = '&deg;';
        entities['177'] = '&plusmn;';
        entities['178'] = '&sup2;';
        entities['179'] = '&sup3;';
        entities['180'] = '&acute;';
        entities['181'] = '&micro;';
        entities['182'] = '&para;';
        entities['183'] = '&middot;';
        entities['184'] = '&cedil;';
        entities['185'] = '&sup1;';
        entities['186'] = '&ordm;';
        entities['187'] = '&raquo;';
        entities['188'] = '&frac14;';
        entities['189'] = '&frac12;';
        entities['190'] = '&frac34;';
        entities['191'] = '&iquest;';
        entities['192'] = '&Agrave;';
        entities['193'] = '&Aacute;';
        entities['194'] = '&Acirc;';
        entities['195'] = '&Atilde;';
        entities['196'] = '&Auml;';
        entities['197'] = '&Aring;';
        entities['198'] = '&AElig;';
        entities['199'] = '&Ccedil;';
        entities['200'] = '&Egrave;';
        entities['201'] = '&Eacute;';
        entities['202'] = '&Ecirc;';
        entities['203'] = '&Euml;';
        entities['204'] = '&Igrave;';
        entities['205'] = '&Iacute;';
        entities['206'] = '&Icirc;';
        entities['207'] = '&Iuml;';
        entities['208'] = '&ETH;';
        entities['209'] = '&Ntilde;';
        entities['210'] = '&Ograve;';
        entities['211'] = '&Oacute;';
        entities['212'] = '&Ocirc;';
        entities['213'] = '&Otilde;';
        entities['214'] = '&Ouml;';
        entities['215'] = '&times;';
        entities['216'] = '&Oslash;';
        entities['217'] = '&Ugrave;';
        entities['218'] = '&Uacute;';
        entities['219'] = '&Ucirc;';
        entities['220'] = '&Uuml;';
        entities['221'] = '&Yacute;';
        entities['222'] = '&THORN;';
        entities['223'] = '&szlig;';
        entities['224'] = '&agrave;';
        entities['225'] = '&aacute;';
        entities['226'] = '&acirc;';
        entities['227'] = '&atilde;';
        entities['228'] = '&auml;';
        entities['229'] = '&aring;';
        entities['230'] = '&aelig;';
        entities['231'] = '&ccedil;';
        entities['232'] = '&egrave;';
        entities['233'] = '&eacute;';
        entities['234'] = '&ecirc;';
        entities['235'] = '&euml;';
        entities['236'] = '&igrave;';
        entities['237'] = '&iacute;';
        entities['238'] = '&icirc;';
        entities['239'] = '&iuml;';
        entities['240'] = '&eth;';
        entities['241'] = '&ntilde;';
        entities['242'] = '&ograve;';
        entities['243'] = '&oacute;';
        entities['244'] = '&ocirc;';
        entities['245'] = '&otilde;';
        entities['246'] = '&ouml;';
        entities['247'] = '&divide;';
        entities['248'] = '&oslash;';
        entities['249'] = '&ugrave;';
        entities['250'] = '&uacute;';
        entities['251'] = '&ucirc;';
        entities['252'] = '&uuml;';
        entities['253'] = '&yacute;';
        entities['254'] = '&thorn;';
        entities['255'] = '&yuml;';
    }

    if (useQuoteStyle !== 'ENT_NOQUOTES') {
        entities['34'] = '&quot;';
    }
    if (useQuoteStyle === 'ENT_QUOTES') {
        entities['39'] = '&#39;';
    }
    entities['60'] = '&lt;';
    entities['62'] = '&gt;';


    // ascii decimals to real symbols
    for (decimal in entities) {
        symbol = String.fromCharCode(decimal);
        hash_map[symbol] = entities[decimal];
    }
    
    return hash_map;
}


function htmlentities (string, quote_style) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: nobbler
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Ratheous
    // -    depends on: get_html_translation_table
    // *     example 1: htmlentities( 'Kevin & van Zonneveld' );
    // *     returns 1: 'Kevin &amp; van Zonneveld'
    // *     example 2: htmlentities( "foo'bar","ENT_QUOTES" );
    // *     returns 2: 'foo&#039;bar'

    var hash_map = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();
    
    if (false === (hash_map = this.get_html_translation_table( 'HTML_ENTITIES', quote_style))) {
        return false;
    }
    hash_map["'"] = '&#039;';
    for (symbol in hash_map) {
        entity = hash_map[symbol];
        tmp_str = tmp_str.split(symbol).join(entity);
    }
    
    return tmp_str;
}
  
function htmlspecialchars (string, quote_style, charset, double_encode) {
    // http://kevin.vanzonneveld.net
    // +   original by: Mirek Slugen
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Nathan
    // +   bugfixed by: Arno
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Ratheous
    // +      input by: Mailfaker (http://www.weedem.fr/)
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +      input by: felix
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // %        note 1: charset argument not supported
    // *     example 1: htmlspecialchars( "<a href='test'>Test</a>", 'ENT_QUOTES' );
    // *     returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
    // *     example 2: htmlspecialchars( "ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
    // *     returns 2: 'ab"c&#039;d'
    // *     example 3: htmlspecialchars( "my "&entity;" is still here", null, null, false);
    // *     returns 3: 'my &quot;&entity;&quot; is still here'

    var optTemp = 0, i = 0, noquotes= false;
    if (typeof quote_style === 'undefined' || quote_style === null) {
        quote_style = 2;
    }
    string = string.toString();
    if (double_encode !== false) { // Put this first to avoid double-encoding
        string = string.replace(/&/g, '&amp;' );
    }
    string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;' );

    var OPTS = {
        'ENT_NOQUOTES': 0,
        'ENT_HTML_QUOTE_SINGLE' : 1,
        'ENT_HTML_QUOTE_DOUBLE' : 2,
        'ENT_COMPAT': 2,
        'ENT_QUOTES': 3,
        'ENT_IGNORE' : 4
    };
    if (quote_style === 0) {
        noquotes = true;
    }
    if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
        quote_style = [].concat(quote_style);
        for (i=0; i < quote_style.length; i++) {
            // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
            if (OPTS[quote_style[i]] === 0) {
                noquotes = true;
            }
            else if (OPTS[quote_style[i]]) {
                optTemp = optTemp | OPTS[quote_style[i]];
            }
        }
        quote_style = optTemp;
    }
    if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
        string = string.replace(/'/g, '&#039;' );
    }
    if (!noquotes) {
        string = string.replace(/"/g, '&quot;' );
    }

    return string;
}

function appendToList(templatedir,additemtype,itemtext,itemurl,itemid,itemparentid,itemdescription) 
{
	var inputvaluevarname = '';
	var inputvaluevarurl = '';
	var inputitemid = '';
	var inputparentid= '';
	var inputdescription = '';
	var inputicon = '';

	if (additemtype == 'Custom') 
	{
		inputvaluevarname = htmlspecialchars(document.getElementById( 'custom_menu_item_name').value, 'ENT_QUOTES' );
		inputvaluevarurl = htmlspecialchars(document.getElementById( 'custom_menu_item_url').value, 'ENT_QUOTES' );
		inputitemid = '';
		inputparentid = '';
		inputlinktype = 'custom';
		inputdescription = htmlspecialchars(document.getElementById( 'custom_menu_item_description').value, 'ENT_QUOTES' );
	}
	else if (additemtype == 'Page')
	{
		inputvaluevarname = htmlspecialchars(itemtext.toString(), 'ENT_QUOTES' );
		inputvaluevarurl = itemurl.toString();
		inputitemid = itemid.toString();
		inputparentid = '0';
		inputlinktype = 'page';
		inputdescription = htmlspecialchars(itemdescription.toString(), 'ENT_QUOTES' );
		
	}
	else if (additemtype == 'Category')
	{
		inputvaluevarname = htmlspecialchars(itemtext.toString(), 'ENT_QUOTES' );
		inputvaluevarurl = itemurl.toString();
		inputitemid = itemid.toString();
		inputparentid = '0';
		inputlinktype = 'category';
		inputdescription = htmlspecialchars(itemdescription.toString(), 'ENT_QUOTES' );
	}
	else 
	{
		inputvaluevarname = '';
		inputvaluevarname = '';
		inputitemid = '';
		inputparentid = '';
		inputlinktype = 'custom';
		inputdescription = '';
	}

	
	
	var count=document.getElementById( 'custom-nav').getElementsByTagName( 'li').length;

	var randomnumber = count;

	var validatetest = 0;

	try 
	{
		var test=document.getElementById( "menu-" + randomnumber.toString()).value;
	}
	catch (err) 
	{
		validatetest = 1;
	}

	while (validatetest == 0) 
	{
		randomnumber = randomnumber + 1;

		try 
		{
			var test2=document.getElementById( "menu-" + randomnumber.toString()).value;
		}
		catch (err) 
		{
			validatetest = 1;
		}
	}
	
	jQuery( '.maintitle').after( '<div id="message" class="updated fade below-h2"><p>Menu Item added!</p></div>' );
	jQuery( '#message').animate({ opacity: 1.0 },2000).fadeOut(300, function(){ jQuery(this).remove();});
	
	jQuery( '#custom-nav').append( '<li id="menu-' + randomnumber + '" value="' + randomnumber + '"><div class="dropzone ui-droppable"></div><dl class="ui-droppable"><dt><span class="title">' + inputvaluevarname + '</span><span class="controls"><span class="type">' + additemtype + '</span><a id="edit' + randomnumber + '" onclick="edititem( ' + randomnumber + ')" value="' + randomnumber +'"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="' + templatedir + '/functions/images/ico-edit.png" /></a> <a id="remove' + randomnumber + '" onclick="removeitem( ' + randomnumber + ')" value="' + randomnumber +'"><img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="' + templatedir + '/functions/images/ico-close.png" /></a> <a href="' + inputvaluevarurl + '" target="_blank"><img alt="View Custom Link" title="View Custom Link" src="' + templatedir + '/functions/images/ico-viewpage.png" /></a></span></dt></dl><a class="hide" href="' + inputvaluevarurl + '">' + inputvaluevarname + '</a><input type="hidden" name="postmenu' + randomnumber + '" id="postmenu' + randomnumber + '" value="' + inputitemid + '" /><input type="hidden" name="parent' + randomnumber + '" id="parent' + randomnumber + '" value="' + inputparentid + '" /><input type="hidden" name="title' + randomnumber + '" id="title' + randomnumber + '" value="' + inputvaluevarname + '" /><input type="hidden" name="linkurl' + randomnumber + '" id="linkurl' + randomnumber + '" value="' + inputvaluevarurl + '" /><input type="hidden" name="description' + randomnumber + '" id="description' + randomnumber + '" value="' + inputdescription + '" /><input type="hidden" name="icon' + randomnumber + '" id="icon' + randomnumber + '" value="' + inputicon + '" /><input type="hidden" name="position' + randomnumber + '" id="position' + randomnumber + '" value="' + randomnumber + '" /><input type="hidden" name="linktype' + randomnumber + '" id="linktype' + randomnumber + '" value="' + inputlinktype + '" /><input type="hidden" name="anchortitle' + randomnumber + '" id="anchortitle' + randomnumber + '" value="' + inputvaluevarname + '" /><input type="hidden" name="newwindow' + randomnumber + '" id="newwindow' + randomnumber + '" value="0" /></li>' );

	jQuery( '#menu-' + randomnumber + '').draggable(
	{
		handle: ' > dl',
		opacity: .8,
		addClasses: false,
		helper: 'clone',
		zIndex: 100
	});

	jQuery( '#menu-' + randomnumber + ' dl, #menu-' + randomnumber + ' .dropzone').droppable({
		accept: '#' + randomnumber + ', #custom-nav li',
		tolerance: 'pointer',
		drop: function(e, ui) 
		{
			var li = jQuery(this).parent();
			var child = !jQuery(this).hasClass( 'dropzone' );
			//Append UL to first child
			if (child && li.children( 'ul').length == 0) 
			{
				li.append( '<ul/>' );
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
	    	}
	});

	updatepostdata();
};



