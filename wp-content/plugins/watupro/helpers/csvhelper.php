<?php
// credit to normadize at http://php.net/manual/en/function.str-getcsv.php
function watupro_parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true) {	
    $enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string);
    $enc = preg_replace_callback(
        '/"(.*?)"/s',
        'watupro_parse_csv_field',
        $enc
    );
    $lines = preg_split($skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s', $enc);
    return array_map(
        'watupro_parse_csv_line',
        $lines
    );
}

function watupro_parse_csv_field($field) {
   return urlencode(utf8_encode($field[1]));
}

function watupro_parse_csv_line($line) {	
	$delimiter=$_POST['delimiter'];
	if($delimiter=="tab") $delimiter="\t";
	
	// convert encoding?
	if(!mb_detect_encoding($line, 'UTF-8', true)) $line = mb_convert_encoding($line, "UTF-8");
	
   $fields = true ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line);
   return array_map(
       'watupro_urlencode_csv_field',
       $fields
   );
}

function watupro_urlencode_csv_field($field) {
 	 return str_replace('!!Q!!', '"', urldecode($field));
 }