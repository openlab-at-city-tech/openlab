<?php

$url = 'http://libguides.citytech.cuny.edu/index_process.php?action=173&key=0&order=0&type_id=0&owner_id=0&group_id=0&num_cols=2&search=';

$fetch = wp_remote_get( $url );
$c = json_decode( wp_remote_retrieve_body( $fetch ) );

$body = $c->data->content;

$dom = new DOMDocument();
$dom->loadHTML( $body );
$xpath = new DOMXpath( $dom );

$titles = $xpath->query('//div[@class="s-lg-gtitle"]');

foreach ( $titles as $title ) {
	var_Dump( $title );
}

