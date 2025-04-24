<?php
if ( !defined('ABSPATH' ) )
    exit();

?>

    <!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <?php
        do_action( 'trp_head' );
    ?>

    <title>TranslatePress</title>
</head>
<body class="trp-editor-body">

    <div id="trp-editor-container">
        <trp-editor
            ref='trp_editor'
        >
        </trp-editor>
    </div>

    <?php do_action( 'trp_translation_manager_footer' ); ?>
</body>
</html>

<?php
