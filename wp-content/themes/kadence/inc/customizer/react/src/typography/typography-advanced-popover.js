/* jshint esversion: 6 */
import PropTypes from "prop-types";
import { __ } from "@wordpress/i18n";
import { Popover, Button, SelectControl } from "@wordpress/components";

/**
 * TypographyAdvancedPopover Component
 *
 * A popover component for advanced typography settings including font size clamp controls.
 * Extracted from the main TypographyComponent for better modularity.
 */
const TypographyAdvancedPopover = ({
	isVisible,
	onClose,
	value,
	onValueChange,
}) => {
	/**
	 * Get font size unit select control
	 * @param {string} unitType - The type of unit (fontSize or screenSize)
	 * @returns {JSX.Element} SelectControl component
	 */
	const getFontSizeUnitSelect = (unitType) => {
		const fontSizeKey = unitType + "Unit";
		const units = ["px", "rem"];
		const screenUnits = ["px"];
		const unitsToUse = unitType === "fontSize" ? units : screenUnits;
		const unitOptions = unitsToUse.map((unit) => ({
			label: unit,
			value: unit,
		}));
		return (
			<SelectControl
				value={value[fontSizeKey]}
				options={unitOptions}
				onChange={(val) => {
					onValueChange(fontSizeKey, val);
				}}
			/>
		);
	};

	if (!isVisible) {
		return null;
	}

	return (
		<Popover
			placement={"overlay"}
			inline={true}
			className="kadence-popover-typography kadence-popover-typography-advanced kadence-customizer-popover"
			onClose={onClose}
		>
			<div>
				<div className="kadence-control-field kadence-title-control">
					<span className="customize-control-title">
						Font Size Clamp
					</span>
				</div>
				<div className="kadence-popover-typography-advanced-content">
					<div className="kadence-typography-advanced-group">
						<span className="kadence-typography-advanced-group-label">
							{__("Font Size Range", "kadence")}
						</span>
						<div className="kadence-typography-advanced-control-row">
							<div className="kadence-typography-advanced-control-column">
								<label className="kadence-typography-advanced-label">
									{__("Min", "kadence")}
								</label>
								<div className="kadence-typography-advanced-control">
									<input
										type="number"
										value={value.minFontSize || ""}
										onChange={(event) => {
											const val =
												event.target.value !== ""
													? Number(event.target.value)
													: "";
											onValueChange("minFontSize", val);
										}}
										className="kadence-typography-advanced-input"
									/>
									{getFontSizeUnitSelect("fontSize")}
								</div>
							</div>
							<div className="kadence-typography-advanced-control-column">
								<label className="kadence-typography-advanced-label">
									{__("Max", "kadence")}
								</label>
								<div className="kadence-typography-advanced-control">
									<input
										type="number"
										value={value.maxFontSize || ""}
										onChange={(event) => {
											const val =
												event.target.value !== ""
													? Number(event.target.value)
													: "";
											onValueChange("maxFontSize", val);
										}}
										className="kadence-typography-advanced-input"
									/>
									{getFontSizeUnitSelect("fontSize")}
								</div>
							</div>
						</div>
					</div>
					<div className="kadence-typography-advanced-group">
						<span className="kadence-typography-advanced-group-label">
							{__("Screen Size Range", "kadence")}
						</span>
						<div className="kadence-typography-advanced-control-row">
							<div className="kadence-typography-advanced-control-column">
								<label className="kadence-typography-advanced-label">
									{__("Min", "kadence")}
								</label>
								<div className="kadence-typography-advanced-control">
									<input
										type="number"
										value={value.minScreenSize || ""}
										onChange={(event) => {
											const val =
												event.target.value !== ""
													? Number(event.target.value)
													: "";
											onValueChange("minScreenSize", val);
										}}
										className="kadence-typography-advanced-input"
									/>
									{getFontSizeUnitSelect("screenSize")}
								</div>
							</div>
							<div className="kadence-typography-advanced-control-column">
								<label className="kadence-typography-advanced-label">
									{__("Max", "kadence")}
								</label>
								<div className="kadence-typography-advanced-control">
									<input
										type="number"
										value={value.maxScreenSize || ""}
										onChange={(event) => {
											const val =
												event.target.value !== ""
													? Number(event.target.value)
													: "";
											onValueChange("maxScreenSize", val);
										}}
										className="kadence-typography-advanced-input"
									/>
									{getFontSizeUnitSelect("screenSize")}
								</div>
							</div>
						</div>
					</div>
				</div>

				<Button
					className="kadence-popover-close"
					icon="no-alt"
					onClick={onClose}
				/>
			</div>
		</Popover>
	);
};

TypographyAdvancedPopover.propTypes = {
	isVisible: PropTypes.bool.isRequired,
	onClose: PropTypes.func.isRequired,
	value: PropTypes.object.isRequired,
	onValueChange: PropTypes.func.isRequired,
};

export default TypographyAdvancedPopover;
