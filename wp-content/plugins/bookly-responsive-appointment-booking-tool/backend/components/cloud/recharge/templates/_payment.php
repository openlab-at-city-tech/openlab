<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="row bg-light py-3 pl-3 mt-n3 mb-3">
    <button class="btn btn-link bookly-js-back" type="button" ><i class="fas fa-fw fa-chevron-left ml-1"></i><?php esc_html_e( 'Back to the list of amounts', 'bookly' ) ?></button>
</div>
<h4 class="text-center pb-2"><?php esc_html_e( 'Please select a payment method', 'bookly' ) ?></h4>
<div class="row justify-content-center pb-0 mt-3">
    <div class="col col-lg-9 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="bookly-js-pay card text-white bg-primary ladda-button" data-gateway="paypal" style="cursor: pointer" data-style="zoom-in">
                    <div class="card-body">
                        <div class="form-row align-items-center">
                            <div class="col-2"><i class="fab fa-3x fa-paypal"></i></div>
                            <div class="col-6">
                                <div class="bookly-js-action"></div>
                                <div class="h2 font-weight-bold mb-0">PayPal</div>
                            </div>
                            <div class="col-4">
                                <span class="h2">$</span><span class="display-4 font-weight-bold bookly-js-amount text-nowrap"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="bookly-pay-card">
                    <div class="form-row my-2">
                        <div class="col"><hr/></div>
                        <div class="col-auto"><h5 class="text-muted"><?php esc_html_e( 'or', 'bookly' ) ?></h5></div>
                        <div class="col"><hr/></div>
                    </div>

                    <div class="bookly-js-pay card text-white bg-success ladda-button" data-gateway="card" style="cursor: pointer" data-style="zoom-in">
                        <div class="card-body">
                            <div class="form-row align-items-center">
                                <div class="col-2"><i class="far fa-3x fa-credit-card"></i></div>
                                <div class="col-6">
                                    <div class="bookly-js-action"></div>
                                    <div class="h2 font-weight-bold mb-0"><?php esc_html_e( 'Credit card', 'bookly' ) ?></div>
                                </div>
                                <div class="col-4">
                                    <span class="h2">$</span><span class="display-4 font-weight-bold bookly-js-amount text-nowrap"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3 text-center" style="color:#595959">
    <div class="col"><i class="fab fa-2x fa-cc-paypal"></i></div>
    <div class="col"><i class="fab fa-2x fa-cc-mastercard"></i></div>
    <div class="col"><i class="fab fa-2x fa-cc-visa"></i></div>
    <div class="col"><i class="fab fa-2x fa-cc-amex"></i></div>
    <div class="col"><i class="fab fa-2x fa-cc-discover"></i></div>
</div>