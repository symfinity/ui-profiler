import { Controller } from '@hotwired/stimulus';

/** Lazy table-search enhancement — native shell filter remains required (DI-3). */
export default class extends Controller {
    connect() {
        this.element.dataset.uiProfilerTableSearchStimulus = '1';
    }
}
