/**
 * Form collector panel — tree navigation and expand/collapse toggles.
 */

class UiProfilerFormPanel {
    #activeTreeItem;
    #activeTreePanel;
    #storage;
    #storageKey = 'sf_toggle_data';
    #togglerStates = {};

    constructor() {
        this.#storage = sessionStorage;
        this.#initTrees();
        this.#initTogglerButtons();
    }

    #initTrees() {
        const treeItems = document.querySelectorAll('.tree .tree-inner');
        treeItems.forEach((treeItem) => {
            const targetId = treeItem.getAttribute('data-tab-target-id');
            const target = document.getElementById(targetId);

            if (!target) {
                throw `Tab target ${targetId} does not exist`;
            }

            treeItem.addEventListener('click', (e) => {
                this.#selectTreeItem(treeItem);

                e.stopPropagation();
                return false;
            });

            target.classList.add('hidden');
        });

        if (treeItems.length > 0) {
            this.#selectTreeItem(treeItems[0]);
        }
    }

    #selectTreeItem(treeItem) {
        const treePanelId = treeItem.getAttribute('data-tab-target-id');
        const treePanel = document.getElementById(treePanelId);

        if (!treePanel) {
            throw `The tree panel ${treePanelId} does not exist`;
        }

        if (this.#activeTreeItem) {
            this.#activeTreeItem.classList.remove('active');
        }

        if (this.#activeTreePanel) {
            this.#activeTreePanel.classList.add('hidden');
        }

        treeItem.classList.add('active');
        treePanel.classList.remove('hidden');

        this.#activeTreeItem = treeItem;
        this.#activeTreePanel = treePanel;
    }

    #initTogglerButtons() {
        this.#togglerStates = this.#getTogglerStates();
        if (!this.#isObject(this.#togglerStates)) {
            this.#togglerStates = {};
        }

        const buttons = document.querySelectorAll('.toggle-button');
        buttons.forEach((button) => {
            const targetId = button.getAttribute('data-toggle-target-id');
            const target = document.getElementById(targetId);

            if (!target) {
                throw `Toggle target ${targetId} does not exist`;
            }

            if (target.classList.contains('hidden')) {
                button.classList.add('closed');
            }

            button.addEventListener('click', (e) => {
                this.#toggleToggler(button);

                e.stopPropagation();
                return false;
            });

            if (Object.prototype.hasOwnProperty.call(this.#togglerStates, targetId)) {
                if (0 === this.#togglerStates[targetId]) {
                    this.#collapseToggler(button);
                } else {
                    this.#expandToggler(button);
                }
            }
        });
    }

    #isTogglerCollapsed(button) {
        return button.classList.contains('closed');
    }

    #isTogglerExpanded(button) {
        return !this.#isTogglerCollapsed(button);
    }

    #expandToggler(button) {
        const targetId = button.getAttribute('data-toggle-target-id');
        const target = document.getElementById(targetId);

        if (!target) {
            throw 'Toggle target ' + targetId + ' does not exist';
        }

        if (this.#isTogglerCollapsed(button)) {
            button.classList.remove('closed');
            target.classList.remove('hidden');

            this.#togglerStates[targetId] = 1;
            this.#saveTogglerStates();
        }
    }

    #collapseToggler(button) {
        const targetId = button.getAttribute('data-toggle-target-id');
        const target = document.getElementById(targetId);

        if (!target) {
            throw `Toggle target ${targetId} does not exist`;
        }

        if (this.#isTogglerExpanded(button)) {
            button.classList.add('closed');
            target.classList.add('hidden');

            this.#togglerStates[targetId] = 0;
            this.#saveTogglerStates();
        }
    }

    #toggleToggler(button) {
        if (button.classList.contains('closed')) {
            this.#expandToggler(button);
        } else {
            this.#collapseToggler(button);
        }
    }

    #saveTogglerStates() {
        this.#storage.setItem(this.#storageKey, JSON.stringify(this.#togglerStates));
    }

    #getTogglerStates() {
        const data = this.#storage.getItem(this.#storageKey);

        if (null !== data) {
            try {
                return JSON.parse(data);
            } catch (e) {
            }
        }

        return {};
    }

    #isObject(variable) {
        return null !== variable && 'object' === typeof variable && !Array.isArray(variable);
    }
}

export function bootFormPanel() {
    const run = () => {
        new UiProfilerFormPanel();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
}
