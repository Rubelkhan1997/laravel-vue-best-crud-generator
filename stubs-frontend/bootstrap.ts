/**
 * Axios Bootstrap
 * Global axios configuration
 */

import axios from 'axios';

// Set axios as global window property
(window as any).axios = axios;

// Default headers
(window as any).axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
(window as any).axios.defaults.headers.common['Accept'] = 'application/json';
(window as any).axios.defaults.headers.common['Content-Type'] = 'application/json';

// Request timeout
(window as any).axios.defaults.timeout = 30000; // 30 seconds
 
