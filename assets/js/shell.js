/**
 * Profiler shell runtime — single file (AssetMapper vendor path; no split imports).
 * Ports stock SymfonyProfiler behavior for SR-2 / SR-4 parity.
 */

export class UiProfilerShell {
    static boot() {
        const run = () => {
            new UiProfilerShell();
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', run);
        } else {
            run();
        }
    }

    constructor() {
        this.createTabs();
        this.createTableSearchFields();
        this.createToggles();
        this.createCopyToClipboard();
        this.convertDateTimesToUserTimezone();
        this.bindSearchShortcut();
    }

    bindSearchShortcut() {
        document.addEventListener('keydown', (event) => {
            if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey) {
                return;
            }

            const target = event.target;
            if (target instanceof HTMLElement && target.closest('input, textarea, select, [contenteditable="true"]')) {
                return;
            }

            const searchField = document.querySelector(
                '[data-ui-profiler-table-search] .table-search-field-input',
            );

            if (!(searchField instanceof HTMLInputElement)) {
                return;
            }

            event.preventDefault();
            searchField.focus();
        });
    }

    createTabs() {
        const tabGroups = document.querySelectorAll('[data-ui-profiler-tabs]:not([data-processed=true])');

        tabGroups.forEach((tabGroup, i) => {
            const tabs = tabGroup.querySelectorAll(':scope > .tab');
            const tabNavigation = document.createElement('div');
            tabNavigation.setAttribute('data-ui-profiler-tab-navigation', '');
            tabNavigation.setAttribute('role', 'tablist');

            let selectedTabId = `tab-${i}-0`;
            tabs.forEach((tab, j) => {
                const tabId = `tab-${i}-${j}`;
                const tabTitle = tab.querySelector('.tab-title').innerHTML;

                const tabNavigationItem = document.createElement('button');
                tabNavigationItem.setAttribute('data-ui-profiler-tab', '');
                tabNavigationItem.setAttribute('data-tab-id', tabId);
                tabNavigationItem.setAttribute('role', 'tab');
                tabNavigationItem.setAttribute('aria-controls', tabId);
                if (tab.classList.contains('active')) {
                    selectedTabId = tabId;
                }
                if (tab.classList.contains('disabled')) {
                    tabNavigationItem.setAttribute('aria-disabled', 'true');
                }
                tabNavigationItem.innerHTML = tabTitle;
                tabNavigation.appendChild(tabNavigationItem);

                const tabContent = tab.querySelector('.tab-content');
                tabContent.parentElement.setAttribute('id', tabId);
            });

            tabGroup.insertBefore(tabNavigation, tabGroup.firstChild);
            document.querySelector('[data-tab-id="' + selectedTabId + '"]').setAttribute('aria-selected', 'true');
        });

        tabGroups.forEach((tabGroup) => {
            const tabs = tabGroup.querySelectorAll(':scope > [data-ui-profiler-tab-navigation] [data-ui-profiler-tab]');
            tabs.forEach((tab) => {
                const tabId = tab.getAttribute('data-tab-id');
                const tabPanel = document.getElementById(tabId);
                tabPanel.setAttribute('role', 'tabpanel');
                tabPanel.setAttribute('aria-labelledby', tabId);
                tabPanel.querySelector('.tab-title').hidden = true;

                if ('true' === tab.getAttribute('aria-selected')) {
                    tabPanel.hidden = false;
                    tab.removeAttribute('tabindex');
                } else {
                    tabPanel.hidden = true;
                    tab.setAttribute('tabindex', '-1');
                }

                tab.addEventListener('click', function (e) {
                    let activeTab = e.target || e.srcElement;

                    while ('button' !== activeTab.tagName.toLowerCase()) {
                        activeTab = activeTab.parentNode;
                    }

                    const siblingTabs = Array.from(activeTab.parentNode.children);
                    siblingTabs.forEach((sibling) => {
                        const siblingTabId = sibling.getAttribute('data-tab-id');
                        const siblingPanel = document.getElementById(siblingTabId);
                        siblingPanel.hidden = true;
                        sibling.removeAttribute('aria-selected');
                        sibling.setAttribute('tabindex', '-1');
                    });

                    activeTab.setAttribute('aria-selected', 'true');
                    activeTab.removeAttribute('tabindex');
                    const activeTabId = activeTab.getAttribute('data-tab-id');
                    document.getElementById(activeTabId).hidden = false;
                });
            });

            tabGroup.setAttribute('data-processed', 'true');
        });
    }

    createTableSearchFields() {
        document.querySelectorAll('[data-ui-profiler-table-search]').forEach((tableWrapper, i) => {
            const searchField = document.createElement('input');
            searchField.type = 'search';
            searchField.placeholder = 'search...';
            searchField.id = `table-search-field-${i}`;
            searchField.classList.add('table-search-field-input');
            searchField.autocapitalize = 'off';
            searchField.autocomplete = 'off';
            searchField.autocorrect = 'off';
            tableWrapper.insertBefore(searchField, tableWrapper.firstChild);

            const labelField = document.createElement('label');
            labelField.htmlFor = `table-search-field-${i}`;
            labelField.classList.add('table-search-field-label');
            labelField.textContent = 'Search inside the contents of the table';
            tableWrapper.insertBefore(labelField, tableWrapper.firstChild);

            searchField.addEventListener('input', () => {
                const query = searchField.value.toLowerCase();
                let allRowsAreHidden = true;
                tableWrapper.querySelectorAll('tbody tr').forEach((row) => {
                    const rowMatchesQuery = row.textContent.toLowerCase().includes(query);
                    row.style.display = rowMatchesQuery ? '' : 'none';

                    if (rowMatchesQuery) {
                        allRowsAreHidden = false;
                    }
                });

                const noResultsElement = tableWrapper.querySelector('.no-results-message');
                if (allRowsAreHidden) {
                    if (null === noResultsElement) {
                        const message = document.createElement('p');
                        message.textContent = 'No results found.';
                        message.classList.add('no-results-message');
                        tableWrapper.appendChild(message);
                    } else {
                        noResultsElement.style.display = '';
                    }
                } else if (null !== noResultsElement) {
                    noResultsElement.style.display = 'none';
                }
            });
        });
    }

    createToggles() {
        document.querySelectorAll('[data-ui-profiler-toggle]:not([data-processed=true])').forEach((toggle) => {
            const elementSelector = toggle.getAttribute('data-ui-profiler-toggle-target')
                ?? toggle.getAttribute('data-toggle-selector');
            const element = elementSelector ? document.querySelector(elementSelector) : null;

            if (!(element instanceof HTMLElement)) {
                return;
            }

            if (toggle.hasAttribute('data-ui-profiler-toggle-open')
                || toggle.hasAttribute('data-toggle-initial') && 'display' === toggle.getAttribute('data-toggle-initial')) {
                element.hidden = false;
                toggle.setAttribute('aria-expanded', 'true');
            } else {
                element.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
            }

            toggle.addEventListener('click', (e) => {
                if (e.target instanceof Element && !toggle.contains(e.target)) {
                    return;
                }

                e.preventDefault();

                if ('' !== window.getSelection().toString()) {
                    return;
                }

                element.hidden = !element.hidden;
                toggle.setAttribute('aria-expanded', element.hidden ? 'false' : 'true');

                const altContent = toggle.getAttribute('data-ui-profiler-toggle-alt')
                    ?? toggle.getAttribute('data-toggle-alt-content');
                if (!altContent) {
                    return;
                }

                if (!toggle.hasAttribute('data-toggle-original-content')) {
                    toggle.setAttribute('data-toggle-original-content', toggle.innerHTML);
                }

                const originalContent = toggle.getAttribute('data-toggle-original-content') ?? '';
                toggle.innerHTML = element.hidden ? originalContent : altContent;
            });

            toggle.setAttribute('data-processed', 'true');
        });
    }

    createCopyToClipboard() {
        if (!navigator.clipboard) {
            return;
        }

        const copyToClipboardElements = document.querySelectorAll('[data-clipboard-text]');

        copyToClipboardElements.forEach((copyToClipboardElement) => {
            copyToClipboardElement.removeAttribute('hidden');

            copyToClipboardElement.addEventListener('click', (e) => {
                e.stopPropagation();

                navigator.clipboard.writeText(copyToClipboardElement.getAttribute('data-clipboard-text'));

                const oldContent = copyToClipboardElement.textContent;

                copyToClipboardElement.textContent = 'Copied!';
                copyToClipboardElement.disabled = true;

                setTimeout(() => {
                    copyToClipboardElement.textContent = oldContent;
                    copyToClipboardElement.disabled = false;
                }, 7000);
            });
        });
    }

    convertDateTimesToUserTimezone() {
        const userTimezoneName = Intl.DateTimeFormat().resolvedOptions().timeZone;

        document.querySelectorAll('time[data-convert-to-user-timezone]').forEach((timeElement) => {
            const iso8601Datetime = timeElement.getAttribute('datetime');
            const dateInUserTimezone = new Date(iso8601Datetime);

            let options = {};
            if (timeElement.hasAttribute('data-render-as-datetime')) {
                options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric',
                };
            } else if (timeElement.hasAttribute('data-render-as-date')) {
                options = { year: 'numeric', month: 'long', day: 'numeric' };
            } else if (timeElement.hasAttribute('data-render-as-time')) {
                options = { hour: 'numeric', minute: 'numeric', second: 'numeric' };
            }

            if (timeElement.hasAttribute('data-render-with-millisecond-precision')) {
                options.fractionalSecondDigits = 3;
            }

            timeElement.textContent = dateInUserTimezone.toLocaleString('en', options);

            if (undefined !== userTimezoneName) {
                const existingTitle = timeElement.getAttribute('title');
                const newTitle = null === existingTitle
                    ? `Date/times shown in your timezone: ${userTimezoneName}`
                    : existingTitle + ` (date/times shown in your timezone: ${userTimezoneName})`;
                timeElement.setAttribute('title', newTitle);
            }
        });
    }
}
