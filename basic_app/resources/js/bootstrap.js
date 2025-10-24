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
