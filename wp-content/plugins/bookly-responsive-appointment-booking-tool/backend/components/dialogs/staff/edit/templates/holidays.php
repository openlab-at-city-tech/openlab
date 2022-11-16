<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div class="bookly-js-holidays-nav text-center">
    <div class="btn-group btn-group-lg" role="group">
        <button class="btn btn-default bookly-js-jCalBtn" data-trigger=".jCal .left" type="button">
            <i class="fas fa-fw fa-angle-left"></i>
        </button>
        <button class="btn btn-default jcal_year" type="button" disabled="disabled"></button>
        <button class="btn btn-default bookly-js-jCalBtn" data-trigger=".jCal .right" type="button">
            <i class="fas fa-fw fa-angle-right"></i>
        </button>
    </div>
</div>
<div class="bookly-js-holidays jCal-wrap mt-4"></div>