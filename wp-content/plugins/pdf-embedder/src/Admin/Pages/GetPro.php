<?php

namespace PDFEmbedder\Admin\Pages;

use PDFEmbedder\Helpers\Links;
use PDFEmbedder\Helpers\Assets;

class GetPro extends Page {

	public const SLUG = 'get-pro';

	public function get_title(): string {

		return __( 'Get Pro', 'pdf-embedder' );
	}

	public function content() {
		?>

		<h3><?php esc_html_e( 'Unlock the Full Power of PDF Embedder Pro!', 'pdf-embedder' ); ?></h3>

		<p>
			<?php esc_html_e( "You've experienced the basics, now take your WordPress PDF integration to the next level. As a valued PDF Embedder user, you're just one step away from accessing a suite of advanced features that will transform your site's document presentation.", 'pdf-embedder' ); ?>
		</p>

		<p>
			<?php
			echo wp_kses(
				__( "We know that you'll truly love PDF Embedder. <strong>It has over 300+ five-star ratings (⭐⭐⭐⭐⭐) and is active on over 300,000 websites!", 'pdf-embedder' ),
				[
					'strong' => [],
				]
			);
			?>
		</p>

		<div id="pdfemb-get-pro-section-banner">

			<h3><?php esc_html_e( 'Why upgrade to Pro?', 'pdf-embedder' ); ?></h3>

			<div class="pdfemb-reasons">
				<ul>
					<li><?php esc_html_e( 'Mobile-Ready', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Track PDF Views & Downloads', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Jump to any Page', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Continuous Scroll Navigation', 'pdf-embedder' ); ?></li>
				</ul>
				<ul>
					<li><?php esc_html_e( 'Enhanced PDF Security', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Custom Watermarks', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Download Button Controls', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Priority Support', 'pdf-embedder' ); ?></li>
				</ul>
			</div>

			<div class="pdfemb-get-pro-button">
				<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/pricing/', 'Admin - Get Pro', 'Upgrade to Pro' ) ); ?>" class="button button-primary" target="_blank">
					<?php esc_html_e( 'Upgrade to PDF Embedder Pro', 'pdf-embedder' ); ?>
				</a>

				<p class="discount">
					<img src="<?php echo esc_url( Assets::url( 'img/admin/discount.svg' ) ); ?>" alt="" /> <span>50% OFF</span> for PDF Embedder users, applied at checkout.
				</p>
			</div>
		</div>

		<?php
	}
}
