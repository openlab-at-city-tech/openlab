<?php //content processing functions

//limit text length
function openlab_shortened_text($text,$limit = "55") {

	$text_length = mb_strlen($text);
	
	echo trim(mb_substr($text, 0, $limit));
	
	if ($text_length > $limit) echo "...";

}