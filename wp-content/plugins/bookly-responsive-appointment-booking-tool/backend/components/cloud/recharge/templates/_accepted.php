<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<style>
    .bookly-success-icon {
        box-sizing: border-box;
        position: relative;
        display: block;
        width: 110px;
        height: 110px;
        border: 4px solid;
        border-radius: 50%
    }
    .bookly-success-icon::after {
        content: "";
        display: block;
        box-sizing: border-box;
        position: absolute;
        left: 20px;
        top: -35px;
        width: 35px;
        height: 89px;
        border-width: 0 4px 4px 0;
        border-style: solid;
        transform-origin: bottom left;
        transform: rotate(45deg)
    }
</style>
<h3 class="text-success text-center pb-0 mb-0"><?php esc_html_e( 'Thank you', 'bookly' ) ?>!</h3>
<div class="text-success py-5">
    <i class="mx-auto bookly-success-icon"></i>
</div>
<p class="text-center bookly-js-message"><?php esc_html_e( 'Your payment has been accepted for processing', 'bookly' ) ?>!</p>