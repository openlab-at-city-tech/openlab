import { RangeControl, TextControl, Panel, TabPanel, PanelBody, ToggleControl, FontSizePicker, CustomSelectControl } from '@wordpress/components';
import {
	__experimentalNumberControl as NumberControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	__experimentalBoxControl as BoxControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem
} from '@wordpress/components';
import {
	InspectorControls,
	PanelColorSettings,
	useBlockProps,
	__experimentalFontFamilyControl as FontFamilyControl
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

export default function showInspectorControls(block_ui_config, attributes, setAttributes, blockName) {

	const { select } = wp.data;
	const pageTemplate = select('core/editor').getEditedPostAttribute('template');

	// Disable links inside blocks content
	const disableLinksInsideBlockContent = (event) => {
		const link = event.target.closest('a');
		if (link && link.closest('.eckb-block-editor-preview') && ! link.closest('.eckb-kb-no-content')) {
			event.preventDefault();
		}
	};

	const blockProps = useBlockProps({
		onClickCapture: disableLinksInsideBlockContent,
	});

	function ensure_number(input_value) {
		return typeof input_value === 'number' ? input_value : parseInt(input_value)
	}

	function check_dependency(field_specs) {

		// Hide on	TODO later: add similar 'show_on_dependencies'
		if (field_specs.hide_on_dependencies) {
			const hide_on_dependencies = Object.entries(field_specs.hide_on_dependencies);
			let show_field = true;
			hide_on_dependencies.forEach(([dependency_name, dependency__value]) => {
				if (attributes[dependency_name] === dependency__value) {
					show_field = false;
				}
			});
			return show_field;
		}

		return true;
	}

	return (
		<div {...blockProps}>
			<InspectorControls>
				<Panel className="epkb-block-editor-controls">
					<TabPanel
						className="epkb-block-editor-tabpanel"
						tabs={Object.entries(block_ui_config).map(([tab_name, tab_config]) => {
							return {
								name: tab_name,
								title: tab_config.title,
								icon: tab_config.icon,
								groups: tab_config.groups,
							};
						})}
					>{(tab) => {

						// Tab
						return <React.Fragment>{

							// Groups in the current tab
							Object.entries(tab.groups).map(([group_key, {title, fields}]) => {
								return <PanelBody key={group_key} title={title} className="epkb-block-ui-section">{

									// Controls in the current group
									Object.entries(fields).map(([field_name, field_specs]) => {

										if (!check_dependency(field_specs)) {
											return null;
										}

										switch (field_specs.setting_type) {

											case 'text':
												return <TextControl
													key={field_name}
													label={field_specs.label}
													value={attributes[field_name] || field_specs.default}
													onChange={(value) => setAttributes({[field_name]: value})}
													help={field_specs.description || ''}
													className="epkb-block-ui-text-control"
												/>;

											case 'number':
												return <NumberControl
													key={field_name}
													isShiftStepEnabled={true}
													shiftStep={1}
													min={field_specs.min}
													max={field_specs.max}
													label={field_specs.label}
													value={(attributes[field_name] === "" || Number.isNaN(parseInt(attributes[field_name]))) ? 1 : parseInt(attributes[field_name])}
													onChange={(value) => setAttributes({[field_name]: (value === "" || Number.isNaN(parseInt(value))) ? 1 : parseInt(value)})}
													className="epkb-block-ui-number-control"
												/>;

											case 'color':
												// epkb-block-editor-controls
												return <PanelColorSettings
													key={field_name}
													colorSettings={[{
														value: attributes[field_name],
														onChange: (newColor) => setAttributes({[field_name]: newColor}),
														enableAlpha: true,
														label: field_specs.label,
													}]}
													className="epkb-block-ui-color-control"
												/>;

											case 'select_buttons_string':
												return <ToggleGroupControl
													key={field_name}
													__nextHasNoMarginBottom
													isBlock
													label={field_specs.label}
													onChange={(value) => setAttributes({[field_name]: value})}
													value={typeof attributes[field_name] !== 'undefined' ? attributes[field_name] : field_specs.default}
													className="epkb-block-ui-select-buttons-control"
												>
													{Object.entries(field_specs.options).map(([option_value, option_label]) => {
														return <ToggleGroupControlOption
															key={option_value}
															label={option_label}
															value={option_value}
														/>
													})}
												</ToggleGroupControl>

											case 'select_buttons':
												return <ToggleGroupControl
													key={field_name}
													__nextHasNoMarginBottom
													isBlock
													label={field_specs.label}
													onChange={(value) => setAttributes({[field_name]: ensure_number(value)})}
													value={typeof attributes[field_name] !== 'undefined' ? ensure_number(attributes[field_name]) : ensure_number(field_specs.default)}
													className="epkb-block-ui-select-buttons-control"
												>
													{Object.entries(field_specs.options).map(([option_value, option_label]) => {
														return <ToggleGroupControlOption
															key={option_value}
															label={option_label}
															value={typeof option_value === 'number' ? option_value : parseInt(option_value)}
														/>
													})}
												</ToggleGroupControl>

											case 'toggle':
												return <ToggleControl
													key={field_name}
													__nextHasNoMarginBottom
													label={field_specs.label}
													checked={attributes[field_name] === 'on'}
													onChange={(value) => setAttributes({[field_name]: (value ? 'on' : 'off')})}
													className="epkb-block-ui-toggle-control"
												/>

											case 'range':
												return <RangeControl
													key={field_name}
													isShiftStepEnabled={true}
													shiftStep={1}
													min={field_specs.min}
													max={field_specs.max}
													label={field_specs.label}
													value={(attributes[field_name] === "" || Number.isNaN(parseInt(attributes[field_name]))) ? parseInt(field_specs.default) : parseInt(attributes[field_name])}
													onChange={(value) => setAttributes({[field_name]: (value === "" || Number.isNaN(parseInt(value))) ? 1 : parseInt(value)})}
													className="epkb-block-ui-range-control"
												/>

											case 'custom_dropdown':
												const selectedKey = attributes[field_name] || field_specs.default;
												const selectedOption = Object.values(field_specs.options).find(
													(option) => option.key === selectedKey
												);
												return <CustomSelectControl
													key={field_name}
													__next40pxDefaultSize={true}
													label={field_specs.label}
													value={selectedOption}
													onChange={(value) => setAttributes({[field_name]: value.selectedItem.key})}
													options={Object.entries(field_specs.options).map(([option_index, options_list]) => {
														return {
															key: options_list.key,
															name: options_list.name,
															style: options_list.style,
														};
													})}
													className="epkb-block-ui-custom-dropdown-control"
												/>

											case 'dropdown':
												return <CustomSelectControl
													key={field_name}
													__next40pxDefaultSize={true}
													label={field_specs.label}
													value={{
														key: attributes[field_name] || field_specs.default,
														name: field_specs.options[attributes[field_name] || field_specs.default],
														style: {},
													}}
													onChange={(value) => setAttributes({[field_name]: value.selectedItem.key})}
													options={Object.entries(field_specs.options).map(([option_key, options_name]) => {
														return {
															key: option_key,
															name: options_name,
															style: {},
														};
													})}
													className="epkb-block-ui-dropdown-control"
												/>

											case 'box_control':
												const sideValues = {[field_specs.side]: attributes[field_name] === "" || Number.isNaN(parseInt(attributes[field_name])) ? field_specs.default : parseInt(attributes[field_name])};
												return <BoxControl
													key={field_name}
													__next40pxDefaultSize={true}
													label={field_specs.label}
													sides={field_specs.side}
													inputProps={{
														min: ensure_number(field_specs.min),
														max: ensure_number(field_specs.max),
													}}
													values={sideValues}
													onChange={(values) => setAttributes({[field_name]: values[field_specs.side] === "" || Number.isNaN(parseInt(values[field_specs.side])) ? field_specs.default : parseInt(values[field_specs.side])})}
													className="epkb-block-ui-box-control"
												/>

											case 'box_control_combined':
												const combinedSidesList = Object.values(field_specs.combined_settings).map((setting_specs) => {
													return setting_specs.side
												});
												const combinedSideValues = Object.fromEntries(
													Object.entries(field_specs.combined_settings).map(([setting_name, setting_specs]) => {
														const combinedOneSideValue = attributes[setting_name] === "" || Number.isNaN(parseInt(attributes[setting_name])) ? setting_specs.default : parseInt(attributes[setting_name]);
														return [setting_specs.side, combinedOneSideValue];
													})
												);
												const defaultSideValues = Object.fromEntries(
													Object.entries(field_specs.combined_settings).map(([setting_name, setting_specs]) => {
														return [setting_specs.side, setting_specs.default]
													})
												);
												return <BoxControl
													key={field_name}
													__next40pxDefaultSize={true}
													label={field_specs.label}
													sides={combinedSidesList}
													resetValues={defaultSideValues}
													inputProps={{
														min: ensure_number(field_specs.min),
														max: ensure_number(field_specs.max),
													}}
													values={combinedSideValues}
													onChange={(values) => {
														Object.entries(values).forEach(([side, value]) => {
															Object.entries(field_specs.combined_settings).forEach(([setting_name, setting_specs]) => {
																if (setting_specs.side === side) {
																	setAttributes({[setting_name]: value === "" || Number.isNaN(parseInt(value)) ? field_specs.combined_settings[setting_name].default : parseInt(value)});
																}
															});
														});
													}}
													className="epkb-block-ui-box-combined-control"
												/>

											case 'typography_controls':
												return <ToolsPanel
													key={field_name}
													label={field_specs.label}
													resetAll={() => {
														setAttributes({
															[field_name]: {
																...attributes[field_name],
																font_family: field_specs.controls.font_family.default,
																font_size: field_specs.controls.font_size.default,
																font_appearance: field_specs.controls.font_appearance.default,
															},
														});
													}}
													className="epkb-typography-controls"
												>
													<ToolsPanelItem
														hasValue={() => { return attributes[field_name].font_family !== field_specs.controls.font_family.default; }}
														label={field_specs.controls.font_family.label}
														onDeselect={() => {
															setAttributes({
																[field_name]: {
																	...attributes[field_name],
																	font_family: field_specs.controls.font_family.default,
																},
															});
														}}
													>
														<FontFamilyControl
															key={field_name + '_font_family'}
															__next40pxDefaultSize={true}
															fontFamilies={Object.entries(field_specs.controls.font_family.options).map(([option_index, options_value]) => {
																return {
																	fontFamily: options_value,
																	name: options_value,
																	slug: options_value,
																};
															})}
															onChange={(value) => {
																const typographyControls = {
																	...attributes[field_name],
																	font_family: value,
																};
																setAttributes({[field_name]: typographyControls});
															}}
															value={attributes[field_name].font_family || field_specs.controls.font_family.default}
														/>
													</ToolsPanelItem>
													<ToolsPanelItem
														hasValue={() => { return true; }}
														label={field_specs.controls.font_size.label}
														onDeselect={() => {
															setAttributes({
																[field_name]: {
																	...attributes[field_name],
																	font_size: field_specs.controls.font_size.default,
																},
															});
														}}
														isShownByDefault
													>
														<FontSizePicker
															key={field_name + '_font_size'}
															fontSizes={Object.entries(field_specs.controls.font_size.options).map(([option_slug, options_list]) => {
																return {
																	name: options_list.name,
																	size: options_list.size,
																	slug: option_slug,
																};
															})}
															value={attributes[field_name].font_size || field_specs.controls.font_size.default}
															onChange={(value) => {
																const typographyControls = {
																	...attributes[field_name],
																	font_size: value,
																};
																setAttributes({[field_name]: typographyControls});
															}}
															disableCustomFontSizes={false}
															withReset={false}
															withSlider={true}
															fallbackFontSize={field_specs.controls.font_size.default}
														/>
													</ToolsPanelItem>
													<ToolsPanelItem
														hasValue={() => { return attributes[field_name].font_appearance !== field_specs.controls.font_appearance.default; }}
														label={field_specs.controls.font_appearance.label}
														onDeselect={() => {
															setAttributes({
																[field_name]: {
																	...attributes[field_name],
																	font_appearance: field_specs.controls.font_appearance.default,
																},
															});
														}}
													>
														<CustomSelectControl
															key={field_name + '_font_appearance'}
															__next40pxDefaultSize={true}
															label={field_specs.controls.font_appearance.label}
															value={(() => {
																const selectedKey = attributes[field_name].font_appearance || field_specs.controls.font_appearance.default;
																const selectedOption = field_specs.controls.font_appearance.options[selectedKey];
																return {
																	key: selectedKey,
																	...selectedOption,
																};
															})()}
															onChange={(value) => {
																const typographyControls = {
																	...attributes[field_name],
																	font_appearance: value.selectedItem.key,
																};
																setAttributes({[field_name]: typographyControls});
															}}
															options={Object.entries(field_specs.controls.font_appearance.options).map(([option_key, options_list]) => {
																return {
																	key: option_key,
																	name: options_list.name,
																	style: options_list.style,
																};
															})}
														/>
													</ToolsPanelItem>
												</ToolsPanel>

											case 'section_description':

												// Optionally show the message only for non-KB block page template
												if ('show_for_non_kb_template' in field_specs && pageTemplate === 'kb-block-page-template') {
													return null;
												}

												return <div key={field_name} className="epkb-block-ui-section-description">
													<span>{field_specs.description}</span>
													{field_specs.link_text.length > 0 ? (<a href={field_specs.link_url}>{field_specs.link_text}</a>) : null}
												</div>;

											default:
												return null;
										}
									})
								}</PanelBody>
							})
						}</React.Fragment>
					}}
					</TabPanel>
				</Panel>
			</InspectorControls>
			<ServerSideRender
				block={blockName}
				attributes={attributes}
				urlQueryArgs={{is_editor_preview: 1}}
				httpMethod="POST"
			/>
		</div>
	);
}
