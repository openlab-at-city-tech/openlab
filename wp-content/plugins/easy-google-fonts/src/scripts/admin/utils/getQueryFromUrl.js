import { getQueryArg } from '@wordpress/url';
import { useLocation } from 'react-router-dom';

export default query => getQueryArg(useLocation().search, query);
