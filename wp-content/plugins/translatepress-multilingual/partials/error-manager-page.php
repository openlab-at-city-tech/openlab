<div id="trp-errors-page" class="wrap">

    <h1> <?php esc_html_e( 'TranslatePress Errors', 'translatepress-multilingual' );?></h1>
    <?php $page_output = apply_filters( 'trp_error_manager_page_output', '' );
    if ( $page_output === '' ){
        $page_output = esc_html__('There are no logged errors.', 'translatepress-multilingual');
    }

    echo $page_output; /* phpcs:ignore */ /* sanitized in the functions hooked to the filters */

    ?>

</div>
