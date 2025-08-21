jQuery(document).ready(function ($) {
    // Attach click event to the dismiss button
    $(document).on('click', '.notice[data-notice="get-start"] button.notice-dismiss', function () {
        // Dismiss the notice via AJAX
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'cv_portfolio_blocks_dismissed_notice',
            },
            success: function () {
                // Remove the notice on success
                $('.notice[data-notice="example"]').remove();
            }
        });
    });
});

// WordClever – AI Content Writer plugin activation
document.addEventListener('DOMContentLoaded', function () {
    const cv_portfolio_blocks_button = document.getElementById('install-activate-button');

    if (!cv_portfolio_blocks_button) return;

    cv_portfolio_blocks_button.addEventListener('click', function (e) {
        e.preventDefault();

        const cv_portfolio_blocks_redirectUrl = cv_portfolio_blocks_button.getAttribute('data-redirect');

        // Step 1: Check if plugin is already active
        const cv_portfolio_blocks_checkData = new FormData();
        cv_portfolio_blocks_checkData.append('action', 'check_wordclever_activation');

        fetch(installWordcleverData.ajaxurl, {
            method: 'POST',
            body: cv_portfolio_blocks_checkData,
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data.active) {
                // Plugin is already active → just redirect
                window.location.href = cv_portfolio_blocks_redirectUrl;
            } else {
                // Not active → proceed with install + activate
                cv_portfolio_blocks_button.textContent = 'Installing & Activating...';

                const cv_portfolio_blocks_installData = new FormData();
                cv_portfolio_blocks_installData.append('action', 'install_and_activate_wordclever_plugin');
                cv_portfolio_blocks_installData.append('_ajax_nonce', installWordcleverData.nonce);

                fetch(installWordcleverData.ajaxurl, {
                    method: 'POST',
                    body: cv_portfolio_blocks_installData,
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        window.location.href = cv_portfolio_blocks_redirectUrl;
                    } else {
                        alert('Activation error: ' + (res.data?.message || 'Unknown error'));
                        cv_portfolio_blocks_button.textContent = 'Try Again';
                    }
                })
                .catch(error => {
                    alert('Request failed: ' + error.message);
                    cv_portfolio_blocks_button.textContent = 'Try Again';
                });
            }
        })
        .catch(error => {
            alert('Check request failed: ' + error.message);
        });
    });
});


