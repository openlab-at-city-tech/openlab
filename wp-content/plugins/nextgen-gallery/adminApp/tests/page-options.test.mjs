/**
 * Run: node --test adminApp/tests/ (Node 22.18+, which strips the TypeScript types itself).
 */
import assert from 'node:assert/strict';
import {test} from 'node:test';

import {mergePageLists} from '../src/utils/page-options.ts';

const fetched = [
	{id: 3, title: 'Apple'},
	{id: 9, title: 'Mango'},
	{id: 4, title: 'Zebra'}
];

test('a created page that the refetch returned takes its sorted place', () => {
	const refetched = [fetched[0], {id: 12, title: 'Kiwi'}, fetched[1], fetched[2]];
	const merged = mergePageLists(refetched, [{id: 12, title: 'Kiwi'}]);

	assert.deepEqual(
		merged.map(p => p.id),
		[3, 12, 9, 4]
	);
});

test('the fetched row wins over the local copy of the same page', () => {
	const refetched = [{id: 12, title: 'Kiwi & Lime'}, ...fetched];
	const merged = mergePageLists(refetched, [{id: 12, title: 'Kiwi &amp; Lime'}]);

	assert.equal(merged.find(p => p.id === 12)?.title, 'Kiwi & Lime');
	assert.equal(merged.filter(p => p.id === 12).length, 1);
});

test('a created page the list does not have yet is shown first', () => {
	const merged = mergePageLists(fetched, [{id: 12, title: 'Kiwi'}]);

	assert.deepEqual(
		merged.map(p => p.id),
		[12, 3, 9, 4]
	);
});

test('no created pages leaves the fetched list as it is', () => {
	assert.deepEqual(mergePageLists(fetched, []), fetched);
});
