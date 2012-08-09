<?php //content processing functions

//limit text length
// Note: In the future this should be swapped with bp_create_excerpt(),
// which is smarter about stripping tags, etc
function openlab_shortened_text($text,$limit = "55") {

	$text_length = mb_strlen($text);

        $text = trim( mb_substr( $text, 0, $limit ) );

        $text = force_balance_tags( $text );

	echo $text;

	if ($text_length > $limit) echo "...";

}
