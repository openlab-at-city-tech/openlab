<?php if ( has_nav_menu( 'typology_social_menu' ) ) : ?>
<li class="typology-actions-button typology-social-icons">
	<span>
		<i class="fa fa-share-alt"></i>
	</span>
	<ul class="sub-menu">
        <li>
            <?php wp_nav_menu( array( 'theme_location' => 'typology_social_menu', 'container'=> '', 'menu_class' => 'typology-soc-menu', 'link_before' => '<span class="typology-social-name">', 'link_after' => '</span>' ) ); ?>
        </li>
	</ul>
</li>
<?php endif; ?>