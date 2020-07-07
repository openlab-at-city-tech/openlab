<?php if(!defined('ABSPATH')) exit; ?>
<style>

    .updates-form-form {
        min-height: calc(100vh - 280px);
    }
    .popup-form-content {
        background: #ffffff;
        min-height: 100px;
        width: 450px;
        text-align: center;
        margin-top: 50px;
        border: solid 1px #c1c1c1;
    }
    .updates-content-buttons button {
        margin: 10px 3px !important;
        float: left;
    }
    .updates-content-buttons a span {
        -webkit-animation: fa-spin 0.75s infinite linear;
        animation: fa-spin 0.75s infinite linear;
    }
    .updates-content-buttons a:hover, .updates-content-buttons a:focus {
        color: #ffffff;
        background-image: linear-gradient(rgba(0,0,0,.1),rgba(0,0,0,.1));
    }
    .updates-content-buttons a:focus {
        outline: 0;
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(50,100,150,.4);
    }
    .updates-content-buttons button.form-cancel-btn {
        float: right !important;
    }
    .form-submit-btn {
        background-color: #3085d6;
    }
    .updates-content-buttons a span {
        -webkit-animation: fa-spin 0.75s infinite linear;
        animation: fa-spin 0.75s infinite linear;
    }
    .add-update-title {
        font-size: 20px;
        line-height: 30px;
        padding: 20px 20px 0;
    }
    .folder-update-input {
        padding: 10px 20px;
    }
    .folder-update-input input {
        width: 100%;
        transition: border-color .3s,box-shadow .3s;
        border: 1px solid #d9d9d9;
        border-radius: .1875em;
        font-size: 1.125em;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.06);
        box-sizing: border-box;
        height: 2.625em;
        margin: 1em auto;
    }
    .updates-content-buttons {
        background: #c1c1c1;
        padding: 0 20px;
    }

</style>
<div class="updates-form-form" >
    <div class="popup-form-content">
        <div id="add-update-title" class="add-update-title">
            Would you like to get feature updates for Folders in real-time?
        </div>
        <div class="folder-update-input">
            <input id="folder_update_email" autocomplete="off" value="<?php echo get_option( 'admin_email' ) ?>" placeholder="Email address">
        </div>
        <div class="updates-content-buttons">
            <button href="javascript:;" class="button button-primary form-submit-btn yes">Yes, I want</button>
            <button href="javascript:;" class="button button-secondary form-cancel-btn no">Skip</button>
            <div style="clear: both"></div>
        </div>
        <input type="hidden" id="folder_update_status" value="<?php echo wp_create_nonce("folder_update_status") ?>">
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        $(document).on("click", ".updates-content-buttons button", function () {
            var updateStatus = 0;
            if ($(this).hasClass("yes")) {
                updateStatus = 1;
            }
            $(".updates-content-buttons button").attr("disabled", true);
            $.ajax({
                url: ajaxurl,
                data: "action=folder_update_status&status=" + updateStatus + "&nonce=" + $("#folder_update_status").val() + "&email=" + $("#folder_update_email").val(),
                type: 'post',
                cache: false,
                success: function () {
                    window.location.reload();
                }
            })
        });
    });
</script>