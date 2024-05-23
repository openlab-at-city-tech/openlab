<?php
 /**
  * Title: Recent Blog
  * Slug: fse-freelancer-portfolio/recent-blog
  */
?>
<!-- wp:group {"align":"full","className":"blog-section","layout":{"inherit":true}} -->
<div class="wp-block-group alignfull blog-section">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":""} -->
		<div class="wp-block-column">
			<!-- wp:query {"queryId":3,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"flex","columns":3},"layout":{"inherit":true}} -->
			<div class="wp-block-query">
				<!-- wp:post-template {"align":"full"} -->
				<!-- wp:group {"layout":{"inherit":true}} -->
				<div class="wp-block-group">
					<!-- wp:post-featured-image {"isLink":true,"align":"full"} /-->
					<!-- wp:group {"align":"full","className":"alignfull wp-block-post-container"} -->
					<div class="wp-block-group alignfull wp-block-post-container">
						<!-- wp:group {"className":"wp-block-post-meta","layout":{"type":"flex","allowOrientation":false}} -->
						<div class="wp-block-group wp-block-post-meta">
							<!-- wp:post-date {"isLink":true} /-->
						</div>
						<!-- /wp:group -->
						<!-- wp:post-title {"isLink":true} /-->
						<!-- wp:post-excerpt {"moreText":"Continue Reading"} /-->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->
				<!-- /wp:post-template -->
				<!-- wp:query-pagination {"align":"full","layout":{"type":"flex","justifyContent":"space-between"}} -->
				<!-- wp:query-pagination-previous {"fontSize":"small"} /-->
				<!-- wp:query-pagination-numbers /-->
				<!-- wp:query-pagination-next {"fontSize":"small"} /-->
				<!-- /wp:query-pagination -->
			</div>
			<!-- /wp:query -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
