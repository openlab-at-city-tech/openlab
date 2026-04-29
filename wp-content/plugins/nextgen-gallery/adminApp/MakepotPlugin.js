/**
 * Webpack plugin to generate POT files with correct JavaScript source references
 *
 * This plugin:
 * 1. Adds @wordpress/babel-plugin-makepot to the babel-loader configuration
 * 2. Post-processes the generated POT file to replace TypeScript source paths with JS build paths
 * 3. Ensures temp files are always cleaned up, even on errors
 */

const fs = require('fs');
const path = require('path');

class MakepotPlugin {
	constructor(options = {}) {
		this.options = {
			output: options.output || 'translations.pot',
			scriptPath: options.scriptPath || '',
			tempOutput: options.tempOutput || options.output + '.temp'
		};
	}

	/**
	 * Safely delete a file if it exists
	 * @param {string} filePath - Path to file to delete
	 */
	safeUnlink(filePath) {
		try {
			if (fs.existsSync(filePath)) {
				fs.unlinkSync(filePath);
			}
		} catch (error) {
			console.warn(`[MakepotPlugin] Could not delete temp file ${filePath}:`, error.message);
		}
	}

	apply(compiler) {
		// Clean up any leftover temp files at the start
		compiler.hooks.beforeCompile.tap('MakepotPlugin', () => {
			this.safeUnlink(this.options.tempOutput);
		});

		// Add @wordpress/babel-plugin-makepot to babel-loader
		compiler.hooks.afterPlugins.tap('MakepotPlugin', () => {
			const rules = compiler.options.module.rules;

			for (const rule of rules) {
				if (rule.test && rule.test.toString().includes('tsx') && rule.use) {
					for (const use of rule.use) {
						if (use.loader === 'babel-loader') {
							use.options.plugins = use.options.plugins || [];
							use.options.plugins.push([
								'@wordpress/babel-plugin-makepot',
								{output: this.options.tempOutput},
								`makepot-${path.basename(this.options.output)}`
							]);
						}
					}
				}
			}
		});

		// Post-process POT file: replace TypeScript paths with JS build paths
		compiler.hooks.afterEmit.tapAsync('MakepotPlugin', (compilation, callback) => {
			if (!fs.existsSync(this.options.tempOutput)) {
				console.warn(`[MakepotPlugin] No POT file found at ${this.options.tempOutput}`);
				callback();
				return;
			}

			try {
				const potContent = fs
					.readFileSync(this.options.tempOutput, 'utf8')
					.replace(/#: src\/[^\s]+\.tsx?:(\d+)/g, `#: ${this.options.scriptPath}:$1`);

				fs.writeFileSync(this.options.output, potContent, 'utf8');
				fs.unlinkSync(this.options.tempOutput);

				console.warn(`[MakepotPlugin] Generated: ${this.options.output}`);
			} catch (error) {
				console.error(`[MakepotPlugin] Error processing POT file:`, error);
			} finally {
				// Always clean up temp file, even on error
				this.safeUnlink(this.options.tempOutput);
			}

			callback();
		});

		// Clean up temp files on failed compilation
		compiler.hooks.failed.tap('MakepotPlugin', () => {
			this.safeUnlink(this.options.tempOutput);
		});
	}
}

module.exports = MakepotPlugin;
