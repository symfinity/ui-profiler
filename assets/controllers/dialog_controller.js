import { Controller } from '@hotwired/stimulus';

/** Optional Stimulus path for settings dialog — native inline script remains authoritative (DI-3). */
export default class extends Controller {
    connect() {
        // Progressive enhancement hook; open/close handled by native script in profiler-settings-dialog.html.twig.
    }
}
