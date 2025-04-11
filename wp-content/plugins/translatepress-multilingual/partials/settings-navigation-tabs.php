<?php
if ( !defined('ABSPATH' ) )
    exit();
?>
<h2 class="nav-tab-wrapper">
        <?php
        foreach( $tabs as $tb ) {
            echo '<a href="' . esc_url( $tb['url'] ) . '" '. ( $tb['page'] == 'trp_translation_editor' ? 'target="_blank"' : '' ) .' class="nav-tab ' . ( ( $active_tab == $tb['page'] ) ? 'nav-tab-active' : '' ) . ( ( $tb['page'] == 'trp_translation_editor' ) ? 'trp-translation-editor' : '' ) . '">' . esc_html( $tb['name'] ) . '</a>';
        }
        ?>
</h2>
