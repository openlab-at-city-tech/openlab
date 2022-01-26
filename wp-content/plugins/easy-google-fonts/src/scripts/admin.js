// External dependencies.
import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router } from 'react-router-dom';
import { ToastProvider } from 'react-toast-notifications';

// WordPress dependencies.
import { getPath } from '@wordpress/url';
import { useSelect } from '@wordpress/data';

// Internal dependencies.
import './admin/store';
import { STORE_KEY } from './admin/store';
import ScreenController from './admin/views/pages/ScreenController';

const AdminScreen = () => {
  // Preload state.
  useSelect(select => select(STORE_KEY).getFontControls());
  useSelect(select => select(STORE_KEY).getApiKey());

  return (
    <Router basename={getPath(easy_google_fonts.admin_url)}>
      <ToastProvider autoDismissTimeout={4500} placement="bottom-right">
        <ScreenController />
      </ToastProvider>
    </Router>
  );
};

ReactDOM.render(<AdminScreen />, document.getElementById('egf-root'));
