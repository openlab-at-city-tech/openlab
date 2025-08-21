<?php

namespace TEC\Common\StellarWP\Assets;

/**
 * Handles adding script data to the page in cases where localizing a
 * specific script is not suitable.
 */
class Data {
	/**
	 * Container for any JS data objects that should be added to the page.
	 *
	 * @var array
	 */
	protected array $data = [];

	/**
	 * Adds the provided data to the list of objects that should be available
	 * to other scripts.
	 *
	 * @param string $key  Object name.
	 * @param array  $data Object data.
	 */
	public function add( string $key, $data ) {
		$hook_prefix = Config::get_hook_prefix();

		/**
		 * Allow plugins to filter data for a specific object.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $data Object data.
		 * @param string $key  Object name.
		 */
		$data = apply_filters( "stellarwp/assets/{$hook_prefix}/data_add_{$key}", $data, $key );

		$this->data[ $key ] = $data;
	}

	/**
	 * Returns the data for the provided object name.
	 *
	 * @param string $key      Object name.
	 * @param mixed  $default  Default value to return if the object is not found.
	 *
	 * @return mixed
	 */
	public function get( string $key, $default = null ) {
		return $this->data[ $key ] ?? $default;
	}

	/**
	 * Removes the provided data from the list of objects that should be available
	 * to other scripts.
	 *
	 * @param string $key Object name.
	 */
	public function remove( $key ) {
		unset( $this->data[ $key ] );
	}

	/**
	 * Returns all data.
	 *
	 * @return array
	 */
	public function get_data() : array {
		return $this->data;
	}

	/**
	 * Checks if there is any stored data.
	 *
	 * @return bool
	 */
	public function has_data() : bool {
		return ! empty( $this->data );
	}

	/**
	 * Prints an individual key value pair.
	 *
	 * @param string $name Object name.
	 * @param mixed  $data Object data.
	 */
	public function print_data( $name, $data ) {
		$data = rawurlencode( wp_json_encode( $data ) );
		?>
		<script id="<?php echo esc_attr( $name ); ?>">
			window['<?php echo esc_attr( $name ); ?>'] = JSON.parse( decodeURIComponent( '<?php echo esc_js( $data ); ?>' ) );
		</script>
		<?php
	}

	/**
	 * Outputs the data.
	 *
	 * @internal
	 */
	public function render_json() {
		if ( empty( $this->data ) ) {
			return;
		}

		echo '<script> /* <![CDATA[ */';

		foreach ( $this->data as $key => $data ) {
			echo 'let ' . esc_attr( $key ) . ' = ' . wp_json_encode( $data ) . ';';
		}

		echo '/* ]]> */ </script>';
	}
}
