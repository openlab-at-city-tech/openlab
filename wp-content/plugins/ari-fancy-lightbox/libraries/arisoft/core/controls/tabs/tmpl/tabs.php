<?php
$stateless = $this->options->stateless;
$state_ctrl_id = $this->get_state_ctrl_id();
?>
<h2 class="nav-tab-wrapper ari-wp-tabs" id="<?php echo esc_attr( $this->id ); ?>"<?php if ( ! $stateless ): ?> data-tabs-state-ctrl="<?php echo esc_attr( '#' . $state_ctrl_id ); ?>"<?php endif; ?>>
    <?php
    foreach ( $this->options->items as $item ):
        $title = is_callable( $item->title ) ? call_user_func( $item->title ) : $item->title;
        ?>
        <a href="#" class="nav-tab<?php if ( $item->active ): ?> nav-tab-active<?php endif; ?>"><?php echo esc_html( $title ); ?></a>
    <?php
    endforeach;
    ?>
</h2>
<?php
foreach ( $this->options->items as $item ):
    ?>
    <div class="nav-container"<?php if ( ! $item->active ): ?> style="display:none;"<?php endif; ?>>
        <?php
        // Render predefined form elements. No user input
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo is_callable( $item->content ) ? call_user_func( $item->content ) : $item->content;
        ?>
    </div>
<?php
endforeach;
?>
<?php
if ( ! $stateless ):
    ?>
    <input type="hidden" id="<?php echo esc_attr( $state_ctrl_id ); ?>" name="<?php echo esc_attr( $state_ctrl_id ); ?>" />
<?php
endif;
?>