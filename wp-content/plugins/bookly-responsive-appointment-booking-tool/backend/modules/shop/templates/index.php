<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components;
use Bookly\Backend\Components\Support;

?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Addons', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-auto">
                    <div class="form-group">
                        <select class="form-control bookly-js-select" id="bookly-shop-sort" data-placeholder="<?php echo esc_attr( __( 'Sort by', 'bookly' ) ) ?>">
                            <option></option>
                            <option value="sales"<?php selected( ! $has_new_items ) ?>><?php esc_html_e( 'Best Sellers', 'bookly' ) ?></option>
                            <option value="rating"><?php esc_html_e( 'Best Rated', 'bookly' ) ?></option>
                            <option value="date"<?php selected( $has_new_items ) ?>><?php esc_html_e( 'Newest Items', 'bookly' ) ?></option>
                            <option value="price_low"><?php esc_html_e( 'Price: low to high', 'bookly' ) ?></option>
                            <option value="price_high"><?php esc_html_e( 'Price: high to low', 'bookly' ) ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-group justify-content-start" id="bookly-shop">
            </div>
            <div id="bookly-shop-loading" class="bookly-loading"></div>
        </div>
    </div>
</div>
<template id="bookly-shop-template" class="hidden">
    <div class="{{plugin_class}} card mb-4 mr-4 border rounded">
        <div class="card-header p-0">
            <img class="card-img-top rounded-top" style="height: 270px;object-fit: cover;" loading="lazy" src="{{image}}" alt="{{title}}"/>
        </div>
        <div class="card-body">
            <div class="d-flex mb-3">
                <div class="flex-fill">
                    <div class="h5 mb-0"><a href="{{url}}" target="_blank" class="text-bookly">{{title}}</a> <span class="badge badge-danger">{{new}}</span></div>
                </div>
                <div class="text-right">
                    <div class="h5 mb-0">{{price}}</div>
                </div>
            </div>
            <div class="d-flex mb-3">
                <div class="flex-fill">
                    <span class="text-warning {{rating_class}}">{{rating}}</span>
                    <span>({{reviews}})</span>
                </div>
                <div class="text-right">
                    <i class="fa fas fa-fw fa-shopping-cart mr-2"></i>{{sales}}
                </div>
            </div>
            <hr/>
            <div class="card-text">
                {{description}}
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex">
                <div class="flex-fill">
                    <div class="{{demo_url_class}}">
                        <a href="{{demo_url}}" class="btn btn-primary" target="_blank"><b><?php esc_html_e( 'Demo', 'bookly' ) ?></b></a>
                    </div>
                </div>
                <div class="text-right">
                    <a href="{{url}}" class="btn {{url_class}}" target="_blank">{{url_text}}</a>
                </div>
            </div>
        </div>
    </div>
</template>
