
			<footer id="typology-footer" class="typology-footer">
                
                <?php $footer_cols_and_sidebars = typology_get_footer_cols_and_sidebars(); ?>
                
                <?php if(!empty($footer_cols_and_sidebars)): ?>
                    
                    <div class="container">
                        <?php foreach ($footer_cols_and_sidebars as $col_and_sidebar) :?>
	
	                        <?php if( is_active_sidebar( $col_and_sidebar['sidebar'] ) ) : ?>
                                <div class="col-lg-<?php echo absint($col_and_sidebar['col'])?> typology-footer-sidebar"><?php dynamic_sidebar( $col_and_sidebar['sidebar'] );?></div>
	                        <?php endif; ?>
                        <?php endforeach; ?>
    
                    </div>

                <?php endif; ?>
			</footer>

		</div>

		<?php get_sidebar(); ?>
		
		<?php wp_footer(); ?>

	</body>
</html>