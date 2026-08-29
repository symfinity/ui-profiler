import { Controller } from '@hotwired/stimulus';

/** Optional Stimulus path for collector tabs — UiProfilerShell.createTabs() runs first (DI-3). */
export default class extends Controller {
    connect() {
        if (this.element.querySelector('[data-ui-profiler-tabs][data-processed="true"]')) {
            return;
        }
    }
}
