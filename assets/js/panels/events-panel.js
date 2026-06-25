/**
 * Events collector panel — dispatcher/listener filter radios.
 */

class UiProfilerEventsPanel {
    constructor() {
        this.#initialize();
    }

    #initialize() {
        this.#updatePanels();

        document.querySelectorAll('#event-filter-dispatcher input, #event-filter-listeners input').forEach((input) => {
            input.addEventListener('change', () => {
                this.#updatePanels();
            });
        });

        document.body.addEventListener('click', (event) => {
            document.querySelectorAll('[data-ui-profiler-panel="events"] details.log-filter').forEach((filterElement) => {
                if (!filterElement.contains(event.target) && filterElement.open) {
                    filterElement.open = false;
                }
            });
        });
    }

    #getActiveDispatcher() {
        const input = document.querySelector('#event-filter-dispatcher input:checked');

        return input ? input.value : null;
    }

    #getActiveListenerType() {
        const input = document.querySelector('#event-filter-listeners input:checked');

        return input ? input.value : 'called';
    }

    #updatePanels() {
        const dispatcher = this.#getActiveDispatcher();
        const listenerType = this.#getActiveListenerType();
        const listenerLabels = {
            called: 'Called',
            not_called: 'Not called',
            orphaned: 'Orphaned',
        };

        document.querySelectorAll('.event-dispatcher-panel').forEach((panel) => {
            panel.hidden = panel.dataset.dispatcher !== dispatcher;
        });

        const activePanel = dispatcher
            ? document.querySelector(`.event-dispatcher-panel[data-dispatcher="${CSS.escape(dispatcher)}"]`)
            : null;

        if (activePanel) {
            activePanel.querySelectorAll('.event-listener-panel').forEach((panel) => {
                panel.hidden = panel.dataset.listenerType !== listenerType;
            });
        }

        const dispatcherValue = document.querySelector('#event-filter-dispatcher .filter-active-value');
        if (dispatcherValue && dispatcher) {
            dispatcherValue.innerText = dispatcher;
        }

        const listenerValue = document.querySelector('#event-filter-listeners .filter-active-value');
        const listenerBadge = document.querySelector('#event-filter-listeners .filter-active-badge');
        if (listenerValue) {
            listenerValue.innerText = listenerLabels[listenerType] ?? listenerType;
        }
        if (activePanel) {
            const counts = {
                called: activePanel.dataset.calledCount ?? '0',
                not_called: activePanel.dataset.notCalledCount ?? '0',
                orphaned: activePanel.dataset.orphanedCount ?? '0',
            };

            if (listenerBadge) {
                listenerBadge.innerText = counts[listenerType] ?? '0';
            }

            Object.entries(counts).forEach(([type, count]) => {
                const optionBadge = document.querySelector(`label[for="filter-event-listener-${type.replace('_', '-')}"] .badge`);
                if (optionBadge) {
                    optionBadge.innerText = count;
                }
            });
        }
    }
}

export function bootEventsPanel() {
    const run = () => {
        new UiProfilerEventsPanel();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
}
