<?php
/*
*************************
* Pretty print some JSON*
*************************  
*/
class Dev {
	function json_format($json) 
	{ 
    	$tab = "  "; 
    	$new_json = ""; 
    	$indent_level = 0; 
    	$in_string = false; 

    	$json_obj = json_decode($json); 

    	if($json_obj === false)
			return false; 

    	$json = json_encode($json_obj); 
    	$len = strlen($json); 

    	for($c = 0; $c < $len; $c++) 
    	{ 
        	$char = $json[$c]; 
        	switch($char) 
        	{ 
            	case '{': 
            	case '[': 
                	if(!$in_string) 
                	{ 
                    	$new_json .= $char . "\n" . str_repeat($tab, $indent_level+1); 
                    	$indent_level++; 
                	} 
               	    else 
                	{ 
                    	$new_json .= $char; 
                	} 
                	break; 
           	 	case '}': 
            	case ']': 
                	if(!$in_string) 
                	{ 
                    	$indent_level--; 
                    	$new_json .= "\n" . str_repeat($tab, $indent_level) . $char; 
                	} 
                	else 
                	{ 
                    	$new_json .= $char; 
                	} 
                	break; 
            	case ',': 
                	if(!$in_string) 
                	{ 
                    	$new_json .= ",\n" . str_repeat($tab, $indent_level); 
                	} 
                	else 
                	{ 
                    	$new_json .= $char; 
                	} 
                	break; 
            	case ':': 
                	if(!$in_string) 
                	{ 
                    	$new_json .= ": "; 
                	} 
                	else 
                	{ 
                    	$new_json .= $char; 
                	} 
                	break; 
            	case '"': 
                	if($c > 0 && $json[$c-1] != '\\') 
                	{ 
                    	$in_string = !$in_string; 
                	} 
            	default: 
                	$new_json .= $char; 
                	break;                    
        	} 
    	} 
		header('Content-type: text/plain');
    	return $new_json; 
	} 
}
?>