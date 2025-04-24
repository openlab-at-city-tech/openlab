<div class="trp-test-api-key-popup-overlay">
    <div class="trp-loading-spinner"></div>
    <div class="trp-test-api-key-popup">
        <div class="trp-test-api-key__header">
            <h3 class="trp-settings-primary-heading"><?php esc_html_e( 'Test API Credentials', 'translatepress-multilingual' );?></h3>
            <div class="trp-test-api-key-close-btn">
                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.5 2L1.5 15M1.5 2L14.5 15" stroke="#1D2327" stroke-width="2.16667" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="trp-test-api-key-referrer__wrapper">
            <div class="trp-test-api-key-referrer">
                <span class="trp-settings-secondary-heading"><?php esc_html_e( 'HTTP Referrer: ', 'translatepress-multilingual' ); ?></span>
                <span class="trp-settings-secondary-heading trp-referrer-name"></span>
            </div>
            <span class="trp-primary-text"><?php esc_html_e( 'Use this HTTP Referrer if the API lets you restrict key usage from its Dashboard.', 'translatepress-multilingual' );?></span>
        </div>

        <div class="trp-test-api-key-response trp-test-api-key-response__wrapper">
            <span class="trp-settings-secondary-heading"><?php esc_html_e( 'Response', 'translatepress-multilingual' );?></span>
            <div class="trp-settings-container"></div>
        </div>

        <div class="trp-test-api-key-response-body trp-test-api-key-response__wrapper">
            <span class="trp-settings-secondary-heading"><?php esc_html_e( 'Response Body', 'translatepress-multilingual' );?></span>
            <div class="trp-settings-container"></div>
        </div>

        <div class="trp-test-api-key-response-full trp-test-api-key-response__wrapper">
            <span class="trp-settings-secondary-heading"><?php esc_html_e( 'Entire Response From wp_remote_get():', 'translatepress-multilingual' );?></span>
            <div class="trp-settings-container"></div>
        </div>

    </div>
</div>