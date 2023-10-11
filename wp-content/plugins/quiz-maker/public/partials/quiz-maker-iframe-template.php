<?php
/**
 * @var int $id The quiz ID.
 * @var array $attr The attributes from shortcode.
 */
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>Quiz Maker</title>
        <?php echo wp_head(); ?>
        <?php echo $this->enqueue_styles(); ?>
        <style>
            /* width */
            ::-webkit-scrollbar {
                width: 10px;
            }

            /* Track */
            ::-webkit-scrollbar-track {
                background-color: #f1f1f1;
            }

            /* Handle */
            ::-webkit-scrollbar-thumb {
                background-color: #aaa;
            }

            /* Handle on hover */
            ::-webkit-scrollbar-thumb:hover {
                background-color: #555;
            }
        </style>
    </head>
    <body style="overflow-x: hidden; background-color: transparent;">
        <?php echo $this->ays_quiz_translate_content( $this->public_obj->show_quiz( $id, $attr ) ); ?>
        <?php echo wp_print_footer_scripts(); ?>
    </body>
</html>
