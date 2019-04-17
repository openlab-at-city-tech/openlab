<?php get_header(); ?>

<div class="johannes-section">
    <div class="container">
        <div class="section-content row justify-content-center container-404">

            <div class="col-12 col-lg-7">
                <span class="h0">404</span>
                <h1 class="title-404"><?php echo johannes_get( 'title' ); ?></h1>
            </div>

            <div class="col-12 col-lg-5 search-alt">
                <p><?php echo johannes_get( 'text' ); ?></p>
                <?php get_search_form(); ?>
            </div>

        </div>
    </div>
</div>

<?php get_footer(); ?>