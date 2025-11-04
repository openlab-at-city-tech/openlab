<?php
/**
 * Title: Footer
 * Slug: patterns-docs/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: A pattern for displaying the site footer.
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:pattern {"slug":"patterns-docs/featured-section-5"} /-->

<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"0"}},"backgroundColor":"quinary","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-quinary-background-color has-background">
	
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"120px"},"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}}}} -->
	<div class="wp-block-columns alignwide" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)">
		

		<!-- wp:column {"width":"60%","style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
		<div class="wp-block-column" style="flex-basis:60%">

			<!-- wp:heading {"level":6,"fontSize":"small"} -->
			<h6 class="wp-block-heading has-small-font-size"><?php esc_html_e( 'About Us', 'patterns-docs' ); ?></h6>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in', 'patterns-docs' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:social-links {"iconColor":"base","iconColorValue":"#1d2746","className":"is-style-logos-only"} -->
			<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"twitter"} /-->

			<!-- wp:social-link {"url":"#","service":"instagram"} /-->

			<!-- wp:social-link {"url":"#","service":"whatsapp"} /--></ul>
			<!-- /wp:social-links -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"20%","style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
		<div class="wp-block-column" style="flex-basis:20%">
			
			<!-- wp:heading {"level":6,"fontSize":"small"} -->
			<h6 class="wp-block-heading has-small-font-size"><?php esc_html_e( 'Quick Links', 'patterns-docs' ); ?></h6>
			<!-- /wp:heading -->     

			<!-- wp:navigation {"overlayMenu":"never","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical"}} -->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'General Setting', 'patterns-docs' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Getting Started', 'patterns-docs' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Installation', 'patterns-docs' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Introduction', 'patterns-docs' ); ?>","url":"#"} /-->
			<!-- /wp:navigation -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"20%","style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
		<div class="wp-block-column" style="flex-basis:20%">
			
			<!-- wp:heading {"level":6,"fontSize":"small"} -->
			<h6 class="wp-block-heading has-small-font-size"><?php esc_html_e( 'Contact', 'patterns-docs' ); ?></h6>
			<!-- /wp:heading -->  
			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group"><!-- wp:paragraph {"className":"pwp-txt-dec-non ","style":{"elements":{"link":{"color":{"text":"var:preset|color|tertiary"},":hover":{"color":{"text":"var:preset|color|primary"}}}}}} -->
			<p class="pwp-txt-dec-non has-link-color"><a href="<?php echo esc_url( 'tel:000 - 9874 563 210' ); ?>"><?php esc_html_e( '000 - 9874 563 210', 'patterns-docs' ); ?></a></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"pwp-txt-dec-non ","style":{"elements":{"link":{":hover":{"color":{"text":"var:preset|color|primary"}},"color":{"text":"var:preset|color|tertiary"}}}}} -->
			<p class="pwp-txt-dec-non has-link-color"><a href="<?php echo esc_url( 'mailto:info@example.com' ); ?>"><?php esc_html_e( 'info@example.com', 'patterns-docs' ); ?></a></p>
			<!-- /wp:paragraph --></div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"align":"full","style":{"border":{"top":{"color":"var:preset|color|quaternary","style":"solid","width":"1px"}},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"backgroundColor":"default","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull has-default-background-color has-background" style="border-top-color:var(--wp--preset--color--quaternary);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">        
   
		<!-- wp:pattern {"slug":"patterns-docs/copyright"} /-->
	</div>
	<!-- /wp:group -->

<!-- wp:pattern {"slug":"patterns-docs/scroll-to-top-button"} /-->
 
</div>
<!-- /wp:group -->
