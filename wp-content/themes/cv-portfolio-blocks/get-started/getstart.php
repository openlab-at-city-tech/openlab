<?php
/**
 * Admin functions.
 *
 * @package CV Portfolio Blocks
 */

define('CV_PORTFOLIO_BLOCKS_SUPPORT',__('https://wordpress.org/support/theme/cv-portfolio-blocks/','cv-portfolio-blocks'));
define('CV_PORTFOLIO_BLOCKS_REVIEW',__('https://wordpress.org/support/theme/cv-portfolio-blocks/reviews/#new-post','cv-portfolio-blocks'));
define('CV_PORTFOLIO_BLOCKS_BUY_NOW',__('https://www.wpradiant.net/products/cv-wordpress-theme','cv-portfolio-blocks'));
define('CV_PORTFOLIO_BLOCKS_DOC_URL',__('https://preview.wpradiant.net/tutorial/cv-portfolio-blocks/','cv-portfolio-blocks'));
define('CV_PORTFOLIO_BLOCKS_LIVE_DEMO',__('https://preview.wpradiant.net/cv-portfolio-blocks/','cv-portfolio-blocks'));
define('CV_PORTFOLIO_BLOCKS_PRO_DOC',__('https://preview.wpradiant.net/tutorial/cv-portfolio-blocks-pro/','cv-portfolio-blocks'));

/**
 * Register admin page.
 *
 * @since 1.0.0
 */

function cv_portfolio_blocks_admin_menu_page() {

	$cv_portfolio_blocks_theme = wp_get_theme( get_template() );

	add_theme_page(
		$cv_portfolio_blocks_theme->display( 'Name' ),
		$cv_portfolio_blocks_theme->display( 'Name' ),
		'manage_options',
		'cv-portfolio-blocks',
		'cv_portfolio_blocks_do_admin_page'
	);

}
add_action( 'admin_menu', 'cv_portfolio_blocks_admin_menu_page' );

function cv_portfolio_blocks_admin_theme_style() {
	wp_enqueue_style('cv-portfolio-blocks-custom-admin-style', esc_url(get_template_directory_uri()) . '/get-started/getstart.css');
	wp_enqueue_script( 'admin-notice-script', get_template_directory_uri() . '/get-started/js/admin-notice-script.js', array( 'jquery' ) );
    wp_localize_script('admin-notice-script', 'example_ajax_obj', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'cv_portfolio_blocks_admin_theme_style');

/**
 * Render admin page.
 *
 * @since 1.0.0
 */
function cv_portfolio_blocks_do_admin_page() {

	$cv_portfolio_blocks_theme = wp_get_theme( get_template() );
	?>
	<div class="cv-portfolio-blocks-appearence wrap about-wrap">
		<div class="head-btn">
			<div><h1><?php echo $cv_portfolio_blocks_theme->display( 'Name' ); ?></h1></div>
			<div class="demo-btn">
				<span>
					<a class="button button-pro" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_BUY_NOW ); ?>" target="_blank"><?php esc_html_e( 'Buy Now', 'cv-portfolio-blocks' ); ?></a>
				</span>
				<span>
					<a class="button button-demo" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_LIVE_DEMO ); ?>" target="_blank"><?php esc_html_e( 'Demo', 'cv-portfolio-blocks' ); ?></a>
				</span>
				<span>
					<a class="button button-doc" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_PRO_DOC ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'cv-portfolio-blocks' ); ?></a>
				</span>
			</div>
		</div>
		
		<div class="two-col">

			<div class="about-text">
				<?php
					$description_raw = $cv_portfolio_blocks_theme->display( 'Description' );
					$main_description = explode( 'Official', $description_raw );
					?>
				<?php echo wp_kses_post( $main_description[0] ); ?>
			</div><!-- .col -->

			<div class="about-img">
				<a href="<?php echo esc_url( $cv_portfolio_blocks_theme->display( 'ThemeURI' ) ); ?>" target="_blank"><img src="<?php echo trailingslashit( get_template_directory_uri() ); ?>screenshot.png" alt="<?php echo esc_attr( $cv_portfolio_blocks_theme->display( 'Name' ) ); ?>" /></a>
			</div><!-- .col -->

		</div><!-- .two-col -->

  <nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu', 'cv-portfolio-blocks' ); ?>">
    <a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cv-portfolio-blocks' ), 'themes.php' ) ) ); ?>" class="nav-tab<?php echo ( isset( $_GET['page'] ) && 'cv-portfolio-blocks' === $_GET['page'] && ! isset( $_GET['tab'] ) ) ?' nav-tab-active' : ''; ?>"><?php esc_html_e( 'About', 'cv-portfolio-blocks' ); ?></a>

    <a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cv-portfolio-blocks', 'tab' => 'free_vs_pro' ), 'themes.php' ) ) ); ?>" class="nav-tab<?php echo ( isset( $_GET['tab'] ) && 'free_vs_pro' === $_GET['tab'] ) ?' nav-tab-active' : ''; ?>"><?php esc_html_e( 'Compare free Vs Pro', 'cv-portfolio-blocks' ); ?></a>

    <a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cv-portfolio-blocks', 'tab' => 'changelog' ), 'themes.php' ) ) ); ?>" class="nav-tab<?php echo ( isset( $_GET['tab'] ) && 'changelog' === $_GET['tab'] ) ?' nav-tab-active' : ''; ?>"><?php esc_html_e( 'Changelog', 'cv-portfolio-blocks' ); ?></a>
  </nav>

    <?php
      cv_portfolio_blocks_main_screen();

      cv_portfolio_blocks_changelog_screen();

      cv_portfolio_blocks_free_vs_pro();
}
/**
 * Output the main about screen.
 */
function cv_portfolio_blocks_main_screen() {
  if ( isset( $_GET['page'] ) && 'cv-portfolio-blocks' === $_GET['page'] && ! isset( $_GET['tab'] ) ) {
  ?>
    
<div class="four-col">

	<div class="col">

		<h3><i class="dashicons dashicons-book-alt"></i><?php esc_html_e( 'Free Theme Directives', 'cv-portfolio-blocks' ); ?></h3>

		<p>
			<?php esc_html_e( 'This article will walk you through the different phases of setting up and handling your WordPress website.', 'cv-portfolio-blocks' ); ?>
		</p>

		<p>
			<a class="button green button-primary" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_DOC_URL ); ?>" target="_blank"><?php esc_html_e( 'Free Documentation', 'cv-portfolio-blocks' ); ?></a>
		</p>

	</div><!-- .col -->

	<div class="col">

		<h3><i class="dashicons dashicons-admin-customizer"></i><?php esc_html_e( 'Full Site Editing', 'cv-portfolio-blocks' ); ?></h3>

		<p>
			<?php esc_html_e( 'We have used Full Site Editing which will help you preview your changes live and fast.', 'cv-portfolio-blocks' ); ?>
		</p>

		<p>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>" ><?php esc_html_e( 'Use Site Editor', 'cv-portfolio-blocks' ); ?></a>
		</p>

	</div><!-- .col -->

	<div class="col">

		<h3><i class="dashicons dashicons-book-alt"></i><?php esc_html_e( 'Leave us a review', 'cv-portfolio-blocks' ); ?></h3>
		<p>
			<?php esc_html_e( 'We would love to hear your feedback.', 'cv-portfolio-blocks' ); ?>
		</p>

		<p>
			<a class="button button-primary" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_REVIEW ); ?>" target="_blank"><?php esc_html_e( 'Review', 'cv-portfolio-blocks' ); ?></a>
		</p>

	</div><!-- .col -->


	<div class="col">

		<h3><i class="dashicons dashicons-sos"></i><?php esc_html_e( 'Help &amp; Support', 'cv-portfolio-blocks' ); ?></h3>

		<p>
			<?php esc_html_e( 'If you have any question/feedback regarding theme, please post in our official support forum.', 'cv-portfolio-blocks' ); ?>
		</p>

		<p>
			<a class="button button-primary" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_SUPPORT ); ?>" target="_blank"><?php esc_html_e( 'Get Support', 'cv-portfolio-blocks' ); ?></a>
		</p>

	</div><!-- .col -->

</div><!-- .four-col -->
  <?php
  }
}

/**
 * Output the changelog screen.
 */
function cv_portfolio_blocks_changelog_screen() {
  if ( isset( $_GET['tab'] ) && 'changelog' === $_GET['tab'] ) {
    global $wp_filesystem;
    ?>
    <div class="wrap about-wrap">
      <p class="about-description"><?php esc_html_e( 'Want to know whats been happening with the latest changes?', 'cv-portfolio-blocks' ); ?></p>
      <?php
        // Get the path to the readme.txt file.
        $readme_file = get_template_directory() . '/README.txt';

        // Check if the readme file exists and is readable.
        if ( file_exists( $readme_file ) && is_readable( $readme_file ) ) {
          $changelog = file_get_contents( $readme_file );
          $changelog_list = cv_portfolio_blocks_parse_changelog( $changelog );
          echo wp_kses_post( $changelog_list );
        } else {
          echo '<p>Changelog file does not exist or is not readable.</p>';
        }
      ?>
    </div>
    <?php
  }
}

/**
 * Parse changelog from readme file.
 * @param  string $content
 * @return string
 */
function cv_portfolio_blocks_parse_changelog( $content ) {
  // Explode content with '== ' to separate main content into an array of headings.
  $content = explode( '== ', $content );

  $changelog_isolated = '';

  // Find the part that starts with 'Changelog ==', i.e., isolate changelog.
  foreach ( $content as $key => $value ) {
    if ( strpos( $value, 'Changelog ==' ) === 0 ) {
      $changelog_isolated = str_replace( 'Changelog ==', '', $value );
    }
  }

  // Explode $changelog_isolated to manipulate it and add HTML elements.
  $changelog_array = explode( '- ', $changelog_isolated );

  // Prepare the HTML structure.
  $changelog = '<pre class="changelog">';
  foreach ( $changelog_array as $value ) {
    // Add opening and closing div and span, only the first span element will have the heading class.
    $value = '<div class="block"><span class="heading">- ' . esc_html( $value ) . '</span></div>';
    // Append the value to the changelog.
    $changelog .= $value;
  }
  $changelog .= '</pre>';

  return wp_kses_post( $changelog );
}

/**
 * Import Demo data for theme using catch themes demo import plugin
 */
function cv_portfolio_blocks_free_vs_pro() {
  if ( isset( $_GET['tab'] ) && 'free_vs_pro' === $_GET['tab'] ) {
  ?>
    <div class="wrap about-wrap">

      <h3 class="about-description"><?php esc_html_e( 'Compare Free Vs Pro', 'cv-portfolio-blocks' ); ?></h3>
      <div class="vs-theme-table">
        <table>
          <thead>
            <tr><th class="head" scope="col"><?php esc_html_e( 'Theme Features', 'cv-portfolio-blocks' ); ?></th>
              <th class="head" scope="col"><?php esc_html_e( 'Free Theme', 'cv-portfolio-blocks' ); ?></th>
              <th class="head" scope="col"><?php esc_html_e( 'Pro Theme', 'cv-portfolio-blocks' ); ?></th>
            </tr>
          </thead>
          <tbody>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><span><?php esc_html_e( 'Responsive Design', 'cv-portfolio-blocks' ); ?></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'Painless Setup', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-saved"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'Color Options', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-saved"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'Premium site demo', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-no-alt"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'Multiple Block Layout', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-no-alt"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'Premium Patterns', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-no-alt"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'Multiple Fonts', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-no-alt"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'Slider Block', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-no-alt"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'Post Listing Block', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-no-alt"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'WooCommerce Filter Block', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-no-alt"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'Gallery Block', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-no-alt"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td headers="features" class="feature"><?php esc_html_e( 'Post Carousel Block', 'cv-portfolio-blocks' ); ?></td>
              <td><span class="dashicons dashicons-no-alt"></span></td>
              <td><span class="dashicons dashicons-saved"></span></td>
            </tr>
            <tr class="odd" scope="row">
              <td class="feature feature--empty"></td>
              <td class="feature feature--empty"></td>
              <td headers="comp-2" class="td-btn-2"><a target="_blank" href="<?php echo esc_url( CV_PORTFOLIO_BLOCKS_BUY_NOW ); ?>" class="sidebar-button single-btn" target="_blank"><?php esc_html_e( 'Buy It Now', 'cv-portfolio-blocks' ); ?></a>

              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  <?php
  }
}