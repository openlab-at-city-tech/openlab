<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/** $this is available here because the file is required from your class method */
$locked = method_exists( $this, 'is_legacy_enabled' ) && $this->is_legacy_enabled();

?>
<div id="trp-settings-page" class="wrap">
    <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>
    <?php do_action( 'trp_settings_navigation_tabs' ); ?>

    <?php if ( $locked ) : ?>
        <div class="trp-ls-lock-note" role="region" aria-label="<?php esc_attr_e( 'Language Switcher update notice', 'translatepress-multilingual' ); ?>">
            <div class="trp-ls-lock-note__content">
                <h2><?php esc_html_e( 'Legacy language switcher is currently enabled', 'translatepress-multilingual' ); ?></h2>
                <p>
                    <?php
                    /* translators: Explain where to toggle legacy back on */
                    echo wp_kses_post(
                        __( 'We’ve upgraded the switcher for richer customization and a better user experience.<br>In order to use the new configurator, turn off
                            <strong>Load legacy language switcher</strong>.', 'translatepress-multilingual' )
                    );
                    ?>
                </p>
                <span class="trp-description-text">
                    <?php
                        echo wp_kses(
                            sprintf(
                                __(
                                    'Note: You can switch back anytime from <strong>Advanced Settings → <a href="%s">Troubleshooting</a></strong>.',
                                    'translatepress-multilingual'
                                ),
                                esc_url( admin_url( 'admin.php?page=trp_advanced_page&tab=troubleshooting' ) )
                            ),
                            array(
                                'strong' => array(),
                                'a'      => array(
                                    'href' => array(),
                                ),
                            )
                        );
                    ?>
                </span>

                <button
                        id="trp-start-new-switcher"
                        class="trp-submit-btn button"
                        data-nonce="<?php echo esc_attr( wp_create_nonce( 'trp_disable_legacy' ) ); ?>"
                        type="button"
                >
                    <?php esc_html_e( 'Enable the new switcher', 'translatepress-multilingual' ); ?>
                </button>

                <div class="trp-ls-lock-error" aria-live="polite"></div>
            </div>
        </div>
    <?php endif; ?>

    <div class="trp-ls-configurator<?php echo $locked ? ' is-locked' : ''; ?>" aria-disabled="<?php echo $locked ? 'true' : 'false'; ?>">
        <!-- Vue mounts here -->
        <div id="tp-language-switcher-root" <?php echo $locked ? 'inert' : ''; ?>></div>
    </div>
</div>

<?php if ( $locked ) : ?>
    <script>
        ( function () {
            const btn = document.getElementById( 'trp-start-new-switcher' );
            if ( ! btn ) return;

            btn.addEventListener( 'click', async function () {
                const nonce = btn.getAttribute( 'data-nonce' );
                btn.disabled = true;
                btn.classList.add( 'is-busy' );

                try {
                    const res = await fetch( window.ajaxurl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                        body: new URLSearchParams({
                           action: 'trp_disable_legacy_language_switcher',
                           nonce: nonce
                        })
                    } );

                    const json = await res.json();

                    if ( ! json.success ) {
                        throw new Error( json.data || 'Unknown error' );
                    }

                    const shell = document.querySelector( '.trp-ls-configurator' );
                    const root  = document.getElementById( 'tp-language-switcher-root' );
                    if ( shell ) shell.classList.remove( 'is-locked' );
                    if ( root )  root.removeAttribute( 'inert' );

                    const note = document.querySelector( '.trp-ls-lock-note' );
                    if ( note ) note.remove();

                } catch ( e ) {
                    const err = document.querySelector( '.trp-ls-lock-error' );
                    if ( err ) err.textContent = e.message || String( e );
                } finally {
                    btn.disabled = false;
                    btn.classList.remove( 'is-busy' );
                }
            } );
        } )();
    </script>
<?php endif; ?>
