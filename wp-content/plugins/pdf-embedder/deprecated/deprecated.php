<?php

/**
 * Class Core_PDF_Embedder.
 * Don't use it, it will be removed in the near future.
 *
 * @since      1.0.0
 * @deprecated 4.7.0
 */
/**
 * Class Core_PDF_Embedder.
 * Don't use it, it will be removed in the near future.
 *
 * @since 1.0.0
 * @deprecated 4.7.0
 */
class Core_PDF_Embedder {

	/**
	 * Singleton Instance.
	 *
	 * @since 4.6.3
	 *
	 * @var Core_PDF_Embedder
	 */
	public static $instance;

	/**
	 * Get the plugin instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Core_PDF_Embedder
	 */
	public static function get_instance(): Core_PDF_Embedder {

		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

/**
 * PDF Embedder Basic Embedder.
 * DO NOT USE IT. This class will be removed soon.
 *
 * @since      1.0.0
 * @deprecated 4.7.0
 */
final class PDF_Embedder_Basic extends Core_PDF_Embedder {

}

/**
 * Global accessor function to singleton instance.
 * This function will soon be removed, do not use it!
 *
 * @since      1.0.0
 * @deprecated 4.7.0
 *
 * @return Core_PDF_Embedder
 */
function pdfembPDFEmbedder() {

	return Core_PDF_Embedder::get_instance();
}
