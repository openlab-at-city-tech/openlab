<?php defined( 'ABSPATH' ) || exit();

/**
 * Contract for provider-specific model catalogs.
 *
 * EPKB_AI_Provider selects implementations based on the active provider.
 * Implementations must provide every static method below so that calls like
 * $catalog_class::method() cannot fail at runtime.
 */
interface EPKB_AI_Model_Catalog_Interface {

	public static function get_default_model();

	public static function get_default_max_output_tokens();

	public static function get_default_max_results();

	public static function get_models( $model_name = null );

	public static function resolve_model_name( $model_name );

	public static function normalize_parameters( $model, $params = array() );

	public static function apply_model_parameters( $request, $model, $params = array() );
}
