<?php

/**
 * OpenLab Custom functionality for Contact Form 7
 */
function openlab_wpcf7_save_contact_form($contact_form, $args, $context) {

    $post_ID = $args['id'];

    //pull out any intro text
    $intro = '';
    $dom = new DOMDocument;
    $dom->encoding = 'utf-8';
    $dom->loadHTML(mb_convert_encoding(stripslashes($args['form']), 'HTML-ENTITIES', 'UTF-8'));
    $p_tags = $dom->getElementsByTagName('p');

    foreach ($p_tags as $p_tag) {
        $innerHTML = openlab_wpcf7_DOMinnerHTML($p_tag);
        $intro .= "<$p_tag->tagName>$innerHTML</$p_tag->tagName>";
    }

    update_post_meta($post_ID, '_form_intro', $intro);
}

//add_action('wpcf7_save_contact_form', 'openlab_wpcf7_save_contact_form', 10, 3);

function openlab_wpcf7_after_save($result) {

    if (!isset($_POST['post_ID'])) {
        return false;
    }

    $post_ID = filter_input(INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT);
    $post_obj = get_post($post_ID);

    if ($post_obj->post_name === 'contact-form-1') {

        $intro = get_post_meta($post_ID, '_form_intro', true);
        if ( ! $intro ) {
            return false;
        }
	$intro = '';

        ob_start();
        include(locate_template('parts/plugin-mods/contact-form-seven-custom.php'));

        $form = ob_get_clean();

        update_post_meta($post_ID, '_form', $intro . $form);
    }
}

//add_action('wpcf7_after_save', 'openlab_wpcf7_after_save', 10, 3);

function openlab_wpcf7_DOMinnerHTML(DOMNode $element) {
    $innerHTML = "";
    $children = $element->childNodes;

    foreach ($children as $child) {
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }

    return $innerHTML;
}

function openlab_wpcf7_contact_form_properties($properties) {

    if (!is_admin()) {
        return $properties;
    }

    $screen = get_current_screen();

    if (!isset($screen->base) || $screen->base !== 'toplevel_page_wpcf7') {
        return $properties;
    }

    if (!isset($_GET['post'])) {
        return $properties;
    }

    $post_ID = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
    $post_obj = get_post($post_ID);

    if ($post_obj->post_name === 'contact-form-1') {

        $intro = get_post_meta($post_ID, '_form_intro', true);
        if ( $intro ) {

            //we'll also includes a heads up to the editor that the form code is now stored in source
            $intro .= <<<HTML


            ***Please Note: Form Code is now stored in the source; please contact the developers for updates to the form fields. Intro text may be modified***'

HTML;

            $properties['form'] = $intro;
        }
    }

    return $properties;
}

//add_action('wpcf7_contact_form_properties', 'openlab_wpcf7_contact_form_properties', 10, 2);

add_filter( 'the_content', function( $content ) {
	if ( ! bp_is_root_blog() ) {
		return $content;
	}

	if ( 51 != get_queried_object_id() ) {
		return $content;
	}

        ob_start();
        include(locate_template('parts/plugin-mods/contact-form-seven-custom.php'));
        $form = ob_get_clean();

	return $form;

} );
