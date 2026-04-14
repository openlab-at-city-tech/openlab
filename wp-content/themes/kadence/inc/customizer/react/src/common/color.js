import PropTypes from "prop-types";
import SwatchesControl from "./swatches";
import KadenceColorPicker from "./color-picker";
import Icons from "./icons";
import { __ } from "@wordpress/i18n";
const { Component, Fragment } = wp.element;
const {
	Button,
	Popover,
	Dashicon,
	ColorIndicator,
	Tooltip,
	Icon,
	TabPanel,
	GradientPicker,
	__experimentalGradientPicker,
} = wp.components;
import KadenceGradientPicker from "../gradient-control/index.js";

class ColorControl extends Component {
	constructor(props) {
		super(props);
		this.onChangeState = this.onChangeState.bind(this);
		this.onChangeComplete = this.onChangeComplete.bind(this);
		this.onChangeGradientComplete =
			this.onChangeGradientComplete.bind(this);
		this.state = {
			isVisible: false,
			refresh: false,
			stateColor: this.props.color,
			color: this.props.color,
			isPalette:
				this.props.color &&
				this.props.color.includes("palette") &&
				!this.props.color.includes("gradient")
					? true
					: false,
			palette:
				this.props.presetColors && this.props.presetColors
					? this.props.presetColors
					: [],
			activePalette:
				this.props.presetColors && this.props.presetColors.active
					? this.props.presetColors.active
					: "palette",
			supportGradient:
				undefined === GradientPicker &&
				undefined === __experimentalGradientPicker
					? false
					: true,
			showSyncMessage: true,
		};
	}

	render() {
		// Helper function to check if we should show sync message
		const shouldShowSyncMessage = () => {
			return (
				this.props.paletteName === "palette10" &&
				this.props.color === "#FfFfFf" &&
				this.state.showSyncMessage === true
			);
		};

		const shouldShowResyncMessage = () => {
			return (
				this.props.paletteName === "palette10" &&
				this.props.color !== "#FfFfFf" &&
				this.state.showSyncMessage === false
			);
		};

		const toggleVisible = () => {
			if (this.props.usePalette) {
				const updateColors = JSON.parse(
					this.props.customizer
						.control("kadence_color_palette")
						.setting.get()
				);
				const active =
					updateColors && updateColors.active
						? updateColors.active
						: "palette";
				this.setState({ palette: updateColors, activePalette: active });
			}
			if (this.state.refresh === true) {
				this.setState({ refresh: false });
			} else {
				this.setState({ refresh: true });
			}
			this.setState({ isVisible: true });
		};

		const toggleClose = () => {
			if (this.state.isVisible === true) {
				this.setState({ isVisible: false });
			}
		};

		const position = this.props.position
			? this.props.position
			: "bottom right";
		const showingGradient =
			this.props.allowGradient && this.state.supportGradient
				? true
				: false;

		const syncMessageContent = () => {
			return (
				<div
					className="kadence-sync-message"
					style={{ padding: "16px", textAlign: "center" }}
				>
					<p>
						{__(
							"This color is currently linked as the compliment to your accent color.",
							"kadence"
						)}
					</p>
					<Button
						onClick={() =>
							this.setState({ showSyncMessage: false })
						}
						variant="secondary"
						isSmall
					>
						{__("Unlink Color", "kadence")}
					</Button>
				</div>
			);
		};

		const resyncMessageContent = () => {
			return (
				<div
					className="kadence-sync-message"
					style={{ padding: "16px", textAlign: "center" }}
				>
					<Button
						onClick={() => {
							this.setState({ showSyncMessage: true });
							this.onChangeState({ hex: "#FfFfFf" }, "");
							this.props.onChangeComplete({ hex: "#FfFfFf" }, "");
						}}
						variant="secondary"
						isSmall
					>
						{__("Link Color", "kadence")}
					</Button>
				</div>
			);
		};
		const paletteIndexRegex = this.state.color?.match(/\d+$/)?.[0] - 1;
		const paletteIndex = this.state.isPalette
			? paletteIndexRegex
			: undefined;
		return (
			<div
				className={"kadence-color-picker-wrap " + this.props.className}
			>
				{this.props.colorDefault &&
					this.props.color &&
					this.props.color !== this.props.colorDefault && (
						<Tooltip text={__("Clear")}>
							<span className="tooltip-clear">
								<Button
									className="components-color-palette__clear"
									type="button"
									onClick={() => {
										this.setState({
											color: this.props.colorDefault,
											isPalette: "",
										});
										this.props.onChangeComplete("", "");
									}}
									isSmall
								>
									<Dashicon icon="redo" />
								</Button>
							</span>
						</Tooltip>
					)}
				{showingGradient && (
					<Fragment>
						{this.state.isVisible && (
							<Popover
								position={position}
								inline={true}
								anchor={
									undefined !==
									this.props?.controlRef?.current
										? this.props.controlRef.current
										: undefined
								}
								className="kadence-popover-color kadence-popover-color-gradient kadence-customizer-popover"
								onClose={toggleClose}
							>
								{shouldShowSyncMessage() ? (
									syncMessageContent()
								) : (
									<TabPanel
										className="kadence-popover-tabs kadence-background-tabs"
										activeClass="active-tab"
										initialTabName={
											this.state.color &&
											this.state.color.includes(
												"gradient"
											)
												? "gradient"
												: "color"
										}
										tabs={[
											{
												name: "color",
												title: __("Color", "kadence"),
												className:
													"kadence-color-background",
											},
											{
												name: "gradient",
												title: __(
													"Gradient",
													"kadence"
												),
												className:
													"kadence-gradient-background",
											},
										]}
									>
										{(tab) => {
											let tabout;
											if (tab.name) {
												if ("gradient" === tab.name) {
													tabout = (
														<Fragment>
															<KadenceGradientPicker
																value={
																	this.state
																		.color &&
																	this.state.color.includes(
																		"gradient"
																	)
																		? this
																				.state
																				.color
																		: ""
																}
																onChange={(
																	gradient
																) =>
																	this.onChangeGradientComplete(
																		gradient
																	)
																}
																activePalette={
																	this.state
																		.palette &&
																	this.state
																		.palette[
																		this
																			.state
																			.activePalette
																	]
																		? this
																				.state
																				.palette[
																				this
																					.state
																					.activePalette
																		  ]
																		: []
																}
															/>
														</Fragment>
													);
												} else {
													tabout = (
														<Fragment>
															{this.state
																.refresh && (
																<Fragment>
																	<KadenceColorPicker
																		color={
																			this
																				.state
																				.isPalette &&
																			this
																				.state
																				.palette
																				.palette &&
																			this
																				.state
																				.palette
																				.palette[
																				paletteIndex
																			]
																				? this
																						.state
																						.palette
																						.palette[
																						paletteIndex
																				  ]
																						?.color
																				: this
																						.state
																						.color
																		}
																		onChange={(
																			color
																		) =>
																			this.onChangeState(
																				color,
																				""
																			)
																		}
																		onChangeComplete={(
																			color
																		) =>
																			this.onChangeComplete(
																				color,
																				""
																			)
																		}
																	/>
																	{this.props
																		.usePalette && (
																		<SwatchesControl
																			colors={
																				this
																					.state
																					.palette &&
																				this
																					.state
																					.palette[
																					this
																						.state
																						.activePalette
																				]
																					? this
																							.state
																							.palette[
																							this
																								.state
																								.activePalette
																					  ]
																					: []
																			}
																			isPalette={
																				this
																					.state
																					.isPalette
																					? this
																							.state
																							.color
																					: ""
																			}
																			onClick={(
																				color,
																				palette
																			) =>
																				this.onChangeComplete(
																					color,
																					palette
																				)
																			}
																		/>
																	)}
																</Fragment>
															)}
															{!this.state
																.refresh && (
																<Fragment>
																	<KadenceColorPicker
																		//presetColors={ [] }
																		color={
																			this
																				.state
																				.isPalette &&
																			this
																				.state
																				.palette[
																				this
																					.state
																					.activePalette
																			] &&
																			this
																				.state
																				.palette[
																				this
																					.state
																					.activePalette
																			][
																				paletteIndex
																			]
																				? this
																						.state
																						.palette[
																						this
																							.state
																							.activePalette
																				  ][
																						paletteIndex
																				  ]
																						?.color
																				: this
																						.state
																						.color
																		}
																		onChange={(
																			color
																		) =>
																			this.onChangeState(
																				color,
																				""
																			)
																		}
																		onChangeComplete={(
																			color
																		) =>
																			this.onChangeComplete(
																				color,
																				""
																			)
																		}
																		//width={ 300 }
																		//styles={ styleing }
																	/>
																	{this.props
																		.usePalette && (
																		<SwatchesControl
																			colors={
																				this
																					.state
																					.palette &&
																				this
																					.state
																					.palette[
																					this
																						.state
																						.activePalette
																				]
																					? this
																							.state
																							.palette[
																							this
																								.state
																								.activePalette
																					  ]
																					: []
																			}
																			isPalette={
																				this
																					.state
																					.isPalette
																					? this
																							.state
																							.color
																					: ""
																			}
																			onClick={(
																				color,
																				palette
																			) =>
																				this.onChangeComplete(
																					color,
																					palette
																				)
																			}
																		/>
																	)}
																</Fragment>
															)}
														</Fragment>
													);
												}
											}
											return (
												<div>
													{tabout}
													{shouldShowResyncMessage()
														? resyncMessageContent()
														: ""}
												</div>
											);
										}}
									</TabPanel>
								)}
							</Popover>
						)}
					</Fragment>
				)}
				{!showingGradient && (
					<Fragment>
						{this.state.isVisible && this.state.refresh && (
							<Popover
								position="top right"
								inline={true}
								anchor={
									undefined !==
									this.props?.controlRef?.current
										? this.props.controlRef.current
										: undefined
								}
								className="kadence-popover-color kadence-customizer-popover"
								onClose={toggleClose}
							>
								{shouldShowSyncMessage() ? (
									syncMessageContent()
								) : (
									<Fragment>
										<KadenceColorPicker
											color={
												this.state.isPalette &&
												this.state.palette.palette &&
												this.state.palette.palette[
													paletteIndex
												]
													? this.state.palette
															.palette[
															paletteIndex
													  ]?.color
													: this.state.color
											}
											onChange={(color) =>
												this.onChangeState(color, "")
											}
											onChangeComplete={(color) =>
												this.onChangeComplete(color, "")
											}
										/>
										{this.props.usePalette && (
											<SwatchesControl
												colors={
													this.state.palette &&
													this.state.palette[
														this.state.activePalette
													]
														? this.state.palette[
																this.state
																	.activePalette
														  ]
														: []
												}
												isPalette={
													this.state.isPalette
														? this.state.color
														: ""
												}
												onClick={(color, palette) =>
													this.onChangeComplete(
														color,
														palette
													)
												}
											/>
										)}
										{shouldShowResyncMessage()
											? resyncMessageContent()
											: ""}
									</Fragment>
								)}
							</Popover>
						)}
						{this.state.isVisible && !this.state.refresh && (
							<Popover
								position={position}
								inline={true}
								anchor={
									undefined !==
									this.props?.controlRef?.current
										? this.props.controlRef.current
										: undefined
								}
								className="kadence-popover-color kadence-customizer-popover"
								onClose={toggleClose}
							>
								{shouldShowSyncMessage() ? (
									syncMessageContent()
								) : (
									<Fragment>
										<KadenceColorPicker
											//presetColors={ [] }
											color={
												this.state.isPalette &&
												this.state.palette[
													this.state.activePalette
												] &&
												this.state.palette[
													this.state.activePalette
												][paletteIndex]
													? this.state.palette[
															this.state
																.activePalette
													  ][paletteIndex]?.color
													: this.state.color
											}
											onChange={(color) =>
												this.onChangeState(color, "")
											}
											onChangeComplete={(color) =>
												this.onChangeComplete(color, "")
											}
											//width={ 300 }
											//styles={ styleing }
										/>
										{this.props.usePalette && (
											<SwatchesControl
												colors={
													this.state.palette &&
													this.state.palette[
														this.state.activePalette
													]
														? this.state.palette[
																this.state
																	.activePalette
														  ]
														: []
												}
												isPalette={
													this.state.isPalette
														? this.state.color
														: ""
												}
												onClick={(color, palette) =>
													this.onChangeComplete(
														color,
														palette
													)
												}
											/>
										)}
										{shouldShowResyncMessage()
											? resyncMessageContent()
											: ""}
									</Fragment>
								)}
							</Popover>
						)}
					</Fragment>
				)}
				<Tooltip
					text={
						this.props.tooltip
							? this.props.tooltip
							: __("Select Color", "kadence")
					}
				>
					<div className="color-button-wrap">
						<Button
							className={"kadence-color-icon-indicate"}
							onClick={() => {
								this.state.isVisible
									? toggleClose()
									: toggleVisible();
							}}
							disabled={this.props.disabled}
						>
							<ColorIndicator
								className="kadence-advanced-color-indicate"
								colorValue={
									this.state.isPalette
										? "var(--global-" +
										  this.props.color +
										  ")"
										: this.props.color
								}
							/>
							{this.state.isPalette && (
								<Icon className="dashicon" icon={Icons.globe} />
							)}
						</Button>
					</div>
				</Tooltip>
			</div>
		);
	}

	onChangeState(color, palette) {
		let newColor;
		if (palette) {
			newColor = palette;
		} else if (
			undefined !== color.rgb &&
			undefined !== color.rgb.a &&
			1 !== color.rgb.a
		) {
			newColor =
				"rgba(" +
				color.rgb.r +
				"," +
				color.rgb.g +
				"," +
				color.rgb.b +
				"," +
				color.rgb.a +
				")";
		} else {
			newColor = color.hex;
		}
		this.setState({ color: newColor, isPalette: palette ? true : false });
		if (undefined !== this.props.onChange) {
			this.props.onChange(color, palette);
		}
	}
	onChangeGradientComplete(gradient) {
		let newColor;
		if (undefined === gradient) {
			newColor = "";
		} else {
			newColor = gradient;
		}
		this.setState({ color: newColor, isPalette: false });
		this.props.onChangeComplete(newColor, "");
	}
	onChangeComplete(color, palette) {
		let newColor;
		if (palette) {
			newColor = palette;
		} else if (
			undefined !== color.rgb &&
			undefined !== color.rgb.a &&
			1 !== color.rgb.a
		) {
			newColor =
				"rgba(" +
				color.rgb.r +
				"," +
				color.rgb.g +
				"," +
				color.rgb.b +
				"," +
				color.rgb.a +
				")";
		} else {
			newColor = color.hex;
		}
		this.setState({ color: newColor, isPalette: palette ? true : false });
		this.props.onChangeComplete(color, palette);
	}
}

ColorControl.propTypes = {
	color: PropTypes.string,
	usePalette: PropTypes.bool,
	palette: PropTypes.string,
	presetColors: PropTypes.array,
	onChangeComplete: PropTypes.func,
	onChange: PropTypes.func,
	customizer: PropTypes.object,
};

export default ColorControl;
