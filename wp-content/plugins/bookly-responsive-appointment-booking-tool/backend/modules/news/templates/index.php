<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Controls;
/** @var Bookly\Lib\Entities\News[] $news_list */
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'News', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card-group justify-content-start" id="bookly-news-list">
    </div>
    <div class="w-100 text-center mt-4">
        <?php Controls\Buttons::render( 'bookly-more-news', 'btn-primary btn-lg', __( 'More news', 'bookly' ) ) ?>
    </div>

    <div class="bookly-collapse" id="bookly-news-template">
        <div class="card m-3 border rounded{{border}}" style="max-width: 476px; min-width: 476px;">
            {{media}}
            <div class="card-body">
                <h5 class="card-title">{{title}}</h5>
                <p class="card-text">
                    {{text}}
                    {{button}}
                </p>
            </div>
            <div class="card-footer">
                <small class="text-muted">{{date}}</small>
            </div>
        </div>
    </div>
</div>