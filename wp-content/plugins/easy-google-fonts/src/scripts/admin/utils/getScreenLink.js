/**
 * WordPress dependancies
 */
import { addQueryArgs } from '@wordpress/url';

const getScreenLink = (screen, queryArgs = {}) => {
  return addQueryArgs('/options-general.php', { page: 'easy-google-fonts', screen, ...queryArgs });
};

export default getScreenLink;
