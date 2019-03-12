const docs = {
	config: {
		breakPoint: 960
	}
};

const menu = {
	element: '',
	button: '',
	setup: function() {
		menu.element = document.querySelector('.nav');
		menu.button = document.querySelector('#menu-button');

		menu.setVisibility();
		menu.button.addEventListener('click', menu.toggle, false);
		window.addEventListener('resize', menu.setVisibility);
	},

	toggle: function() {
		let expanded = menu.button.getAttribute('aria-expanded') === 'true' || false;
		menu.button.setAttribute('aria-expanded', !expanded);
		menu.element.hidden = !menu.element.hidden;
	},

	setVisibility: function() {
		if (window.innerWidth >= docs.config.breakPoint) {
			menu.button.setAttribute('aria-expanded', true);
			menu.button.hidden = true;
			menu.element.hidden = false;
		} else {
			menu.button.setAttribute('aria-expanded', false);
			menu.button.hidden = false;
			menu.element.hidden = true;
		}
	}
}

menu.setup();
