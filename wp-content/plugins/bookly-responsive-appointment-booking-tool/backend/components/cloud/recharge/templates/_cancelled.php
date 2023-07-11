<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<style>
    .bookly-fail-icon {
        box-sizing: border-box;
        position: relative;
        display: block;
        width: 110px;
        height: 110px;
        border: 4px solid;
        border-radius: 50%;
    }
    .bookly-fail-icon::after,
    .bookly-fail-icon::before {
        content: "";
        display: block;
        box-sizing: border-box;
        position: absolute;
        width: 73px;
        height: 6px;
        background: #dc3545;
        transform: rotate(45deg);
        border-radius: 5px;
        top: 48px;
        left: 15px;
    }
    .bookly-fail-icon::after {
        transform: rotate(-45deg)
    }
</style>
<h3 class="text-danger text-center pb-0 mb-0"><?php esc_html_e( 'Oops', 'bookly' ) ?>!</h3>
<div class="text-danger py-5">
    <i class="mx-auto bookly-fail-icon"></i>
</div>
<p class="text-center bookly-js-message"><?php esc_html_e( 'Your payment has been cancelled', 'bookly' ) ?>!</p>