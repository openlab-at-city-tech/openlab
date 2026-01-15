/**
 * Meta Options build.
 */
import RadioIconComponent from "./radio-icon.js";
import capitalizeFirstLetter from "./capitalize-first.js";
import { PluginSidebar, PluginSidebarMoreMenuItem } from "@wordpress/edit-post";
import { __ } from "@wordpress/i18n";
import { Component, Fragment } from "@wordpress/element";
import {
	ToggleControl,
	SelectControl,
	TextControl,
} from "@wordpress/components";
import { withSelect, withDispatch } from "@wordpress/data";
import { compose } from "@wordpress/compose";

class KadenceThemeLayout extends Component {
	constructor() {
		super(...arguments);
		this.state = {};
	}
	render() {
		const selectOptions = Object.keys(kadenceMetaParams.sidebars).map(
			(item) => {
				return {
					label: kadenceMetaParams.sidebars[item].label,
					value: kadenceMetaParams.sidebars[item].value,
				};
			}
		);
		const sidebarOptions = {
			default: {
				name: __("Default", "kadence"),
				icon: "inherit",
			},
			normal: {
				name: __("Normal", "kadence"),
				icon: "normal",
			},
			narrow: {
				name: __("Narrow", "kadence"),
				icon: "narrow",
			},
			fullwidth: {
				name: __("Fullwidth", "kadence"),
				icon: "fullwidth",
			},
			left: {
				name: __("Left Sidebar", "kadence"),
				icon: "leftsidebar",
			},
			right: {
				name: __("Right Sidebar", "kadence"),
				icon: "rightsidebar",
			},
		};
		const titleOptions = {
			default: {
				tooltip: __("Inherited Default", "kadence"),
				icon: "inherit",
			},
			hide: {
				tooltip: __("Hide Title", "kadence"),
				icon: "hidetitle",
			},
			normal: {
				tooltip: __("Show In Content", "kadence"),
				icon: "incontent",
			},
			above: {
				tooltip: __("Show Above Content", "kadence"),
				icon: "abovecontent",
			},
		};
		const newTitleOptions = {
			default: {
				name: __("Default", "kadence"),
			},
			show: {
				name: __("Enable", "kadence"),
			},
			hide: {
				name: __("Disable", "kadence"),
			},
		};
		const boxedOptions = {
			default: {
				name: __("Default", "kadence"),
			},
			boxed: {
				name: __("Boxed", "kadence"),
			},
			unboxed: {
				name: __("Unboxed", "kadence"),
			},
		};
		const paddingOptions = {
			default: {
				name: __("Default", "kadence"),
			},
			show: {
				name: __("Enable", "kadence"),
			},
			hide: {
				name: __("Disable", "kadence"),
			},
			top: {
				name: __("Top Only", "kadence"),
			},
			bottom: {
				name: __("Bottom Only", "kadence"),
			},
		};
		const featuredOptions = {
			default: {
				name: __("Default", "kadence"),
			},
			show: {
				name: __("Enable", "kadence"),
			},
			hide: {
				name: __("Disable", "kadence"),
			},
		};
		const featuredPositionOptions = {
			default: {
				name: __("Default", "kadence"),
			},
			above: {
				name: __("Above", "kadence"),
			},
			behind: {
				name: __("Behind", "kadence"),
			},
			below: {
				name: __("Below", "kadence"),
			},
		};
		const transparentOptions = {
			default: {
				name: __("Default", "kadence"),
			},
			enable: {
				name: __("Enable", "kadence"),
			},
			disable: {
				name: __("Disable", "kadence"),
			},
		};
		//console.log( kadenceMetaParams );
		const icon = (
			<svg
				width="20px"
				height="20px"
				xmlns="http://www.w3.org/2000/svg"
				fillRule="evenodd"
				strokeLinejoin="round"
				strokeMiterlimit="2"
				clipRule="evenodd"
				viewBox="0 0 50 40"
			>
				<path
					fill="#CDCDCD"
					d="M9.857 8.351H29.519V15.874H9.857z"
				></path>
				<path
					fill="#CCC"
					fillRule="nonzero"
					d="M10.259 17.908h18.847c.225 0 .41.354.41.786 0 .431-.185.785-.41.785H10.259c-.225 0-.41-.354-.41-.785 0-.432.185-.786.41-.786z"
				></path>
				<path
					fill="#8E8E8E"
					d="M47.109 38.98H2.891a1.9 1.9 0 01-1.898-1.898V2.918A1.9 1.9 0 012.891 1.02h44.218a1.9 1.9 0 011.898 1.898v34.164a1.9 1.9 0 01-1.898 1.898zm-.102-33.614H2.993V36.98h44.014V5.366zm-8.172-2.94a.9.9 0 110 1.8.9.9 0 010-1.8zm7.153 0a.9.9 0 110 1.8.9.9 0 010-1.8zm-3.538 0a.9.9 0 110 1.8.9.9 0 010-1.8z"
				></path>
				<path
					fill="#515151"
					d="M40.119 13.838l4.705 4.844-10.54 9.899a110.5 110.5 0 01-3.115 1.566 64.17 64.17 0 01-2.948 1.35 32.236 32.236 0 01-1.114.445 13 13 0 01-.794.269 4.38 4.38 0 01-.619.145 1.67 1.67 0 01-.189.018h-.061c-.089-.003-.206-.018-.258-.101-.043-.068-.043-.159-.038-.235l.007-.061a2.98 2.98 0 01.179-.646c.09-.245.193-.485.301-.722.186-.408.387-.809.594-1.206.369-.708.759-1.405 1.157-2.097a104.799 104.799 0 012.183-3.624l10.55-9.844zM30.686 24.71l2.542 2.725-3.053 1.621-1.329-1.217 1.84-3.129zm11.137-12.463l2.23-2.081s1.959-1.222 4.028.819c1.729 1.706.765 3.92.765 3.92l-2.323 2.182-4.7-4.84z"
				></path>
				<path
					fill="#E5E5E5"
					d="M40.152 26.389v7.571h-8.567v-1.649l.108-.045c.987-.415 1.96-.862 2.92-1.336a44.58 44.58 0 001.751-.906l3.788-3.635zm0-15.912l-8.567 8.041V8.421h8.567v2.056z"
				></path>
				<path
					fill="#CCC"
					fillRule="nonzero"
					d="M28.872 21.063l-.592.557s-.284.383-.716 1.015H10.259a.256.256 0 01-.039-.003.332.332 0 01-.19-.132l-.023-.033c-.01-.014-.018-.029-.027-.043a1.15 1.15 0 01-.124-.436 1.435 1.435 0 01.006-.334c.012-.091.033-.18.066-.266.009-.025.02-.049.031-.073l.032-.059.027-.041a.392.392 0 01.025-.032.308.308 0 01.177-.116.515.515 0 01.039-.004h18.613z"
				></path>
				<path
					fill="#CDCDCD"
					fillRule="nonzero"
					d="M26.519 24.219a47.303 47.303 0 00-.953 1.572H10.259a.172.172 0 01-.039-.004.326.326 0 01-.19-.131.405.405 0 01-.023-.034c-.01-.014-.018-.028-.027-.043a1.156 1.156 0 01-.124-.436 1.426 1.426 0 01.006-.333c.012-.091.033-.181.066-.266.009-.025.02-.049.031-.073l.032-.059.027-.041a.392.392 0 01.025-.032.308.308 0 01.177-.116.172.172 0 01.039-.004h16.26z"
				></path>
				<path
					fill="#CCC"
					fillRule="nonzero"
					d="M23.417 30.531c-.152.573-.233 1.106-.214 1.571H10.259a.256.256 0 01-.039-.003.328.328 0 01-.19-.132l-.023-.033c-.01-.014-.018-.029-.027-.043a1.162 1.162 0 01-.124-.436 1.436 1.436 0 01.006-.334c.012-.09.033-.18.066-.266.009-.025.02-.049.031-.073l.032-.059.027-.041a.392.392 0 01.025-.032.313.313 0 01.177-.116.256.256 0 01.039-.003h13.158z"
				></path>
				<path
					fill="#CDCDCD"
					fillRule="nonzero"
					d="M24.701 27.375a22.69 22.69 0 00-.732 1.572h-13.71c-.013 0-.026-.002-.039-.004a.326.326 0 01-.19-.131.405.405 0 01-.023-.034c-.01-.014-.018-.028-.027-.043a1.162 1.162 0 01-.124-.436 1.436 1.436 0 01.006-.334 1.177 1.177 0 01.097-.338c.01-.021.021-.04.032-.059l.027-.042a.698.698 0 01.025-.032.312.312 0 01.177-.115.172.172 0 01.039-.004h14.442z"
				></path>
			</svg>
		);
		return (
			<Fragment>
				<PluginSidebarMoreMenuItem
					target="theme-meta-panel"
					icon={icon}
				>
					{kadenceMetaParams.post_type_name +
						" " +
						__("Settings", "kadence")}
				</PluginSidebarMoreMenuItem>
				<PluginSidebar
					isPinnable={true}
					icon={icon}
					name="theme-meta-panel"
					title={
						kadenceMetaParams.post_type_name +
						" " +
						__("Settings", "kadence")
					}
				>
					<div className="kadence-sidebar-container components-panel__body is-opened">
						<RadioIconComponent
							label={__("Transparent Header", "kadence")}
							value={
								undefined !== this.props.meta &&
								undefined !==
									this.props.meta._kad_post_transparent &&
								"" !== this.props.meta._kad_post_transparent
									? this.props.meta._kad_post_transparent
									: "default"
							}
							customClass="three-col-short"
							options={transparentOptions}
							onChange={(value) => {
								this.props.setMetaFieldValue(
									value,
									"_kad_post_transparent"
								);
							}}
						/>
						<RadioIconComponent
							label={
								kadenceMetaParams.post_type_name +
								" " +
								__("Title", "kadence")
							}
							customClass="three-col-short"
							value={
								undefined !== this.props.meta &&
								undefined !== this.props.meta._kad_post_title &&
								"" !== this.props.meta._kad_post_title
									? this.props.meta._kad_post_title
									: "default"
							}
							options={newTitleOptions}
							onChange={(value) => {
								let title = value;
								if ("default" === value) {
									title = kadenceMetaParams.title;
								} else if ("show" === value) {
									title = kadenceMetaParams.title_position;
								}
								document.body.classList.remove(
									"post-content-title-normal"
								);
								document.body.classList.remove(
									"post-content-title-above"
								);
								document.body.classList.remove(
									"post-content-title-hide"
								);
								document.body.classList.add(
									"post-content-title-" + title
								);
								document.body.classList.remove(
									"admin-color-pct-normal"
								);
								document.body.classList.remove(
									"admin-color-pct-above"
								);
								document.body.classList.remove(
									"admin-color-pct-hide"
								);
								document.body.classList.add(
									"admin-color-pct-" + title
								);
								let gEditor = document.querySelector(
									"iframe[name=editor-canvas]"
								);
								let gBody =
									gEditor?.contentWindow?.document?.body;
								if (gBody) {
									gBody.classList.remove(
										"admin-color-pct-normal"
									);
									gBody.classList.remove(
										"admin-color-pct-above"
									);
									gBody.classList.remove(
										"admin-color-pct-hide"
									);
									gBody.classList.add(
										"admin-color-pct-" + title
									);
								}
								this.props.setMetaFieldValue(
									value,
									"_kad_post_title"
								);
							}}
						/>
						<RadioIconComponent
							label={
								kadenceMetaParams.post_type_name +
								" " +
								__("Layout", "kadence")
							}
							customClass="three-col-square"
							value={
								undefined !== this.props.meta &&
								undefined !==
									this.props.meta._kad_post_layout &&
								"" !== this.props.meta._kad_post_layout
									? this.props.meta._kad_post_layout
									: "default"
							}
							options={sidebarOptions}
							onChange={(value) => {
								let layout = value;
								let sidebar = "none";
								if ("left" === value || "right" === value) {
									layout = "narrow";
									sidebar = value;
								} else if ("default" === value) {
									layout = kadenceMetaParams.layout;
									sidebar = kadenceMetaParams.sidebar;
								}
								document.body.classList.remove(
									"post-content-width-narrow"
								);
								document.body.classList.remove(
									"post-content-width-normal"
								);
								document.body.classList.remove(
									"post-content-width-fullwidth"
								);

								document.body.classList.remove(
									"post-content-sidebar-right"
								);
								document.body.classList.remove(
									"post-content-sidebar-left"
								);
								document.body.classList.remove(
									"post-content-sidebar-none"
								);
								document.body.classList.add(
									"post-content-width-" + layout
								);
								document.body.classList.add(
									"post-content-sidebar-" + sidebar
								);
								document.body.classList.remove(
									"admin-color-pcw-narrow"
								);
								document.body.classList.remove(
									"admin-color-pcw-normal"
								);
								document.body.classList.remove(
									"admin-color-pcw-fullwidth"
								);

								document.body.classList.remove(
									"admin-color-pc-sidebar-left"
								);
								document.body.classList.remove(
									"admin-color-pc-sidebar-right"
								);
								document.body.classList.remove(
									"admin-color-pc-sidebar-none"
								);
								document.body.classList.add(
									"admin-color-pcw-" + layout
								);
								document.body.classList.add(
									"admin-color-pc-sidebar-" + sidebar
								);

								let gEditor = document.querySelector(
									"iframe[name=editor-canvas]"
								);
								let gBody =
									gEditor?.contentWindow?.document?.body;
								if (gBody) {
									gBody.classList.remove(
										"admin-color-pcw-narrow"
									);
									gBody.classList.remove(
										"admin-color-pcw-normal"
									);
									gBody.classList.remove(
										"admin-color-pcw-fullwidth"
									);
									gBody.classList.add(
										"admin-color-pcw-" + layout
									);
									gBody.classList.remove(
										"admin-color-pc-sidebar-left"
									);
									gBody.classList.remove(
										"admin-color-pc-sidebar-right"
									);
									gBody.classList.remove(
										"admin-color-pc-sidebar-none"
									);
									gBody.classList.add(
										"admin-color-pc-sidebar-" + sidebar
									);
								}
								this.props.setMetaFieldValue(
									value,
									"_kad_post_layout"
								);
							}}
						/>
						{((undefined !== this.props.meta &&
							undefined !== this.props.meta._kad_post_layout &&
							"" !== this.props.meta._kad_post_layout &&
							("left" === this.props.meta._kad_post_layout ||
								"right" ===
									this.props.meta._kad_post_layout)) ||
							(undefined !== this.props.meta &&
								undefined !==
									this.props.meta._kad_post_layout &&
								"default" ===
									this.props.meta._kad_post_layout &&
								("left" === kadenceMetaParams.sidebar ||
									"right" === kadenceMetaParams.sidebar)) ||
							(undefined !== this.props.meta &&
								undefined !==
									this.props.meta._kad_post_layout &&
								"" === this.props.meta._kad_post_layout &&
								("left" === kadenceMetaParams.sidebar ||
									"right" ===
										kadenceMetaParams.sidebar))) && (
							<div className="kadence-control-field kadence-select-control">
								<div className="kadence-title-control-bar">
									<span className="customize-control-title">
										{capitalizeFirstLetter(
											kadenceMetaParams.post_type
										) +
											" " +
											__("sidebar", "kadence")}
									</span>
								</div>
								<SelectControl
									value={
										undefined !== this.props.meta &&
										undefined !==
											this.props.meta
												._kad_post_sidebar_id &&
										"" !==
											this.props.meta._kad_post_sidebar_id
											? this.props.meta
													._kad_post_sidebar_id
											: "default"
									}
									options={selectOptions}
									onChange={(val) => {
										this.props.setMetaFieldValue(
											val,
											"_kad_post_sidebar_id"
										);
									}}
								/>
							</div>
						)}
						<RadioIconComponent
							label={__("Content Style", "kadence")}
							value={
								undefined !== this.props.meta &&
								undefined !==
									this.props.meta._kad_post_content_style &&
								"" !== this.props.meta._kad_post_content_style
									? this.props.meta._kad_post_content_style
									: "default"
							}
							customClass="three-col-short"
							options={boxedOptions}
							onChange={(value) => {
								let boxed = value;
								if ("default" === value) {
									boxed = kadenceMetaParams.boxed;
								}
								document.body.classList.remove(
									"post-content-style-boxed"
								);
								document.body.classList.remove(
									"post-content-style-unboxed"
								);
								document.body.classList.add(
									"post-content-style-" + boxed
								);
								document.body.classList.remove(
									"admin-color-pcs-boxed"
								);
								document.body.classList.remove(
									"admin-color-pcs-unboxed"
								);
								document.body.classList.add(
									"admin-color-pcs-" + boxed
								);
								let gEditor = document.querySelector(
									"iframe[name=editor-canvas]"
								);
								let gBody =
									gEditor?.contentWindow?.document?.body;
								if (gBody) {
									gBody.classList.remove(
										"admin-color-pcs-boxed"
									);
									gBody.classList.remove(
										"admin-color-pcs-unboxed"
									);
									gBody.classList.add(
										"admin-color-pcs-" + boxed
									);
								}
								this.props.setMetaFieldValue(
									value,
									"_kad_post_content_style"
								);
							}}
						/>
						<RadioIconComponent
							label={__("Content Vertical Spacing", "kadence")}
							value={
								undefined !== this.props.meta &&
								undefined !==
									this.props.meta
										._kad_post_vertical_padding &&
								"" !==
									this.props.meta._kad_post_vertical_padding
									? this.props.meta._kad_post_vertical_padding
									: "default"
							}
							options={paddingOptions}
							customClass="three-col-short"
							onChange={(value) => {
								let padding = value;
								if ("default" === value) {
									padding = kadenceMetaParams.vpadding;
								}
								document.body.classList.remove(
									"post-content-vertical-padding-show"
								);
								document.body.classList.remove(
									"post-content-vertical-padding-hide"
								);
								document.body.classList.remove(
									"post-content-vertical-padding-top"
								);
								document.body.classList.remove(
									"post-content-vertical-padding-bottom"
								);
								document.body.classList.add(
									"post-content-vertical-padding-" + padding
								);
								let gEditor = document.querySelector(
									"iframe[name=editor-canvas]"
								);
								let gBody =
									gEditor?.contentWindow?.document?.body;
								if (gBody) {
									gBody.classList.remove(
										"admin-color-pcvp-show"
									);
									gBody.classList.remove(
										"admin-color-pcvp-hide"
									);
									gBody.classList.remove(
										"admin-color-pcvp-top"
									);
									gBody.classList.remove(
										"admin-color-pcvp-bottom"
									);
									gBody.classList.add(
										"admin-color-pcvp-" + padding
									);
								}
								this.props.setMetaFieldValue(
									value,
									"_kad_post_vertical_padding"
								);
							}}
						/>
						{kadenceMetaParams.supports_feature && (
							<Fragment>
								<RadioIconComponent
									label={__("Show Featured Image", "kadence")}
									value={
										undefined !== this.props.meta &&
										undefined !==
											this.props.meta._kad_post_feature &&
										"" !== this.props.meta._kad_post_feature
											? this.props.meta._kad_post_feature
											: "default"
									}
									options={featuredOptions}
									customClass="three-col-short"
									onChange={(value) => {
										this.props.setMetaFieldValue(
											value,
											"_kad_post_feature"
										);
									}}
								/>
								{((undefined !== this.props.meta &&
									undefined !==
										this.props.meta._kad_post_feature &&
									"show" ===
										this.props.meta._kad_post_feature) ||
									(undefined !== this.props.meta &&
										undefined !==
											this.props.meta._kad_post_feature &&
										"default" ===
											this.props.meta._kad_post_feature &&
										"show" === kadenceMetaParams.feature) ||
									(undefined !== this.props.meta &&
										undefined !==
											this.props.meta._kad_post_feature &&
										"" ===
											this.props.meta._kad_post_feature &&
										"show" ===
											kadenceMetaParams.feature)) && (
									<RadioIconComponent
										label={__(
											"Featured Image Position",
											"kadence"
										)}
										value={
											undefined !== this.props.meta &&
											undefined !==
												this.props.meta
													._kad_post_feature_position &&
											"" !==
												this.props.meta
													._kad_post_feature_position
												? this.props.meta
														._kad_post_feature_position
												: "default"
										}
										options={featuredPositionOptions}
										customClass="two-col-short"
										onChange={(value) => {
											this.props.setMetaFieldValue(
												value,
												"_kad_post_feature_position"
											);
										}}
									/>
								)}
							</Fragment>
						)}
						<div style={{ paddingTop: 20 + "px" }}></div>
						<TextControl
							label={__("Additional CSS class(es)", "kadence")}
							value={
								undefined !== this.props.meta &&
								undefined !==
									this.props.meta._kad_post_classname &&
								"" !== this.props.meta._kad_post_classname
									? this.props.meta._kad_post_classname
									: ""
							}
							onChange={(value) => {
								this.props.setMetaFieldValue(
									value,
									"_kad_post_classname"
								);
							}}
							help={__(
								"The class(es) will be added to the body of this page. Separate multiple classes with a space.",
								"kadence"
							)}
						/>
						<div style={{ paddingTop: 10 + "px" }}></div>
						<ToggleControl
							label={__("Disable Header", "kadence")}
							checked={
								undefined !== this.props.meta &&
								undefined !==
									this.props.meta._kad_post_header &&
								"" !== this.props.meta._kad_post_header
									? this.props.meta._kad_post_header
									: false
							}
							onChange={(value) => {
								this.props.setMetaFieldValue(
									value,
									"_kad_post_header"
								);
							}}
						/>
						<ToggleControl
							label={__("Disable Footer", "kadence")}
							checked={
								undefined !== this.props.meta &&
								undefined !==
									this.props.meta._kad_post_footer &&
								"" !== this.props.meta._kad_post_footer
									? this.props.meta._kad_post_footer
									: false
							}
							onChange={(value) => {
								this.props.setMetaFieldValue(
									value,
									"_kad_post_footer"
								);
							}}
						/>
					</div>
				</PluginSidebar>
			</Fragment>
		);
	}
}
export default compose(
	withSelect((select) => {
		const postMeta = select("core/editor").getEditedPostAttribute("meta");
		const oldPostMeta =
			select("core/editor").getCurrentPostAttribute("meta");
		return {
			meta: { ...oldPostMeta, ...postMeta },
			oldMeta: oldPostMeta,
		};
	}),
	withDispatch((dispatch) => ({
		setMetaFieldValue: (value, field) =>
			dispatch("core/editor").editPost({ meta: { [field]: value } }),
	}))
)(KadenceThemeLayout);
