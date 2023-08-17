<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="bookly-js-dashboard-appointments">
    <div class="row pb-3">
        <div class="col-md-6 col-lg-3">
            <div class="form-row">
                <div class="col-4 text-right d-none d-md-block">
                    <i class="far fa-calendar-check fa-4x fa-fw text-muted"></i>
                </div>
                <div class="col-md-6 col-lg-8">
                    <span style="font-size: 30px" class="bookly-js-approved d-md-block">&nbsp;</span>
                    <span style="font-size: 20px"><a href="#" class="bookly-js-href-approved text-wrap"><?php esc_html_e( 'Approved appointments', 'bookly' ) ?></a></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-row">
                <div class="col-4 text-right d-none d-md-block">
                    <i class="far fa-hourglass fa-4x fa-fw text-muted"></i>
                </div>
                <div class="col-md-6 col-lg-8">
                    <span style="font-size: 30px" class="bookly-js-pending d-md-block">&nbsp;</span>
                    <span style="font-size: 20px"><a href="#" class="bookly-js-href-pending text-wrap"><?php esc_html_e( 'Pending appointments', 'bookly' ) ?></a></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-row">
                <div class="col-4 text-right d-none d-md-block">
                    <i class="far fa-calendar fa-4x fa-fw text-muted"></i>
                </div>
                <div class="col-md-6 col-lg-8">
                    <span style="font-size: 30px" class="bookly-js-total d-md-block">&nbsp;</span>
                    <span style="font-size: 20px"><a href="#" class="bookly-js-href-total text-wrap"><?php esc_html_e( 'Total appointments', 'bookly' ) ?></a></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-row">
                <div class="col-4 text-right pr-3 d-none d-md-block">
                    <i class="far fa-money-bill-alt fa-4x fa-fw text-muted"></i>
                </div>
                <div class="col-md-6 col-lg-8">
                    <span style="font-size: 30px" class="bookly-js-revenue d-md-block">&nbsp;</span>
                    <span style="font-size: 20px"><a href="#" class="bookly-js-href-revenue text-wrap"><?php esc_html_e( 'Revenue', 'bookly' ) ?></a></span>
                </div>
            </div>
        </div>
    </div>
    <div>
        <canvas id="canvas" class="w-100" style="height: 500px"></canvas>
    </div>
</div>