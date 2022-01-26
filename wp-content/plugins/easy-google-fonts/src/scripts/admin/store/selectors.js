/**
 * Font Control Selectors
 * @param {*} name
 */
export const getFontControls = state => {
  return state.fontControls || {};
};

export const getFontControl = (state, id) => {
  return state.fontControls[id] || {};
};

/**
 * API Key Selectors
 * @param {*} name
 */
export const getApiKey = state => {
  return state.apiKey || '';
};
