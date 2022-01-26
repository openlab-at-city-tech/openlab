import { combineReducers } from '@wordpress/data';

/**
 * Font Control Reducers
 * @param {*} state
 * @param {*} action
 */
export const fontControlsReducer = (state = {}, action) => {
  switch (action.type) {
    case 'CREATE_FONT_CONTROL':
      return { ...state, [action.payload.id]: action.payload.fontControl };
      break;

    case 'UPDATE_FONT_CONTROL':
      return { ...state, [action.payload.id]: action.payload.fontControl };
      break;

    case 'UPDATE_FONT_CONTROL_FORCE_STYLES':
      return { ...state, [action.payload.id]: action.payload.fontControl };
      break;

    case 'DELETE_FONT_CONTROL':
      let allFontControls = { ...state };
      delete allFontControls[action.payload.id];
      return allFontControls;
      break;

    case 'DELETE_ALL_FONT_CONTROLS':
      return action.payload.fontControls;
      break;

    case 'HYDRATE_FONT_CONTROLS':
      return action.payload.fontControls;
      break;

    default:
      return state;
  }
};

/**
 * API Key Reducers
 * @param {*} state
 * @param {*} action
 */
export const apiKeyReducer = (state = '', action) => {
  switch (action.type) {
    case 'UPDATE_API_KEY':
      return action.payload.apiKey;
      break;

    case 'HYDRATE_API_KEY':
      return action.payload.apiKey;
      break;

    default:
      return state;
  }
};

export default combineReducers({
  fontControls: fontControlsReducer,
  apiKey: apiKeyReducer
});
