/* jshint esversion: 6 */

const { Component, Fragment } = wp.element;
import { isString } from "lodash";
import KadenceColorPicker from "../../common/color-picker";
import SwatchesControl from "../../common/swatches";

class KadenceGradientColorPicker extends Component {
	constructor() {
		super(...arguments);
		this.state = {
			color: this.props.color ? this.props.color : "",
		};
	}
	render() {
		const getColorValue = () => {
			let color;
			const paletteIndex = this.state.color?.match(/\d+$/)?.[0] - 1;
			if (
				undefined !== this.state.color &&
				"" !== this.state.color &&
				isString(this.state.color) &&
				this.state.color.includes("palette") &&
				this.props.activePalette &&
				this.props.activePalette[paletteIndex]
			) {
				color = this.props.activePalette[paletteIndex]?.color;
			} else {
				color = this.state.color;
			}
			return color;
		};
		return (
			<div className="kadence-background-color-wrap">
				<KadenceColorPicker
					color={getColorValue()}
					onChangeComplete={(color) => {
						let rgb = {
							type: "rgba",
							value: "",
						};
						if (undefined !== color.rgb) {
							this.setState({ color: color.rgb });
							rgb.value = color.rgb;
						} else {
							this.setState({ color: color.hex });
							rgb.type = "literal";
							rgb.value = color.hex;
						}

						this.props.onChange(rgb);
					}}
				/>
				<SwatchesControl
					colors={this.props.activePalette}
					isPalette={
						undefined !== this.state.color &&
						"" !== this.state.color &&
						isString(this.state.color) &&
						this.state.color.includes("palette")
							? this.state.color
							: ""
					}
					onClick={(color, palette) => {
						this.setState({
							color: "var(--global-" + palette + ")",
						});
						let rgb = {
							type: "literal",
							value: "var(--global-" + palette + ")",
						};
						this.props.onChange(rgb);
					}}
				/>
			</div>
		);
	}
}
export default KadenceGradientColorPicker;
