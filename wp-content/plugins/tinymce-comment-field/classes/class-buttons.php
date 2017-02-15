<?php
class TMCECF_Buttons {

    public static function getTeeny() {

        return array(
            'bold' => __('Bold', 'tinymce-comment-field'),
            'italic' => __('Italic', 'tinymce-comment-field'),
            'underline' => __('Underline', 'tinymce-comment-field'),
            'strikethrough' => __('Strikethrough', 'tinymce-comment-field'),
            'bullist' => __('Bullet List', 'tinymce-comment-field'),
            'numlist' => __('Numbered List', 'tinymce-comment-field'),
            'outdent' => __('Decrease Indent', 'tinymce-comment-field'),
            'indent' => __('Increase Indent', 'tinymce-comment-field'),
            'blockquote' => __('Blockquote', 'tinymce-comment-field'),
            'cut' => __('Cut', 'tinymce-comment-field'),
            'copy' => __('Copy', 'tinymce-comment-field'),
            'paste' => __('Paste', 'tinymce-comment-field'),
            'undo' => __('Undo', 'tinymce-comment-field'),
            'redo' => __('Redo', 'tinymce-comment-field'),
            'link' => __('Add Link', 'tinymce-comment-field'),
            'unlink' => __('Remove Link', 'tinymce-comment-field'),
            //"image" => __("Image", "tinymce-comment-field"),
            'removeformat' => __('Remove Format', 'tinymce-comment-field'),
            'formatselect' => __('Format Select', 'tinymce-comment-field'),
            'fontselect' => __('Font Select', 'tinymce-comment-field'),
            'fontsizeselect' => __('Font Size Select', 'tinymce-comment-field'),
        );
    }
}
