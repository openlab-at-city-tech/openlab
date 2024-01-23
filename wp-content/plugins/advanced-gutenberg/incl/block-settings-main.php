<?php

namespace PublishPress\Blocks;

/*
 * PublishPress blocks configurations
 */
if ( ! class_exists( '\\PublishPress\\Blocks\\Configuration' ) ) {
	class Configuration {
		public static function defaultConfig() {
			return array(
				'advgb-accordions'    => array(
					array(
						'label'    => __( 'Accordion Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Bottom spacing', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'marginBottom',
								'min'   => 0,
								'max'   => 50,
							),
							array(
								'title' => __( 'Initial Collapsed', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'collapsedAll',
							),
						)
					),
					array(
						'label'    => __( 'Header Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'headerBgColor',
							),
							array(
								'title' => __( 'Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'headerTextColor'
							),
							array(
								'title'   => __( 'Header Icon', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'headerIcon',
								'options' => array(
									array(
										'label' => __( 'Plus', 'advanced-gutenberg' ),
										'value' => 'plus',
									),
									array(
										'label' => __( 'Plus Circle', 'advanced-gutenberg' ),
										'value' => 'plusCircle',
									),
									array(
										'label' => __( 'Plus Circle Outline', 'advanced-gutenberg' ),
										'value' => 'plusCircleOutline',
									),
									array(
										'label' => __( 'Plus Square Outline', 'advanced-gutenberg' ),
										'value' => 'plusBox',
									),
									array(
										'label' => __( 'Unfold Arrow', 'advanced-gutenberg' ),
										'value' => 'unfold',
									),
									array(
										'label' => __( 'Horizontal Dots', 'advanced-gutenberg' ),
										'value' => 'threeDots',
									),
									array(
										'label' => __( 'Arrow Down', 'advanced-gutenberg' ),
										'value' => 'arrowDown',
									),
								)
							),
							array(
								'title' => __( 'Header Icon Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'headerIconColor',
							),
						),
					),
					array(
						'label'    => __( 'Body Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'bodyBgColor',
							),
							array(
								'title' => __( 'Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'bodyTextColor',
							),
						),
					),
					array(
						'label'    => __( 'Border Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Border Style', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'borderStyle',
								'options' => array(
									array(
										'label' => __( 'Solid', 'advanced-gutenberg' ),
										'value' => 'solid',
									),
									array(
										'label' => __( 'Dashed', 'advanced-gutenberg' ),
										'value' => 'dashed',
									),
									array(
										'label' => __( 'Dotted', 'advanced-gutenberg' ),
										'value' => 'dotted',
									),
								)
							),
							array(
								'title' => __( 'Border Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'borderColor',
							),
							array(
								'title' => __( 'Border Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'borderWidth',
								'min'   => 1,
								'max'   => 10,
							),
							array(
								'title' => __( 'Border Radius', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'borderRadius',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
				),
				'advgb-button'        => array(
					array(
						'label'    => __( 'Text and Color', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Text Size', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textSize',
								'min'   => 10,
								'max'   => 100,
							),
							array(
								'title' => __( 'Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'textColor'
							),
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'bgColor',
							),
						),
					),
					array(
						'label'    => __( 'Border Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Border Style', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'borderStyle',
								'options' => array(
									array(
										'label' => __( 'Solid', 'advanced-gutenberg' ),
										'value' => 'solid',
									),
									array(
										'label' => __( 'Dashed', 'advanced-gutenberg' ),
										'value' => 'dashed',
									),
									array(
										'label' => __( 'Dotted', 'advanced-gutenberg' ),
										'value' => 'dotted',
									),
								)
							),
							array(
								'title' => __( 'Border Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'borderColor',
							),
							array(
								'title' => __( 'Border Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'borderWidth',
								'min'   => 1,
								'max'   => 10,
							),
							array(
								'title' => __( 'Border Radius', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'borderRadius',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
					array(
						'label'    => __( 'Margin Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Margin Top', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'marginTop',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Right', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'marginRight',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Bottom', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'marginBottom',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Left', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'marginLeft',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
					array(
						'label'    => __( 'Padding Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Padding Top', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'paddingTop',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Right', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'paddingRight',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Bottom', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'paddingBottom',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Left', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'paddingLeft',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
					array(
						'label'    => __( 'Hover Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'hoverTextColor'
							),
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'hoverBgColor',
							),
							array(
								'title' => __( 'Shadow Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'hoverShadowColor',
							),
							array(
								'title' => __( 'Shadow H Offset', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'hoverShadowH',
								'min'   => - 50,
								'max'   => 50,
							),
							array(
								'title' => __( 'Shadow V Offset', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'hoverShadowV',
								'min'   => - 50,
								'max'   => 50,
							),
							array(
								'title' => __( 'Shadow Blur', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'hoverShadowBlur',
								'min'   => 0,
								'max'   => 50,
							),
							array(
								'title' => __( 'Shadow Spread', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'hoverShadowSpread',
								'min'   => 0,
								'max'   => 50,
							),
							array(
								'title' => __( 'Transition Speed', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'transitionSpeed',
								'min'   => 0,
								'max'   => 3,
							),
						),
					),
				),
				'advgb-image'         => array(
					array(
						'label'    => __( 'Click action', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Action on click', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'openOnClick',
								'options' => array(
									array(
										'label' => __( 'None', 'advanced-gutenberg' ),
										'value' => 'none',
									),
									array(
										'label' => __( 'Open image in a lightbox', 'advanced-gutenberg' ),
										'value' => 'lightbox',
									),
									array(
										'label' => __( 'Open custom URL', 'advanced-gutenberg' ),
										'value' => 'url',
									),
								),
							),
							array(
								'title' => __( 'Open link in a new tab', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'linkInNewTab',
							),
						)
					),
					array(
						'label'    => __( 'Image Size', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Full width', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'fullWidth',
							),
							array(
								'title' => __( 'Height', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'height',
								'min'   => 100,
								'max'   => 1000,
							),
							array(
								'title' => __( 'Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'width',
								'min'   => 200,
								'max'   => 1300,
							),
						),
					),
					array(
						'label'    => __( 'Color', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Title Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'titleColor',
							),
							array(
								'title' => __( 'Subtitle Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'subtitleColor',
							),
							array(
								'title' => __( 'Overlay Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'overlayColor',
							),
						),
					),
					array(
						'label'    => __( 'Text Alignment', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Vertical Alignment', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'vAlign',
								'options' => array(
									array(
										'label' => __( 'Top', 'advanced-gutenberg' ),
										'value' => 'flex-start',
									),
									array(
										'label' => __( 'Center', 'advanced-gutenberg' ),
										'value' => 'center',
									),
									array(
										'label' => __( 'Bottom', 'advanced-gutenberg' ),
										'value' => 'flex-end',
									),
								),
							),
							array(
								'title'   => __( 'Horizontal Alignment', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'hAlign',
								'options' => array(
									array(
										'label' => __( 'Left', 'advanced-gutenberg' ),
										'value' => 'flex-start',
									),
									array(
										'label' => __( 'Center', 'advanced-gutenberg' ),
										'value' => 'center',
									),
									array(
										'label' => __( 'Right', 'advanced-gutenberg' ),
										'value' => 'flex-end',
									),
								),
							),
						),
					),
				),
				'advgb-list'          => array(
					array(
						'label'    => __( 'Text Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Text Size', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'fontSize',
								'min'   => 10,
								'max'   => 100,
							),
						),
					),
					array(
						'label'    => __( 'Icon Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Icon style', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'icon',
								'options' => array(
									array(
										'label' => __( 'None', 'advanced-gutenberg' ),
										'value' => '',
									),
									array(
										'label' => __( 'Pushpin', 'advanced-gutenberg' ),
										'value' => 'admin-post',
									),
									array(
										'label' => __( 'Configuration', 'advanced-gutenberg' ),
										'value' => 'admin-generic',
									),
									array(
										'label' => __( 'Flag', 'advanced-gutenberg' ),
										'value' => 'flag',
									),
									array(
										'label' => __( 'Star', 'advanced-gutenberg' ),
										'value' => 'star-filled',
									),
									array(
										'label' => __( 'Checkmark', 'advanced-gutenberg' ),
										'value' => 'yes',
									),
									array(
										'label' => __( 'Minus', 'advanced-gutenberg' ),
										'value' => 'minus',
									),
									array(
										'label' => __( 'Plus', 'advanced-gutenberg' ),
										'value' => 'plus',
									),
									array(
										'label' => __( 'Play', 'advanced-gutenberg' ),
										'value' => 'controls-play',
									),
									array(
										'label' => __( 'Arrow Right', 'advanced-gutenberg' ),
										'value' => 'arrow-right-alt',
									),
									array(
										'label' => __( 'X Cross', 'advanced-gutenberg' ),
										'value' => 'dismiss',
									),
									array(
										'label' => __( 'Warning', 'advanced-gutenberg' ),
										'value' => 'warning',
									),
									array(
										'label' => __( 'Help', 'advanced-gutenberg' ),
										'value' => 'editor-help',
									),
									array(
										'label' => __( 'Info', 'advanced-gutenberg' ),
										'value' => 'info',
									),
									array(
										'label' => __( 'Circle', 'advanced-gutenberg' ),
										'value' => 'marker',
									),
								),
							),
							array(
								'title' => __( 'Icon color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'iconColor',
							),
							array(
								'title' => __( 'Icon Size', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconSize',
								'min'   => 10,
								'max'   => 100,
							),
							array(
								'title' => __( 'Line Height', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'lineHeight',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'margin',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'padding',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
				),
				'advgb-table'         => array(
					array(
						'label'    => __( 'Table Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Max width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'maxWidth',
								'min'   => 0,
								'max'   => 1999,
							),
						),
					),
				),
				'advgb-video'         => array(
					array(
						'label'    => __( 'Video Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Open video in lightbox', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'openInLightbox',
							),
							array(
								'title' => __( 'Full width', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'videoFullWidth'
							),
							array(
								'title' => __( 'Video Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'videoWidth',
								'min'   => 100,
								'max'   => 1000,
							),
							array(
								'title' => __( 'Video Height', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'videoHeight',
								'min'   => 300,
								'max'   => 7000,
							),
							array(
								'title' => __( 'Overlay color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'overlayColor',
							),
						),
					),
					array(
						'label'    => __( 'Play Button Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Button Icon', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'playButtonIcon',
								'options' => array(
									array(
										'label' => __( 'Normal', 'advanced-gutenberg' ),
										'value' => 'normal',
									),
									array(
										'label' => __( 'Filled Circle', 'advanced-gutenberg' ),
										'value' => 'circleFill',
									),
									array(
										'label' => __( 'Outline Circle', 'advanced-gutenberg' ),
										'value' => 'circleOutline',
									),
									array(
										'label' => __( 'Video Camera', 'advanced-gutenberg' ),
										'value' => 'videoCam',
									),
									array(
										'label' => __( 'Filled Square', 'advanced-gutenberg' ),
										'value' => 'squareCurved',
									),
									array(
										'label' => __( 'Star Sticker', 'advanced-gutenberg' ),
										'value' => 'starSticker',
									),
								)
							),
							array(
								'title' => __( 'Button Size', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'playButtonSize',
								'min'   => 40,
								'max'   => 200,
							),
							array(
								'title' => __( 'Button Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'playButtonColor',
							),
						),
					),
				),
				'advgb-count-up'      => array(
					array(
						'label'    => __( 'General Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Number of columns', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'columns',
								'min'   => 1,
								'max'   => 3,
							),
						),
					),
					array(
						'label'    => __( 'Color Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Header Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'headerTextColor',
							),
							array(
								'title' => __( 'Count Up Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'countUpNumberColor',
							),
							array(
								'title' => __( 'Description Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'descTextColor',
							),
						),
					),
					array(
						'label'    => __( 'Count Up Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Count Up Number Size', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'countUpNumberSize',
								'min'   => 10,
								'max'   => 100,
							),
						),
					),
				),
				'advgb-map'           => array(
					array(
						'label'    => __( 'Location Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'type'  => 'hidden',
								'name'  => 'useLatLng',
								'value' => 1,
							),
							array(
								'title' => __( 'Latitude', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'lat',
								'min'   => 0,
								'max'   => 999,
							),
							array(
								'title' => __( 'Longitude', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'lng',
								'min'   => 0,
								'max'   => 999,
							),
						),
					),
					array(
						'label'    => __( 'Map Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Zoom Level', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'zoom',
								'min'   => 0,
								'max'   => 25,
							),
							array(
								'title' => __( 'Height', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'height',
								'min'   => 300,
								'max'   => 1000,
							),
							array(
								'title'   => __( 'Map style', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'mapStyle',
								'options' => array(
									array( 'label' => __( 'Silver', 'advanced-gutenberg' ), 'value' => 'silver' ),
									array( 'label' => __( 'Retro', 'advanced-gutenberg' ), 'value' => 'retro' ),
									array( 'label' => __( 'Dark', 'advanced-gutenberg' ), 'value' => 'dark' ),
									array( 'label' => __( 'Night', 'advanced-gutenberg' ), 'value' => 'night' ),
									array( 'label' => __( 'Aubergine', 'advanced-gutenberg' ), 'value' => 'aubergine' ),
									array( 'label' => __( 'Custom', 'advanced-gutenberg' ), 'value' => 'custom' ),
								)
							),
						),
					),
					array(
						'label'    => __( 'Marker Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Marker Title', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'markerTitle',
							),
							array(
								'title' => __( 'Marker Description', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'markerDesc',
							),
						),
					),
				),
				'advgb-social-links'  => array(
					array(
						'label'    => __( 'Icon 1 Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Icon', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'icon1.icon',
								'options' => array(
									array( 'label' => __( 'Blogger', 'advanced-gutenberg' ), 'value' => 'blogger' ),
									array( 'label' => __( 'Facebook', 'advanced-gutenberg' ), 'value' => 'facebook' ),
									array( 'label' => __( 'Flickr', 'advanced-gutenberg' ), 'value' => 'flickr' ),
									array( 'label' => __( 'Google Plus', 'advanced-gutenberg' ), 'value' => 'google' ),
									array( 'label' => __( 'Instagram', 'advanced-gutenberg' ), 'value' => 'instagram' ),
									array( 'label' => __( 'LinkedIn', 'advanced-gutenberg' ), 'value' => 'linkedin' ),
									array( 'label' => __( 'Email', 'advanced-gutenberg' ), 'value' => 'mail' ),
									array( 'label' => __( 'Picasa', 'advanced-gutenberg' ), 'value' => 'picasa' ),
									array( 'label' => __( 'Pinterest', 'advanced-gutenberg' ), 'value' => 'pinterest' ),
									array( 'label' => __( 'Reddit', 'advanced-gutenberg' ), 'value' => 'reddit' ),
									array( 'label' => __( 'Skype', 'advanced-gutenberg' ), 'value' => 'skype' ),
									array(
										'label' => __( 'Sound Cloud', 'advanced-gutenberg' ),
										'value' => 'soundcloud'
									),
									array( 'label' => __( 'Tumblr', 'advanced-gutenberg' ), 'value' => 'tumblr' ),
									array( 'label' => __( 'Twitter', 'advanced-gutenberg' ), 'value' => 'twitter' ),
									array( 'label' => __( 'Vimeo', 'advanced-gutenberg' ), 'value' => 'vimeo' ),
									array( 'label' => __( 'Youtube', 'advanced-gutenberg' ), 'value' => 'youtube' ),
								)
							),
							array(
								'title' => __( 'Icon Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'icon1.iconColor',
							),
							array(
								'title' => __( 'Icon Link', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'icon1.link',
							),
						),
					),
					array(
						'label'    => __( 'Icon 2 Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Icon', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'icon2.icon',
								'options' => array(
									array( 'label' => __( 'Blogger', 'advanced-gutenberg' ), 'value' => 'blogger' ),
									array( 'label' => __( 'Facebook', 'advanced-gutenberg' ), 'value' => 'facebook' ),
									array( 'label' => __( 'Flickr', 'advanced-gutenberg' ), 'value' => 'flickr' ),
									array( 'label' => __( 'Google Plus', 'advanced-gutenberg' ), 'value' => 'google' ),
									array( 'label' => __( 'Instagram', 'advanced-gutenberg' ), 'value' => 'instagram' ),
									array( 'label' => __( 'LinkedIn', 'advanced-gutenberg' ), 'value' => 'linkedin' ),
									array( 'label' => __( 'Email', 'advanced-gutenberg' ), 'value' => 'mail' ),
									array( 'label' => __( 'Picasa', 'advanced-gutenberg' ), 'value' => 'picasa' ),
									array( 'label' => __( 'Pinterest', 'advanced-gutenberg' ), 'value' => 'pinterest' ),
									array( 'label' => __( 'Reddit', 'advanced-gutenberg' ), 'value' => 'reddit' ),
									array( 'label' => __( 'Skype', 'advanced-gutenberg' ), 'value' => 'skype' ),
									array(
										'label' => __( 'Sound Cloud', 'advanced-gutenberg' ),
										'value' => 'soundcloud'
									),
									array( 'label' => __( 'Tumblr', 'advanced-gutenberg' ), 'value' => 'tumblr' ),
									array( 'label' => __( 'Twitter', 'advanced-gutenberg' ), 'value' => 'twitter' ),
									array( 'label' => __( 'Vimeo', 'advanced-gutenberg' ), 'value' => 'vimeo' ),
									array( 'label' => __( 'Youtube', 'advanced-gutenberg' ), 'value' => 'youtube' ),
								)
							),
							array(
								'title' => __( 'Icon Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'icon2.iconColor',
							),
							array(
								'title' => __( 'Icon Link', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'icon2.link',
							),
						),
					),
					array(
						'label'    => __( 'Icon 3 Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Icon', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'icon3.icon',
								'options' => array(
									array( 'label' => __( 'Blogger', 'advanced-gutenberg' ), 'value' => 'blogger' ),
									array( 'label' => __( 'Facebook', 'advanced-gutenberg' ), 'value' => 'facebook' ),
									array( 'label' => __( 'Flickr', 'advanced-gutenberg' ), 'value' => 'flickr' ),
									array( 'label' => __( 'Google Plus', 'advanced-gutenberg' ), 'value' => 'google' ),
									array( 'label' => __( 'Instagram', 'advanced-gutenberg' ), 'value' => 'instagram' ),
									array( 'label' => __( 'LinkedIn', 'advanced-gutenberg' ), 'value' => 'linkedin' ),
									array( 'label' => __( 'Email', 'advanced-gutenberg' ), 'value' => 'mail' ),
									array( 'label' => __( 'Picasa', 'advanced-gutenberg' ), 'value' => 'picasa' ),
									array( 'label' => __( 'Pinterest', 'advanced-gutenberg' ), 'value' => 'pinterest' ),
									array( 'label' => __( 'Reddit', 'advanced-gutenberg' ), 'value' => 'reddit' ),
									array( 'label' => __( 'Skype', 'advanced-gutenberg' ), 'value' => 'skype' ),
									array(
										'label' => __( 'Sound Cloud', 'advanced-gutenberg' ),
										'value' => 'soundcloud'
									),
									array( 'label' => __( 'Tumblr', 'advanced-gutenberg' ), 'value' => 'tumblr' ),
									array( 'label' => __( 'Twitter', 'advanced-gutenberg' ), 'value' => 'twitter' ),
									array( 'label' => __( 'Vimeo', 'advanced-gutenberg' ), 'value' => 'vimeo' ),
									array( 'label' => __( 'Youtube', 'advanced-gutenberg' ), 'value' => 'youtube' ),
								)
							),
							array(
								'title' => __( 'Icon Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'icon3.iconColor',
							),
							array(
								'title' => __( 'Icon Link', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'icon3.link',
							),
						),
					),
					array(
						'label'    => __( 'General Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Icon Alignment', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'align',
								'options' => array(
									array( 'label' => __( 'Left', 'advanced-gutenberg' ), 'value' => 'left' ),
									array( 'label' => __( 'Center', 'advanced-gutenberg' ), 'value' => 'center' ),
									array( 'label' => __( 'Right', 'advanced-gutenberg' ), 'value' => 'right' ),
								)
							),
							array(
								'title' => __( 'Icon Size', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconSize',
								'min'   => 20,
								'max'   => 60,
							),
							array(
								'title' => __( 'Icon Space', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconSpace',
								'min'   => 0,
								'max'   => 30,
							),
						),
					),
				),
				'advgb-summary'       => array(
					array(
						'label'    => __( 'General Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Load minimized', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'loadMinimized',
							),
							array(
								'title' => __( 'Table of Contents header title', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'headerTitle',
							),
							array(
								'title' => __( 'Anchor color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'anchorColor',
							),
							array(
								'title'   => __( 'Table of Contents Alignment', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'align',
								'options' => array(
									array( 'label' => __( 'Left', 'advanced-gutenberg' ), 'value' => 'left' ),
									array( 'label' => __( 'Center', 'advanced-gutenberg' ), 'value' => 'center' ),
									array( 'label' => __( 'Right', 'advanced-gutenberg' ), 'value' => 'right' ),
								)
							),
						),
					),
				),
				'advgb-adv-tabs'      => array(
					array(
						'label'    => __( 'Tab Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Tabs Style', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'tabsStyle',
								'options' => array(
									array(
										'label' => __( 'Horizontal', 'advanced-gutenberg' ),
										'value' => 'horz',
									),
									array(
										'label' => __( 'Vertical', 'advanced-gutenberg' ),
										'value' => 'vert',
									),
								)
							),
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'headerBgColor',
							),
							array(
								'title' => __( 'Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'headerTextColor',
							),
						),
					),
					array(
						'label'    => __( 'Active Tab Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'activeTabBgColor',
							),
							array(
								'title' => __( 'Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'activeTabTextColor',
							),
						),
					),
					array(
						'label'    => __( 'Body Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'bodyBgColor',
							),
							array(
								'title' => __( 'Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'bodyTextColor',
							),
						),
					),
					array(
						'label'    => __( 'Border Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Border Style', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'borderStyle',
								'options' => array(
									array( 'label' => __( 'Solid', 'advanced-gutenberg' ), 'value' => 'solid' ),
									array( 'label' => __( 'Dashed', 'advanced-gutenberg' ), 'value' => 'dashed' ),
									array( 'label' => __( 'Dotted', 'advanced-gutenberg' ), 'value' => 'dotted' ),
								)
							),
							array(
								'title' => __( 'Border Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'borderColor',
							),
							array(
								'title' => __( 'Border Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'borderWidth',
								'min'   => 1,
								'max'   => 10,
							),
							array(
								'title' => __( 'Border Radius', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'borderRadius',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
				),
				'advgb-testimonial'   => array(
					array(
						'label'    => __( 'General Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Columns', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'columns',
								'min'   => 1,
								'max'   => 3,
							),
						),
					),
					array(
						'label'    => __( 'Avatar Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'avatarColor',
							),
							array(
								'title' => __( 'Border Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'avatarBorderColor',
							),
							array(
								'title' => __( 'Border Radius (%)', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'avatarBorderRadius',
								'min'   => 0,
								'max'   => 50,
							),
							array(
								'title' => __( 'Border Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'avatarBorderWidth',
								'min'   => 0,
								'max'   => 5,
							),
							array(
								'title' => __( 'Avatar Size', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'avatarSize',
								'min'   => 50,
								'max'   => 130,
							),
						),
					),
					array(
						'label'    => __( 'Text Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Name Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'nameColor',
							),
							array(
								'title' => __( 'Position Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'positionColor',
							),
							array(
								'title' => __( 'Description Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'descColor',
							),
						),
					),
				),
				'advgb-woo-products'  => array(
					array(
						'label'    => __( 'Layout Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Columns', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'columns',
								'min'   => 1,
								'max'   => 4,
							),
							array(
								'title' => __( 'Number of Products', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'numberOfProducts',
								'min'   => 1,
								'max'   => 48,
							),
						),
					),
				),
				'advgb-contact-form'  => array(
					array(
						'label'    => __( 'Text Label', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Name placeholder', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'nameLabel',
							),
							array(
								'title' => __( 'Email placeholder', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'emailLabel',
							),
							array(
								'title' => __( 'Message placeholder', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'msgLabel',
							),
							array(
								'title' => __( 'Submit label', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'submitLabel',
							),
							array(
								'title' => __( 'Success text', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'successLabel',
							),
							array(
								'title' => __( 'Error text', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'alertLabel',
							),
						),
					),
					array(
						'label'    => __( 'Input Color', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'bgColor',
							),
							array(
								'title' => __( 'Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'textColor',
							),
						),
					),
					array(
						'label'    => __( 'Border Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Border Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'borderColor',
							),
							array(
								'title'   => __( 'Border Style', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'borderStyle',
								'options' => array(
									array(
										'label' => __( 'Solid', 'advanced-gutenberg' ),
										'value' => 'solid',
									),
									array(
										'label' => __( 'Dashed', 'advanced-gutenberg' ),
										'value' => 'dashed',
									),
									array(
										'label' => __( 'Dotted', 'advanced-gutenberg' ),
										'value' => 'dotted',
									),
								),
							),
							array(
								'title' => __( 'Border Radius', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'borderRadius',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
					array(
						'label'    => __( 'Submit Button Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Border and Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'submitColor',
							),
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'submitBgColor',
							),
							array(
								'title' => __( 'Border Radius', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'submitRadius',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title'   => __( 'Button Position', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'submitPosition',
								'options' => array(
									array(
										'label' => __( 'Center', 'advanced-gutenberg' ),
										'value' => 'center',
									),
									array(
										'label' => __( 'Left', 'advanced-gutenberg' ),
										'value' => 'left',
									),
									array(
										'label' => __( 'Right', 'advanced-gutenberg' ),
										'value' => 'right',
									),
								),
							),
						),
					),
				),
				'advgb-newsletter'    => array(
					array(
						'label'    => __( 'Form Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Form style', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'formStyle',
								'options' => array(
									array(
										'label' => __( 'Normal', 'advanced-gutenberg' ),
										'value' => 'default',
									),
									array(
										'label' => __( 'Alternative', 'advanced-gutenberg' ),
										'value' => 'alt',
									),
								),
							),
							array(
								'title' => __( 'Form width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'formWidth',
								'min'   => 200,
								'max'   => 1000,
							),
						),
					),
					array(
						'label'    => __( 'Text Label', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'First Name placeholder', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'fnameLabel',
							),
							array(
								'title' => __( 'Last Name placeholder', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'lnameLabel',
							),
							array(
								'title' => __( 'Email placeholder', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'emailLabel',
							),
							array(
								'title' => __( 'Submit label', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'submitLabel',
							),
							array(
								'title' => __( 'Success text', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'successLabel',
							),
							array(
								'title' => __( 'Error text', 'advanced-gutenberg' ),
								'type'  => 'text',
								'name'  => 'alertLabel',
							),
						),
					),
					array(
						'label'    => __( 'Input Color', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'bgColor',
							),
							array(
								'title' => __( 'Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'textColor',
							),
						),
					),
					array(
						'label'    => __( 'Border Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Border Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'borderColor',
							),
							array(
								'title'   => __( 'Border Style', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'borderStyle',
								'options' => array(
									array(
										'label' => __( 'Solid', 'advanced-gutenberg' ),
										'value' => 'solid',
									),
									array(
										'label' => __( 'Dashed', 'advanced-gutenberg' ),
										'value' => 'dashed',
									),
									array(
										'label' => __( 'Dotted', 'advanced-gutenberg' ),
										'value' => 'dotted',
									),
								),
							),
							array(
								'title' => __( 'Border Radius', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'borderRadius',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
					array(
						'label'    => __( 'Submit Button Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Border and Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'submitColor',
							),
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'submitBgColor',
							),
							array(
								'title' => __( 'Border Radius', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'submitRadius',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
				),
				'advgb-images-slider' => array(
					array(
						'label'    => __( 'Images Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Action on click', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'actionOnClick',
								'options' => array(
									array(
										'label' => __( 'Open image in lightbox', 'advanced-gutenberg' ),
										'value' => 'lightbox',
									),
									array(
										'label' => __( 'Open custom link', 'advanced-gutenberg' ),
										'value' => 'link',
									),
								),
							),
							array(
								'title' => __( 'Full width', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'fullWidth',
							),
							array(
								'title' => __( 'Auto Height', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'autoHeight',
							),
							array(
								'title' => __( 'Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'width',
								'min'   => 200,
								'max'   => 1300,
							),
							array(
								'title' => __( 'Height', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'height',
								'min'   => 100,
								'max'   => 1000,
							),
							array(
								'title' => __( 'Always show overlay', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'alwaysShowOverlay',
							),
						),
					),
					array(
						'label'    => __( 'Color Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Hover Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'hoverColor',
							),
							array(
								'title' => __( 'Title Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'titleColor',
							),
							array(
								'title' => __( 'Text Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'textColor',
							),
						),
					),
					array(
						'label'    => __( 'Text Alignment', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Vertical Alignment', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'vAlign',
								'options' => array(
									array(
										'label' => __( 'Top', 'advanced-gutenberg' ),
										'value' => 'flex-start',
									),
									array(
										'label' => __( 'Center', 'advanced-gutenberg' ),
										'value' => 'center',
									),
									array(
										'label' => __( 'Bottom', 'advanced-gutenberg' ),
										'value' => 'flex-end',
									),
								),
							),
							array(
								'title'   => __( 'Horizontal Alignment', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'hAlign',
								'options' => array(
									array(
										'label' => __( 'Left', 'advanced-gutenberg' ),
										'value' => 'flex-start',
									),
									array(
										'label' => __( 'Center', 'advanced-gutenberg' ),
										'value' => 'center',
									),
									array(
										'label' => __( 'Right', 'advanced-gutenberg' ),
										'value' => 'flex-end',
									),
								),
							),
						),
					),
				),
				'advgb-columns'       => array(
					array(
						'label'    => __( 'Columns Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Space between column', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'gutter',
								'options' => array(
									array(
										'label' => __( 'None', 'advanced-gutenberg' ),
										'value' => 0,
									),
									array(
										'label' => __( '10px', 'advanced-gutenberg' ),
										'value' => 10,
									),
									array(
										'label' => __( '20px', 'advanced-gutenberg' ),
										'value' => 20,
									),
									array(
										'label' => __( '30px', 'advanced-gutenberg' ),
										'value' => 30,
									),
									array(
										'label' => __( '40px', 'advanced-gutenberg' ),
										'value' => 40,
									),
									array(
										'label' => __( '50px', 'advanced-gutenberg' ),
										'value' => 50,
									),
									array(
										'label' => __( '70px', 'advanced-gutenberg' ),
										'value' => 70,
									),
									array(
										'label' => __( '90px', 'advanced-gutenberg' ),
										'value' => 90,
									),
								),
							),
							array(
								'title'   => __( 'Vertical space when collapsed', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'collapsedGutter',
								'options' => array(
									array(
										'label' => __( 'None', 'advanced-gutenberg' ),
										'value' => 0,
									),
									array(
										'label' => __( '10px', 'advanced-gutenberg' ),
										'value' => 10,
									),
									array(
										'label' => __( '20px', 'advanced-gutenberg' ),
										'value' => 20,
									),
									array(
										'label' => __( '30px', 'advanced-gutenberg' ),
										'value' => 30,
									),
									array(
										'label' => __( '40px', 'advanced-gutenberg' ),
										'value' => 40,
									),
									array(
										'label' => __( '50px', 'advanced-gutenberg' ),
										'value' => 50,
									),
									array(
										'label' => __( '70px', 'advanced-gutenberg' ),
										'value' => 70,
									),
									array(
										'label' => __( '90px', 'advanced-gutenberg' ),
										'value' => 90,
									),
								),
							),
							array(
								'title' => __( 'Collapsed order RTL', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'collapsedRtl',
							)
						),
					),
					array(
						'label'    => __( 'Row Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Vertical Align', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'vAlign',
								'options' => array(
									array(
										'label' => __( 'Top', 'advanced-gutenberg' ),
										'value' => 'top',
									),
									array(
										'label' => __( 'Middle', 'advanced-gutenberg' ),
										'value' => 'middle',
									),
									array(
										'label' => __( 'Bottom', 'advanced-gutenberg' ),
										'value' => 'bottom',
									),
									array(
										'label' => __( 'Full Height', 'advanced-gutenberg' ),
										'value' => 'full',
									),
								),
							),
							array(
								'title' => __( 'Columns Wrapped', 'advanced-gutenberg' ),
								'type'  => 'checkbox',
								'name'  => 'columnsWrapped',
							),
							array(
								'title'   => __( 'Wrapper Tag', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'wrapperTag',
								'options' => array(
									array(
										'label' => __( 'Div', 'advanced-gutenberg' ),
										'value' => 'div',
									),
									array(
										'label' => __( 'Header', 'advanced-gutenberg' ),
										'value' => 'header',
									),
									array(
										'label' => __( 'Section', 'advanced-gutenberg' ),
										'value' => 'section',
									),
									array(
										'label' => __( 'Main', 'advanced-gutenberg' ),
										'value' => 'main',
									),
									array(
										'label' => __( 'Article', 'advanced-gutenberg' ),
										'value' => 'article',
									),
									array(
										'label' => __( 'Aside', 'advanced-gutenberg' ),
										'value' => 'aside',
									),
									array(
										'label' => __( 'Footer', 'advanced-gutenberg' ),
										'value' => 'footer',
									),
								),
							),
							array(
								'title' => __( 'Content Max Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'contentMaxWidth',
								'min'   => 0,
								'max'   => 2000,
							),
							array(
								'title' => __( 'Content Min Height', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'contentMinHeight',
								'min'   => 0,
								'max'   => 2000,
							),
						),
					),
				),
				'advgb-column'        => array(
					array(
						'label'    => __( 'Border Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Border style', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'borderStyle',
								'options' => array(
									array(
										'label' => __( 'None', 'advanced-gutenberg' ),
										'value' => 'none',
									),
									array(
										'label' => __( 'Solid', 'advanced-gutenberg' ),
										'value' => 'solid',
									),
									array(
										'label' => __( 'Dotted', 'advanced-gutenberg' ),
										'value' => 'dotted',
									),
									array(
										'label' => __( 'Dashed', 'advanced-gutenberg' ),
										'value' => 'dashed',
									),
								),
							),
							array(
								'title' => __( 'Border Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'borderColor',
							),
							array(
								'title' => __( 'Border Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'borderWidth',
								'min'   => 0,
								'max'   => 20,
							),
							array(
								'title' => __( 'Border Radius', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'borderRadius',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
					array(
						'label'    => __( 'Column Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title'   => __( 'Text alignment', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'textAlign',
								'options' => array(
									array(
										'label' => __( 'Left', 'advanced-gutenberg' ),
										'value' => 'left',
									),
									array(
										'label' => __( 'Center', 'advanced-gutenberg' ),
										'value' => 'center',
									),
									array(
										'label' => __( 'Right', 'advanced-gutenberg' ),
										'value' => 'right',
									),
								),
							),
						),
					)
				),
				'advgb-icon'          => array(
					array(
						'label'    => __( 'General Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Number of icons', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'numberItem',
								'min'   => 1,
								'max'   => 10,
							),
						),
					),

				),
				'advgb-infobox'       => array(
					array(
						'label'    => __( 'Container Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'containerBackground',
							),
							array(
								'title' => __( 'Border Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'containerBorderBackground',
							),
							array(
								'title' => __( 'Border Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'containerBorderWidth',
								'min'   => 0,
								'max'   => 40,
							),
							array(
								'title' => __( 'Border Radius', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'containerBorderRadius',
								'min'   => 0,
								'max'   => 200,
							),
							array(
								'title' => __( 'Padding Top', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'containerPaddingTop',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Bottom', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'containerPaddingBottom',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Left', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'containerPaddingLeft',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Right', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'containerPaddingRight',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
					array(
						'label'    => __( 'Icon Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Icon Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'iconColor',
							),
							array(
								'title' => __( 'Icon Size', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconSize',
								'min'   => 1,
								'max'   => 200,
							),
							array(
								'title' => __( 'Background Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'iconBackground',
							),
							array(
								'title' => __( 'Border Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'iconBorderBackground',
							),
							array(
								'title' => __( 'Border Width', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconBorderWidth',
								'min'   => 0,
								'max'   => 40,
							),
							array(
								'title' => __( 'Border Radius', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconBorderRadius',
								'min'   => 0,
								'max'   => 200,
							),
							array(
								'title' => __( 'Padding Top', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconPaddingTop',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Bottom', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconPaddingBottom',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Left', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconPaddingLeft',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Right', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconPaddingRight',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Top', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconMarginTop',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Bottom', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconMarginBottom',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Left', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconMarginLeft',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Right', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'iconMarginRight',
								'min'   => 0,
								'max'   => 100,
							),
						),
					),
					array(
						'label'    => __( 'Title Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Title Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'titleColor',
							),
							array(
								'title' => __( 'Font Size (px)', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'titleSize',
								'min'   => 1,
								'max'   => 200,
							),
							array(
								'title' => __( 'Line Height (px)', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'titleLineHeight',
								'min'   => 1,
								'max'   => 200,
							),
							array(
								'title'   => __( 'HTML Tag', 'advanced-gutenberg' ),
								'type'    => 'select',
								'name'    => 'titleHtmlTag',
								'options' => array(
									array(
										'label' => __( 'H1', 'advanced-gutenberg' ),
										'value' => 'h1',
									),
									array(
										'label' => __( 'H2', 'advanced-gutenberg' ),
										'value' => 'h2',
									),
									array(
										'label' => __( 'H3', 'advanced-gutenberg' ),
										'value' => 'h3',
									),
									array(
										'label' => __( 'H4', 'advanced-gutenberg' ),
										'value' => 'h4',
									),
									array(
										'label' => __( 'H5', 'advanced-gutenberg' ),
										'value' => 'h5',
									),
									array(
										'label' => __( 'H6', 'advanced-gutenberg' ),
										'value' => 'h6',
									),
								),
							),
							array(
								'title' => __( 'Padding Top', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'titlePaddingTop',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Bottom', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'titlePaddingBottom',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Left', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'titlePaddingLeft',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Right', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'titlePaddingRight',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Top', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'titleMarginTop',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Bottom', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'titleMarginBottom',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Left', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'titleMarginLeft',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Right', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'titleMarginRight',
								'min'   => 0,
								'max'   => 100,
							),
						)
					),
					array(
						'label'    => __( 'Text Settings', 'advanced-gutenberg' ),
						'settings' => array(
							array(
								'title' => __( 'Color', 'advanced-gutenberg' ),
								'type'  => 'color',
								'name'  => 'textColor',
							),
							array(
								'title' => __( 'Font Size (px)', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textSize',
								'min'   => 1,
								'max'   => 200,
							),
							array(
								'title' => __( 'Line Height (px)', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textLineHeight',
								'min'   => 1,
								'max'   => 200,
							),
							array(
								'title' => __( 'Padding Top', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textPaddingTop',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Bottom', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textPaddingBottom',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Left', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textPaddingLeft',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Padding Right', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textPaddingRight',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Top', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textMarginTop',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Bottom', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textMarginBottom',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Left', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textMarginLeft',
								'min'   => 0,
								'max'   => 100,
							),
							array(
								'title' => __( 'Margin Right', 'advanced-gutenberg' ),
								'type'  => 'number',
								'name'  => 'textMarginRight',
								'min'   => 0,
								'max'   => 100,
							),
						)
					),
				),
			);
		}
	}
}
