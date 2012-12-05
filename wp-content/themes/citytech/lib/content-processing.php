<?php //content processing functions

//body classes for specific pages - partly legacy from Genesis Connect
add_filter('body_class','openlab_conditional_body_classes');

function openlab_conditional_body_classes($classes)
{
	global $post;
	if (is_front_page() || is_404())
	{
		$classes[] = 'full-width-content';
	} else if ($post->post_name == 'register')
	{
		$classes[] = 'content-sidebar';
	} 
	
	return $classes;
}

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

//truncate links in profile fields - I'm using $field->data->value to just truncate the link name (it was giving odd results when trying to truncate $value)
add_filter('bp_get_the_profile_field_value','openlab_filter_profile_fields',10,2);
 
function openlab_filter_profile_fields($value,$type)
{
	global $field;
	$truncate_link_candidates = array('Website','LinkedIn Profile Link','Facebook Profile Link');
	if (in_array($field->name,$truncate_link_candidates))
	{
		$args = array(
		'ending'            => __( '&hellip;', 'buddypress' ),
		'exact'             => true,
		'html'              => false,
		'filter_shortcodes' => $filter_shortcodes_default
		);
		$truncated_link = bp_create_excerpt($field->data->value, 40, $args);
		$full_link = openlab_http_check($field->data->value);
		$value = '<a href="'.$full_link.'">'.openlab_http_check($truncated_link).'</a>';
	}
	return $value;
}

function openlab_http_check($link)
{
	$http_check = strpos($link, "http");
	
	if ($http_check == false)
	{
		$link = "http://".$link;
	}
	
	return $link;
}

