/**
 * Advanced Gutenberg Custom Styles Manager
 */
class AdvGbCustomStyles {
    constructor($) {
        this.$ = $;
        this.activeTab = localStorage.getItem('advgb_active_tab') || 'style-editor';
        this.editor = null;
        this.styleId = null;
        this.isProActive = window.advgbCustomStyles?.isProActive || false;

        this.init();
    }

    init() {
        this.initializeUI();
        this.initializeEventHandlers();
        this.initializeEditor();
        this.loadInitialStyle();
    }

    // Core initialization methods
    initializeUI() {
        this.setActiveTab();
        this.initCustomStyleMenu();
        this.initColorPickers();
        this.initFieldsets();
        this.setupLivePreview();
        this.enableAddNewButton();
    }

    initializeEventHandlers() {
        this.bindTabEvents();
        this.bindFormEvents();
        this.bindPreviewEvents();
    }


    enableAddNewButton() {
        this.$('.advgb-customstyles-new')
            .prop('disabled', false)
            .removeAttr('disabled');
    }

    // UI Management
    setActiveTab() {
        this.$('.advgb-main-tabs .advgb-tab').removeClass('active');
        this.$(`.advgb-main-tabs .advgb-tab[data-tab="${this.activeTab}"]`).addClass('active');

        this.$('.advgb-tab-content.main-tab-content').hide();
        this.$(`[data-tab-content="${this.activeTab}"]`).show();
    }

    initFieldsets() {
        const self = this;

        this.$('.advgb-fieldset').each(function () {
            const $fieldset = self.$(this);
            const $content = $fieldset.find('.advgb-fieldset-content');
            const $legend = $fieldset.find('legend');

            if (!$legend.find('.dashicons').length) {
                $legend.prepend('<span class="dashicons dashicons-arrow-down"></span>');
            }

            $fieldset.removeClass('collapsed');
            $content.show();
        });

        this.$('.advgb-fieldset legend').on('click', function (e) {
            e.preventDefault();
            self.toggleFieldset(self.$(this).closest('.advgb-fieldset'));
        });

        this.initPreviewFieldset();
    }

    toggleFieldset($fieldset) {
        const $content = $fieldset.find('.advgb-fieldset-content');
        const $arrow = $fieldset.find('.dashicons');

        $content.slideToggle(200, () => {
            $fieldset.toggleClass('collapsed');

            const isCollapsed = $fieldset.hasClass('collapsed');
            $arrow.toggleClass('dashicons-arrow-down', !isCollapsed)
                .toggleClass('dashicons-arrow-right', isCollapsed);

            if ($fieldset.hasClass('advgb-preview-fieldset') && !isCollapsed) {
                this.updatePreviewContent();
            }
        });
    }

    initPreviewFieldset() {
        const $previewFieldset = this.$('.advgb-preview-fieldset');
        $previewFieldset.addClass('collapsed initializing');
        $previewFieldset.find('.advgb-fieldset-content').hide();
        $previewFieldset.find('.dashicons')
            .removeClass('dashicons-arrow-down')
            .addClass('dashicons-arrow-right');
    }

    initColorPickers() {
        this.$('.minicolors').each((index, element) => {
            const $element = this.$(element);
            if (!$element.data('minicolors-initialized')) {
                $element.minicolors({
                    theme: 'bootstrap',
                    change: (value, opacity) => this.updateCSSFromUI()
                });
                $element.data('minicolors-initialized', true);
            }
        });
    }

    // Event Handlers
    bindTabEvents() {
        const self = this;

        this.$('.advgb-main-tabs .advgb-tabs-panel .advgb-tab').on('click', function (e) {
            e.preventDefault();
            const newTab = self.$(this).data('tab');
            self.switchMainTab(newTab);
        });

        this.$('.advgb-sub-tabs .advgb-tabs-panel .advgb-tab').on('click', function (e) {
            e.preventDefault();
            const tabId = self.$(this).data('tab');
            const tabWrap = self.$(this).closest('.advgb-tabs-panel');

            tabWrap.find('.advgb-tab').removeClass('active');
            self.$('.advgb-tab-content.sub-tab-content').hide();

            self.$(this).addClass('active');
            self.$(`[data-tab-content="${tabId}"]`).show();
        });
    }

    switchMainTab(newTab) {
        // Handle data conversion between tabs
        if (newTab === 'custom-css' && this.activeTab === 'style-editor') {
            this.updateCSSFromUI();
        } else if (newTab === 'style-editor' && this.activeTab === 'custom-css') {
            this.handleCustomCSSChange();
        }

        this.activeTab = newTab;
        localStorage.setItem('advgb_active_tab', this.activeTab);
        this.setActiveTab();

        // Handle preview updates for tab switching
        this.handleTabSwitchForPreview();

        // Refresh editor when needed
        if (this.activeTab === 'custom-css' && this.editor) {
            this.editor.refresh();
        }
    }

    bindFormEvents() {
        const self = this;

        this.$('.style-input').on('input change', function () {
            if (self.activeTab === 'style-editor') {
                self.handleStyleInputChange();
            }
        });

        this.$('#advgb-customstyles-classname').on('input', () => {
            this.updatePreviewContent();
        });

        this.$('#save_custom_styles').on('click', (e) => {
            e.preventDefault();
            this.saveCustomStyleChanges();
        });
    }

    bindPreviewEvents() {
        let cssChangeWait;
        this.$('#advgb-customstyles-css').on('input propertychange', () => {
            clearTimeout(cssChangeWait);
            cssChangeWait = setTimeout(() => {
                this.parseCustomStyleCss();
            }, 500);
        });
    }

    // Editor Management
    initializeEditor() {
        const cssArea = document.getElementById('advgb-customstyles-css');

        if (cssArea) {
            this.editor = CodeMirror.fromTextArea(cssArea, {
                mode: 'css',
                lineNumbers: true,
                extraKeys: { "Ctrl-Space": "autocomplete" }
            });

            this.setupEditorEvents();
        }
    }

    setupEditorEvents() {
        this.editor.on("change", () => {
            if (this.activeTab === 'custom-css') {
                this.handleCustomCSSChange();
            }
            this.updateSimplePreview();

            if (!this.$('.advgb-preview-fieldset').hasClass('collapsed')) {
                this.updatePreviewContent();
            }
        });

        this.editor.on("blur", () => {
            this.editor.save();
            this.$('#advgb-customstyles-css').trigger('propertychange');
        });
    }

    // Data Management
    buildStructuredDataFromUI() {
        const cssArray = { base: {}, states: {}, nested: {} };
        const validElements = this.getValidElements();
        const validStates = this.getValidStates();

        this.$('.style-input').each((index, element) => {
            const $input = this.$(element);
            this.processStyleInput($input, cssArray, validElements, validStates);
        });

        return this.cleanupCSSArray(cssArray);
    }

    processStyleInput($input, cssArray, validElements, validStates) {
        const fullProperty = $input.data('css-property');
        let value = $input.val();

        // Add validation for required data attributes
        if (!fullProperty || !value || value === '' || fullProperty.includes('promo')) {
            return;
        }

        if ($input.data('unit') && !isNaN(value)) {
            value += $input.data('unit');
        }

        const propertyData = this.parseProperty(fullProperty, value, validElements, validStates);

        if (propertyData && propertyData.type && propertyData.property) {
            this.assignPropertyToCSSArray(propertyData, cssArray, value);
        }
    }

    parseProperty(fullProperty, value, validElements, validStates) {
        const parts = fullProperty.split('-');
        return this.detectPropertyPattern(parts, validElements, validStates);
    }

    detectPropertyPattern(parts, validElements, validStates) {
        const baseCssProperties = [
            'color', 'background-color', 'background', 'padding', 'padding-top', 'padding-right',
            'padding-bottom', 'padding-left', 'margin', 'margin-top', 'margin-right', 'margin-bottom',
            'margin-left', 'border', 'border-top', 'border-right', 'border-bottom', 'border-left',
            'border-width', 'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width',
            'border-color', 'border-top-color', 'border-right-color', 'border-bottom-color', 'border-left-color',
            'border-style', 'border-top-style', 'border-right-style', 'border-bottom-style', 'border-left-style',
            'border-radius', 'border-top-left-radius', 'border-top-right-radius', 'border-bottom-right-radius', 'border-bottom-left-radius',
            'font-family', 'font-size', 'font-weight', 'font-style', 'line-height', 'letter-spacing',
            'text-align', 'text-decoration', 'text-transform', 'text-shadow',
            'display', 'position', 'width', 'height', 'max-width', 'min-width', 'max-height', 'min-height',
            'float', 'clear', 'overflow', 'z-index', 'opacity', 'box-shadow', 'transform', 'transition'
        ];

        const fullProperty = parts.join('-');

        // Pattern 1: Base CSS property
        if (baseCssProperties.includes(fullProperty)) {
            return { type: 'base', property: fullProperty };
        }

        // Pattern 2: Element property (p-color, h1-font-size, section-background-color, etc.)
        if (validElements.includes(parts[0]) && parts.length >= 2) {
            const parsed = this.parsePropertyWithState(fullProperty, validStates);
            if (parsed.isState) {
                return {
                    type: 'nested-state',
                    element: parsed.element || parts[0],
                    state: parsed.state || ':hover',
                    property: parsed.property
                };
            } else {
                return {
                    type: 'nested',
                    element: parsed.element,
                    property: parsed.property
                };
            }
        }

        // Pattern 3: Element with state (a-hover-color, img-hover-border-color)
        if (parts.length >= 3 && validElements.includes(parts[0]) && validStates.includes(parts[1])) {
            return {
                type: 'nested-state',
                element: parts[0],
                state: ':' + parts[1],
                property: parts.slice(2).join('-')
            };
        }

        // Pattern 4: Complex nested (h1-a-color, div-p-color)
        if (parts.length >= 3 && validElements.includes(parts[0]) && validElements.includes(parts[1])) {
            return {
                type: 'nested',
                element: parts[0] + ' ' + parts[1],
                property: parts.slice(2).join('-')
            };
        }

        // Pattern 5: Complex nested with state (h1-a-hover-color)
        if (parts.length >= 4 && validElements.includes(parts[0]) && validElements.includes(parts[1]) && validStates.includes(parts[2])) {
            return {
                type: 'nested-state',
                element: parts[0] + ' ' + parts[1],
                state: ':' + parts[2],
                property: parts.slice(3).join('-')
            };
        }

        // Pattern 6: Base state (hover-color, focus-background)
        if (validStates.includes(parts[0]) && parts.length === 2) {
            return {
                type: 'state',
                state: ':' + parts[0],
                property: parts[1]
            };
        }

        // Default: treat as base property
        return { type: 'base', property: fullProperty };
    }

    parsePropertyWithState(fullProperty, validStates) {
        const parts = fullProperty.split('-');

        // Check for state at any position in complex properties
        for (let i = 0; i < parts.length - 1; i++) {
            if (validStates.includes(parts[i])) {
                // Found a state, split the property around it
                const beforeState = parts.slice(0, i).join('-');
                const state = ':' + parts[i];
                const afterState = parts.slice(i + 1).join('-');

                return {
                    element: beforeState || parts[0],
                    state: state,
                    property: afterState,
                    isState: true
                };
            }
        }

        // No state found, this is a regular nested element property
        const element = parts[0];
        const property = parts.slice(1).join('-');

        return {
            element: element,
            property: property,
            isState: false
        };
    }

    assignPropertyToCSSArray(propertyData, cssArray, value) {
        const { type, property, element, state } = propertyData;

        if (!property || !value) {
            return;
        }

        switch (type) {
            case 'base':
                cssArray.base[property] = value;
                break;
            case 'state':
                if (!state) break; // Skip if state is undefined
                if (!cssArray.states[state]) cssArray.states[state] = {};
                cssArray.states[state][property] = value;
                break;
            case 'nested':
                if (!element) break; // Skip if element is undefined
                if (!cssArray.nested[element]) cssArray.nested[element] = {};
                cssArray.nested[element][property] = value;
                break;
            case 'nested-state':
                if (!element || !state) break; // Skip if element or state is undefined
                if (!cssArray.nested[element]) cssArray.nested[element] = { states: {} };
                if (!cssArray.nested[element].states) cssArray.nested[element].states = {};
                if (!cssArray.nested[element].states[state]) cssArray.nested[element].states[state] = {};
                cssArray.nested[element].states[state][property] = value;
                break;
        }
    }

    cleanupCSSArray(cssArray) {
        for (const element in cssArray.nested) {
            if (Object.keys(cssArray.nested[element]).length === 0) {
                delete cssArray.nested[element];
            } else if (cssArray.nested[element].states && Object.keys(cssArray.nested[element].states).length === 0) {
                delete cssArray.nested[element].states;
            }
        }
        if (Object.keys(cssArray.states).length === 0) delete cssArray.states;
        if (Object.keys(cssArray.nested).length === 0) delete cssArray.nested;

        return cssArray;
    }

    // Input Change Handlers
    handleStyleInputChange() {
        this.updateCSSFromUI();

        this.updateSimplePreview();

        if (!this.$('.advgb-preview-fieldset').hasClass('collapsed')) {
            this.updatePreviewContent();
        }
    }

    updateCSSFromUI() {
        if (this.activeTab !== 'style-editor') {
            return;
        }

        const cssArray = this.buildStructuredDataFromUI();
        const scssString = this.arrayToSCSS(cssArray);

        if (this.editor) {
            this.editor.setValue('{\n' + scssString + '\n}');
            this.editor.refresh();

            // Force update of simple preview after UI changes
            setTimeout(() => {
                this.updateSimplePreview();
            }, 50);
        }
    }

    arrayToSCSS(cssArray) {
        let scss = '';

        // Base styles
        if (cssArray.base && Object.keys(cssArray.base).length > 0) {
            for (const prop in cssArray.base) {
                if (cssArray.base[prop] && cssArray.base[prop] !== '') {
                    scss += '    ' + prop + ': ' + cssArray.base[prop] + ';\n';
                }
            }
        }

        // Base states
        if (cssArray.states) {
            for (const state in cssArray.states) {
                if (Object.keys(cssArray.states[state]).length > 0) {
                    scss += '\n    &' + state + ' {\n';
                    for (const stateProp in cssArray.states[state]) {
                        if (cssArray.states[state][stateProp] && cssArray.states[state][stateProp] !== '') {
                            scss += '        ' + stateProp + ': ' + cssArray.states[state][stateProp] + ';\n';
                        }
                    }
                    scss += '    }\n';
                }
            }
        }

        // Nested elements
        if (cssArray.nested) {
            for (const selector in cssArray.nested) {
                const elementRules = cssArray.nested[selector];
                let hasRegularRules = false;
                let hasStateRules = elementRules.states && Object.keys(elementRules.states).length > 0;
                let regularRules = '';

                // Collect regular rules (non-state)
                for (const prop in elementRules) {
                    if (prop !== 'states' && elementRules[prop] && elementRules[prop] !== '') {
                        hasRegularRules = true;
                        regularRules += '        ' + prop + ': ' + elementRules[prop] + ';\n';
                    }
                }

                // Only create the nested block if we have rules
                if (hasRegularRules || hasStateRules) {
                    scss += '\n    &' + selector + ' {\n';

                    // Add regular rules
                    if (hasRegularRules) {
                        scss += regularRules;
                    }

                    // Add nested element states
                    if (hasStateRules) {
                        for (const nestedState in elementRules.states) {
                            if (Object.keys(elementRules.states[nestedState]).length > 0) {
                                scss += '\n        &' + nestedState + ' {\n';
                                for (const nestedProp in elementRules.states[nestedState]) {
                                    if (elementRules.states[nestedState][nestedProp] && elementRules.states[nestedState][nestedProp] !== '') {
                                        scss += '            ' + nestedProp + ': ' + elementRules.states[nestedState][nestedProp] + ';\n';
                                    }
                                }
                                scss += '        }\n';
                            }
                        }
                    }

                    scss += '    }\n';
                }
            }
        }

        return scss;
    }

    handleCustomCSSChange() {
        if (this.activeTab !== 'custom-css') return;

        if (!this.editor?.getValue) return;

        const cssContent = this.editor.getValue();

        if (!cssContent || cssContent.trim() === '') {
            return;
        }

        try {
            // Parse SCSS to structured data
            const parsedData = this.parseSCSSToArray(cssContent);

            // Update UI fields with parsed data - DON'T clear fields first
            this.populateUIFieldsFromParsedData(parsedData);

            // Update preview
            this.updateStylePreview();

        } catch (error) {
            console.error('Error parsing SCSS in handleCustomCSSChange:', error);
        }
    }

    parseSCSSToArray(scssContent) {
        const result = { base: {}, states: {}, nested: {} };

        if (!scssContent) return result;

        try {
            let innerContent = scssContent.trim();
            if (innerContent.startsWith('{') && innerContent.endsWith('}')) {
                innerContent = innerContent.substring(1, innerContent.length - 1).trim();
            }

            const lines = innerContent.split('\n');
            let currentBlock = 'base'; // 'base', 'state', 'nested', 'nested-state'
            let currentSelector = '';
            let currentStateSelector = '';
            const braceStack = [];

            // Define valid HTML elements that can be nested
            const validNestedElements = ['a', 'p', 'span', 'blockquote', 'ul', 'li', 'ol', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'img', 'video', 'div', 'section', 'button', 'input'];

            for (let i = 0; i < lines.length; i++) {
                let line = lines[i].trim();
                if (!line) continue;

                // Handle block starts
                if (line.endsWith('{')) {
                    const selector = line.replace('{', '').trim();

                    if (selector.startsWith('&')) {
                        const cleanSelector = selector.substring(1).trim();

                        if (cleanSelector.startsWith(':')) {
                            // State selector
                            if (currentBlock === 'nested') {
                                braceStack.push('nested-state');
                                currentBlock = 'nested-state';
                            } else {
                                braceStack.push('state');
                                currentBlock = 'state';
                            }
                            currentStateSelector = cleanSelector;
                        } else {
                            // Nested element selector
                            braceStack.push('nested');
                            currentBlock = 'nested';
                            currentSelector = cleanSelector;
                            currentStateSelector = '';
                        }
                    }
                    continue;
                }

                // Handle block ends
                if (line === '}') {
                    if (braceStack.length > 0) {
                        const previousBlock = braceStack.pop();
                        if (braceStack.length === 0) {
                            currentBlock = 'base';
                        } else {
                            currentBlock = braceStack[braceStack.length - 1];
                        }

                        if (previousBlock === 'nested') {
                            currentSelector = '';
                        }
                        if (previousBlock === 'state' || previousBlock === 'nested-state') {
                            currentStateSelector = '';
                        }
                    }
                    continue;
                }

                // Parse CSS declarations
                if (line.includes(':')) {
                    const parts = line.split(':');
                    const property = parts[0].trim();
                    const value = parts.slice(1).join(':').replace(';', '').trim();

                    // Check if this is a nested element property (like "a-color")
                    // Only match if it starts with a valid HTML element name
                    const nestedMatch = property.match(/^([a-z]+)-([a-z].+)$/);

                    if (nestedMatch && currentBlock === 'nested') {
                        const nestedElement = nestedMatch[1];
                        const nestedProperty = nestedMatch[2];

                        // Only treat as nested element if it's a valid HTML element
                        if (validNestedElements.includes(nestedElement)) {
                            const fullSelector = currentSelector + ' ' + nestedElement;

                            if (!result.nested[fullSelector]) {
                                result.nested[fullSelector] = {};
                            }
                            result.nested[fullSelector][nestedProperty] = value;
                        } else {
                            // It's a regular CSS property with hyphens (like background-color)
                            if (!result.nested[currentSelector]) {
                                result.nested[currentSelector] = {};
                            }
                            result.nested[currentSelector][property] = value;
                        }
                    } else {
                        // Regular property assignment
                        switch (currentBlock) {
                            case 'base':
                                result.base[property] = value;
                                break;
                            case 'state':
                                if (!result.states[currentStateSelector]) {
                                    result.states[currentStateSelector] = {};
                                }
                                result.states[currentStateSelector][property] = value;
                                break;
                            case 'nested':
                                if (!result.nested[currentSelector]) {
                                    result.nested[currentSelector] = {};
                                }
                                result.nested[currentSelector][property] = value;
                                break;
                            case 'nested-state':
                                if (!result.nested[currentSelector]) {
                                    result.nested[currentSelector] = { states: {} };
                                }
                                if (!result.nested[currentSelector].states) {
                                    result.nested[currentSelector].states = {};
                                }
                                if (!result.nested[currentSelector].states[currentStateSelector]) {
                                    result.nested[currentSelector].states[currentStateSelector] = {};
                                }
                                result.nested[currentSelector].states[currentStateSelector][property] = value;
                                break;
                        }
                    }
                }
            }

            // Clean up empty objects
            if (Object.keys(result.states).length === 0) delete result.states;
            if (Object.keys(result.nested).length === 0) delete result.nested;

            return result;

        } catch (error) {
            console.error('Error parsing SCSS:', error);
            return { base: {}, states: {}, nested: {} };
        }
    }

    populateUIFieldsFromParsedData(parsedData) {

        if (parsedData.base) {
            for (const prop in parsedData.base) {
                const $input = this.$('[data-css-property="' + prop + '"]');
                if ($input.length && parsedData.base[prop]) {
                    this.setInputValue($input, parsedData.base[prop]);
                }
            }
        }

        if (parsedData.states) {
            for (const state in parsedData.states) {
                const stateName = state.replace(':', '');
                for (const prop in parsedData.states[state]) {
                    const stateProperty = stateName + '-' + prop;
                    const $input = this.$('[data-css-property="' + stateProperty + '"]');
                    if ($input.length && parsedData.states[state][prop]) {
                        this.setInputValue($input, parsedData.states[state][prop]);
                    }
                }
            }
        }

        if (parsedData.nested) {
            for (const selector in parsedData.nested) {
                const elementRules = parsedData.nested[selector];

                // Handle regular nested properties
                for (const prop in elementRules) {
                    if (prop !== 'states' && elementRules[prop]) {
                        let elementProperty = selector + '-' + prop;
                        if (selector.includes(' ')) {
                            elementProperty = selector.replace(/ /g, '-') + '-' + prop;
                        }
                        const $input = this.$('[data-css-property="' + elementProperty + '"]');
                        if ($input.length) {
                            this.setInputValue($input, elementRules[prop]);
                        }
                    }
                }

                // Handle nested states
                if (elementRules.states) {
                    for (const state in elementRules.states) {
                        const stateName = state.replace(':', '');
                        for (const prop in elementRules.states[state]) {
                            let stateProperty = selector + '-' + stateName + '-' + prop;
                            if (selector.includes(' ')) {
                                stateProperty = selector.replace(/ /g, '-') + '-' + stateName + '-' + prop;
                            }
                            const $input = this.$('[data-css-property="' + stateProperty + '"]');
                            if ($input.length && elementRules.states[state][prop]) {
                                this.setInputValue($input, elementRules.states[state][prop]);
                            }
                        }
                    }
                }
            }
        }
    }

    setInputValue($input, value) {
        const config = $input.data('field-config');

        if (!value) return;

        if (config && config.unit && value.endsWith(config.unit)) {
            value = value.replace(config.unit, '');
        }

        if (config && config.type === 'color') {
            if ($input.hasClass('minicolors')) {
                $input.minicolors('value', value);
            } else {
                $input.val(value);
            }
        } else if (config && config.type === 'number') {
            const numVal = value.replace(/[^0-9.]/g, '');
            $input.val(numVal);
        } else {
            $input.val(value);
        }
    }

    // Preview System
    setupLivePreview() {
        this.updateSimplePreview();

        if (!this.$('.advgb-preview-fieldset').hasClass('collapsed')) {
            this.updatePreviewContent();
        }
    }

    updateSimplePreview() {
        if (!this.editor?.getValue) return;

        const cssContent = this.editor.getValue();

        const finalCSS = this.convertSCSSToCSS(cssContent, false);

        this.$('.advgb-simple-preview').addClass('advgb-preview-loading');

        setTimeout(() => {
            this.$('#advgb-simple-preview-temp-styles').remove();

            if (finalCSS && finalCSS.trim() !== '') {
                const styleElement = this.$('<style id="advgb-simple-preview-temp-styles"></style>');
                styleElement.text(finalCSS);
                this.$('head').append(styleElement);
            }

            this.$('.advgb-simple-preview').removeClass('advgb-preview-loading');
        }, 100);
    }

    updatePreviewContent() {
        const className = this.$('#advgb-customstyles-classname').val().trim() || 'custom-style';
        const previewTarget = this.$('#advgb-preview-target');
        const cssData = this.buildStructuredDataFromUI();

        previewTarget.empty()
            .removeAttr('class')
            .addClass(`advgb-customstyles-target ${className}`)
            .html(this.buildPreviewContent(cssData));

        this.applyStylesToPreview();
    }

    buildPreviewContent(cssData) {
        const hasNestedStyles = cssData.nested && Object.keys(cssData.nested).length > 0;

        if (!hasNestedStyles) {
            return this.getComprehensivePreview();
        }

        return this.buildTargetedPreview(cssData.nested);
    }

    buildTargetedPreview(nestedStyles) {
        let content = '<p>This is a paragraph with <strong>bold text</strong> and <em>italic text</em>.</p>';

        Object.keys(nestedStyles).forEach(selector => {
            content += this.getPreviewElementForSelector(selector);
        });

        return content;
    }

    getPreviewElementForSelector(selector) {
        const elements = {
            h1: '<h1>This is a H1 heading</h1>',
            h2: '<h2>This is a H2 heading</h2>',
            h3: '<h3>This is a H3 heading</h3>',
            a: '<p>This paragraph contains a <a href="#" class="preview-link">styled link</a>.</p>',
            blockquote: '<blockquote>This is a styled blockquote.</blockquote>',
            ul: '<ul><li>First list item</li><li>Second list item</li></ul>',
            button: '<div class="preview-buttons"><button type="button" class="preview-button">Styled Button</button></div>',
            img: '<div class="preview-image-container"><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPlByZXZpZXcgSW1hZ2U8L3RleHQ+PC9zdmc+" alt="Preview" class="preview-image" /></div>'
        };

        return elements[selector] || '';
    }

    getComprehensivePreview() {
        return `
            <h1>Main Heading (H1)</h1>
            <h2>Sub Heading (H2)</h2>
            <p>This is a regular paragraph with <strong>bold text</strong>, <em>italic text</em>, and a <a href="#" class="preview-link">link</a>.</p>
            <blockquote>This is a blockquote element.</blockquote>
            <ul>
                <li>Unordered list item one</li>
                <li>Unordered list item two</li>
            </ul>
            <div class="preview-div">Styled div container</div>
            <div class="preview-buttons">
                <button type="button" class="preview-button">Primary Button</button>
            </div>
            <div class="preview-form">
                <input type="text" class="preview-input" placeholder="Text input field" />
            </div>
        `;
    }

    applyStylesToPreview() {
        if (!this.editor?.getValue) return;

        const cssContent = this.editor.getValue();

        const finalCSS = this.convertSCSSToCSS(cssContent, true);
        const className = this.$('#advgb-customstyles-classname').val().trim() || 'custom-style';

        this.$('#advgb-preview-styles').remove();

        if (finalCSS && finalCSS.trim() !== '') {
            const finalCSSWithClass = finalCSS.replace(/\.advgb-customstyles-target/g, '.' + className);
            this.$('<style id="advgb-preview-styles"></style>')
                .text(finalCSSWithClass)
                .appendTo('head');
        }
    }

    applyCSSToElement(css, $element) {
        this.$('#advgb-simple-preview-temp-styles').remove();

        if (css && css.trim() !== '') {
            const styleElement = this.$('<style id="advgb-simple-preview-temp-styles"></style>');
            styleElement.text(css);
            this.$('head').append(styleElement);
        }
    }

    // Handle tab switching for simple preview
    handleTabSwitchForPreview() {
        if (this.activeTab === 'style-editor') {
            this.updateSimplePreview();
        }

        if (this.activeTab === 'custom-css') {
            // Small delay to ensure editor is ready
            setTimeout(() => {
                this.updateSimplePreview();
            }, 100);
        }
    }

    // Manual SCSS to CSS converter for Custom CSS tab
    manualSCSSToCSS(scssContent, className) {
        const wrappedContent = '{\n' + scssContent + '\n}';
        const css = this.convertSCSSToCSS(wrappedContent, true);

        // Replace the preview class with the actual class name
        return css.replace(/\.advgb-customstyles-target/g, '.' + className);
    }

    // CSS/SCSS Conversion
    convertSCSSToCSS(scssContent, includeNested = false) {
        try {
            let css = '';
            const parsedData = this.parseSCSSToArray(scssContent);

            // Base styles
            if (parsedData.base && Object.keys(parsedData.base).length > 0) {
                css += '.advgb-customstyles-target {\n';
                for (const prop in parsedData.base) {
                    if (parsedData.base[prop]) {
                        css += '    ' + prop + ': ' + parsedData.base[prop] + ';\n';
                    }
                }
                css += '}\n';
            }

            // Base states
            if (parsedData.states) {
                for (const state in parsedData.states) {
                    if (state && state.startsWith(':') && Object.keys(parsedData.states[state]).length > 0) {
                        css += '.advgb-customstyles-target' + state + ' {\n';
                        for (const prop in parsedData.states[state]) {
                            if (parsedData.states[state][prop]) {
                                css += '    ' + prop + ': ' + parsedData.states[state][prop] + ';\n';
                            }
                        }
                        css += '}\n';
                    }
                }
            }

            // Nested elements
            if (includeNested && parsedData.nested) {
                for (const selector in parsedData.nested) {
                    if (!selector) continue;

                    const elementRules = parsedData.nested[selector];
                    const fullSelector = '.advgb-customstyles-target ' + selector;

                    // Regular nested styles
                    let regularRules = '';
                    for (const prop in elementRules) {
                        if (prop !== 'states' && elementRules[prop]) {
                            regularRules += '    ' + prop + ': ' + elementRules[prop] + ';\n';
                        }
                    }

                    if (regularRules) {
                        css += fullSelector + ' {\n' + regularRules + '}\n';
                    }

                    // Nested element states
                    if (elementRules.states) {
                        for (const state in elementRules.states) {
                            if (state && state.startsWith(':') && Object.keys(elementRules.states[state]).length > 0) {
                                css += fullSelector + state + ' {\n';
                                for (const prop in elementRules.states[state]) {
                                    if (elementRules.states[state][prop]) {
                                        css += '    ' + prop + ': ' + elementRules.states[state][prop] + ';\n';
                                    }
                                }
                                css += '}\n';
                            }
                        }
                    }
                }
            }

            return css;

        } catch (error) {
            console.error('Error converting SCSS to CSS:', error);
            return '.advgb-customstyles-target {}';
        }
    }

    parseCustomStyleCss() {
        const previewTarget = this.$("#advgb-customstyles-preview .advgb-customstyles-target");
        const cssContent = this.editor.getValue();

        // For the main preview, use base styles only
        const finalCSS = this.convertSCSSToCSS(cssContent, false);

        previewTarget.removeAttr('style');

        // Apply base styles directly to the preview element
        const declarations = finalCSS.split(';').filter(Boolean);

        declarations.forEach(decl => {
            const parts = decl.split(':').map(part => part.trim());
            if (parts.length >= 2) {
                const property = parts[0];
                const value = parts.slice(1).join(':').trim();
                if (property && value && property.includes('advgb-customstyles-target')) {
                    // Extract just the property:value pairs from the CSS rule
                    const cleanDecl = decl.replace(/\.advgb-customstyles-target\s*\{?\s*/, '').replace(/\s*\}/, '');
                    const cleanParts = cleanDecl.split(':').map(part => part.trim());
                    if (cleanParts.length >= 2) {
                        const cleanProp = cleanParts[0];
                        const cleanValue = cleanParts.slice(1).join(':').trim();
                        if (cleanProp && cleanValue) {
                            previewTarget.css(cleanProp, cleanValue);
                        }
                    }
                }
            }
        });
    }

    updateStylePreview() {
        if (!this.editor?.getValue) return;

        const cssContent = this.editor.getValue();
        const finalCSS = this.convertSCSSToCSS(cssContent);
        const previewTarget = this.$(".advgb-simple-preview .advgb-customstyles-target");

        previewTarget.removeAttr('style');
        this.applyCSSToElement(finalCSS, previewTarget);
    }

    // Custom Style Menu Management
    initCustomStyleMenu() {
        this.initCustomStyleNew();
        this.initCustomStyleDelete();
        this.initCustomStyleCopy();
        this.initTableLinks();
    }

    initCustomStyleNew() {
        const self = this;
        this.$('.advgb-customstyles-new').off('click').on('click', function (e) {
            e.preventDefault();
            const that = this;
            const nonce_val = self.$('#advgb_cstyles_nonce_field').val();

            self.ajaxRequest({
                action: 'advgb_custom_styles_ajax',
                task: 'new',
                nonce: nonce_val
            }, {
                beforeSend: () => {
                    self.$('#customstyles-tab').append('<div class="advgb-overlay-box"></div>');
                },
                success: (res) => {
                    const newItem = `
                        <li class="advgb-customstyles-items" data-id-customstyle="${res.id}">
                            <a><i class="title-icon"></i>
                            <span class="advgb-customstyles-items-title">${res.title}</span></a>
                            <a class="copy"><span class="dashicons dashicons-admin-page"></span></a>
                            <a class="trash"><span class="dashicons dashicons-no"></span></a>
                            <ul style="margin-left: 30px">
                                <li class="advgb-customstyles-items-class">(${res.name})</li>
                            </ul>
                        </li>`;

                    self.$('.advgb-customstyles-list').prepend(newItem);

                    self.initCustomStyleMenu();
                    self.customStylePreview(res.id);
                },
                complete: () => {
                    // DON'T remove overlay here, let customStylePreview handle it
                    //self.$('#customstyles-tab').find('.advgb-overlay-box').remove();
                },
                error: () => {
                    this.$('#customstyles-tab').find('.advgb-overlay-box').remove();
                }
            });
        });
    }

    initCustomStyleDelete() {
        const self = this;
        this.$('#mybootstrap .advgb-customstyles-items a.trash').off('click').on('click', function (e) {
            e.preventDefault();
            const that = this;
            const styleName = self.$(this).prev().prev().text().trim();
            const cf = confirm(`Do you really want to delete "${styleName}"?`);

            if (cf === true) {
                const id = self.$(that).parent().data('id-customstyle');
                const nonce_val = self.$('#advgb_cstyles_nonce_field').val();

                self.ajaxRequest({
                    action: 'advgb_custom_styles_ajax',
                    id: id,
                    task: 'delete',
                    nonce: nonce_val
                }, {
                    beforeSend: () => {
                        self.$('#customstyles-tab').append('<div class="advgb-overlay-box"></div>');
                    },
                    success: (res) => {
                        self.$(that).parent().remove();
                        if (res.id == self.styleId) {
                            self.customStylePreview();
                        } else {
                            self.customStylePreview(self.styleId);
                        }
                    },
                    complete: () => {
                        self.$('#customstyles-tab').find('.advgb-overlay-box').remove();
                    }
                });
            }
        });
    }

    initCustomStyleCopy() {
        const self = this;
        this.$('#mybootstrap .advgb-customstyles-items a.copy').off('click').on('click', function (e) {
            e.preventDefault();
            const that = this;
            const id = self.$(that).parent().data('id-customstyle');
            const nonce_val = self.$('#advgb_cstyles_nonce_field').val();

            self.ajaxRequest({
                action: 'advgb_custom_styles_ajax',
                id: id,
                task: 'copy',
                nonce: nonce_val
            }, {
                beforeSend: () => {
                    self.$('#customstyles-tab').append('<div class="advgb-overlay-box"></div>');
                },
                success: (res) => {
                    self.$(that).parent().after(
                        `<li class="advgb-customstyles-items" data-id-customstyle="${res.id}">
                            <a><i class="title-icon" style="background-color: ${res.identifyColor}"></i>
                            <span class="advgb-customstyles-items-title">${res.title}</span></a>
                            <a class="copy"><span class="dashicons dashicons-admin-page"></span></a>
                            <a class="trash"><span class="dashicons dashicons-no"></span></a>
                            <ul style="margin-left: 30px">
                                <li class="advgb-customstyles-items-class">(${res.name})</li>
                            </ul>
                        </li>`
                    );
                    self.initCustomStyleMenu();
                    self.customStylePreview(res.id);
                },
                complete: () => {
                    // DON'T remove overlay here, let customStylePreview handle it
                    //self.$('#customstyles-tab').find('.advgb-overlay-box').remove();
                },
                error: () => {
                    this.$('#customstyles-tab').find('.advgb-overlay-box').remove();
                }
            });
        });
    }

    initTableLinks() {
        const self = this;
        this.$('#mybootstrap .advgb-customstyles-items').off('click').on('click', function (e) {
            e.preventDefault();
            const id = self.$(this).data('id-customstyle');
            self.customStylePreview(id);
        });
    }

    // Style Loading and Saving
    loadInitialStyle() {
        this.styleId = this.advgbGetCookie('advgbCustomStyleID');

        // Fix Codemirror not displayed properly
        this.$('a[href="#custom-styles"]').one('click', () => {
            if (this.editor) {
                this.editor.refresh();
            }
            this.customStylePreview(this.styleId);
        });

        this.customStylePreview(this.styleId);
    }

    customStylePreview(id_element = false) {
        if (typeof id_element === "undefined" || !id_element) {
            const firstStyle = this.$('#mybootstrap ul.advgb-customstyles-list li:first-child');
            id_element = firstStyle.data('id-customstyle');
            firstStyle.addClass('active');
        }

        if (typeof id_element === "undefined" || id_element === "") return;

        this.clearAllFields();

        this.$('#mybootstrap .advgb-customstyles-list li').removeClass('active');
        this.$('#mybootstrap .advgb-customstyles-list li[data-id-customstyle=' + id_element + ']').addClass('active');

        document.cookie = 'advgbCustomStyleID=' + id_element;
        const nonce_val = this.$('#advgb_cstyles_nonce_field').val();

        this.ajaxRequest({
            action: 'advgb_custom_styles_ajax',
            id: id_element,
            task: 'preview',
            nonce: nonce_val
        }, {
            beforeSend: () => {
                this.$('#advgb-customstyles-info').append('<div class="advgb-overlay-box"></div>');
            },
            success: (res) => {
                // Update basic fields
                this.$('#advgb-customstyles-title').val(res.title);
                this.$('#advgb-customstyles-classname').val(res.name);

                // Handle identify color
                const $identifyColor = this.$('#advgb-customstyles-identify-color');
                if ($identifyColor.data('minicolors-initialized')) {
                    $identifyColor.minicolors('value', res.identifyColor);
                } else {
                    $identifyColor.val(res.identifyColor);
                }

                // Set active tab
                if (res.active_tab) {
                    this.activeTab = res.active_tab;
                    localStorage.setItem('advgb_active_tab', this.activeTab);
                    this.setActiveTab();
                }

                // Handle CSS
                this.styleId = id_element;
                let cssContent = res.css;

                if (res.css_array) {
                    this.populateUIFields(res.css_array);
                    cssContent = '{\n' + res.css + '\n}';
                } else {
                    // Legacy CSS string
                    this.populateUIFields(res.css);
                    cssContent = '{\n' + res.css + '\n}';
                }

                // Update editor
                this.$('#advgb-customstyles-css').val(cssContent);
                if (this.editor) {
                    this.editor.setValue(cssContent);
                    this.editor.refresh();
                }
                this.parseCustomStyleCss();
            },
            complete: () => {
                this.$('#advgb-customstyles-info').find('.advgb-overlay-box').remove();
                this.$('#customstyles-tab').find('.advgb-overlay-box').remove();
            },
            error: () => {
                this.$('#customstyles-tab').find('.advgb-overlay-box').remove();
                this.$('#advgb-customstyles-info').find('.advgb-overlay-box').css({
                    backgroundImage: 'none',
                    backgroundColor: '#ff0000',
                    opacity: 0.2
                });
            }
        });
    }

    populateUIFields(cssData) {

        if (!cssData) {
            this.clearAllFields();
            return;
        }

        // If it's already an object with nested structure (from array save)
        if (cssData.base || cssData.nested || cssData.states) {
            this.populateFromStructuredData(cssData);
        }
        // If it's a flat object (legacy format)
        else if (typeof cssData === 'object') {
            this.populateFromFlatData(cssData);
        }
        // If it's a string (custom CSS tab)
        else if (typeof cssData === 'string') {
            // Parse SCSS string to structured data
            const parsedData = this.parseSCSSString(cssData);
            this.populateFromStructuredData(parsedData);
        }
    }

    populateFromStructuredData(cssData) {

        if (cssData.base) {
            for (const prop in cssData.base) {
                const $input = this.$('[data-css-property="' + prop + '"]');
                if ($input.length && cssData.base[prop]) {
                    this.setInputValue($input, cssData.base[prop]);
                }
            }
        }

        if (cssData.states) {
            for (const state in cssData.states) {
                const stateName = state.replace(':', '');
                for (const prop in cssData.states[state]) {
                    const stateProperty = stateName + '-' + prop;
                    const $input = this.$('[data-css-property="' + stateProperty + '"]');
                    if ($input.length && cssData.states[state][prop]) {
                        this.setInputValue($input, cssData.states[state][prop]);
                    }
                }
            }
        }

        if (cssData.nested) {
            for (const selector in cssData.nested) {
                const elementRules = cssData.nested[selector];

                for (const prop in elementRules) {
                    if (prop !== 'states' && elementRules[prop]) {
                        let elementProperty = selector + '-' + prop;
                        if (selector.includes(' ')) {
                            elementProperty = selector.replace(/ /g, '-') + '-' + prop;
                        }
                        const $input = this.$('[data-css-property="' + elementProperty + '"]');
                        if ($input.length) {
                            this.setInputValue($input, elementRules[prop]);
                        }
                    }
                }

                if (elementRules.states) {
                    for (const state in elementRules.states) {
                        const stateName = state.replace(':', '');
                        for (const prop in elementRules.states[state]) {
                            let stateProperty = selector + '-' + stateName + '-' + prop;
                            if (selector.includes(' ')) {
                                stateProperty = selector.replace(/ /g, '-') + '-' + stateName + '-' + prop;
                            }
                            const $input = this.$('[data-css-property="' + stateProperty + '"]');
                            if ($input.length && elementRules.states[state][prop]) {
                                this.setInputValue($input, elementRules.states[state][prop]);
                            }
                        }
                    }
                }
            }
        }
    }

    populateFromFlatData(cssData) {
        for (const prop in cssData) {
            const $input = this.$('[data-css-property="' + prop + '"]');
            if ($input.length) {
                this.setInputValue($input, cssData[prop]);
            }
        }
    }

    parseSCSSString(scssString) {
        const result = {
            base: {},
            states: {},
            nested: {}
        };

        if (!scssString) return result;

        const lines = scssString.split('\n');
        let currentSelector = 'base';
        let currentState = 'normal';
        let inNestedBlock = false;

        lines.forEach(function (line) {
            line = line.trim();

            if (line === '' || line === '{' || line === '}') {
                return;
            }

            // Check for nested selector or state
            if (line.startsWith('&')) {
                const selector = line.replace('&', '').replace('{', '').trim();

                // Check if it's a state
                if (selector.startsWith(':')) {
                    currentState = selector;
                    currentSelector = 'base';
                } else {
                    currentSelector = selector;
                    currentState = 'normal';
                }

                inNestedBlock = true;
                return;
            }

            // Parse CSS declaration with context
            if (line.includes(':')) {
                const parts = line.split(':');
                const property = parts[0].trim();
                const value = parts.slice(1).join(':').replace(';', '').trim();

                if (currentSelector === 'base' && currentState === 'normal') {
                    result.base[property] = value;
                } else if (currentSelector === 'base' && currentState !== 'normal') {
                    if (!result.states[currentState]) {
                        result.states[currentState] = {};
                    }
                    result.states[currentState][property] = value;
                } else if (currentSelector !== 'base' && currentState === 'normal') {
                    if (!result.nested[currentSelector]) {
                        result.nested[currentSelector] = {};
                    }
                    result.nested[currentSelector][property] = value;
                } else {
                    if (!result.nested[currentSelector]) {
                        result.nested[currentSelector] = { states: {} };
                    }
                    if (!result.nested[currentSelector].states) {
                        result.nested[currentSelector].states = {};
                    }
                    if (!result.nested[currentSelector].states[currentState]) {
                        result.nested[currentSelector].states[currentState] = {};
                    }
                    result.nested[currentSelector].states[currentState][property] = value;
                }
            }
        });

        return result;
    }

    // Helper to strip outer braces
    stripOuterBraces(content) {
        let cleaned = content.trim();

        // Remove outer braces if content is wrapped in them
        if (cleaned.startsWith('{') && cleaned.endsWith('}')) {
            cleaned = cleaned.substring(1, cleaned.length - 1).trim();
        }

        return cleaned;
    }

    saveCustomStyleChanges() {
        const myStyleTitle = this.$('#advgb-customstyles-title').val().trim();
        const myClassname = this.$('#advgb-customstyles-classname').val().trim();
        const myIdentifyColor = this.$('#advgb-customstyles-identify-color').val().trim();
        const nonce_val = this.$('#advgb_cstyles_nonce_field').val();

        let cssData = {};
        let generatedCSS = '';

        if (this.activeTab === 'style-editor') {
            cssData = this.buildStructuredDataFromUI();

            const scssString = this.arrayToSCSS(cssData);
            const cssContent = '{\n' + scssString + '\n}';
            generatedCSS = this.convertSCSSToCSS(cssContent, true);

            generatedCSS = generatedCSS.replace(/\.advgb-customstyles-target/g, '.' + myClassname);

        } else {
            this.activeTab = 'custom-css';
            const cssContent = this.editor.getValue();
            cssData = this.stripOuterBraces(cssContent);

            generatedCSS = this.manualSCSSToCSS(cssContent, myClassname);
        }

        this.ajaxRequest({
            action: 'advgb_custom_styles_ajax',
            id: this.styleId,
            title: myStyleTitle,
            name: myClassname,
            mycss: this.activeTab === 'custom-css' ? cssData : '',
            css_array: this.activeTab === 'style-editor' ? cssData : null,
            generated_css: generatedCSS,
            mycolor: myIdentifyColor,
            active_tab: this.activeTab,
            task: 'style_save',
            nonce: nonce_val
        }, {
            beforeSend: () => {
                this.$('#customstyles-tab').append('<div class="advgb-overlay-box"></div>');
            },
            success: () => {
                this.$('#advgb-customstyles-info form').submit();
            },
            complete: () => {
                this.$('#customstyles-tab').find('.advgb-overlay-box').remove();
            }
        });
    }

    getValidElements() {
        return [
            'p', 'span', 'blockquote', 'ul', 'li', 'ol', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'img', 'video', 'div', 'section', 'button', 'input'
        ];
    }

    getValidStates() {
        return ['hover', 'focus', 'active', 'visited'];
    }

    clearAllFields() {
        this.$('.style-input').val('');

        this.$('.minicolors').each((index, element) => {
            const $element = this.$(element);
            if ($element.data('minicolors-initialized')) {
                $element.minicolors('value', '');
            } else {
                $element.val('');
            }
        });

        if (this.editor) {
            this.editor.setValue('');
            this.editor.refresh();
        }
    }

    ajaxRequest(data, callbacks = {}) {
        const defaults = {
            url: window.ajaxurl,
            type: 'POST',
            dataType: 'json'
        };

        const settings = {
            ...defaults,
            data: data,
            ...callbacks
        };

        this.$.ajax(settings);
    }

    advgbGetCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
}

jQuery(document).ready(function ($) {
    window.advGbCustomStyles = new AdvGbCustomStyles($);
});