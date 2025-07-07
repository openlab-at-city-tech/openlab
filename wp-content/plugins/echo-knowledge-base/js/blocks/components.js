import { useState, useEffect } from '@wordpress/element';
import { RangeControl, TextControl, Panel, TabPanel, PanelBody, ToggleControl, FontSizePicker, CustomSelectControl, CheckboxControl, BaseControl } from '@wordpress/components';
import {
	__experimentalNumberControl as NumberControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	__experimentalToggleGroupControlOptionIcon as ToggleGroupControlOptionIcon,
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
import { useDispatch } from '@wordpress/data';
import { show_kb_block_page_template } from "./utils";

export default function EpkbInspectorControls({ block_ui_config, attributes, setAttributes, blockName }) {

	const { select } = wp.data;
	const { editPost } = useDispatch('core/editor');
	const coreStore = select('core');
	const editorStore = select('core/editor');

	const pageTemplate = editorStore.getEditedPostAttribute('template');
	const currentlyStoredTemplate = coreStore.getEntityRecord('postType', editorStore.getCurrentPostType(), editorStore.getCurrentPostId())?.template;
	const isTemplateToggleChecked = pageTemplate === block_ui_config.settings.kb_block_page_template;

	// To store temporary values of block without actually storing the value in the block attributes (e.g. like selected presets)
	const [epkbBlockStorage, setEpkbBlockStorage] = useState({});

	// A set of all field names which are currently used for the current block UI and the field types (to detect whether dependency field is actually present in the UI or is only present in attributes)
	const currentUiFieldTypes = Object.fromEntries(
		Object.values(block_ui_config)
			.filter(tab => tab.groups)
			.flatMap(tab => Object.values(tab.groups))
			.filter(group => group.fields)
			.flatMap(group => Object.entries(group.fields))
			.map(([field_name, field_config]) => [field_name, field_config.setting_type])
	);

	// Disable links inside blocks content
	const disableLinksInsideBlockContent = (event) => {
		const link = event.target.closest('a');
		if (link && link.closest('.eckb-block-editor-preview') && ! link.closest('.eckb-kb-no-content') && ! link.closest('#eckb-kb-faqs-not-assigned')) {
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

		let show_field = true;

		// Hide the current field when any of the target fields has certain value
		if (field_specs.hide_on_dependencies) {
			const hide_on_dependencies = Object.entries(field_specs.hide_on_dependencies);
			hide_on_dependencies.forEach(([dependency_name, dependency_value]) => {
				if (dependency_name in currentUiFieldTypes && currentUiFieldTypes[dependency_name].length && attributes[dependency_name] === dependency_value) {
					show_field = false;
				}
			});
		}

		// Hide the current field when any of the target selection controls has certain amount of selected options
		if (field_specs.hide_on_selection_amount_dependencies) {
			const hide_on_selection_amount_dependencies = Object.entries(field_specs.hide_on_selection_amount_dependencies);
			hide_on_selection_amount_dependencies.forEach(([dependency_name, dependency_amount]) => {
				if (dependency_name in currentUiFieldTypes && currentUiFieldTypes[dependency_name].length && attributes[dependency_name].length === dependency_amount) {
					show_field = false;
				}
			});
		}

		return show_field;
	}

	function is_disabled_by_dependency(field_specs) {
		let disabled = false;

		// Disable on
		if (field_specs.disable_on_dependencies) {
			const disable_on_dependencies = Object.entries(field_specs.disable_on_dependencies);
			disable_on_dependencies.forEach(([dependency_name, dependency_value]) => {
				if (attributes[dependency_name] === dependency_value) {
					disabled = true;
				}
			});
		}

		return disabled;
	}

	// show KB block page template by removing inline CSS which hides it (running each time when this block is added or edited)
	show_kb_block_page_template();

	// Effect to synchronize template_toggle attributes after render
	useEffect(() => {
		// Find all template_toggle fields
		const templateToggleFields = Object.values(block_ui_config)
			.filter(tab => tab.groups)
			.flatMap(tab => Object.values(tab.groups))
			.filter(group => group.fields)
			.flatMap(group => Object.entries(group.fields))
			.filter(([, fconf]) => fconf.setting_type === 'template_toggle')
			.map(([fname]) => fname);

		templateToggleFields.forEach((toggleFieldName) => {
			if (isTemplateToggleChecked && attributes[toggleFieldName] !== 'on') {
				setAttributes({ [toggleFieldName]: 'on' });
			} else if (!isTemplateToggleChecked && attributes[toggleFieldName] === 'on') {
				setAttributes({ [toggleFieldName]: 'off' });
			}
		});
	}, [isTemplateToggleChecked, attributes, block_ui_config, setAttributes]);

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

								// Filter fields for the current group by their dependencies
								const filteredFields = Object.entries(fields).filter(([field_name, field_specs]) => check_dependency(field_specs));

								// Do not display the current group if it currently does not have any field
								if (!filteredFields.length) {
									return null;
								}
								const GroupKeyClass =
									typeof group_key === "string" && group_key.trim() !== ""
										? " epkb-"+group_key
										: "";

								return <PanelBody key={group_key} title={title} className={"epkb-block-ui-section" + GroupKeyClass}>{

									// Controls in the current group
									filteredFields.map(([field_name, field_specs]) => {

										if (!check_dependency(field_specs)) {
											return null;
										}

										const isDisabled = is_disabled_by_dependency(field_specs);

										switch (field_specs.setting_type) {

											case 'text':
												return <TextControl
													key={field_name}
													__nextHasNoMarginBottom={true}
													__next40pxDefaultSize={true}
													disabled={isDisabled}
													label={field_specs.label}
													value={attributes[field_name]}
													onChange={(value) => setAttributes({[field_name]: value})}
													help={field_specs.description || ''}
													className="epkb-block-ui-text-control"
												/>;

											case 'number':
												return <NumberControl
													key={field_name}
													__next40pxDefaultSize={true}
													disabled={isDisabled}
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
													__nextHasNoMarginBottom={true}
													__next40pxDefaultSize={true}
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

											case 'select_buttons_icon':
												return <ToggleGroupControl
													key={field_name}
													__nextHasNoMarginBottom={true}
													__next40pxDefaultSize={true}
													isBlock
													label={field_specs.label}
													onChange={(value) => setAttributes({[field_name]: value})}
													value={typeof attributes[field_name] !== 'undefined' ? attributes[field_name] : field_specs.default}
													className="epkb-block-ui-select-buttons-control"
												>
													{Object.entries(field_specs.options).map(([option_value, option_data]) => {
														return <ToggleGroupControlOptionIcon
															key={option_value}
															icon={<span className={'epkbfa' + ' ' + option_data.icon_class}></span>}
															label={option_data.label}
															value={option_value}
														/>
													})}
												</ToggleGroupControl>

											case 'select_buttons':
												return <ToggleGroupControl
													key={field_name}
													__nextHasNoMarginBottom={true}
													__next40pxDefaultSize={true}
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
													disabled={isDisabled}
													__nextHasNoMarginBottom={true}
													label={field_specs.label}
													checked={attributes[field_name] === 'on'}
													onChange={(value) => setAttributes({[field_name]: (value ? 'on' : 'off')})}
													className="epkb-block-ui-toggle-control"
												/>

											case 'custom_toggle':
												return <ToggleControl
													key={field_name}
													disabled={isDisabled}
													__nextHasNoMarginBottom={true}
													label={field_specs.label}
													checked={attributes[field_name] === field_specs.options['on']}
													onChange={(value) => setAttributes({[field_name]: (value ? field_specs.options['on'] : field_specs.options['off'])})}
													className="epkb-block-ui-toggle-control"
												/>

											case 'template_toggle':
												return <ToggleControl
													key={field_name}
													disabled={isDisabled}
													__nextHasNoMarginBottom={true}
													label={field_specs.label}
													checked={isTemplateToggleChecked}
													onChange={(value) => {

														// Update value in the block attributes
														setAttributes({[field_name]: (value ? 'on' : 'off')});

														// Toggle is 'ON' - set the KB template
														if ( value ) {
															editPost({ template: block_ui_config.settings.kb_block_page_template });
															return;
														}

														// Toggle is 'OFF' - set to the currently stored template or to the default one (if KB template is the stored template)
														editPost({ template: currentlyStoredTemplate === block_ui_config.settings.kb_block_page_template ? '' : currentlyStoredTemplate });
													}}
													className="epkb-block-ui-toggle-control"
												/>

											case 'range':
												return <RangeControl
													key={field_name}
													__nextHasNoMarginBottom={true}
													__next40pxDefaultSize={true}
													disabled={isDisabled}
													isShiftStepEnabled={true}
													shiftStep={1}
													min={field_specs.min}
													max={field_specs.max}
													label={field_specs.label}
													value={(attributes[field_name] === "" || Number.isNaN(parseInt(attributes[field_name]))) ? parseInt(field_specs.default) : parseInt(attributes[field_name])}
													onChange={(value) => setAttributes({[field_name]: (value === "" || Number.isNaN(parseInt(value))) ? 1 : parseInt(value)})}
													className="epkb-block-ui-range-control"
													help={<>
														{field_specs.description && (<span className="epkb-help-description">{field_specs.description}</span>)}
														{field_specs.help_text && (<span className="epkb-help-text">{field_specs.help_text}{" "}<a href={field_specs.help_link_url} target="_blank" rel="noopener noreferrer">{field_specs.help_link_text}</a></span>)}
													</>}
												/>

											case 'range_float':
												return <RangeControl
													key={field_name}
													__nextHasNoMarginBottom={true}
													__next40pxDefaultSize={true}
													disabled={isDisabled}
													isShiftStepEnabled={true}
													shiftStep={0.05}
													step={0.05}
													min={field_specs.min}
													max={field_specs.max}
													label={field_specs.label}
													value={(attributes[field_name] === "" || Number.isNaN(parseFloat(attributes[field_name]))) ? parseFloat(field_specs.default) : parseFloat(attributes[field_name])}
													onChange={(value) => setAttributes({[field_name]: (value === "" || Number.isNaN(parseFloat(value))) ? parseFloat(field_specs.default).toFixed(2) : parseFloat(value).toFixed(2)})}
													className="epkb-block-ui-range-control"
													help={<>
														{field_specs.description && (<span className="epkb-help-description">{field_specs.description}</span>)}
														{field_specs.help_text && (<span className="epkb-help-text">{field_specs.help_text}{" "}<a href={field_specs.help_link_url} target="_blank" rel="noopener noreferrer">{field_specs.help_link_text}</a></span>)}
													</>}
												/>

											case 'custom_dropdown':
												const selectedKey = attributes[field_name] || field_specs.default;
												const selectedOption = Object.values(field_specs.options).find(
													(option) => option.key === selectedKey
												);
												return <CustomSelectControl
													key={field_name}
													disabled={isDisabled}
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
													disabled={isDisabled}
													__next40pxDefaultSize={true}
													label={field_specs.label}
													value={{
														key: attributes[field_name] || field_specs.default,
														name: field_specs.options[attributes[field_name] || field_specs.default],
														style: {},
													}}
													onChange={(value) => {
														setAttributes({[field_name]: value.selectedItem.key});

														// EL.AY sidebar components - deselect the same component if it is selected in another sidebar
														/*switch (field_name) {
															case 'nav_sidebar_left':
															case 'kb_sidebar_left':
															case 'toc_left':
																setAttributes({[field_name.replace('_left', '_right')]: '0'});
																break;
															case 'nav_sidebar_right':
															case 'kb_sidebar_right':
															case 'toc_right':
																setAttributes({[field_name.replace('_right', '_left')]: '0'});
																break;
														}*/
													}}
													options={Object.entries(field_specs.options).map(([option_key, options_name]) => {
														return {
															key: option_key,
															name: options_name,
															style: {},
														};
													})}
													className="epkb-block-ui-dropdown-control"
												/>

											case 'presets_dropdown':

												// Get current selected preset name from temporary storage
												const selectedPresetKey = epkbBlockStorage[field_name] ? epkbBlockStorage[field_name] : field_specs.default;

												return <CustomSelectControl
													key={field_name}
													disabled={isDisabled}
													__next40pxDefaultSize={true}
													label={field_specs.label}
													value={{
														key: selectedPresetKey,
														name: field_specs.presets[selectedPresetKey].label,
														style: {},
													}}
													onChange={(value) => {

														// If user selected 'current' in presets dropdown, then do nothing
														if (value.selectedItem.key === 'current') {
															return;
														}

														// Update preset name in temporary storage (the preset value is not storing in the block attributes and only needed for editor preview)
														setEpkbBlockStorage((prevState) => ({
															...prevState,
															[field_name]: value.selectedItem.key,
														}));

														// Apply preset settings for editor preview
														Object.entries(field_specs.presets[value.selectedItem.key].settings).forEach(([setting_name, setting_value]) => {
															setAttributes({
																[setting_name]: setting_value
															});
														});

														// Set preset name to make the non-block KB code working correctly (icons change for editor preview)
														setAttributes({
															theme_presets: value.selectedItem.key,
															theme_name: value.selectedItem.key,
														});
													}}
													options={Object.entries(field_specs.presets).map(([preset_key, preset_config]) => {
														return {
															key: preset_key,
															name: preset_config.label,
															style: {},
														};
													})}
													className="epkb-block-ui-presets-dropdown-control"
												/>

											case 'checkbox_multi_select':
												return <BaseControl
													key={field_name}
													label={field_specs.label}
													__nextHasNoMarginBottom={true}
													className="epkb-block-ui-checkbox-multi-select-control">{
													Object.entries(field_specs.options).map(([option_key, option_value]) => {
														return <CheckboxControl
															key={field_name + '_' + option_key}
															disabled={isDisabled}
															__nextHasNoMarginBottom={true}
															checked={attributes[field_name].indexOf(parseInt(option_key)) !== -1}
															label={option_value}
															onChange={(isChecked) => {
																const intValue = parseInt(option_key);
																const newValues = isChecked ? [...attributes[field_name], intValue] : attributes[field_name].filter(item => item !== intValue);
																setAttributes({[field_name]: newValues});
															}}
														/>
													})
												}</BaseControl>

											case 'box_control':
												const sideValues = {[field_specs.side]: attributes[field_name] === "" || Number.isNaN(parseInt(attributes[field_name])) ? field_specs.default : parseInt(attributes[field_name])};
												return <BoxControl
													key={field_name}
													__next40pxDefaultSize={true}
													label={field_specs.label}
													sides={[field_specs.side]}
													inputProps={{
														min: ensure_number(field_specs.min),
														max: ensure_number(field_specs.max),
													}}
													values={sideValues}
													onChange={(values) => {
														const newValue = values[field_specs.side] || values;
														setAttributes({
															[field_name]: newValue === "" || Number.isNaN(parseInt(newValue)) 
																? field_specs.default 
																: parseInt(newValue)
														});
													}}
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
																	const newValue = typeof value === 'object' ? value.value : value;
																	setAttributes({
																		[setting_name]: newValue === "" || Number.isNaN(parseInt(newValue)) 
																			? field_specs.combined_settings[setting_name].default 
																			: parseInt(newValue)
																	});
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
															__nextHasNoMarginBottom={true}
															__next40pxDefaultSize={true}
															fontFamilies={Object.entries(epkb_block_editor_vars.font_families).map(([option_index, options_value]) => {
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
															__next40pxDefaultSize={true}
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
												const linkUrl = field_specs.link_text.length > 0 ? field_specs.link_url.replaceAll('epkb_post_type_1', 'epkb_post_type_' + attributes['kb_id']) : '';
												return <div key={field_name} className="epkb-block-ui-section-description">
													<span>{field_specs.description}</span>
													{field_specs.link_text.length > 0 ? (<a href={linkUrl} target="_blank" rel="noopener noreferrer">{field_specs.link_text}</a>) : null}
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
				epkbBlockStorage={epkbBlockStorage}	// ensure the preview is re-rendered when the local storage is updated
				urlQueryArgs={{is_editor_preview: 1}}
				httpMethod="POST"
			/>
		</div>
	);
}
