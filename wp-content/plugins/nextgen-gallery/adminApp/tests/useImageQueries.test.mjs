/**
 * Cache invalidation of the image mutation hooks.
 *
 * Run: yarn --cwd adminApp test   (node --test, no browser, no WordPress)
 *
 * The hooks are transpiled with the app's own Babel presets and evaluated with
 * stubbed react-query / api-fetch / notifications modules, so each mutation's
 * onSuccess runs exactly as in the app and the invalidated query keys can be read.
 */
import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import {createRequire} from 'node:module';
import {dirname, resolve} from 'node:path';
import {test} from 'node:test';
import {fileURLToPath} from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const appRequire = createRequire(resolve(here, '../package.json'));
const babel = appRequire('@babel/core');

function loadHooks(relativePath) {
	const file = resolve(here, '../src', relativePath);
	const {code} = babel.transformSync(readFileSync(file, 'utf8'), {
		filename: file,
		babelrc: false,
		configFile: false,
		presets: [
			[appRequire.resolve('@babel/preset-env'), {targets: {node: 'current'}, modules: 'commonjs'}],
			appRequire.resolve('@babel/preset-typescript')
		]
	});

	const invalidated = [];
	const queryClient = {
		invalidateQueries: ({queryKey}) => invalidated.push(queryKey),
		setQueryData: () => {}
	};
	const stubs = {
		'@tanstack/react-query': {
			useQueryClient: () => queryClient,
			// The hooks return useMutation(options): hand the options back so the test can call onSuccess.
			useMutation: options => options
		},
		'@wordpress/api-fetch': {default: async () => ({})},
		'@src/hooks/useNotifications': {
			useNotifications: () => ({showSuccess() {}, showWarning() {}, showError() {}})
		}
	};
	const module = {exports: {}};
	const localRequire = id => {
		if (id in stubs) {
			return stubs[id];
		}
		if (id.startsWith('@src/')) {
			return {}; // type-only imports (removed by Babel) or unused helpers
		}
		throw new Error(`unexpected import ${id}`);
	};
	new Function('require', 'module', 'exports', code)(localRequire, module, module.exports);

	return {hooks: module.exports, invalidated};
}

const keys = invalidated => invalidated.map(key => JSON.stringify(key));

for (const [hook, response] of [
	['useBulkAddTags', {success_count: 2, failure_count: 0, message: 'ok'}],
	['useBulkRemoveTags', {success_count: 2, failure_count: 0, message: 'ok'}]
]) {
	test(`${hook} refreshes the tag list after a successful save`, () => {
		const {hooks, invalidated} = loadHooks('hooks/useImageQueries.ts');
		hooks[hook]().onSuccess(response);

		assert.ok(keys(invalidated).includes('["gallery"]'), 'gallery images are refreshed');
		assert.ok(
			keys(invalidated).includes('["tags"]'),
			`the "Filter by tag" picker query ['tags'] is invalidated (got ${keys(invalidated).join(', ')})`
		);
	});
}

test('useUpdateImage keeps refreshing the tag list', () => {
	const {hooks, invalidated} = loadHooks('hooks/useImageQueries.ts');
	hooks.useUpdateImage().onSuccess({pid: 1, galleryid: 3});
	assert.ok(keys(invalidated).includes('["tags"]'));
});
