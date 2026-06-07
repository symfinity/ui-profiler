import { Controller } from '@hotwired/stimulus';

/**
 * Optional Stimulus layer (051 DI-2) — native shell.js owns open/close; this enhances when ux-runtime present.
 */
export default class extends Controller {
    connect() {
        this.element.dataset.uiProfilerDialogStimulus = '1';
    }
}
