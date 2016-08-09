<?php
/**
 * Template Name: WeBWorK
 */

get_header(); ?>

<div id="webwork-app" class="webwork-app">
Loading...
</div><!-- .content-area -->

<?php

\WeBWorK\Client::set_up_app();

get_footer(); ?>
