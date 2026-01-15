import PropTypes from "prop-types";
import { __ } from "@wordpress/i18n";
/**
 * WordPress dependencies
 */
import { createRef, Component, Fragment } from "@wordpress/element";
import map from "lodash/map";
import ColorControl from "../common/color.js";
const {
	ButtonGroup,
	Dashicon,
	Tooltip,
	Button,
	Popover,
	TabPanel,
	TextareaControl,
} = wp.components;
class ColorComponent extends Component {
	constructor(props) {
		super(props);
		this.handleChangeComplete = this.handleChangeComplete.bind(this);
		this.handleChangePalette = this.handleChangePalette.bind(this);
		this.handleTextImport = this.handleTextImport.bind(this);
		this.handlePresetImport = this.handlePresetImport.bind(this);
		this.updateValues = this.updateValues.bind(this);
		let value = JSON.parse(this.props.control.setting.get());
		let baseDefault = kadenceCustomizerControlsData.palette
			? JSON.parse(kadenceCustomizerControlsData.palette)
			: { palette: [] };
		this.defaultValue = this.props.control.params.default
			? {
					...baseDefault,
					...this.props.control.params.default,
			  }
			: baseDefault;
		value = value
			? {
					...this.defaultValue,
					...value,
			  }
			: this.defaultValue;
		let defaultParams = {
			reset: '{"palette":[{"color":"#3182CE","slug":"palette1","name":"Palette Color 1"},{"color":"#2B6CB0","slug":"palette2","name":"Palette Color 2"},{"color":"#1A202C","slug":"palette3","name":"Palette Color 3"},{"color":"#2D3748","slug":"palette4","name":"Palette Color 4"},{"color":"#4A5568","slug":"palette5","name":"Palette Color 5"},{"color":"#718096","slug":"palette6","name":"Palette Color 6"},{"color":"#EDF2F7","slug":"palette7","name":"Palette Color 7"},{"color":"#F7FAFC","slug":"palette8","name":"Palette Color 8"},{"color":"#FFFFFF","slug":"palette9","name":"Palette Color 9"},{"color":"#FfFfFf","slug":"palette10","name":"Palette Color Complement"},{"color":"#13612e","slug":"palette11","name":"Palette Color Success"},{"color":"#1159af","slug":"palette12","name":"Palette Color Info"},{"color":"#b82105","slug":"palette13","name":"Palette Color Alert"},{"color":"#f7630c","slug":"palette14","name":"Palette Color Warning"},{"color":"#f5a524","slug":"palette15","name":"Palette Color Rating"}],"second-palette":[{"color":"#3182CE","slug":"palette1","name":"Palette Color 1"},{"color":"#2B6CB0","slug":"palette2","name":"Palette Color 2"},{"color":"#1A202C","slug":"palette3","name":"Palette Color 3"},{"color":"#2D3748","slug":"palette4","name":"Palette Color 4"},{"color":"#4A5568","slug":"palette5","name":"Palette Color 5"},{"color":"#718096","slug":"palette6","name":"Palette Color 6"},{"color":"#EDF2F7","slug":"palette7","name":"Palette Color 7"},{"color":"#F7FAFC","slug":"palette8","name":"Palette Color 8"},{"color":"#FFFFFF","slug":"palette9","name":"Palette Color 9"},{"color":"#FfFfFf","slug":"palette10","name":"Palette Color Complement"},{"color":"#13612e","slug":"palette11","name":"Palette Color Success"},{"color":"#1159af","slug":"palette12","name":"Palette Color Info"},{"color":"#b82105","slug":"palette13","name":"Palette Color Alert"},{"color":"#f7630c","slug":"palette14","name":"Palette Color Warning"},{"color":"#f5a524","slug":"palette15","name":"Palette Color Rating"}],"third-palette":[{"color":"#3182CE","slug":"palette1","name":"Palette Color 1"},{"color":"#2B6CB0","slug":"palette2","name":"Palette Color 2"},{"color":"#1A202C","slug":"palette3","name":"Palette Color 3"},{"color":"#2D3748","slug":"palette4","name":"Palette Color 4"},{"color":"#4A5568","slug":"palette5","name":"Palette Color 5"},{"color":"#718096","slug":"palette6","name":"Palette Color 6"},{"color":"#EDF2F7","slug":"palette7","name":"Palette Color 7"},{"color":"#F7FAFC","slug":"palette8","name":"Palette Color 8"},{"color":"#FFFFFF","slug":"palette9","name":"Palette Color 9"},{"color":"#FfFfFf","slug":"palette10","name":"Palette Color Complement"},{"color":"#13612e","slug":"palette11","name":"Palette Color Success"},{"color":"#1159af","slug":"palette12","name":"Palette Color Info"},{"color":"#b82105","slug":"palette13","name":"Palette Color Alert"},{"color":"#f7630c","slug":"palette14","name":"Palette Color Warning"},{"color":"#f5a524","slug":"palette15","name":"Palette Color Rating"}],"active":"palette"}',
			palettes: kadenceCustomizerControlsData.palettePresets
				? kadenceCustomizerControlsData.palettePresets
				: [],
			colors: {
				palette1: {
					tooltip: __("1 - Accent", "kadence"),
					palette: false,
				},
				palette2: {
					tooltip: __("2 - Accent - alt", "kadence"),
					palette: false,
				},
				palette3: {
					tooltip: __("3 - Strongest text", "kadence"),
					palette: false,
				},
				palette4: {
					tooltip: __("4 - Strong Text", "kadence"),
					palette: false,
				},
				palette5: {
					tooltip: __("5 - Medium text", "kadence"),
					palette: false,
				},
				palette6: {
					tooltip: __("6 - Subtle Text", "kadence"),
					palette: false,
				},
				palette7: {
					tooltip: __("7 - Subtle Background", "kadence"),
					palette: false,
				},
				palette8: {
					tooltip: __("8 - Lighter Background", "kadence"),
					palette: false,
				},
				palette9: {
					tooltip: __("9 - White or offwhite", "kadence"),
					palette: false,
				},
				palette10: {
					tooltip: __("10 - Accent - Complement", "kadence"),
					palette: false,
				},
				palette11: {
					tooltip: __("11 - Notices - Success", "kadence"),
					palette: false,
				},
				palette12: {
					tooltip: __("12 - Notices - Info", "kadence"),
					palette: false,
				},
				palette13: {
					tooltip: __("13 - Notices - Alert", "kadence"),
					palette: false,
				},
				palette14: {
					tooltip: __("14 - Notices - Warning", "kadence"),
					palette: false,
				},
				palette15: {
					tooltip: __("15 - Notices - Rating", "kadence"),
					palette: false,
				},
			},
			paletteMap: {
				palette: {
					tooltip: __("Palette 1", "kadence"),
				},
				"second-palette": {
					tooltip: __("Palette 2", "kadence"),
				},
				"third-palette": {
					tooltip: __("Palette 3", "kadence"),
				},
			},
		};
		this.controlParams = this.props.control.params.input_attrs
			? {
					...defaultParams,
					...this.props.control.params.input_attrs,
			  }
			: defaultParams;
		this.state = {
			value: value,
			colorPalette: [],
			fresh: "start",
			isVisible: false,
			textImport: "",
			importError: false,
		};
		this.anchorNodeRef = createRef();
	}
	handleChangePalette(active) {
		let value = this.state.value;
		const newItems = this.state.value[active].map((item, index) => {
			const colorToUse =
				item.slug === "palette10" && item.color === "#FfFfFf"
					? "oklch(from var(--global-palette1) calc(l + 0.10 * (1 - l)) calc(c * 1.00) calc(h + 180) / 100%)"
					: item.color;
			document.documentElement.style.setProperty(
				"--global-" + item.slug,
				colorToUse
			);
			return item;
		});
		value.active = active;
		value[active] = newItems;
		this.setState({
			fresh: this.state.fresh !== "start" ? "start" : "second",
		});
		this.updateValues(value);
	}
	handlePresetImport(preset) {
		const presetPalettes = JSON.parse(this.controlParams.palettes);
		// Verify data.
		if (
			presetPalettes &&
			presetPalettes[preset] &&
			9 === presetPalettes[preset].length
		) {
			const newItems = presetPalettes[preset].map((item, index) => {
				if (item.color) {
					this.handleChangeComplete(
						{ hex: item.color },
						false,
						"",
						index
					);
				}
			});
			this.setState({
				fresh: this.state.fresh !== "start" ? "start" : "second",
				importError: false,
			});
		} else {
			this.setState({ importPresetError: true });
		}
	}
	handleTextImport() {
		const importText = this.state.textImport;
		if (!importText) {
			this.setState({ importError: true });
			return;
		}
		const textImportData = JSON.parse(importText);

		// Get current palette
		const currentPalette = this.state.value[this.state.value.active];

		// Get imported palette
		let sanitizedPalette = null;

		// Check if imported palette is in named format (object with palette1, palette2, etc.)
		if (
			textImportData &&
			!Array.isArray(textImportData) &&
			typeof textImportData === "object"
		) {
			// Named format - loop through the palette colors
			sanitizedPalette = [];
			currentPalette.forEach((paletteItem) => {
				const paletteKey = paletteItem.slug;

				// Find the corresponding named color in the import
				if (
					textImportData[paletteKey] &&
					textImportData[paletteKey].color
				) {
					// Start with a copy of current paletteItem, then merge in changes
					sanitizedPalette.push({
						...paletteItem,
						...textImportData[paletteKey],
					});
				} else {
					// Use current palette values for any that are missing
					sanitizedPalette.push(paletteItem);
				}
			});
		} else if (textImportData instanceof Array) {
			// Array format - continue with existing logic
			sanitizedPalette = textImportData;
		}

		// Use the sanitized palette in the usual loop to complete the import
		// Verify data.
		if (
			sanitizedPalette &&
			sanitizedPalette instanceof Array &&
			sanitizedPalette[0] &&
			sanitizedPalette[0].color
		) {
			const newItems = sanitizedPalette.map((item, index) => {
				if (item.color) {
					this.handleChangeComplete(
						{ hex: item.color },
						false,
						"",
						index
					);
				}
			});
			this.setState({
				fresh: this.state.fresh !== "start" ? "start" : "second",
				textImport: "",
				isVisible: false,
				importError: false,
			});
		} else {
			this.setState({ importError: true });
		}
	}
	convertPaletteToNamedFormat(paletteArray) {
		const namedFormat = {};
		paletteArray.forEach((paletteItem) => {
			namedFormat[paletteItem.slug] = { color: paletteItem.color };
		});
		return namedFormat;
	}

	handleChangeComplete(color, isPalette, item, index) {
		let newColor = {};
		if (
			undefined !== color.rgb &&
			undefined !== color.rgb.a &&
			1 !== color.rgb.a
		) {
			newColor.color =
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
			newColor.color = color.hex;
		}
		let value = this.state.value;
		const newItems = this.state.value[this.state.value.active].map(
			(item, thisIndex) => {
				if (parseInt(index) === parseInt(thisIndex)) {
					item = { ...item, ...newColor };
					const colorToSet =
						this.state.value[this.state.value.active][index]
							.slug === "palette10" &&
						newColor.color === "#FfFfFf"
							? "oklch(from var(--global-palette1) calc(l + 0.10 * (1 - l)) calc(c * 1.00) calc(h + 180) / 100%)"
							: newColor.color;
					document.documentElement.style.setProperty(
						"--global-" +
							this.state.value[this.state.value.active][index]
								.slug,
						colorToSet
					);
				}

				return item;
			}
		);
		value[this.state.value.active] = newItems;
		this.updateValues(value);
	}

	render() {
		const toggleVisible = () => {
			this.setState({ isVisible: true });
		};
		const toggleClose = () => {
			if (this.state.isVisible === true) {
				this.setState({ isVisible: false });
			}
		};
		const presetPalettes = JSON.parse(this.controlParams.palettes);
		const currentPaletteData = this.convertPaletteToNamedFormat(
			this.state.value[this.state.value.active]
		);
		const currentPaletteJson = JSON.stringify(currentPaletteData);

		const paletteGroupData = {
			accent: {
				name: __("Accents", "kadence"),
				colors: ["palette1", "palette2", "palette10"],
			},
			contrast: {
				name: __("Contrast", "kadence"),
				colors: ["palette3", "palette4", "palette5", "palette6"],
			},
			base: {
				name: __("Base", "kadence"),
				colors: ["palette7", "palette8", "palette9"],
			},
			notice: {
				name: __("Notices", "kadence"),
				colors: [
					"palette11",
					"palette12",
					"palette13",
					"palette14",
					"palette15",
				],
			},
		};

		return (
			<div className="kadence-control-field kadence-palette-control kadence-color-control">
				<div className="kadence-palette-header">
					<Tooltip text={__("Reset Values", "kadence")}>
						<Button
							className="reset kadence-reset"
							onClick={() => {
								let value = this.state.value;
								const reset = JSON.parse(
									this.controlParams.reset
								);
								const newItems = this.state.value[
									this.state.value.active
								].map((item, thisIndex) => {
									item = {
										...item,
										...reset[this.state.value.active][
											thisIndex
										],
									};
									document.documentElement.style.setProperty(
										"--global-" +
											reset[this.state.value.active][
												thisIndex
											].slug,
										item.slug === "palette10" &&
											item.color === "#FfFfFf"
											? "oklch(from var(--global-palette1) calc(l + 0.10 * (1 - l)) calc(c * 1.00) calc(h + 180) / 100%)"
											: reset[this.state.value.active][
													thisIndex
											  ].color
									);
									return item;
								});
								value[this.state.value.active] = newItems;
								this.updateValues(value);
							}}
						>
							<Dashicon icon="image-rotate" />
						</Button>
					</Tooltip>
					{this.props.control.params.label && (
						<span className="customize-control-title">
							{this.props.control.params.label}
						</span>
					)}
					{!this.props.hideResponsive && (
						<div className="floating-controls">
							<ButtonGroup>
								{Object.keys(this.controlParams.paletteMap).map(
									(palette) => {
										return (
											<Tooltip
												text={
													this.controlParams
														.paletteMap[palette]
														.tooltip
												}
											>
												<Button
													isTertiary
													className={
														(palette ===
														this.state.value.active
															? "active-palette "
															: "") + palette
													}
													onClick={() => {
														this.handleChangePalette(
															palette
														);
													}}
												>
													{
														this.controlParams
															.paletteMap[palette]
															.tooltip
													}
												</Button>
											</Tooltip>
										);
									}
								)}
							</ButtonGroup>
						</div>
					)}
				</div>
				<div
					ref={this.anchorNodeRef}
					className="kadence-palette-colors"
				>
					{Object.keys(paletteGroupData).map((group, index) => {
						return (
							<div className="kadence-palette-group">
								<div className="kadence-palette-group-name">
									{paletteGroupData[group].name}
								</div>
								<div className="kadence-palette-group-colors">
									{paletteGroupData[group].colors.map(
										(item, colorIndex) => {
											const paletteIndex =
												item.match(/\d+$/)?.[0] - 1;
											return (
												<ColorControl
													key={
														item +
														this.state.value
															.active +
														this.state.fresh
													}
													presetColors={
														this.state.colorPalette
													}
													color={
														undefined !==
															this.state.value[
																this.state.value
																	.active
															][paletteIndex] &&
														this.state.value[
															this.state.value
																.active
														][paletteIndex].color
															? this.state.value[
																	this.state
																		.value
																		.active
															  ][paletteIndex]
																	.color
															: ""
													}
													isPalette={""}
													usePalette={false}
													paletteName={item}
													className={"kt-" + item}
													tooltip={
														undefined !==
														this.controlParams
															.colors[item]
															.tooltip
															? this.controlParams
																	.colors[
																	item
															  ].tooltip
															: ""
													}
													onChangeComplete={(
														color,
														isPalette
													) =>
														this.handleChangeComplete(
															color,
															isPalette,
															item,
															paletteIndex
														)
													}
													controlRef={
														this.anchorNodeRef
													}
												/>
											);
										}
									)}
								</div>
							</div>
						);
					})}
				</div>
				<div className={"kadence-palette-import-wrap"}>
					<Button
						className={"kadence-palette-import"}
						onClick={() => {
							this.state.isVisible
								? toggleClose()
								: toggleVisible();
						}}
					>
						<Dashicon icon="portfolio" />
					</Button>
					{this.state.isVisible && (
						<Popover
							position="bottom right"
							inline={true}
							className="kadence-palette-popover-copy-paste kadence-customizer-popover"
							onClose={toggleClose}
						>
							<TabPanel
								className="kadence-palette-popover-tabs"
								activeClass="active-tab"
								initialTabName={"import"}
								tabs={[
									{
										name: "import",
										title: __("Select Palette", "kadence"),
										className: "kadence-color-presets",
									},
									{
										name: "custom",
										title: __("Export/Import", "kadence"),
										className: "kadence-export-import",
									},
								]}
							>
								{(tab) => {
									let tabout;
									if (tab.name) {
										if ("import" === tab.name) {
											tabout = (
												<Fragment>
													{Object.keys(
														presetPalettes
													).map((item, index) => {
														return (
															<Button
																className={
																	"kadence-palette-item"
																}
																style={{
																	height: "100%",
																	width: "100%",
																}}
																onClick={() =>
																	this.handlePresetImport(
																		item
																	)
																}
																tabIndex={0}
															>
																{Object.keys(
																	presetPalettes[
																		item
																	]
																).map(
																	(
																		color,
																		subIndex
																	) => {
																		return (
																			<div
																				key={
																					subIndex
																				}
																				style={{
																					width: 26,
																					height: 26,
																					marginBottom: 0,
																					transform:
																						"scale(1)",
																					transition:
																						"100ms transform ease",
																				}}
																				className="kadence-swatche-item-wrap"
																			>
																				<span
																					className={
																						"kadence-swatch-item"
																					}
																					style={{
																						height: "100%",
																						display:
																							"block",
																						width: "100%",
																						border: "1px solid rgb(218, 218, 218)",
																						borderRadius:
																							"50%",
																						color: `${presetPalettes[item][color].color}`,
																						boxShadow: `inset 0 0 0 ${
																							30 /
																							2
																						}px`,
																						transition:
																							"100ms box-shadow ease",
																					}}
																				></span>
																			</div>
																		);
																	}
																)}
															</Button>
														);
													})}
												</Fragment>
											);
										} else {
											tabout = (
												<Fragment>
													<h2>
														{__(
															"Export",
															"kadence"
														)}
													</h2>
													<TextareaControl
														label=""
														help={__(
															"Copy export data to use in another site.",
															"kadence"
														)}
														value={
															currentPaletteJson
														}
														onChange={false}
													/>
													<h2>
														{__(
															"Import",
															"kadence"
														)}
													</h2>
													<TextareaControl
														label={__(
															"Import color set from text data.",
															"kadence"
														)}
														help={__(
															"Follow format from export above.",
															"kadence"
														)}
														value={
															this.state
																.textImport
														}
														onChange={(text) =>
															this.setState({
																textImport:
																	text,
															})
														}
													/>
													{this.state.importError && (
														<p
															style={{
																color: "red",
															}}
														>
															{__(
																"Error with Import data",
																"kadence"
															)}
														</p>
													)}
													<Button
														className={
															"kadence-import-button"
														}
														isPrimary
														disabled={
															this.state
																.textImport
																? false
																: true
														}
														onClick={() =>
															this.handleTextImport()
														}
													>
														{__(
															"Import",
															"kadence"
														)}
													</Button>
												</Fragment>
											);
										}
									}
									return <div>{tabout}</div>;
								}}
							</TabPanel>
						</Popover>
					)}
				</div>
				{this.props.control.params.description && (
					<span className="customize-control-description">
						<a
							href="https://kadence-theme.com/docs/how-to-use-the-kadence-color-palette/"
							target="_blank"
						>
							{this.props.control.params.description}
						</a>
					</span>
				)}
			</div>
		);
	}

	updateValues(value) {
		this.setState({ value: value });
		this.props.control.setting.set(JSON.stringify(value));
		kadenceCustomizerControlsData.palette = JSON.stringify(value);
	}
}

ColorComponent.propTypes = {
	control: PropTypes.object.isRequired,
};

export default ColorComponent;
