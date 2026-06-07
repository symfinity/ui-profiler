import { Controller } from '@hotwired/stimulus';

/** Optional Stimulus path for table search — UiProfilerShell.createTableSearchFields() runs first (DI-3). */
export default class extends Controller {
    connect() {
        if (this.element.querySelector('[data-ui-profiler-table-search][data-processed="true"]')) {
            return;
        }
    }
}
