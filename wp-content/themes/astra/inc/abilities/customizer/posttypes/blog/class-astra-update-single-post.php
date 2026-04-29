<?php
/**
 * Update Single Post Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Single_Post
 */
class Astra_Update_Single_Post extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-single-post';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Single Post Settings', 'astra' );
		$this->description = __( 'Updates the Astra theme single post settings including container layout, container style, sidebar layout, sidebar style, content width, and related posts settings.', 'astra' );

		$this->meta = array(
			'tool_type' => 'write',
		);
	}

	/**
	 * Get tool type.
	 *
	 * @return string
	 */
	public function get_tool_type() {
		return 'write';
	}

	/**
	 * Get input schema.
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'container_layout'      => array(
					'type'        => 'string',
					'description' => __( 'Container layout for single posts. Options: "default" (Default), "normal-width-container" (Normal), "narrow-width-container" (Narrow), "full-width-container" (Full Width).', 'astra' ),
					'enum'        => array( 'default', 'normal-width-container', 'narrow-width-container', 'full-width-container' ),
				),
				'container_style'       => array(
					'type'        => 'string',
					'description' => __( 'Container style. Options: "boxed" (Boxed), "unboxed" (Unboxed).', 'astra' ),
					'enum'        => array( 'boxed', 'unboxed' ),
				),
				'sidebar_layout'        => array(
					'type'        => 'string',
					'description' => __( 'Sidebar layout for single posts. Options: "default" (Default), "no-sidebar" (No Sidebar), "left-sidebar" (Left Sidebar), "right-sidebar" (Right Sidebar).', 'astra' ),
					'enum'        => array( 'default', 'no-sidebar', 'left-sidebar', 'right-sidebar' ),
				),
				'sidebar_style'         => array(
					'type'        => 'string',
					'description' => __( 'Sidebar style for single posts. Options: "default" (Default), "unboxed" (Unboxed), "boxed" (Boxed).', 'astra' ),
					'enum'        => array( 'default', 'unboxed', 'boxed' ),
				),
				'content_width'         => array(
					'type'        => 'string',
					'description' => __( 'Content width setting. Options: "default" (Default), "custom" (Custom).', 'astra' ),
					'enum'        => array( 'default', 'custom' ),
				),
				'content_max_width'     => array(
					'type'        => 'integer',
					'description' => __( 'Custom content max width in pixels (0-1920). Only applies when content_width is set to "custom".', 'astra' ),
				),
				'related_posts_enabled' => array(
					'type'        => 'boolean',
					'description' => __( 'Enable or disable related posts display on single posts.', 'astra' ),
				),
				'related_posts_count'   => array(
					'type'        => 'integer',
					'description' => __( 'Number of related posts to display (1-20).', 'astra' ),
				),
			),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'set single post container to full width',
			'change post sidebar to left',
			'enable related posts',
			'set post container to narrow',
			'update post sidebar style to boxed',
			'disable related posts',
			'change post layout to normal width',
			'set related posts count to 3',
			'update single post to no sidebar',
			'set post container style to unboxed',
			'change post content width to custom',
			'enable related posts with 4 items',
			'set single post to right sidebar',
			'update post container to boxed',
			'change post layout to default',
			'set custom content width to 900px',
			'update sidebar to default style',
			'enable 6 related posts',
			'set post to full width no sidebar',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$updated         = false;
		$update_messages = array();

		if ( isset( $args['container_layout'] ) && ! empty( $args['container_layout'] ) ) {
			$container_layout = sanitize_text_field( $args['container_layout'] );
			$valid_layouts    = array( 'default', 'normal-width-container', 'narrow-width-container', 'full-width-container' );
			if ( ! in_array( $container_layout, $valid_layouts, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: container layout value */
					sprintf( __( 'Invalid container_layout: %s.', 'astra' ), $container_layout ),
					__( 'Valid options: default, normal-width-container, narrow-width-container, full-width-container', 'astra' )
				);
			}

			$layout_labels = array(
				'default'                => 'Default',
				'normal-width-container' => 'Normal',
				'narrow-width-container' => 'Narrow',
				'full-width-container'   => 'Full Width',
			);

			astra_update_option( 'single-post-ast-content-layout', $container_layout );
			$updated           = true;
			$update_messages[] = sprintf( 'Container layout set to %s', $layout_labels[ $container_layout ] );
		}

		if ( isset( $args['container_style'] ) && ! empty( $args['container_style'] ) ) {
			$container_style = sanitize_text_field( $args['container_style'] );
			$valid_styles    = array( 'boxed', 'unboxed' );
			if ( ! in_array( $container_style, $valid_styles, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: container style value */
					sprintf( __( 'Invalid container_style: %s.', 'astra' ), $container_style ),
					__( 'Valid options: boxed, unboxed', 'astra' )
				);
			}

			$style_labels = array(
				'boxed'   => 'Boxed',
				'unboxed' => 'Unboxed',
			);

			astra_update_option( 'site-content-style', $container_style );
			$updated           = true;
			$update_messages[] = sprintf( 'Container style set to %s', $style_labels[ $container_style ] );
		}

		if ( isset( $args['sidebar_layout'] ) && ! empty( $args['sidebar_layout'] ) ) {
			$sidebar_layout = sanitize_text_field( $args['sidebar_layout'] );
			$valid_sidebars = array( 'default', 'no-sidebar', 'left-sidebar', 'right-sidebar' );
			if ( ! in_array( $sidebar_layout, $valid_sidebars, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: sidebar layout value */
					sprintf( __( 'Invalid sidebar_layout: %s.', 'astra' ), $sidebar_layout ),
					__( 'Valid options: default, no-sidebar, left-sidebar, right-sidebar', 'astra' )
				);
			}

			$sidebar_labels = array(
				'default'       => 'Default',
				'no-sidebar'    => 'No Sidebar',
				'left-sidebar'  => 'Left Sidebar',
				'right-sidebar' => 'Right Sidebar',
			);

			astra_update_option( 'single-post-sidebar-layout', $sidebar_layout );
			$updated           = true;
			$update_messages[] = sprintf( 'Sidebar layout set to %s', $sidebar_labels[ $sidebar_layout ] );
		}

		if ( isset( $args['sidebar_style'] ) && ! empty( $args['sidebar_style'] ) ) {
			$sidebar_style = sanitize_text_field( $args['sidebar_style'] );
			$valid_styles  = array( 'default', 'unboxed', 'boxed' );
			if ( ! in_array( $sidebar_style, $valid_styles, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: sidebar style value */
					sprintf( __( 'Invalid sidebar_style: %s.', 'astra' ), $sidebar_style ),
					__( 'Valid options: default, unboxed, boxed', 'astra' )
				);
			}

			$style_labels = array(
				'default' => 'Default',
				'unboxed' => 'Unboxed',
				'boxed'   => 'Boxed',
			);

			astra_update_option( 'single-post-sidebar-style', $sidebar_style );
			$updated           = true;
			$update_messages[] = sprintf( 'Sidebar style set to %s', $style_labels[ $sidebar_style ] );
		}

		if ( isset( $args['content_width'] ) && ! empty( $args['content_width'] ) ) {
			$content_width = sanitize_text_field( $args['content_width'] );
			$valid_widths  = array( 'default', 'custom' );
			if ( ! in_array( $content_width, $valid_widths, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: content width value */
					sprintf( __( 'Invalid content_width: %s.', 'astra' ), $content_width ),
					__( 'Valid options: default, custom', 'astra' )
				);
			}

			astra_update_option( 'blog-single-width', $content_width );
			$updated           = true;
			$update_messages[] = sprintf( 'Content width set to %s', ucfirst( $content_width ) );
		}

		if ( isset( $args['content_max_width'] ) ) {
			$content_max_width = absint( $args['content_max_width'] );
			if ( $content_max_width > 1920 ) {
				return Astra_Abilities_Response::error(
					/* translators: %d: content max width value */
					sprintf( __( 'Invalid content_max_width: %d.', 'astra' ), $content_max_width ),
					__( 'Value must be between 0 and 1920 pixels.', 'astra' )
				);
			}

			astra_update_option( 'blog-single-max-width', $content_max_width );
			$updated           = true;
			$update_messages[] = sprintf( 'Content max width set to %dpx', $content_max_width );
		}

		if ( isset( $args['related_posts_enabled'] ) ) {
			$related_posts_enabled = (bool) $args['related_posts_enabled'];
			astra_update_option( 'enable-related-posts', $related_posts_enabled );
			$updated           = true;
			$update_messages[] = sprintf( 'Related posts %s', $related_posts_enabled ? 'enabled' : 'disabled' );
		}

		if ( isset( $args['related_posts_count'] ) ) {
			$related_posts_count = absint( $args['related_posts_count'] );
			if ( $related_posts_count < 1 || $related_posts_count > 20 ) {
				return Astra_Abilities_Response::error(
					/* translators: %d: related posts count value */
					sprintf( __( 'Invalid related_posts_count: %d.', 'astra' ), $related_posts_count ),
					__( 'Value must be between 1 and 20.', 'astra' )
				);
			}

			astra_update_option( 'related-posts-total-count', $related_posts_count );
			$updated           = true;
			$update_messages[] = sprintf( 'Related posts count set to %d', $related_posts_count );
		}

		if ( ! $updated ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide at least one setting to update.', 'astra' )
			);
		}

		$message = implode( ', ', $update_messages ) . '.';

		return Astra_Abilities_Response::success(
			$message,
			array(
				'updated' => true,
			)
		);
	}
}

Astra_Update_Single_Post::register();
