<?php

$comment_field_css_path = get_option("tinymce-comment-field_css-path");
delete_option("tinymce-comment-field_css-url");
delete_option("tinymce-comment-field_css-path");
delete_option("tinymce-comment-field_options");
delete_option("tinymce-comment-field_ignore_compatibility_issue");

try {
    unlink($comment_field_css_path);
} catch (Exception $ex) {
    //something went wrong
}

