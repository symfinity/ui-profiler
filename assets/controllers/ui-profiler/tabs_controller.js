import { Controller } from '@hotwired/stimulus';

/** Lazy tabs enhancement — native UiProfilerShell.createTabs() remains the required path (DI-1). */
export default class extends Controller {
    connect() {
        this.element.dataset.uiProfilerTabsStimulus = '1';
    }
}
