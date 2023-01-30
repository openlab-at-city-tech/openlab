const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require('path');

const frontendConfig = { 
	...defaultConfig,
	name: 'frontend',
	entry: {
		'frontend': './src/frontend.js'
	},
	output: {
		path: path.join(__dirname, './build/frontend'),
		filename: 'frontend.build.js'
	}
};

const blocksConfig = {
    ...defaultConfig,
	name: 'blocks',
	entry: {
		'blocks': './src/blocks.js'
	},
	output: {
		path: path.join(__dirname, './build/blocks'),
		filename: 'blocks.build.js'
	}
};

module.exports = [
	frontendConfig, blocksConfig
];
