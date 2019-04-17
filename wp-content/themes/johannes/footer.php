<?php if ( johannes_get( 'display', 'footer' ) ): ?>

    <?php if ( johannes_get( 'footer', 'instagram' ) ): ?>
        <?php get_template_part( 'template-parts/footer/instagram' ); ?>
    <?php endif; ?>

    <?php get_template_part( 'template-parts/ads/above-footer' ); ?>

    <footer id="johannes-footer" class="johannes-footer">
        <div class="container">

            <?php if ( johannes_get( 'footer', 'widgets' ) ) : ?>
                <div class="footer-divider"></div>
                <?php get_template_part( 'template-parts/footer/widgets' ); ?>
            <?php endif; ?>

            <?php if ( johannes_get( 'footer', 'copyright' ) ) : ?>
                <div class="johannes-copyright">
                    <p><?php echo do_shortcode( wp_kses_post( johannes_get( 'footer', 'copyright' ) ) ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </footer>

    <?php if ( johannes_get( 'footer', 'popup' ) ) : ?>
        <?php get_template_part( 'template-parts/footer/gallery-placeholder' ); ?>
    <?php endif; ?>

<?php endif; ?>

</div>


<?php if( johannes_get( 'footer', 'go_to_top' ) ) : ?>
    <a href="javascript:void(0)" id="johannes-goto-top" class="johannes-goto-top"><i class="jf jf-chevron-up"></i></a>
<?php endif; ?>

<?php get_template_part( 'template-parts/footer/overlay' ); ?>
<?php get_template_part( 'template-parts/footer/hidden-sidebar' ); ?>

<?php wp_footer(); ?>
</body>

</html>