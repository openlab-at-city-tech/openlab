<?php

use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

if ( ! function_exists( 'kenta_get_html_attributes' ) ) {
	/**
	 * Get html element attributes
	 *
	 * @return mixed|null
	 *
	 * @since v1.3.0
	 */
	function kenta_get_html_attributes( $key = null ) {

		$default_scheme = CZ::checked( 'kenta_default_dark_scheme' ) ? 'dark' : 'light';

		if ( CZ::checked( 'kenta_save_color_scheme' ) ) {
			$attrs = [
				'data-save-color-scheme' => 'yes',
				'data-kenta-blog-id'     => kenta_blog_id(),
				'data-kenta-theme'       => $_COOKIE[ kenta_blog_id( 'color-mode' ) ] ?? $default_scheme,
			];
		} else {
			$attrs = [
				'data-save-color-scheme' => 'no',
				'data-kenta-blog-id'     => kenta_blog_id(),
				'data-kenta-theme'       => $default_scheme,
			];
		}

		$attrs = apply_filters( 'kenta_html_attributes', $attrs );

		if ( $key === null ) {
			return $attrs;
		}

		return $attrs[ $key ] ?? null;
	}
}

if ( ! function_exists( 'kenta_html_attributes' ) ) {
	/**
	 * Output html element attributes
	 */
	function kenta_html_attributes() {
		Utils::print_attribute_string( kenta_get_html_attributes() );
	}
}

if ( ! function_exists( 'kenta_image_size_options' ) ) {
	/**
	 * @param bool $add_disable
	 * @param array $allowed
	 * @param bool $show_dimension
	 *
	 * @return array
	 */
	function kenta_image_size_options( $add_disable = true, array $allowed = [], $show_dimension = true ) {

		global $_wp_additional_image_sizes;

		$choices = [];

		if ( true === $add_disable ) {
			$choices['disable'] = 'No Image';
		}

		$choices['thumbnail'] = 'Thumbnail';
		$choices['medium']    = 'Medium';
		$choices['large']     = 'Large';
		$choices['full']      = 'Full (original)';

		if ( true === $show_dimension ) {
			foreach ( [ 'thumbnail', 'medium', 'large' ] as $_size ) {
				$choices[ $_size ] = $choices[ $_size ] . ' (' . get_option( $_size . '_size_w' ) . 'x' . get_option( $_size . '_size_h' ) . ')';
			}
		}

		if ( ! empty( $_wp_additional_image_sizes ) && is_array( $_wp_additional_image_sizes ) ) {
			foreach ( $_wp_additional_image_sizes as $key => $size ) {
				$choices[ $key ] = $key;
				if ( true === $show_dimension ) {
					$choices[ $key ] .= ' (' . $size['width'] . 'x' . $size['height'] . ')';
				}
			}
		}

		if ( ! empty( $allowed ) ) {
			foreach ( $choices as $key => $value ) {
				if ( ! in_array( $key, $allowed, true ) ) {
					unset( $choices[ $key ] );
				}
			}
		}

		return $choices;
	}
}

if ( ! function_exists( 'kenta_image' ) ) {
	/**
	 * Get image file
	 *
	 * @param $name
	 *
	 * @return mixed|string
	 */
	function kenta_image( $name ) {
		$svgs = [
			'none'                    => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 32 32"><path d="M9.943 17.415h-0.065l-2.095-3.199-2.559-3.777h-1.984v11.025h2.191v-6.944h0.095l1.819 2.784 2.793 4.16h1.996v-11.025h-2.191v6.977zM12.904 22.135h1.615l4.049-12.271h-1.633l-4.031 12.271zM24.92 10.439h-2.24l-3.874 11.025h2.336l0.72-2.273h3.841l0.672 2.273h2.384l-3.84-11.025zM22.455 17.352l0.447-1.456 0.85-2.864h0.063l0.866 2.913 0.431 1.408h-2.656z"></path></svg>',
			/**
			 * Divider
			 */
			'divider-1'               => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M9,17.2l5.1-10.9L15,6.8L9.9,17.6L9,17.2z"/></svg>',
			/**
			 * Text Align
			 */
			'text-left'               => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M3.328 14.4c-1.056 0-1.984-0.8-1.984-1.728s0.928-1.728 1.984-1.728h24c1.088 0 2.016 0.8 2.016 1.728s-0.928 1.728-2.016 1.728h-24zM3.328 21.056c-1.056 0-1.984-0.8-1.984-1.728s0.928-1.728 1.984-1.728h20c1.088 0 2.016 0.8 2.016 1.728s-0.928 1.728-2.016 1.728h-20zM3.328 27.744c-1.056 0-1.984-0.8-1.984-1.76s0.928-1.728 1.984-1.728h25.344c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.76-1.984 1.76h-25.344zM3.328 7.744c-1.056 0-1.984-0.8-1.984-1.76s0.928-1.728 1.984-1.728h17.344c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.76-1.984 1.76h-17.344z"></path></svg>',
			'text-center'             => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M6.016 14.4c-1.088 0-2.016-0.8-2.016-1.728s0.928-1.728 2.016-1.728h19.84c1.216 0 2.016 0.8 2.016 1.728s-0.928 1.728-2.016 1.728h-19.84zM8.672 21.056c-1.056 0-2.016-0.8-2.016-1.728s0.96-1.728 2.016-1.728h14.656c1.088 0 2.016 0.8 2.016 1.728s-0.928 1.728-2.016 1.728h-14.656zM3.328 27.744c-1.056 0-1.984-0.8-1.984-1.76s0.928-1.728 1.984-1.728h25.344c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.76-1.984 1.76h-25.344zM11.456 7.744c-1.184 0-2.112-0.8-2.112-1.76s0.928-1.728 2.112-1.728h9.088c1.184 0 2.112 0.8 2.112 1.728s-0.928 1.76-2.112 1.76h-9.088z"></path></svg>',
			'text-right'              => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M28.672 14.4h-24c-1.056 0-2.016-0.8-2.016-1.728s0.96-1.728 2.016-1.728h24c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.728-1.984 1.728zM28.672 21.056h-20c-1.056 0-2.016-0.8-2.016-1.728s0.96-1.728 2.016-1.728h20c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.728-1.984 1.728zM28.672 27.744h-25.344c-1.056 0-1.984-0.8-1.984-1.76s0.928-1.728 1.984-1.728h25.344c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.76-1.984 1.76zM28.672 7.744h-17.344c-1.056 0-1.984-0.8-1.984-1.76s0.928-1.728 1.984-1.728h17.344c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.76-1.984 1.76z"></path></svg>',
			'text-justify'            => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M3.328 27.744c-1.056 0-1.984-0.8-1.984-1.76s0.928-1.728 1.984-1.728h25.344c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.76-1.984 1.76h-25.344zM3.328 21.056c-1.056 0-1.984-0.8-1.984-1.728s0.928-1.728 1.984-1.728h25.344c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.728-1.984 1.728h-25.344zM3.328 14.4c-1.056 0-1.984-0.8-1.984-1.728s0.928-1.728 1.984-1.728h25.344c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.728-1.984 1.728h-25.344zM3.328 7.744c-1.056 0-1.984-0.8-1.984-1.76s0.928-1.728 1.984-1.728h25.344c1.056 0 1.984 0.8 1.984 1.728s-0.928 1.76-1.984 1.76h-25.344z"></path></svg>',
			/**
			 * Justify Content
			 */
			'justify-space-between-v' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M30.656 29.344c0.736 0 1.344 0.576 1.344 1.312 0 0.704-0.512 1.28-1.184 1.344h-29.472c-0.736 0-1.344-0.608-1.344-1.344 0-0.672 0.512-1.248 1.184-1.312h29.472zM24.8 21.344c1.024 0 1.856 0.832 1.856 1.856v3.456h-21.312v-3.456c0-1.024 0.832-1.856 1.856-1.856h17.6zM26.656 5.344v3.456c0 1.024-0.832 1.856-1.856 1.856h-17.6c-1.024 0-1.856-0.832-1.856-1.856v-3.456h21.312zM30.656 0c0.736 0 1.344 0.608 1.344 1.344 0 0.672-0.512 1.248-1.184 1.312h-29.472c-0.736 0-1.344-0.576-1.344-1.312 0-0.704 0.512-1.28 1.184-1.344h29.472z"></path></svg>',
			'justify-space-around-v'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M30.656 29.344c0.736 0 1.344 0.576 1.344 1.312 0 0.704-0.512 1.28-1.184 1.344h-29.472c-0.736 0-1.344-0.608-1.344-1.344 0-0.672 0.512-1.248 1.184-1.312h29.472zM24.8 17.344c1.024 0 1.856 0.832 1.856 1.856v1.6c0 1.024-0.832 1.856-1.856 1.856h-17.6c-1.024 0-1.856-0.832-1.856-1.856v-1.6c0-1.024 0.832-1.856 1.856-1.856h17.6zM24.8 9.344c1.024 0 1.856 0.832 1.856 1.856v1.6c0 1.024-0.832 1.856-1.856 1.856h-17.6c-1.024 0-1.856-0.832-1.856-1.856v-1.6c0-1.024 0.832-1.856 1.856-1.856h17.6zM30.656 0c0.736 0 1.344 0.608 1.344 1.344 0 0.672-0.512 1.248-1.184 1.312h-29.472c-0.736 0-1.344-0.576-1.344-1.312 0-0.704 0.512-1.28 1.184-1.344h29.472z"></path></svg>',
			'justify-start-v'         => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M24.928 20c0.96 0 1.728 0.768 1.728 1.728v1.856c0 0.96-0.768 1.76-1.728 1.76h-17.856c-0.96 0-1.728-0.8-1.728-1.76v-1.856c0-0.96 0.768-1.728 1.728-1.728h17.856zM24.928 12c0.96 0 1.728 0.768 1.728 1.728v1.856c0 0.96-0.768 1.76-1.728 1.76h-17.856c-0.96 0-1.728-0.8-1.728-1.76v-1.856c0-0.96 0.768-1.728 1.728-1.728h17.856zM30.656 6.656c0.736 0 1.344 0.608 1.344 1.344 0 0.672-0.512 1.248-1.184 1.312l-0.16 0.032h-29.312c-0.736 0-1.344-0.608-1.344-1.344 0-0.672 0.512-1.248 1.184-1.312l0.16-0.032h29.312z"></path></svg>',
			'justify-center-v'        => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M24.928 20c0.96 0 1.728 0.768 1.728 1.728v1.856c0 0.96-0.768 1.76-1.728 1.76h-17.856c-0.96 0-1.728-0.8-1.728-1.76v-1.856c0-0.96 0.768-1.728 1.728-1.728h17.856zM30.656 14.656c0.736 0 1.344 0.608 1.344 1.344 0 0.672-0.512 1.248-1.184 1.312l-0.16 0.032h-29.312c-0.736 0-1.344-0.608-1.344-1.344 0-0.672 0.512-1.248 1.184-1.312l0.16-0.032h29.312zM24.928 6.656c0.96 0 1.728 0.768 1.728 1.728v1.888c0 0.96-0.768 1.728-1.728 1.728h-17.856c-0.96 0-1.728-0.768-1.728-1.728v-1.888c0-0.96 0.768-1.728 1.728-1.728h17.856z"></path></svg>',
			'justify-end-v'           => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M30.656 22.656c0.736 0 1.344 0.608 1.344 1.344 0 0.672-0.512 1.248-1.184 1.312l-0.16 0.032h-29.312c-0.736 0-1.344-0.608-1.344-1.344 0-0.672 0.512-1.248 1.184-1.312l0.16-0.032h29.312zM24.928 14.656c0.96 0 1.728 0.768 1.728 1.728v1.888c0 0.96-0.768 1.728-1.728 1.728h-17.856c-0.96 0-1.728-0.768-1.728-1.728v-1.888c0-0.96 0.768-1.728 1.728-1.728h17.856zM24.928 6.656c0.96 0 1.728 0.768 1.728 1.728v1.888c0 0.96-0.768 1.728-1.728 1.728h-17.856c-0.96 0-1.728-0.768-1.728-1.728v-1.888c0-0.96 0.768-1.728 1.728-1.728h17.856z"></path></svg>',

			'justify-space-between-h' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M1.344 0c0.672 0 1.248 0.512 1.312 1.184v29.472c0 0.736-0.576 1.344-1.312 1.344-0.704 0-1.248-0.512-1.344-1.184v-29.472c0-0.736 0.608-1.344 1.344-1.344zM30.656 0c0.704 0 1.28 0.512 1.344 1.184v29.472c0 0.736-0.608 1.344-1.344 1.344-0.672 0-1.248-0.512-1.312-1.184v-29.472c0-0.736 0.576-1.344 1.312-1.344zM8.8 5.344c1.024 0 1.856 0.832 1.856 1.856v17.6c0 1.024-0.832 1.856-1.856 1.856h-3.456v-21.312h3.456zM26.656 5.344v21.312h-3.456c-1.024 0-1.856-0.832-1.856-1.856v-17.6c0-1.024 0.832-1.856 1.856-1.856h3.456z"></path></svg>',
			'justify-space-around-h'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M30.656 0c0.704 0 1.28 0.512 1.344 1.184v29.472c0 0.736-0.608 1.344-1.344 1.344-0.672 0-1.248-0.512-1.312-1.184v-29.472c0-0.736 0.576-1.344 1.312-1.344zM1.344 0c0.672 0 1.248 0.512 1.312 1.184v29.472c0 0.736-0.576 1.344-1.312 1.344-0.704 0-1.248-0.512-1.344-1.184v-29.472c0-0.736 0.608-1.344 1.344-1.344zM20.8 5.344c1.024 0 1.856 0.832 1.856 1.856v17.6c0 1.024-0.832 1.856-1.856 1.856h-1.6c-1.024 0-1.856-0.832-1.856-1.856v-17.6c0-1.024 0.832-1.856 1.856-1.856h1.6zM12.8 5.344c1.024 0 1.856 0.832 1.856 1.856v17.6c0 1.024-0.832 1.856-1.856 1.856h-1.6c-1.024 0-1.856-0.832-1.856-1.856v-17.6c0-1.024 0.832-1.856 1.856-1.856h1.6z"></path></svg>',
			'justify-start-h'         => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M8 0c0.672 0 1.248 0.512 1.312 1.184l0.032 0.16v29.312c0 0.736-0.608 1.344-1.344 1.344-0.672 0-1.248-0.512-1.312-1.184l-0.032-0.16v-29.312c0-0.736 0.608-1.344 1.344-1.344zM15.616 5.344c0.96 0 1.728 0.768 1.728 1.728v17.856c0 0.96-0.768 1.728-1.728 1.728h-1.888c-0.96 0-1.728-0.768-1.728-1.728v-17.856c0-0.96 0.768-1.728 1.728-1.728h1.888zM23.616 5.344c0.96 0 1.728 0.768 1.728 1.728v17.856c0 0.96-0.768 1.728-1.728 1.728h-1.888c-0.96 0-1.728-0.768-1.728-1.728v-17.856c0-0.96 0.768-1.728 1.728-1.728h1.888z"></path></svg>',
			'justify-center-h'        => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M16 0c0.672 0 1.248 0.512 1.312 1.184l0.032 0.16v29.312c0 0.736-0.608 1.344-1.344 1.344-0.672 0-1.248-0.512-1.312-1.184l-0.032-0.16v-29.312c0-0.736 0.608-1.344 1.344-1.344zM23.616 5.344c0.96 0 1.728 0.768 1.728 1.728v17.856c0 0.96-0.768 1.728-1.728 1.728h-1.888c-0.96 0-1.728-0.768-1.728-1.728v-17.856c0-0.96 0.768-1.728 1.728-1.728h1.888zM10.272 5.344c0.96 0 1.728 0.768 1.728 1.728v17.856c0 0.96-0.768 1.728-1.728 1.728h-1.856c-0.96 0-1.76-0.768-1.76-1.728v-17.856c0-0.96 0.8-1.728 1.76-1.728h1.856z"></path></svg>',
			'justify-end-h'           => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M24 0c0.672 0 1.248 0.512 1.312 1.184l0.032 0.16v29.312c0 0.736-0.608 1.344-1.344 1.344-0.672 0-1.248-0.512-1.312-1.184l-0.032-0.16v-29.312c0-0.736 0.608-1.344 1.344-1.344zM18.272 5.344c0.96 0 1.728 0.768 1.728 1.728v17.856c0 0.96-0.768 1.728-1.728 1.728h-1.856c-0.96 0-1.76-0.768-1.76-1.728v-17.856c0-0.96 0.8-1.728 1.76-1.728h1.856zM10.272 5.344c0.96 0 1.728 0.768 1.728 1.728v17.856c0 0.96-0.768 1.728-1.728 1.728h-1.856c-0.96 0-1.76-0.768-1.76-1.728v-17.856c0-0.96 0.8-1.728 1.76-1.728h1.856z"></path></svg>',
			/**
			 * Device
			 */
			'desktop'                 => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M17.856 26.528c0.832 0 1.472 0.672 1.472 1.472v0.768c0 0.224-0.192 0.448-0.448 0.448h-5.76c-0.256 0-0.448-0.224-0.448-0.448v-0.768c0-0.8 0.64-1.472 1.472-1.472h3.712zM27.648 2.816c1.664 0 3.008 1.376 3.008 3.040v16.288c0 1.696-1.344 3.040-3.008 3.040h-23.296c-1.664 0-3.008-1.344-3.008-3.040v-16.288c0-1.664 1.344-3.040 3.008-3.040h23.296zM27.648 5.504h-23.296c-0.16 0-0.32 0.096-0.352 0.256v13.696h24v-13.6c0-0.192-0.128-0.32-0.288-0.352h-0.064z"></path></svg>',
			'tablet'                  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M25.216 1.344c1.536 0 2.784 1.248 2.784 2.784v23.744c0 1.536-1.248 2.784-2.784 2.784h-18.4c-1.568 0-2.816-1.248-2.816-2.784v-23.744c0-1.536 1.248-2.784 2.816-2.784h18.4zM25.216 4h-18.4c-0.096 0-0.128 0.064-0.16 0.128v0 23.744c0 0.064 0.064 0.128 0.128 0.128h18.432c0.064 0 0.096-0.064 0.128-0.128v0-23.744c0-0.064-0.064-0.128-0.128-0.128v0zM18.656 24.672c0.736 0 1.344 0.608 1.344 1.312v0.256c0 0.224-0.192 0.416-0.448 0.416h-7.104c-0.256 0-0.448-0.192-0.448-0.416v-0.256c0-0.704 0.608-1.312 1.344-1.312h5.312z"></path></svg>',
			'mobile'                  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32"><path d="M22.4 1.344c1.536 0 2.816 1.248 2.816 2.784v23.744c0 1.536-1.28 2.784-2.816 2.784h-12.8c-1.536 0-2.784-1.248-2.784-2.784v-23.744c0-1.536 1.248-2.784 2.784-2.784h12.8zM22.4 4h-12.8c-0.064 0-0.128 0.064-0.128 0.128v0 23.744c0 0.064 0.064 0.128 0.128 0.128v0h12.8c0.064 0 0.128-0.064 0.128-0.128v0-23.744c0-0.064-0.032-0.128-0.128-0.128v0zM17.152 24.672c0.736 0 1.344 0.608 1.344 1.312v0.256c0 0.224-0.192 0.416-0.448 0.416h-3.776c-0.256 0-0.448-0.192-0.448-0.416v-0.256c0-0.704 0.608-1.312 1.344-1.312h1.984z"></path></svg>',
			/**
			 * Breadcrumb Separator
			 */
			'breadcrumb-sep-1'        => '<svg width="16" height="16" viewBox="0 0 20 20"><path d="M7.7,20c-0.3,0-0.5-0.1-0.7-0.3c-0.4-0.4-0.4-1.1,0-1.5l8.1-8.1L6.7,1.8c-0.4-0.4-0.4-1.1,0-1.5 c0.4-0.4,1.1-0.4,1.5,0l9.1,9.1c0.4,0.4,0.4,1.1,0,1.5l-8.8,8.9C8.2,19.9,7.9,20,7.7,20z"/></svg>',
			'breadcrumb-sep-2'        => '<svg width="16" height="16" viewBox="0 0 20 20"><polygon points="7,0 18,10 7,20 "/></svg>',
			'breadcrumb-sep-3'        => '<svg width="16" height="16" viewBox="0 0 20 20"><path d="M6.1,20c-0.2,0-0.3,0-0.5-0.1c-0.5-0.2-0.7-0.8-0.4-1.3l9.5-17.9C15,0.1,15.6,0,16.1,0.2 c0.5,0.2,0.7,0.8,0.4,1.3L6.9,19.4C6.8,19.8,6.5,19.9,6.1,20z"/></svg>',
			'breadcrumb-sep-4'        => '<svg width="16" height="16" viewBox="0 0 448 512"><path d="M246.6 233.4l-160-160c-12.5-12.5-32.75-12.5-45.25 0s-12.5 32.75 0 45.25L178.8 256l-137.4 137.4c-12.5 12.5-12.5 32.75 0 45.25C47.63 444.9 55.81 448 64 448s16.38-3.125 22.62-9.375l160-160C259.1 266.1 259.1 245.9 246.6 233.4zM438.6 233.4l-160-160c-12.5-12.5-32.75-12.5-45.25 0s-12.5 32.75 0 45.25L370.8 256l-137.4 137.4c-12.5 12.5-12.5 32.75 0 45.25C239.6 444.9 247.8 448 256 448s16.38-3.125 22.62-9.375l160-160C451.1 266.1 451.1 245.9 438.6 233.4z"/></svg>',
		];

		$svgs = apply_filters( 'kenta_svg_images', $svgs );

		if ( ! isset( $svgs[ $name ] ) ) {
			return '';
		}

		return $svgs[ $name ];
	}
}

if ( ! function_exists( 'kenta_image_url' ) ) {
	/**
	 * Get image file url
	 *
	 * @param $path
	 *
	 * @return string
	 */
	function kenta_image_url( $path ): string {
		return trailingslashit( get_template_directory_uri() ) . 'dist/images/' . $path;
	}
}

if ( ! function_exists( 'kenta_get_sidebar_layout' ) ) {
	/**
	 * Get current post/page/store sidebar layout
	 *
	 * @param $post_type
	 *
	 * @return mixed|string
	 * @since 2.0.0
	 */
	function kenta_get_sidebar_layout( $post_type = 'page' ) {
		$layout       = 'no-sidebar';
		$page_sidebar = kenta_get_current_post_meta( 'site-sidebar-layout' );
		if ( $page_sidebar && $page_sidebar !== 'default' ) {
			$layout = $page_sidebar;
		} else if ( ( ! is_front_page() || is_home() ) && CZ::checked( "kenta_{$post_type}_sidebar_section" ) ) {
			$layout = CZ::get( "kenta_{$post_type}_sidebar_layout" );
		}

		return $layout;
	}
}

if ( ! function_exists( 'kenta_get_container_style' ) ) {
	/**
	 * @return mixed|string
	 */
	function kenta_get_container_style( $post_type = 'page' ) {
		$page_container = kenta_get_current_post_meta( 'site-container-style' );
		if ( $page_container && $page_container !== 'default' ) {
			return $page_container;
		}

		if ( $post_type === 'post' ) {
			return CZ::get( 'kenta_single_post_container_style' );
		}

		if ( $post_type === 'page' ) {
			return CZ::get( 'kenta_pages_container_style' );
		}

		return 'boxed';
	}
}

if ( ! function_exists( 'kenta_get_container_layout' ) ) {
	/**
	 * @return mixed|string
	 */
	function kenta_get_container_layout( $post_type = 'page' ) {
		$option_type              = $post_type === 'page' ? 'pages' : 'single_post';
		$content_container_layout = kenta_get_current_post_meta( 'site-container-layout' );
		if ( $content_container_layout === 'default' ) {
			$content_container_layout = CZ::get( 'kenta_' . $option_type . '_container_layout' ) ?? 'normal';
		}

		return $content_container_layout;
	}
}

if ( ! function_exists( 'kenta_container_css' ) ) {
	/**
	 * Get container css
	 *
	 * @param string $layout
	 * @param array $css
	 *
	 * @return []|array|string[]
	 */
	function kenta_container_css( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'layout'  => 'narrow',
			'sidebar' => 'no-sidebar',
			'style'   => 'boxed',
			'css'     => array(),
		) );

		$sidebar = $args['sidebar'];
		$style   = $args['style'];

		return array_merge( $args['css'], [
			'kenta-container flex flex-col lg:flex-row flex-grow z-[1]' => true,
			'kenta-max-w-wide mx-auto'                                  => $style === 'boxed' && ( $sidebar === 'right-sidebar' || $sidebar === 'left-sidebar' ),
			'is-style-' . $style                                        => true,
			'is-align-' . $args['layout']                               => true,
			'kenta-no-sidebar no-sidebar'                               => $sidebar !== 'right-sidebar' && $sidebar !== 'left-sidebar',
			'kenta-right-sidebar lg:flex-row'                           => $sidebar === 'right-sidebar',
			'kenta-left-sidebar lg:flex-row-reverse'                    => $sidebar === 'left-sidebar',
		] );
	}
}

if ( ! function_exists( 'kenta_render_posts_list' ) ) {
	/**
	 * Render posts list
	 */
	function kenta_render_posts_list() {

		$attrs = [
			'class'            => 'flex flex-wrap card-list',
			'data-card-layout' => CZ::get( 'kenta_archive_layout' ),
		];

		if ( is_customize_preview() ) {
			$attrs['data-shortcut']          = 'border';
			$attrs['data-shortcut-location'] = 'kenta_archive';
		}

		if ( have_posts() ) {
			?>
            <div <?php Utils::print_attribute_string( $attrs ); ?>>
				<?php
				// posts loop
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'entry' );
				}
				?>
            </div>

			<?php
			/**
			 * Hook - kenta_action_posts_pagination.
			 */
			do_action( 'kenta_action_posts_pagination' );
		} else {
			get_template_part( 'template-parts/content', 'none' );
		}
	}
}

if ( ! function_exists( 'kenta_post_metas' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @param $id
	 * @param array|string[] $items
	 * @param array $args
	 * @param null $options
	 * @param array $settings
	 */
	function kenta_post_metas(
		$id, array $items = [
		'posted_on',
		'views',
		'comments'
	], $args = [], $options = null, $settings = []
	) {
		$default_args = [
			'before' => '',
			'after'  => '',
			'sep'    => '',
			'style'  => '',
		];

		$options = $options ?? CZ::getFacadeRoot();

		extract( array_merge( $default_args, $args ) );
		$divider = $options->get( 'kenta_' . $id . '_meta_items_divider', $settings );
		$icon    = $options->get( 'kenta_' . $id . '_meta_items_style', $settings ) === 'icon';

		echo $before;

		foreach ( $items as $item ) {

			if ( $item === 'byline' ) {

				$byline = sprintf(
					'%s',
					'<a class="' . $style . '" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
				);

				if ( ! empty( $byline ) ) {
					echo '<span class="byline meta-item"> ' . ( $icon ? IconsManager::render( $options->get( 'kenta_' . $id . '_byline_icon' ) ) : '' ) . $byline . '</span>';
				}
			} elseif ( $item === 'published' ) {
				$date_format   = $options->get( 'kenta_' . $id . '_published_format', $settings );
				$show_modified = $options->checked( 'kenta_' . $id . '_show_modified_date', $settings );

				$time_string = '<time class="published updated" datetime="%1$s">%2$s</time>';
				if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
					if ( $show_modified ) {
						$time_string = '<time class="published hidden" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
					} else {
						$time_string = '<time class="published" datetime="%1$s">%2$s</time><time class="updated hidden" datetime="%3$s">%4$s</time>';
					}
				}

				$time_string = sprintf( $time_string,
					esc_attr( get_the_date( 'c' ) ),
					esc_html( get_the_date( $date_format ) ),
					esc_attr( get_the_modified_date( 'c' ) ),
					esc_html( get_the_modified_date( $date_format ) )
				);

				$posted_on = sprintf(
					'%s',
					'<a class="' . $style . '" href="' . esc_url( get_permalink() ) . '" rel="bookmark"><span class="entry-date">' . $time_string . '</span></a>'
				);

				if ( ! empty( $posted_on ) ) {
					echo '<span class="meta-item posted-on">' . ( $icon ? IconsManager::render( $options->get( 'kenta_' . $id . '_published_icon' ) ) : '' ) . $posted_on . '</span>';
				}
			} elseif ( $item === 'comments' ) {
				if ( ! comments_open( get_the_ID() ) || get_comments_number() <= 0 ) {
					continue;
				}

				echo '<span class="meta-item comments-link">';
				echo $icon ? IconsManager::render( $options->get( 'kenta_' . $id . '_comments_icon' ) ) : '';
				comments_popup_link( false, false, false, $style );
				echo '</span>';
			}

			if ( $divider !== 'none' ) {
				echo '<span class="meta-divider">';
				echo kenta_image( $divider );
				echo '</span>';
			} else {
				echo '<span class="meta-empty-divider mr-2"></span>';
			}
		}

		echo $after;
	}
}

if ( ! function_exists( 'kenta_post_categories' ) ) {
	/**
	 * Prints HTML with categories information for the current post.
	 *
	 * @param string $before
	 * @param string $after
	 * @param array $style
	 */
	function kenta_post_categories( $before = '', $after = '', $style = [] ) {
		// Hide category for pages.
		if ( 'post' !== get_post_type() || empty( get_the_category() ) ) {
			return;
		}

		global $wp_rewrite;

		$style = esc_attr( Utils::clsx( $style ) );

		$rel = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';
		echo $before;
		foreach ( get_the_category() as $category ) {
			echo '<a class="' . $style . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '" ' . $rel . '>' . esc_html( $category->name ) . '</a>';
		}
		echo $after;
	}
}

if ( ! function_exists( 'kenta_post_tags' ) ) {
	/**
	 * Prints HTML with tags information for the current post.
	 *
	 * @param string $before
	 * @param string $after
	 * @param array $style
	 */
	function kenta_post_tags( $before = '', $after = '', $style = [] ) {
		// Hide tag text for pages.
		if ( 'post' !== get_post_type() ) {
			return;
		}

		$tags = get_the_tags();

		if ( is_wp_error( $tags ) || empty( $tags ) ) {
			return;
		}

		$style = esc_attr( Utils::clsx( $style ) );

		$tag_links = array_map( function ( $tag ) use ( $style ) {
			return '<a class="' . $style . '" href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" rel="tag">' . $tag->name . '</a>';
		}, $tags );

		/* Translators: used between list items, there is a space after the comma. */
		echo $before . implode( '', $tag_links ) . $after;
	}
}

if ( ! function_exists( 'kenta_post_structure' ) ) {
	/**
	 * Render post structure
	 */
	function kenta_post_structure( $id, $structure, $metas, $args = [] ) {
		$args = wp_parse_args( $args, [
			'title_link'   => false,
			'title_tag'    => 'h1',
			'excerpt_type' => 'full',
			'options'      => CZ::getFacadeRoot(),
			'settings'     => [],
		] );

		$options    = $args['options'];
		$settings   = $args['settings'];
		$title_link = $args['title_link'];
		$title_tag  = $args['title_tag'];

		kenta_app()->instance( 'store.excerpt_more_text', (string) $options->get( 'kenta_' . $id . '_excerpt_more_text', $settings ) );
		kenta_app()->instance( 'store.excerpt_length', (string) $options->get( 'kenta_' . $id . '_excerpt_length', $settings ) );

		$content_open = false;
		?>
		<?php foreach ( $structure as $item ): ?>
			<?php if (
				$item === 'thumbnail' && ( has_post_thumbnail() || (
						CZ::checked( 'kenta_' . $id . '_thumbnail_use_fallback' )
						&& CZ::hasImage( 'kenta_post_featured_image_fallback' )
					) )
			): ?>
				<?php
				if ( $content_open ) {
					$content_open = false;
					echo '</div>';
				}
				?>

                <a href="<?php the_permalink() ?>" class="<?php Utils::the_clsx( [
					'card-thumbnail entry-thumbnail last:mb-0',
					'card-content' => ! $options->checked( 'kenta_' . $id . '_thumbnail_full_width', $settings )
				] ); ?>">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( $options->get( 'kenta_' . $id . '_thumbnail_size' ), [
							'class' => 'w-full h-full',
						] );
					} else {
						echo '<img class="w-full h-full wp-post-image" ' . Utils::render_attribute_string( CZ::imgAttrs( 'kenta_post_featured_image_fallback' ) ) . ' />';
					}
					?>
                </a>
			<?php else: ?>
				<?php
				if ( ! $content_open ) {
					$content_open = true;
					echo '<div class="card-content flex-grow">';
				}
				?>
			<?php endif; ?>
			<?php if ( $item === 'title' && ! $title_link ): ?>
                <div class="entry-title mb-gutter last:mb-0">
					<?php the_title( "<$title_tag>", "</$title_tag>" ); ?>
                </div>
			<?php endif; ?>
			<?php if ( $item === 'title' && $title_link ): ?>
				<?php
				echo wp_kses_post( sprintf(
					'<%1$s class="entry-title mb-half-gutter last:mb-0">%2$s %3$s</%1$s>',
					$title_tag,
					sprintf(
						'<a class="link" href="%1$s" rel="bookmark">%2$s</a>',
						esc_url( get_permalink() ),
						get_the_title()
					),
					( get_edit_post_link() ? sprintf(
						'<span class="link text-xs font-normal"><a href="%1$s">%2$s</a></span>',
						get_edit_post_link(),
						__( 'Edit', 'kenta' )
					) : '' )
				) );
				?>
			<?php endif; ?>
			<?php if ( $item === 'metas' ): ?>
                <div class="entry-metas mb-half-gutter last:mb-0">
					<?php kenta_post_metas( $id, $metas, [
						'style' => 'entry-meta-link',
					], $options, $settings ); ?>
                </div>
			<?php endif; ?>
			<?php if ( $item === 'categories' ): ?>
				<?php
				kenta_post_categories(
					'<div class="entry-categories cat-taxonomies break-words mb-2 last:mb-0" data-tax-type="' . $options->get( 'kenta_' . $id . '_tax_style_cats', $settings ) . '">',
					'</div>',
					[ 'entry-tax-item mr-2 last:mr-0' ]
				);
				?>
			<?php endif; ?>

			<?php if ( $item === 'tags' ): ?>
				<?php
				kenta_post_tags(
					'<div class="entry-tags cat-taxonomies mb-2 break-words last:mb-0" data-tax-type="' . $options->get( 'kenta_' . $id . '_tax_style_tags', $settings ) . '">',
					'</div>',
					[ 'entry-tax-item mr-2 last:mr-0' ]
				);
				?>
			<?php endif; ?>
			<?php if ( $item === 'excerpt' ): ?>
                <div class="entry-excerpt mb-gutter last:mb-0">
					<?php
					if ( $args['excerpt_type'] === 'full' ) {
						echo get_the_content();
					} else {
						echo get_the_excerpt();
					}
					?>
                </div>
			<?php endif; ?>
			<?php if ( $item === 'read-more' ): ?>
                <div class="mb-gutter last:mb-0">
                    <a class="<?php Utils::the_clsx( [
						'kenta-button',
						'kenta-button-' . $options->get( 'kenta_' . $id . '_read_more_arrow_dir', $settings ),
						'entry-read-more'
					] ); ?>" href="<?php the_permalink() ?>" rel="bookmark">
						<?php
						if ( $options->checked( 'kenta_' . $id . '_read_more_show_arrow', $settings ) ) {
							echo '<span class="kenta-button-icon">';
							IconsManager::print( $options->get( 'kenta_' . $id . '_read_more_arrow', $settings ) );
							echo '</span>';
						}
						?>
                        <span class="kenta-button-text">
                            <?php echo esc_html( $options->get( 'kenta_' . $id . '_read_more_text', $settings ) ); ?>
                        </span>
                    </a>
                </div>
			<?php endif; ?>
			<?php if ( $item === 'divider' ): ?>
                <div class="<?php Utils::the_clsx( [
					'entry-divider',
					'full-width' => $options->checked( 'kenta_' . $id . '_divider_full_width', $settings )
				] ); ?>"></div>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php
	}
}

if ( ! function_exists( 'kenta_post_elements_css' ) ) {
	/**
	 * Generate dynamic css for post elements
	 *
	 * @param $scope_selector
	 * @param $id
	 * @param $elements
	 * @param null $options
	 * @param array $settings
	 *
	 * @return array
	 */
	function kenta_post_elements_css( $scope_selector, $id, $elements, $options = null, array $settings = [] ) {
		$options = $options ?? CZ::getFacadeRoot();
		$css     = [];

		foreach ( $elements as $element ) {

			// title
			if ( $element === 'title' ) {
				$css["$scope_selector .entry-title"] = array_merge(
					Css::typography( $options->get( 'kenta_' . $id . '_title_typography', $settings ) ),
					Css::colors( $options->get( 'kenta_' . $id . '_title_color', $settings ), [
						'initial' => '--kenta-link-initial-color',
						'hover'   => '--kenta-link-hover-color',
					] ) );
			}

			// taxonomies
			if ( $element === 'categories' || $element === 'tags' ) {
				$tax      = $element === 'categories' ? '_cats' : '_tags';
				$selector = ".entry-{$element}";

				$tax_css  = Css::typography( $options->get( 'kenta_' . $id . '_tax_typography' . $tax, $settings ) );
				$tax_type = $options->get( 'kenta_' . $id . '_tax_style' . $tax, $settings );

				if ( $tax_type === 'default' ) {
					$tax_css = array_merge(
						$tax_css,
						[
							'--kenta-tax-bg-initial' => 'var(--kenta-transparent)',
							'--kenta-tax-bg-hover'   => 'var(--kenta-transparent)',
						],
						Css::colors( $options->get( 'kenta_' . $id . '_tax_default_color' . $tax, $settings ), [
							'initial' => '--kenta-tax-text-initial',
							'hover'   => '--kenta-tax-text-hover',
						] )
					);
				} else {
					$tax_css = array_merge(
						$tax_css,
						Css::colors( $options->get( 'kenta_' . $id . '_tax_badge_text_color' . $tax, $settings ), [
							'initial' => '--kenta-tax-text-initial',
							'hover'   => '--kenta-tax-text-hover',
						] ),
						Css::colors( $options->get( 'kenta_' . $id . '_tax_badge_bg_color' . $tax, $settings ), [
							'initial' => '--kenta-tax-bg-initial',
							'hover'   => '--kenta-tax-bg-hover',
						] )
					);
				}

				$css["$scope_selector $selector"] = $tax_css;
			}

			// excerpt
			if ( $element === 'excerpt' ) {
				$css["$scope_selector .entry-excerpt"] = array_merge(
					Css::typography( $options->get( 'kenta_' . $id . '_excerpt_typography', $settings ) ),
					Css::colors( $options->get( 'kenta_' . $id . '_excerpt_color', $settings ), [
						'initial' => 'color'
					] )
				);
			}

			// divider
			if ( $element === 'divider' ) {
				$css["$scope_selector .entry-divider"] = Css::border(
					$options->get( 'kenta_' . $id . '_divider', $settings ),
					'--entry-divider'
				);
			}

			// metas
			if ( $element === 'metas' ) {
				$css["$scope_selector .entry-metas"] = array_merge(
					Css::typography( $options->get( 'kenta_' . $id . '_meta_typography', $settings ) ),
					Css::colors( $options->get( 'kenta_' . $id . '_meta_color', $settings ), [
						'initial' => '--kenta-meta-link-initial-color',
						'hover'   => '--kenta-meta-link-hover-color',
					] )
				);
			}

			// thumbnail
			if ( $element === 'thumbnail' ) {
				$css["$scope_selector .entry-thumbnail"] = array_merge(
					[ 'height' => CZ::get( 'kenta_' . $id . '_thumbnail_height' ) ],
					Css::dimensions( CZ::get( 'kenta_' . $id . '_thumbnail_radius' ), 'border-radius' ),
					Css::shadow( CZ::get( 'kenta_' . $id . '_thumbnail_shadow' ) ),
					Css::filters( CZ::get( 'kenta_' . $id . '_thumbnail_filter' ) )
				);
			}

			if ( $element === 'read-more' ) {
				$preset = kenta_button_preset( 'kenta_' . $id . '_read_more_', CZ::get( 'kenta_' . $id . '_read_more_preset' ) );

				$css["$scope_selector .entry-read-more"] = array_merge(
					[
						'--kenta-button-height' => CZ::get( 'kenta_' . $id . '_read_more_min_height' )
					],
					Css::shadow( CZ::get( 'kenta_' . $id . '_read_more_shadow', $preset ) ),
					Css::typography( CZ::get( 'kenta_' . $id . '_read_more_typography', $preset ) ),
					Css::dimensions( CZ::get( 'kenta_' . $id . '_read_more_padding', $preset ), '--kenta-button-padding' ),
					Css::dimensions( CZ::get( 'kenta_' . $id . '_read_more_radius', $preset ), '--kenta-button-radius' ),
					Css::colors( CZ::get( 'kenta_' . $id . '_read_more_text_color', $preset ), [
						'initial' => '--kenta-button-text-initial-color',
						'hover'   => '--kenta-button-text-hover-color',
					] ),
					Css::colors( CZ::get( 'kenta_' . $id . '_read_more_button_color', $preset ), [
						'initial' => '--kenta-button-initial-color',
						'hover'   => '--kenta-button-hover-color',
					] ),
					Css::border( CZ::get( 'kenta_' . $id . '_read_more_border', $preset ), '--kenta-button-border' )
				);

				$css["$scope_selector .entry-read-more:hover"] = Css::shadow( CZ::get( 'kenta_' . $id . '_read_more_shadow_active', $preset ) );
			}
		}

		return $css;
	}
}

if ( ! function_exists( 'kenta_show_article_feature_image' ) ) {
	/**
	 * Show feature image
	 *
	 * @param string $preview_location
	 * @param $prefix
	 */
	function kenta_show_article_feature_image( $preview_location, $prefix ) {

		$thumb_attrs = [
			'class' => $prefix . '_feature_image article-featured-image kenta-max-w-content mx-auto',
		];

		if ( is_customize_preview() ) {
			$thumb_attrs['data-shortcut']          = 'border';
			$thumb_attrs['data-shortcut-location'] = $preview_location . ':' . $prefix . '_featured_image';
		}

		do_action( 'kenta_before_render_featured_image', $prefix );
		if ( has_post_thumbnail() || CZ::hasImage( $prefix . '_featured_image_fallback' ) ) {
			$width = CZ::get( $prefix . '_featured_image_width' );
			$size  = CZ::get( $prefix . '_featured_image_size' );
			$attrs = array(
				'class' => Utils::clsx( [
					'h-full object-center object-cover',
					'w-full'    => $width === 'default',
					'alignwide' => $width === 'wide',
					'alignfull' => $width === 'full',
				] )
			);

			echo '<div ' . Utils::render_attribute_string( $thumb_attrs ) . '>';
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( $size, $attrs );
			} else {
				// Show fallback image
				$attrs = array_merge( CZ::imgAttrs( $prefix . '_featured_image_fallback' ), $attrs );
				echo '<img ' . Utils::render_attribute_string( $attrs ) . '/>';
			}
			echo '</div>';
		}
		do_action( 'kenta_after_render_featured_image', $prefix );
	}
}

if ( ! function_exists( 'kenta_show_article_header' ) ) {
	/**
	 * Show article header
	 *
	 * @param $preview_location
	 * @param string $type
	 * @param bool $header
	 * @param bool $image
	 */
	function kenta_show_article_header( $preview_location, string $type = 'post', $header = true, $image = true ) {

		$prefix           = 'kenta_' . $type;
		$header_elements  = CZ::layers( $prefix . '_header_elements' );
		$header_available = $header
		                    && ! empty( $header_elements )
		                    && kenta_get_current_post_meta( 'disable-article-header' ) !== 'yes'
		                    && CZ::checked( "{$prefix}_header" );

		if ( is_front_page() && ! is_home() ) {
			$header_available = CZ::checked( 'kenta_show_frontpage_header' );
		}

		$has_featured_image = $image && CZ::checked( "{$prefix}_featured_image" );
		$featured_image_pos = CZ::get( "{$prefix}_featured_image_position" );

		$header_attrs = [
			'class' => "kenta-{$type}-header" . ' kenta-article-header kenta-max-w-content has-global-padding mx-auto relative z-[1]',
		];

		if ( is_customize_preview() ) {
			$header_attrs['data-shortcut']          = 'border';
			$header_attrs['data-shortcut-location'] = "$preview_location:{$prefix}_header";
		}

		if ( $has_featured_image ) {
			if ( $featured_image_pos === 'above' ) {
				kenta_show_article_feature_image( $preview_location, $prefix );
			}

			if ( $featured_image_pos === 'behind' && $header_available ) {

				$background_attrs = [
					'class' => 'kenta-article-header-background alignfull mb-gutter',
				];

				if ( is_customize_preview() ) {
					$background_attrs['data-shortcut']          = 'border';
					$background_attrs['data-shortcut-location'] = $preview_location . ':' . $prefix . '_featured_image';
				}

				echo '<div ' . Utils::render_attribute_string( $background_attrs ) . '>';
				kenta_show_article_feature_image( $preview_location, $prefix );
				echo '<div class="container mx-auto px-gutter relative">';
			}
		}
		?>

		<?php if ( $header_available ): ?>
            <header <?php Utils::print_attribute_string( $header_attrs ); ?>>
				<?php
				kenta_post_structure( $type, $header_elements, CZ::layers( "{$prefix}_metas" ), [
					'title_link' => false,
					'title_tag'  => CZ::get( "{$prefix}_title_tag" ),
				] );
				?>
            </header>
		<?php endif; ?>

		<?php
		if ( $has_featured_image ) {
			if ( $featured_image_pos === 'behind' && $header_available ) {
				echo '</div></div>';
			}

			if ( $featured_image_pos === 'below' ) {
				kenta_show_article_feature_image( $preview_location, $prefix );
			}
		}
		?>
		<?php
	}
}

if ( ! function_exists( 'kenta_show_article' ) ) {
	/**
	 * Show article content
	 *
	 * @param $preview_location
	 * @param string $type
	 * @param bool $header
	 */
	function kenta_show_article( $preview_location, string $type = 'post', $header = true ) {
		$content_attrs = [
			'class' => Utils::clsx( apply_filters( 'kenta_article_content_classes', array(
				'kenta-article-content',
				'is-layout-constrained',
				'kenta-entry-content entry-content',
				'has-global-padding',
				'clearfix',
				'mx-auto'
			), $type ) ),
		];

		if ( is_customize_preview() ) {
			$content_attrs['data-shortcut']          = 'border';
			$content_attrs['data-shortcut-location'] = 'kenta_content';
		}

		?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php kenta_show_article_header( $preview_location, $type, $header ); ?>

            <!-- Article Content -->
            <div <?php Utils::print_attribute_string( $content_attrs ); ?>>

				<?php

				the_content();

				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'kenta' ),
					'after'  => '</div>',
				) );
				?>
            </div>
        </article>
		<?php
	}
}

if ( ! function_exists( 'kenta_button_preset' ) ) {
	/**
	 * Get button preset style
	 *
	 * @param $id
	 * @param $preset
	 *
	 * @return array|mixed
	 */
	function kenta_button_preset( $id, $preset ) {
		$presets = [
			'ghost'   => [
				$id . 'text_color'    => [
					'initial' => 'var(--kenta-primary-color)',
					'hover'   => 'var(--kenta-primary-active)',
				],
				$id . 'button_color'  => [
					'initial' => 'var(--kenta-transparent)',
					'hover'   => 'var(--kenta-transparent)',
				],
				$id . 'border'        => [
					'style' => 'none',
				],
				$id . 'shadow'        => [
					'enable' => 'no'
				],
				$id . 'shadow_active' => [
					'enable' => 'no'
				],
				$id . 'padding'       => [
					'top'    => '0px',
					'right'  => '0px',
					'bottom' => '0px',
					'left'   => '0px',
				],
			],
			'solid'   => [
				$id . 'shadow_active' => [
					'enable'     => 'yes',
					'color'      => 'var(--kenta-accent-color)',
					'horizontal' => '0',
					'vertical'   => '5px',
					'blur'       => '10px',
					'spread'     => '-5px',
				],
			],
			'outline' => [
				$id . 'text_color'   => [
					'initial' => 'var(--kenta-primary-color)',
					'hover'   => 'var(--kenta-base-color)',
				],
				$id . 'button_color' => [
					'initial' => 'var(--kenta-transparent)',
					'hover'   => 'var(--kenta-primary-color)',
				],
				$id . 'border'       => [
					'style' => 'solid',
					'width' => 1,
					'color' => 'var(--kenta-primary-color)',
					'hover' => 'var(--kenta-primary-color)',
				],
			],
			'invert'  => [
				$id . 'button_color' => [
					'initial' => 'var(--kenta-accent-color)',
					'hover'   => 'var(--kenta-primary-color)',
				],
				$id . 'border'       => [
					'style' => 'solid',
					'width' => 1,
					'color' => 'var(--kenta-accent-color)',
					'hover' => 'var(--kenta-primary-color)',
				],
			],
			'primary' => [
				$id . 'button_color' => [
					'initial' => 'var(--kenta-primary-color)',
					'hover'   => 'var(--kenta-primary-active)',
				],
				$id . 'border'       => [
					'style' => 'solid',
					'width' => 1,
					'color' => 'var(--kenta-primary-color)',
					'hover' => 'var(--kenta-primary-active)',
				],
			],
			'accent'  => [
				$id . 'button_color'  => [
					'initial' => 'var(--kenta-accent-color)',
					'hover'   => 'var(--kenta-accent-active)',
				],
				$id . 'border'        => [
					'style' => 'solid',
					'width' => 1,
					'color' => 'var(--kenta-accent-color)',
					'hover' => 'var(--kenta-accent-active)',
				],
				$id . 'shadow_active' => [
					'enable'     => 'yes',
					'color'      => 'var(--kenta-accent-active)',
					'horizontal' => '0',
					'vertical'   => '5px',
					'blur'       => '10px',
					'spread'     => '-5px',
				],
			],
		];

		return apply_filters( $id . 'preset_args', $presets[ $preset ] ?? [], $id, $preset );
	}
}

if ( ! function_exists( 'kenta_card_style_preset_options' ) ) {
	/**
	 * Card style options
	 *
	 * @return array
	 */
	function kenta_card_style_preset_options() {
		return array(
			'ghost'                 => __( 'Ghost', 'kenta' ),
			'plain'                 => __( 'Plain', 'kenta' ),
			'bordered'              => __( 'Bordered', 'kenta' ),
			'shadowed'              => __( 'Shadowed', 'kenta' ),
			'mixed'                 => __( 'Mixed', 'kenta' ),
			'inner-shadow'          => __( 'Inner Shadow', 'kenta' ),
			'inner-shadow-bordered' => __( 'Inner Shadow Bordered', 'kenta' ),
			'solid-shadow'          => __( 'Solid Shadow', 'kenta' ),
			'active'                => __( 'Active', 'kenta' ),
			'custom'                => __( 'Custom (Premium)', 'kenta' ),
		);
	}
}

if ( ! function_exists( 'kenta_card_preset_style' ) ) {
	function kenta_card_preset_style( $preset ) {
		switch ( $preset ) {
			case 'ghost':
				return [
					'background' => 'none',
					'border'     => 'none',
					'box-shadow' => 'none',
				];
			case 'plain':
				return [
					'background' => 'var(--kenta-base-color)',
					'border'     => 'none',
					'box-shadow' => 'none',
				];
			case 'bordered':
				return [
					'background' => 'var(--kenta-base-color)',
					'border'     => '1px solid var(--kenta-base-300)',
					'box-shadow' => 'none',
				];
			case 'shadowed':
				return [
					'border'     => 'none',
					'background' => 'var(--kenta-base-color)',
					'box-shadow' => '0 0 14px rgba(70,71,73,0.1)'
				];
			case 'mixed':
				return [
					'background' => 'var(--kenta-base-color)',
					'border'     => '1px solid var(--kenta-base-300)',
					'box-shadow' => '0 0 14px rgba(70,71,73,0.1)'
				];
			case 'inner-shadow':
				return [
					'border'     => 'none',
					'background' => 'var(--kenta-base-color)',
					'box-shadow' => 'rgba(44,62,80,0.25) 0px 20px 16px -15px'
				];
			case 'inner-shadow-bordered':
				return [
					'border'     => '1px solid var(--kenta-base-300)',
					'background' => 'var(--kenta-base-color)',
					'box-shadow' => 'rgba(44,62,80,0.25) 0px 20px 16px -15px'
				];
			case 'solid-shadow':
				return [
					'border'     => '2px solid var(--kenta-base-300)',
					'background' => 'var(--kenta-base-color)',
					'box-shadow' => 'var(--kenta-base-200) 10px 10px 0px 0px'
				];
			case 'active':
				return [
					'border'     => 'none',
					'border-top' => '3px solid var(--kenta-primary-color)',
					'background' => 'var(--kenta-base-color)',
					'box-shadow' => '0 1px 2px rgba(70,71,73,0.15)'
				];
		}

		return array();
	}
}

if ( ! function_exists( 'kenta_form_style_presets' ) ) {
	/**
	 * @return array
	 */
	function kenta_form_style_presets() {
		return [
			'.kenta-form-classic' => [],
			'.kenta-form-modern'  => [
				'--kenta-form-border-top'             => 'none',
				'--kenta-form-border-right'           => 'none',
				'--kenta-form-border-left'            => 'none',
				'--kenta-form-border-bottom'          => '2px solid var(--kenta-form-border-color)',
				'--kenta-form-checkbox-border-top'    => '2px solid var(--kenta-form-background-color)',
				'--kenta-form-checkbox-border-right'  => '2px solid var(--kenta-form-background-color)',
				'--kenta-form-checkbox-border-left'   => '2px solid var(--kenta-form-background-color)',
				'--kenta-form-checkbox-border-bottom' => '2px solid var(--kenta-form-border-color)',
			],
		];
	}
}

if ( ! function_exists( 'kenta_get_current_post_meta' ) ) {
	/**
	 * Get post meta value
	 *
	 * @param $id
	 * @param bool $single
	 *
	 * @return mixed
	 */
	function kenta_get_current_post_meta( $id, $single = true ) {

		global $post;

		if ( ! $post ) {
			return '';
		}

		$meta  = get_post_meta( $post->ID, $id, $single );
		$query = get_query_var( $id );

		return ! empty( $query ) && ( $meta === 'default' || $meta === '' ) ? $query : $meta;
	}
}

if ( ! function_exists( 'kenta_is_transparent_header' ) ) {
	/**
	 * Is transparent header or not
	 *
	 * @return bool
	 */
	function kenta_is_transparent_header() {

		$transparent = kenta_get_current_post_meta( 'site-transparent-header' );
		if ( $transparent === 'default' ) {
			$transparent = CZ::checked( 'kenta_enable_transparent_header' );

			if ( $transparent ) {
				if ( is_archive() || is_home() || is_search() ) {
					$transparent = ! CZ::checked( 'kenta_disable_archive_transparent_header' );
				}

				if ( is_page() && CZ::checked( 'kenta_disable_pages_transparent_header' ) ) {
					$transparent = false;
				}

				if ( is_single() && CZ::checked( 'kenta_disable_posts_transparent_header' ) ) {
					$transparent = false;
				}

				if ( KENTA_WOOCOMMERCE_ACTIVE && is_product() ) {
					$transparent = false;
				}
			}
		} else {
			$transparent = $transparent === 'enable';
		}

		return apply_filters( 'kenta_filter_is_transparent_header', $transparent );
	}
}

if ( ! function_exists( 'kenta_color_presets' ) ) {
	/**
	 * Get kenta color presets
	 *
	 * @return array
	 */
	function kenta_color_presets() {
		$presets = [
			'preset-1'      => [
				'kenta-primary-color'  => '#0258c7',
				'kenta-primary-active' => '#0e80e8',
				'kenta-accent-color'   => '#181f28',
				'kenta-accent-active'  => '#334155',
				'kenta-base-300'       => '#e2e8f0',
				'kenta-base-200'       => '#f1f5f9',
				'kenta-base-100'       => '#f8fafc',
				'kenta-base-color'     => '#ffffff',
			],
			'preset-2'      => [
				'kenta-primary-color'  => '#f8c240',
				'kenta-primary-active' => '#e8950e',
				'kenta-accent-color'   => '#181f28',
				'kenta-accent-active'  => '#334155',
				'kenta-base-300'       => '#e2e8f0',
				'kenta-base-200'       => '#f1f5f9',
				'kenta-base-100'       => '#f8fafc',
				'kenta-base-color'     => '#ffffff',
			],
			'preset-3'      => [
				'kenta-primary-color'  => '#7678ed',
				'kenta-primary-active' => '#5253cd',
				'kenta-accent-color'   => '#181f28',
				'kenta-accent-active'  => '#334155',
				'kenta-base-300'       => '#e2e8f0',
				'kenta-base-200'       => '#f1f5f9',
				'kenta-base-100'       => '#f8fafc',
				'kenta-base-color'     => '#ffffff',
			],
			'preset-4'      => [
				'kenta-primary-color'  => '#00a4db',
				'kenta-primary-active' => '#096dd9',
				'kenta-accent-color'   => '#181f28',
				'kenta-accent-active'  => '#334155',
				'kenta-base-300'       => '#e2e8f0',
				'kenta-base-200'       => '#f1f5f9',
				'kenta-base-100'       => '#f8fafc',
				'kenta-base-color'     => '#ffffff',
			],
			'preset-5'      => [
				'kenta-primary-color'  => '#dc2626',
				'kenta-primary-active' => '#b91c1c',
				'kenta-accent-color'   => '#181f28',
				'kenta-accent-active'  => '#334155',
				'kenta-base-300'       => '#e2e8f0',
				'kenta-base-200'       => '#f1f5f9',
				'kenta-base-100'       => '#f8fafc',
				'kenta-base-color'     => '#ffffff',
			],
			'preset-6'      => [
				'kenta-primary-color'  => '#0d9488',
				'kenta-primary-active' => '#10b981',
				'kenta-accent-color'   => '#181f28',
				'kenta-accent-active'  => '#334155',
				'kenta-base-300'       => '#e2e8f0',
				'kenta-base-200'       => '#f1f5f9',
				'kenta-base-100'       => '#f8fafc',
				'kenta-base-color'     => '#ffffff',
			],
			'high-contrast' => [
				'kenta-primary-color'  => '#000000',
				'kenta-primary-active' => '#000000',
				'kenta-accent-color'   => '#000000',
				'kenta-accent-active'  => '#000000',
				'kenta-base-300'       => '#000000',
				'kenta-base-200'       => '#000000',
				'kenta-base-100'       => '#ffffff',
				'kenta-base-color'     => '#ffffff',
			],
		];

		return apply_filters( 'kenta_filter_color_presets', $presets );
	}
}

if ( ! function_exists( 'kenta_dark_color_presets' ) ) {
	function kenta_dark_color_presets() {
		$presets = [
			'preset-1'      => [
				'kenta-primary-color'  => '#0258c7',
				'kenta-primary-active' => '#0e80e8',
				'kenta-accent-color'   => '#f3f4f6',
				'kenta-accent-active'  => '#a3a9a3',
				'kenta-base-300'       => '#353f49',
				'kenta-base-200'       => '#2a323b',
				'kenta-base-100'       => '#212a33',
				'kenta-base-color'     => '#17212a',
			],
			'preset-2'      => [
				'kenta-primary-color'  => '#f8c240',
				'kenta-primary-active' => '#e8950e',
				'kenta-accent-color'   => '#f3f4f6',
				'kenta-accent-active'  => '#a3a9a3',
				'kenta-base-300'       => '#353f49',
				'kenta-base-200'       => '#2a323b',
				'kenta-base-100'       => '#212a33',
				'kenta-base-color'     => '#17212a',
			],
			'preset-3'      => [
				'kenta-primary-color'  => '#7678ed',
				'kenta-primary-active' => '#5253cd',
				'kenta-accent-color'   => '#f3f4f6',
				'kenta-accent-active'  => '#a3a9a3',
				'kenta-base-300'       => '#353f49',
				'kenta-base-200'       => '#2a323b',
				'kenta-base-100'       => '#212a33',
				'kenta-base-color'     => '#17212a',
			],
			'preset-4'      => [
				'kenta-primary-color'  => '#00a4db',
				'kenta-primary-active' => '#096dd9',
				'kenta-accent-color'   => '#f3f4f6',
				'kenta-accent-active'  => '#a3a9a3',
				'kenta-base-300'       => '#353f49',
				'kenta-base-200'       => '#2a323b',
				'kenta-base-100'       => '#212a33',
				'kenta-base-color'     => '#17212a',
			],
			'preset-5'      => [
				'kenta-primary-color'  => '#dc2626',
				'kenta-primary-active' => '#b91c1c',
				'kenta-accent-color'   => '#f3f4f6',
				'kenta-accent-active'  => '#a3a9a3',
				'kenta-base-300'       => '#353f49',
				'kenta-base-200'       => '#2a323b',
				'kenta-base-100'       => '#212a33',
				'kenta-base-color'     => '#17212a',
			],
			'preset-6'      => [
				'kenta-primary-color'  => '#0d9488',
				'kenta-primary-active' => '#10b981',
				'kenta-accent-color'   => '#f3f4f6',
				'kenta-accent-active'  => '#a3a9a3',
				'kenta-base-300'       => '#353f49',
				'kenta-base-200'       => '#2a323b',
				'kenta-base-100'       => '#212a33',
				'kenta-base-color'     => '#17212a',
			],
			'high-contrast' => [
				'kenta-primary-color'  => '#ffffff',
				'kenta-primary-active' => '#ffffff',
				'kenta-accent-color'   => '#ffffff',
				'kenta-accent-active'  => '#ffffff',
				'kenta-base-300'       => '#ffffff',
				'kenta-base-200'       => '#ffffff',
				'kenta-base-100'       => '#000000',
				'kenta-base-color'     => '#000000',
			],
		];

		return apply_filters( 'kenta_filter_dark_color_presets', $presets );
	}
}

if ( ! function_exists( 'kenta_show_archive_header' ) ) {
	/**
	 * Show archive header
	 */
	function kenta_show_archive_header() {

		$should_show_archive_header =
			! ( is_home() && CZ::checked( 'kenta_disable_blogs_archive_header' ) )
			&& ! ( is_search() && ! have_posts() )
			&& ! ( kenta_is_woo_shop() && CZ::checked( 'kenta_disable_shop_archive_header' ) );

		if ( ! apply_filters( 'kenta_should_show_archive_header', $should_show_archive_header ) ) {
			return;
		}

		$attrs = [
			'class' => Utils::clsx( array(
				'kenta-archive-header has-global-padding' => true,
				'kenta-archive-header-has-overlay'        => CZ::checked( 'kenta_archive_header_has_overlay' ),
			) )
		];

		if ( is_customize_preview() ) {
			$attrs['data-shortcut']          = 'border';
			$attrs['data-shortcut-location'] = 'kenta_archive:kenta_archive_title';
		}

		?>
        <section <?php \LottaFramework\Utils::print_attribute_string( $attrs ); ?>>
            <div class="container kenta-max-w-wide mx-auto">
				<?php
				if ( is_search() ) {
					?>
                    <h1 class="archive-title">
						<?php
						/* translators: %s: Keywords searched by users */
						printf( CZ::get( 'kenta_search_archive_header_prefix' ) . ' %s', '<span>' . get_search_query() . '</span>' );
						?>
                    </h1>
					<?php
				} else if ( kenta_is_woo_shop() ) {
					?>
                    <h1 class="archive-title"><?php woocommerce_page_title(); ?></h1>
                    <div class="archive-description mt-half-gutter"><?php woocommerce_taxonomy_archive_description(); ?></div>
					<?php
				} else {
					the_archive_title( '<h1 class="archive-title">', '</h1>' );
					the_archive_description( '<div class="archive-description mt-half-gutter">', '</div>' );
				}
				?>
            </div>
        </section>
		<?php
	}
}

if ( ! function_exists( 'kenta_show_share_box' ) ) {
	/**
	 * @param $type
	 * @param $location
	 */
	function kenta_show_share_box( $type, $location ) {
		$color = CZ::get( 'kenta_' . $type . '_share_box_icons_color_type' );
		$shape = CZ::get( 'kenta_' . $type . '_share_box_icons_shape' );
		$fill  = CZ::get( 'kenta_' . $type . '_share_box_shape_fill_type' );

		$attrs = [
			'class' => Utils::clsx( [
				'kenta-socials',
				'kenta-' . $type . '-socials',
				'kenta-socials-' . $color,
				'kenta-socials-' . $shape,
				'kenta-socials-' . $fill => $shape !== 'none',
			] )
		];

		if ( is_customize_preview() ) {
			$attrs['data-shortcut']          = 'border';
			$attrs['data-shortcut-location'] = $location;
		}

		$link_attrs = [
			'class' => 'kenta-social-link',
		];

		if ( CZ::checked( 'kenta_' . $type . '_share_box_open_new_tab' ) ) {
			$link_attrs['target'] = '_blank';
		}

		if ( CZ::checked( 'kenta_' . $type . '_share_box_no_follow' ) ) {
			$link_attrs['rel'] = 'nofollow';
		}

		$socials = CZ::repeater( 'kenta_social_networks' );
		?>
        <div class="mx-auto kenta-max-w-content has-global-padding">
            <div <?php Utils::print_attribute_string( $attrs ); ?>>
				<?php
				foreach ( $socials as $social ) {
					if ( isset( $social['share'] ) ) {
						$home_url  = Utils::encode_uri_component( get_the_permalink() );
						$share_url = str_replace(
							'{url}',
							$home_url,
							str_replace(
								'{text}',
								Utils::encode_uri_component( get_the_title() ),
								$social['share']
							)
						);

						?>
                        <a <?php Utils::print_attribute_string( $link_attrs ); ?>
                                style="--kenta-official-color: <?php echo esc_attr( $social['color']['official'] ?? 'var(--kenta-primary-active)' ) ?>;"
                                href="<?php echo esc_url( $share_url ) ?>">
                            <span class="kenta-social-icon">
                                <?php IconsManager::print( $social['icon'] ); ?>
                            </span>
                        </a>
						<?php
					}
				}
				?>
            </div>
        </div>
		<?php
	}
}

if ( ! function_exists( 'kenta_scroll_reveal_args' ) ) {
	/**
	 * Scroll reveal args
	 *
	 * @return array
	 */
	function kenta_scroll_reveal_args() {
		return [
			'delay'    => absint( CZ::get( 'kenta_scroll_reveal_delay' ) ),
			'duration' => absint( CZ::get( 'kenta_scroll_reveal_duration' ) ),
			'interval' => absint( CZ::get( 'kenta_scroll_reveal_interval' ) ),
			'opacity'  => floatval( CZ::get( 'kenta_scroll_reveal_opacity' ) ),
			'scale'    => floatval( CZ::get( 'kenta_scroll_reveal_scale' ) ),
			'origin'   => CZ::get( 'kenta_scroll_reveal_origin' ),
			'distance' => CZ::get( 'kenta_scroll_reveal_distance' ),
		];
	}
}

if ( ! function_exists( 'kenta_get_preloader' ) ) {
	/**
	 * Get preloader
	 *
	 * @param $id
	 *
	 * @return array
	 */
	function kenta_get_preloader( $id ) {
		$presets = [
			'preset-1'  => [
				'html'      => '<div class="kenta-preloader-1"></div>',
				'css'       => [
					'.kenta-preloader-1'        => [
						'width'         => '48px',
						'height'        => '48px',
						'margin'        => 'auto',
						'border'        => '3px solid var(--kenta-preloader-accent)',
						'border-radius' => '50%',
						'display'       => 'inline-block',
						'position'      => 'relative',
						'box-sizing'    => 'border-box',
						'animation'     => 'preloaderAnim 1s linear infinite',
					],
					'.kenta-preloader-1::after' => [
						'content'             => "''",
						'box-sizing'          => 'border-box',
						'position'            => 'absolute',
						'left'                => '50%',
						'top'                 => '50%',
						'transform'           => 'translate(-50%, -50%)',
						'width'               => '40px',
						'height'              => '40px',
						'border-radius'       => '50%',
						'border'              => '3px solid transparent',
						'border-bottom-color' => 'var(--kenta-preloader-primary)',
					],
				],
				'keyframes' => [
					'preloaderAnim' => [
						'0%'   => [ 'transform' => 'rotate(0deg)' ],
						'100%' => [ 'transform' => 'rotate(360deg)' ],
					],
				],
			],
			'preset-2'  => [
				'html'      => '<div class="kenta-preloader-2"></div>',
				'css'       => [
					'.kenta-preloader-2' => [
						'width'               => '48px',
						'height'              => '48px',
						'margin'              => 'auto',
						'border'              => '5px solid var(--kenta-preloader-accent)',
						'border-bottom-color' => 'var(--kenta-preloader-primary)',
						'border-radius'       => '50%',
						'display'             => 'inline-block',
						'box-sizing'          => 'border-box',
						'animation'           => 'preloaderAnim 1s linear infinite',
					]
				],
				'keyframes' => [
					'preloaderAnim' => [
						'0%'   => [ 'transform' => 'rotate(0deg)' ],
						'100%' => [ 'transform' => 'rotate(360deg)' ],
					],
				],
			],
			'preset-3'  => [
				'html'      => '<div class="kenta-preloader-3"></div>',
				'css'       => [
					'.kenta-preloader-3'                                   => [
						'width'         => '48px',
						'height'        => '48px',
						'margin'        => 'auto',
						'border-radius' => '50%',
						'display'       => 'inline-block',
						'position'      => 'relative',
						'border'        => '3px solid',
						'border-color'  => 'var(--kenta-preloader-accent) var(--kenta-preloader-accent) transparent transparent',
						'box-sizing'    => 'border-box',
						'animation'     => 'preloaderAnim 1s linear infinite',
					],
					'.kenta-preloader-3::after,.kenta-preloader-3::before' => [
						'content'          => "''",
						'box-sizing'       => 'border-box',
						'position'         => 'absolute',
						'left'             => '0',
						'right'            => '0',
						'top'              => '0',
						'bottom'           => '0',
						'margin'           => 'auto',
						'border'           => '3px solid',
						'border-color'     => 'transparent transparent var(--kenta-preloader-primary ) var(--kenta-preloader-primary )',
						'width'            => '40px',
						'height'           => '40px',
						'border-radius'    => '50%',
						'animation'        => 'rotationBack 0.5s linear infinite',
						'transform-origin' => 'center center',
					],
					'.kenta-preloader-3::before'                           => [
						'width'        => '32px',
						'height'       => '32px',
						'border-color' => 'var(--kenta-preloader-accent ) var(--kenta-preloader-accent ) transparent transparent',
						'animation'    => 'rotation 1.5s linear infinite',
					],
				],
				'keyframes' => [
					'preloaderAnim' => [
						'0%'   => [ 'transform' => 'rotate(0deg)' ],
						'100%' => [ 'transform' => 'rotate(360deg)' ],
					],
					'rotationBack'  => [
						'0%'   => [ 'transform' => 'rotate(0deg)' ],
						'100%' => [ 'transform' => 'rotate(-360deg)' ],
					],
				],
			],
			'preset-4'  => [
				'html'      => '<div class="kenta-preloader-4"></div>',
				'css'       => [
					'.kenta-preloader-4'                                   => [
						'transform'     => 'rotateZ(45deg)',
						'perspective'   => '1000px',
						'border-radius' => '50%',
						'width'         => '48px',
						'height'        => '48px',
						'margin'        => 'auto',
						'color'         => 'var(--kenta-preloader-accent)',
					],
					'.kenta-preloader-4::before,.kenta-preloader-4::after' => [
						'content'       => "''",
						'display'       => 'block',
						'position'      => 'absolute',
						'top'           => '0',
						'left'          => '0',
						'width'         => 'inherit',
						'height'        => 'inherit',
						'border-radius' => '50%',
						'transform'     => 'rotateX(70deg)',
						'animation'     => '1s spin linear infinite',
					],
					'.kenta-preloader-4::after'                            => [
						'color'           => 'var(--kenta-preloader-primary)',
						'transform'       => 'rotateY(70deg)',
						'animation-delay' => '.4s',
					]
				],
				'keyframes' => [
					'rotate'    => [
						'0%'   => [
							'transform' => 'translate(-50%, -50%) rotateZ(0deg)',
						],
						'100%' => [
							'transform' => 'translate(-50%, -50%) rotateZ(360deg)',
						],
					],
					'rotateccw' => [
						'0%'   => [
							'transform' => 'translate(-50%, -50%) rotate(0deg)',
						],
						'100%' => [
							'transform' => 'translate(-50%, -50%) rotate(-360deg)',
						],
					],
					'spin'      => [
						'0%,'  => [],
						'100%' => [
							'box-shadow' => '.2em 0px 0 0px currentcolor',
						],
						'12%'  => [
							'box-shadow' => '.2em .2em 0 0 currentcolor',
						],
						'25%'  => [
							'box-shadow' => '0 .2em 0 0px currentcolor',
						],
						'37%'  => [
							'box-shadow' => '-.2em .2em 0 0 currentcolor',
						],
						'50%'  => [
							'box-shadow' => '-.2em 0 0 0 currentcolor',
						],
						'62%'  => [
							'box-shadow' => '-.2em -.2em 0 0 currentcolor',
						],
						'75%'  => [
							'box-shadow' => '0px -.2em 0 0 currentcolor',
						],
						'87%'  => [
							'box-shadow' => '.2em -.2em 0 0 currentcolor',
						],
					],
				],
			],
			'preset-5'  => [
				'html'      => '<div class="kenta-preloader-5"></div>',
				'css'       => [
					'.kenta-preloader-5'        => [
						'width'         => '48px',
						'height'        => '48px',
						'margin'        => 'auto',
						'border-radius' => '50%',
						'display'       => 'inline-block',
						'border-top'    => '4px solid var(--kenta-preloader-accent)',
						'border-right'  => '4px solid transparent',
						'box-sizing'    => 'border-box',
						'animation'     => 'preloaderAnim 1s linear infinite',
					],
					'.kenta-preloader-5::after' => [
						'content'       => "''",
						'box-sizing'    => 'border-box',
						'position'      => 'absolute',
						'left'          => '0',
						'top'           => '0',
						'width'         => '48px',
						'height'        => '48px',
						'border-radius' => '50%',
						'border-left'   => '4px solid var(--kenta-preloader-primary)',
						'border-bottom' => '4px solid transparent',
						'animation'     => 'preloaderAnim 0.5s linear infinite reverse',
					],
				],
				'keyframes' => [
					'preloaderAnim' => [
						'0%'   => [ 'transform' => 'rotate(0deg)' ],
						'100%' => [ 'transform' => 'rotate(360deg)' ],
					]
				],
			],
			'preset-6'  => [
				'html'      => '<div class="kenta-preloader-6"></div>',
				'css'       => [
					'.kenta-preloader-6' => [
						'position'      => 'relative',
						'margin'        => 'auto',
						'border'        => '24px solid var(--kenta-preloader-accent)',
						'border-radius' => '50%',
						'transform'     => 'rotate(45deg)',
						'animation'     => 'pieFill 3s linear infinite',
					]
				],
				'keyframes' => [
					'pieFill' => [
						'0%, 19%'   => [ 'border-color' => 'var(--kenta-preloader-accent) var(--kenta-preloader-accent) var(--kenta-preloader-accent) var(--kenta-preloader-accent)' ],
						'20%, 39%'  => [ 'border-color' => 'var(--kenta-preloader-primary) var(--kenta-preloader-accent) var(--kenta-preloader-accent) var(--kenta-preloader-accent)' ],
						'40%, 59%'  => [ 'border-color' => 'var(--kenta-preloader-primary) var(--kenta-preloader-primary) var(--kenta-preloader-accent) var(--kenta-preloader-accent)' ],
						'60%, 79%'  => [ 'border-color' => 'var(--kenta-preloader-primary) var(--kenta-preloader-primary) var(--kenta-preloader-primary) var(--kenta-preloader-accent)' ],
						'80%, 100%' => [ 'border-color' => 'var(--kenta-preloader-primary) var(--kenta-preloader-primary) var(--kenta-preloader-primary) var(--kenta-preloader-primary)' ],
					],
				],
			],
			'preset-7'  => [
				'html'      => '<div class="kenta-preloader-7"></div>',
				'css'       => [
					'.kenta-preloader-7'                                    => [
						'width'         => '8px',
						'height'        => '40px',
						'border-radius' => '4px',
						'display'       => 'block',
						'margin'        => '20px auto',
						'position'      => 'relative',
						'background'    => 'currentColor',
						'color'         => 'var(--kenta-preloader-accent)',
						'box-sizing'    => 'border-box',
						'animation'     => 'preloaderAnim 0.3s 0.3s linear infinite alternate',
					],
					'.kenta-preloader-7::after, .kenta-preloader-7::before' => [
						'content'       => "''",
						'width'         => '8px',
						'height'        => '40px',
						'border-radius' => '4px',
						'background'    => 'currentColor',
						'position'      => 'absolute',
						'top'           => '50%',
						'transform'     => 'translateY(-50%)',
						'left'          => '20px',
						'box-sizing'    => 'border-box',
						'animation'     => 'preloaderAnim 0.3s 0.45s linear infinite alternate',
					],
					'.kenta-preloader-7::before'                            => [
						'left'            => '-20px',
						'animation-delay' => '0s',
					],
				],
				'keyframes' => [
					'preloaderAnim' => [
						'0%'   => [ 'height' => '48px' ],
						'100%' => [ 'height' => '4px' ]
					],
				],
			],
			'preset-8'  => [
				'html'      => '<div class="kenta-preloader-8"></div>',
				'css'       => [
					'.kenta-preloader-8' => [
						'width'         => '48px',
						'height'        => '6px',
						'display'       => 'block',
						'margin'        => 'auto',
						'position'      => 'relative',
						'border-radius' => '4px',
						'color'         => 'var(--kenta-preloader-accent)',
						'box-sizing'    => 'border-box',
						'animation'     => 'preloaderAnim 0.6s linear infinite',
					]
				],
				'keyframes' => [
					'preloaderAnim' => [
						'0%'   => [ 'box-shadow' => '-10px 20px, 10px 35px, 0px 50px' ],
						'25%'  => [ 'box-shadow' => '0px 20px, 0px 35px, 10px 50px' ],
						'50%'  => [ 'box-shadow' => '10px 20px, -10px 35px, 0px 50px' ],
						'75%'  => [ 'box-shadow' => '0px 20px, 0px 35px, -10px 50px' ],
						'100%' => [ 'box-shadow' => '-10px 20px, 10px 35px, 0px 50px' ],
					],
				],
			],
			'preset-9'  => [
				'html'      => '<div class="kenta-preloader-9"></div>',
				'css'       => [
					'.kenta-preloader-9' => [
						'width'         => '8px',
						'height'        => '48px',
						'margin'        => 'auto',
						'display'       => 'inline-block',
						'position'      => 'relative',
						'border-radius' => '4px',
						'color'         => 'var(--kenta-preloader-accent)',
						'box-sizing'    => 'border-box',
						'animation'     => 'preloaderAnim 0.6s linear infinite',
					]
				],
				'keyframes' => [
					'preloaderAnim' => [
						'0%'   => [ 'box-shadow' => '20px -10px, 40px 10px, 60px 0px' ],
						'25%'  => [ 'box-shadow' => '20px 0px, 40px 0px, 60px 10px' ],
						'50%'  => [ 'box-shadow' => '20px 10px, 40px -10px, 60px 0px' ],
						'75%'  => [ 'box-shadow' => '20px 0px, 40px 0px, 60px -10px' ],
						'100%' => [ 'box-shadow' => '20px -10px, 40px 10px, 60px 0px' ],
					],
				],
			],
			'preset-10' => [
				'html'      => '<div class="kenta-preloader-10"></div>',
				'css'       => [
					'.kenta-preloader-10'                                    => [
						'width'         => '4.8px',
						'height'        => '4.8px',
						'display'       => 'block',
						'margin'        => '20px auto',
						'position'      => 'relative',
						'border-radius' => '4px',
						'color'         => 'var(--kenta-preloader-accent)',
						'background'    => 'currentColor',
						'box-sizing'    => 'border-box',
						'animation'     => 'preloaderAnim 0.3s 0.3s linear infinite alternate',
					],
					'.kenta-preloader-10::after,.kenta-preloader-10::before' => [
						'content'       => "''",
						'box-sizing'    => 'border-box',
						'width'         => '4.8px',
						'height'        => '4.8px',
						'border-radius' => '4px',
						'background'    => 'currentColor',
						'position'      => 'absolute',
						'left'          => '50%',
						'transform'     => 'translateX(-50%)',
						'top'           => '15px',
						'animation'     => 'preloaderAnim 0.3s 0.45s linear infinite alternate',
					],
					'.kenta-preloader-10::after'                             => [
						'top'             => '-15px',
						'animation-delay' => '0s',
					],
				],
				'keyframes' => [
					'preloaderAnim' => [
						'0%'   => [ 'width' => '4.8px' ],
						'100%' => [ 'width' => '48px' ]
					]
				],
			],
		];

		return $presets[ $id ] ?? [ 'html' => '', 'css' => [] ];
	}
}
