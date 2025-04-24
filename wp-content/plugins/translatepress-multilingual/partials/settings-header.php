<?php
    $full_logo = TRP_PLUGIN_URL . 'assets/images/tp-logo-with-text-dark.svg';
    $small_logo = TRP_PLUGIN_URL . 'assets/images/tp-logo.png';
?>

<div id="trp-settings-header">
    <div class="trp-settings-logo">
        <img src="<?php echo esc_url( $full_logo ); ?>"
             srcset="<?php echo esc_url( $small_logo ); ?> 128w, <?php echo esc_url( $full_logo ); ?> 177w"
             sizes="(max-width: 520px) 40px, 177px"
             alt="TranslatePress Logo">
    </div>

    <div id="trp-header-items-wrapper">
        <a class="trp-header-link" href="https://translatepress.com/support/">
            <svg class="trp-header-link-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 15V17C14 17.2652 13.8946 17.5196 13.7071 17.7071C13.5196 17.8946 13.2652 18 13 18H6L3 21V11C3 10.7348 3.10536 10.4804 3.29289 10.2929C3.48043 10.1054 3.73478 10 4 10H6M21 14L18 11H11C10.7348 11 10.4804 10.8946 10.2929 10.7071C10.1054 10.5196 10 10.2652 10 10V4C10 3.73478 10.1054 3.48043 10.2929 3.29289C10.4804 3.10536 10.7348 3 11 3H20C20.2652 3 20.5196 3.10536 20.7071 3.29289C20.8946 3.48043 21 3.73478 21 4V14Z" stroke="#949494" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="trp-header-item-text trp-primary-text">Support</span>
        </a>
        <a class="trp-header-link" href="https://translatepress.com/docs/translatepress/">
            <svg class="trp-header-link-icon" width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 1V5C10 5.26522 10.1054 5.51957 10.2929 5.70711C10.4804 5.89464 10.7348 6 11 6H15M10 1H3C2.46957 1 1.96086 1.21071 1.58579 1.58579C1.21071 1.96086 1 2.46957 1 3V17C1 17.5304 1.21071 18.0391 1.58579 18.4142C1.96086 18.7893 2.46957 19 3 19H13C13.5304 19 14.0391 18.7893 14.4142 18.4142C14.7893 18.0391 15 17.5304 15 17V6M10 1L15 6M5 15H11M5 11H11" stroke="#949494" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="trp-header-item-text trp-primary-text">Documentation</span>
        </a>
        <a id="trp-upgrade-now-button" class="trp-header-link" href="https://translatepress.com/pricing/">Upgrade now</a>
    </div>
</div>
<h1></h1> <!-- Needed for error placement. WordPress positions them relative to the h1 -->