(function() {
	'use strict';

	tinymce.create('tinymce.plugins.accordionShortcodesExtensions', {
		init: function(editor, url) {
			if (accordionShortcodesPrefix == undefined) {
				var accordionShortcodesPrefix = '';
			}

			// Accordion group
			editor.addButton('AccordionShortcode', {
				title: 'Add an accordion group',
				image: url + '/images/accordion.gif',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Accordion Shortcode',
						body: [
							{
								type: 'checkbox',
								name: 'autoclose',
								label: 'Auto Close Accordions',
								checked: true
							},
							{
								type: 'checkbox',
								name: 'openfirst',
								label: 'Open First Accordion'
							},
							{
								type: 'checkbox',
								name: 'openall',
								label: 'Open All Accordions'
							},
							{
								type: 'checkbox',
								name: 'clicktoclose',
								label: 'Click to Close Accordions'
							},
							{
								type: 'checkbox',
								name: 'scroll',
								label: 'Scroll to Top of Accordion'
							},
							{
								type: 'listbox',
								name: 'tag',
								label: 'HTML Tag for Title',
								minWidth: 75,
								values: [
									{text: '---', value: null},
									{text: 'h1',  value: 'h1'},
									{text: 'h2',  value: 'h2'},
									{text: 'h3',  value: 'h3'},
									{text: 'h4',  value: 'h4'},
									{text: 'h5',  value: 'h5'},
									{text: 'h6',  value: 'h6'},
									{text: 'p',   value: 'p'},
									{text: 'div', value: 'div'}
								]
							}
						],
						onsubmit: function(e) {
							var shortcode = '[' + accordionShortcodesPrefix + 'accordion';

							if (e.data.autoclose === false) {
								shortcode += ' autoclose=' + e.data.autoclose;
							}
							if (e.data.openfirst) {
								shortcode += ' openfirst=' + e.data.openfirst;
							}
							if (e.data.openall) {
								shortcode += ' openall=' + e.data.openall;
							}
							if (e.data.clicktoclose) {
								shortcode += ' clicktoclose=' + e.data.clicktoclose;
							}
							if (e.data.scroll) {
								shortcode += ' scroll=' + e.data.scroll;
							}
							if (e.data.tag) {
								shortcode += ' tag=' + e.data.tag;
							}

							shortcode += ']' + editor.selection.getContent() + '[/' + accordionShortcodesPrefix + 'accordion]';

							editor.insertContent(shortcode);
						}
					});
				}
			});

			// Accordion item
			editor.addButton('AccordionItemShortcode', {
				title: 'Add an accordion item',
				image: url + '/images/accordion-item.gif',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Accordion Item Shortcode',
						body: [
							{
								type: 'textbox',
								name: 'title',
								label: 'Accordion Item Title',
								minWidth: 300
							},
							{
								type: 'listbox',
								name: 'initialstate',
								label: 'Initial State (optional)',
								minWidth: 75,
								values: [
									{text: '---',    value: null},
									{text: 'open',   value: 'open'},
									{text: 'closed', value: 'closed'},
								]
							},
							{
								type: 'textbox',
								name: 'id',
								label: 'ID (optional)',
								minWidth: 300
							},
							{
								type: 'container',
								html: 'Each ID on a single page must be unique and cannot contain spaces.'
							}
						],
						onsubmit: function(e) {
							var shortcode = '[' + accordionShortcodesPrefix + 'accordion-item title="';

							if (e.data.title) {
								shortcode += e.data.title;
							}
							shortcode += '"';

							if (e.data.id) {
								shortcode += ' id=' + e.data.id.replace(/\s+/g, '-');
							}
							if (e.data.initialstate) {
								shortcode += ' state=' + e.data.initialstate;
							}

							shortcode += ']' + editor.selection.getContent() + '[/' + accordionShortcodesPrefix + 'accordion-item]';

							editor.insertContent(shortcode);
						}
					})
				}
			});
		}
	});

	tinymce.PluginManager.add('accordionShortcodesExtensions', tinymce.plugins.accordionShortcodesExtensions);
}());
