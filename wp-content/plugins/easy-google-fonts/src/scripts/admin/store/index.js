import { registerStore } from '@wordpress/data';
import { controls } from '@wordpress/data-controls';
import reducer from './reducer';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';

export const STORE_KEY = 'egf/font-controls';

const store = registerStore(STORE_KEY, {
  reducer,
  selectors,
  actions,
  controls,
  resolvers
});

export default store;
