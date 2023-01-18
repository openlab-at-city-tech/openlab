<?php
namespace ElementsKit_Lite\Modules\Layout_Manager;

defined( 'ABSPATH' ) || exit;

class Init {
	private $dir;
	private $url;

	public function __construct() {

		// get current directory path.
		$this->dir = dirname( __FILE__ ) . '/';

		// get current module's url.
		$this->url = \ElementsKit_Lite::plugin_url() . 'modules/layout-manager/';

		// print views and tab variables on footer.
		add_action( 'elementor/editor/footer', array( $this, 'script_variables' ) );

		// enqueue editor js for elementor.
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_scripts' ), 1 );

		// enqueue editor css.
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_styles' ) );

		// enqueue modal's preview css.
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'preview_styles' ) );

		new Layout_List_Api();
		new Layout_Import_Api();
	}

	public function editor_scripts() {
		wp_enqueue_script( 'ekit-layout-library-script', $this->url . 'assets/js/ekit-layout-library.js', array( 'jquery', 'wp-element' ), \ElementsKit_Lite::version(), true );
	}

	public function editor_styles() {
		wp_enqueue_style( 'ekit-layout-library-style', $this->url . 'assets/css/ekit-layout-library.css', array(), \ElementsKit_Lite::version() );
	}

	public function preview_styles() {
		wp_enqueue_style( 'ekit-layout-library-preview-style', $this->url . 'assets/css/preview.css', array(), \ElementsKit_Lite::version() );
	}

	public function script_variables() { ?>
		<script type="text/javascript">

		var ElementsKitLayoutManager = {
			"defaultTab": "pages",
			"nonce": "<?php echo esc_attr(wp_create_nonce( 'wp_rest' )); ?>",
			"buttonIcon": "<?php echo esc_url( \ElementsKit_Lite\Libs\Framework\Attr::get_url() . 'assets/images/ekit_icon.svg' ); ?>",
			"infoIcon": "<?php echo  esc_url( $this->url . 'assets/img/info.svg' ); ?>",
			"apiUrl": "<?php echo esc_url( get_rest_url( null, 'elementskit/v1/layout-manager-api/layout_list' ) ); ?>",
			"hasPro": <?php echo ( \ElementsKit_Lite::package_type() == 'free' ? 'false' : 'true' ); ?>,
			"licenseStatus": "<?php echo esc_attr( \ElementsKit_Lite::license_status() ); ?>",
			"links": {
				"go_premium": "https://wpmet.com/elementskit-pricing",
				"active_license": "<?php echo esc_url( admin_url() ); ?>admin.php?page=elementskit-license"
			},
			"banner": {
				"enable": true,
				"img": "<?php echo esc_url( $this->url . 'assets/img/banner.jpg' ); ?>",
				"link": "https://wpmet.com/layout-manager-banner",
				"target": '_blank'
			}
		};

		</script> 
		<?php
	}


}
