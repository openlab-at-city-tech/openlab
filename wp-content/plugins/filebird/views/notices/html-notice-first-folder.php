<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="notice notice-info is-dismissible filebird-notice" id="filebird-empty-folder-notice">
    <p>
        <?php esc_html_e( 'Create your first folder for media library now.', 'filebird' ); ?>
        <a href="<?php echo esc_url( admin_url( '/upload.php' ) ); ?>">
            <strong><?php esc_html_e( 'Get Started', 'filebird' ); ?></strong>
        </a>
    </p>
</div>
<script>
document.addEventListener("click", function (e) {
	if (!e.target.closest("#filebird-empty-folder-notice button.notice-dismiss")) return;

	var data = new FormData();
	data.append("action", "fbv_first_folder_notice");
	data.append("nonce", window.fbv_data.nonce);

	if (window.fetch) {
        fetch(window.ajaxurl, { method: "POST", body: data });
    } else {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", window.ajaxurl);
        xhr.send(data);
    }
});
</script>