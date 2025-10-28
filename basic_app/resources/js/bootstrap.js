import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

// Bootstrap 4 bundle (includes Popper)
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// AdminLTE core
import 'admin-lte';

// ✅ Import the plugin
import 'admin-lte/plugins/bs-custom-file-input/bs-custom-file-input.min.js';

// Init after DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (window.bsCustomFileInput?.init) {
        window.bsCustomFileInput.init();
    }
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
