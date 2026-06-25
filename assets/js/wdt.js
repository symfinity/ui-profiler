/**
 * WDT runtime entry — single file intentionally (Symfony AssetMapper skips relative
 * import rewriting for vendor assets; split modules 404 in dogfood/dev).
 */

const noop = () => {};

function hydrateToolbar(container, html) {
    container.innerHTML = html;
}

function extractHeaders(xhr, stackElement) {
    const allHeaders = xhr.getAllResponseHeaders();
    let match;

    if ((match = allHeaders.match(/^x-debug-token:\s+(.*)$/im))) {
        stackElement.profile = match[1];
    }
    if ((match = allHeaders.match(/^x-debug-token-link:\s+(.*)$/im))) {
        stackElement.profilerUrl = match[1];
    }
    if ((match = allHeaders.match(/^symfony-debug-toolbar-replace:\s+(.*)$/im))) {
        stackElement.toolbarReplace = '1' === match[1];
        stackElement.toolbarReplaceFinished = false;
    }
}

function createAjaxRuntime(config, callbacks) {
    const requestStack = [];
    let pendingRequests = 0;
    let successStreak = 4;
    const excludedPattern = new RegExp(config.excludedAjaxPaths);

    const renderAjaxRequests = () => {
        const requestCounter = document.querySelector('[data-ui-profiler-wdt-ajax-counter]');
        if (!requestCounter) {
            return;
        }
        requestCounter.textContent = String(requestStack.length);

        const infoSpan = document.querySelector('[data-ui-profiler-wdt-ajax-info]');
        if (infoSpan) {
            infoSpan.textContent = requestStack.length + ' AJAX request' + (1 !== requestStack.length ? 's' : '');
        }

        const ajaxToolbarPanel = document.querySelector('[data-ui-profiler-wdt-item][data-ui-profiler-collector="ajax"]');
        if (!ajaxToolbarPanel) {
            return;
        }

        if (requestStack.length) {
            ajaxToolbarPanel.style.display = '';
        } else {
            ajaxToolbarPanel.style.display = 'none';
        }

        if (pendingRequests > 0) {
            ajaxToolbarPanel.setAttribute('data-ajax-state', 'loading');
        } else if (successStreak < 4) {
            ajaxToolbarPanel.setAttribute('data-ui-status', 'danger');
            ajaxToolbarPanel.removeAttribute('data-ajax-state');
        } else {
            ajaxToolbarPanel.removeAttribute('data-ajax-state');
            ajaxToolbarPanel.removeAttribute('data-ui-status');
        }
    };

    const startAjaxRequest = (index) => {
        const tbody = document.querySelector('[data-ui-profiler-wdt-ajax-list]');
        if (!tbody) {
            return;
        }

        if (tbody.rows.length >= 100) {
            tbody.deleteRow(0);
        }

        const request = requestStack[index];
        pendingRequests++;
        const row = document.createElement('tr');
        request.DOMNode = row;

        const requestNumberCell = document.createElement('td');
        requestNumberCell.textContent = String(index + 1);
        row.appendChild(requestNumberCell);

        const profilerCell = document.createElement('td');
        profilerCell.textContent = 'n/a';
        row.appendChild(profilerCell);

        const methodCell = document.createElement('td');
        methodCell.textContent = request.method;
        row.appendChild(methodCell);

        const typeCell = document.createElement('td');
        typeCell.textContent = request.type;
        row.appendChild(typeCell);

        const statusCodeCell = document.createElement('td');
        const statusCode = document.createElement('span');
        statusCode.setAttribute('data-ui-role', 'badge');
        statusCode.setAttribute('data-ui-size', 'sm');
        statusCode.setAttribute('data-ui-variant', 'neutral');
        statusCode.textContent = 'n/a';
        statusCodeCell.appendChild(statusCode);
        row.appendChild(statusCodeCell);

        const pathCell = document.createElement('td');
        pathCell.setAttribute('data-ui-profiler-wdt-ajax-url', '');
        if ('GET' === request.method) {
            const pathLink = document.createElement('a');
            pathLink.setAttribute('href', request.url);
            pathLink.textContent = request.url;
            pathCell.appendChild(pathLink);
        } else {
            pathCell.textContent = request.url;
        }
        pathCell.setAttribute('title', request.url);
        row.appendChild(pathCell);

        const durationCell = document.createElement('td');
        durationCell.setAttribute('data-ui-profiler-wdt-ajax-duration', '');
        durationCell.textContent = 'n/a';
        row.appendChild(durationCell);

        request.liveDurationHandle = setInterval(() => {
            durationCell.textContent = `${new Date() - request.start} ms`;
        }, 100);

        row.setAttribute('data-ajax-state', 'loading');
        tbody.insertBefore(row, null);

        const toolbarInfo = document.querySelector('[data-ui-profiler-collector="ajax"] [data-ui-profiler-wdt-panel]');
        if (toolbarInfo) {
            toolbarInfo.scrollTop = toolbarInfo.scrollHeight;
        }

        renderAjaxRequests();
    };

    const finishAjaxRequest = (index) => {
        const request = requestStack[index];
        if (request.liveDurationHandle) {
            clearInterval(request.liveDurationHandle);
        }

        if (!request.DOMNode) {
            return;
        }

        if (request.toolbarReplace && !request.toolbarReplaceFinished && request.profile) {
            request.toolbarReplaceFinished = true;
            for (let elem = request.DOMNode; elem && elem !== document; elem = elem.parentNode) {
                if (elem.id && elem.id.match(/^sfwdt/)) {
                    callbacks.onToolbarReplace(elem.id.replace(/^sfwdt/, ''), request.profile);
                    break;
                }
            }
        }

        if (pendingRequests > 0) {
            pendingRequests--;
        }

        const row = request.DOMNode;
        const profilerCell = row.children[1];
        const methodCell = row.children[2];
        const statusCodeCell = row.children[4];
        const statusCodeElem = statusCodeCell.children[0];
        const durationCell = row.children[6];

        if (request.error) {
            row.setAttribute('data-ajax-state', 'error');
            methodCell.setAttribute('data-ajax-state', 'error');
            successStreak = 0;
        } else {
            row.setAttribute('data-ajax-state', 'ok');
            successStreak++;
        }

        if (request.statusCode) {
            if (request.statusCode < 300) {
                statusCodeElem.setAttribute('data-ui-role', 'badge');
                statusCodeElem.setAttribute('data-ui-size', 'sm');
                statusCodeElem.setAttribute('data-ui-variant', 'neutral');
            } else if (request.statusCode < 400) {
                statusCodeElem.setAttribute('data-ui-role', 'badge');
                statusCodeElem.setAttribute('data-ui-size', 'sm');
                statusCodeElem.setAttribute('data-ui-variant', 'warning');
            } else {
                statusCodeElem.setAttribute('data-ui-role', 'badge');
                statusCodeElem.setAttribute('data-ui-size', 'sm');
                statusCodeElem.setAttribute('data-ui-variant', 'danger');
            }
            statusCodeElem.textContent = String(request.statusCode);
        } else {
            statusCodeElem.setAttribute('data-ui-role', 'badge');
            statusCodeElem.setAttribute('data-ui-size', 'sm');
            statusCodeElem.setAttribute('data-ui-variant', 'danger');
        }

        if (request.duration) {
            durationCell.textContent = `${request.duration} ms`;
        }

        if (request.profilerUrl) {
            profilerCell.textContent = '';
            const profilerLink = document.createElement('a');
            profilerLink.setAttribute('href', request.profilerUrl);
            profilerLink.textContent = request.profile;
            profilerCell.appendChild(profilerLink);
        }

        renderAjaxRequests();
    };

    const shouldTrack = (url) => {
        let path = url;
        if (url.slice(0, 1) === '/') {
            if (0 === url.indexOf(config.basePath)) {
                path = url.slice(config.basePath.length);
            }
        } else if (0 === url.indexOf(config.schemeAndHostBase)) {
            path = url.slice(config.schemeAndHostBase.length);
        }

        return !path.match(excludedPattern);
    };

    const pushRequest = (stackElement) => {
        const idx = requestStack.push(stackElement) - 1;
        startAjaxRequest(idx);
        return idx;
    };

    const patchFetch = () => {
        if (!window.fetch || undefined !== window.fetch.polyfill) {
            return;
        }
        const oldFetch = window.fetch;
        window.fetch = function (...args) {
            const promise = oldFetch.apply(this, args);
            let url = args[0];
            let params = args[1];
            if (Object.prototype.toString.call(args[0]) === '[object Request]') {
                url = args[0].url;
                params = {
                    method: args[0].method,
                };
            } else {
                url = String(url);
            }

            if (shouldTrack(url)) {
                const method = params && undefined !== params.method ? params.method : 'GET';
                const stackElement = {
                    error: false,
                    url,
                    method,
                    type: 'fetch',
                    start: new Date(),
                };
                const idx = pushRequest(stackElement);
                promise.then(
                    (r) => {
                        stackElement.duration = new Date() - stackElement.start;
                        stackElement.error = r.status < 200 || r.status >= 400;
                        stackElement.statusCode = r.status;
                        stackElement.profile = r.headers.get('x-debug-token');
                        stackElement.profilerUrl = r.headers.get('x-debug-token-link');
                        stackElement.toolbarReplaceFinished = false;
                        stackElement.toolbarReplace = '1' === r.headers.get('Symfony-Debug-Toolbar-Replace');
                        finishAjaxRequest(idx);
                    },
                    () => {
                        stackElement.error = true;
                        finishAjaxRequest(idx);
                    },
                );
            }

            return promise;
        };
        Object.defineProperties(window.fetch, Object.getOwnPropertyDescriptors(oldFetch));
    };

    const patchXhr = () => {
        if (!window.XMLHttpRequest || !XMLHttpRequest.prototype.addEventListener) {
            return;
        }
        const proxied = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function (method, url, ...rest) {
            const self = this;
            if (shouldTrack(String(url))) {
                const stackElement = {
                    error: false,
                    url: String(url),
                    method,
                    type: 'xhr',
                    start: new Date(),
                };
                const idx = pushRequest(stackElement);
                this.addEventListener(
                    'readystatechange',
                    function () {
                        if (4 === self.readyState) {
                            stackElement.duration = new Date() - stackElement.start;
                            stackElement.error = self.status < 200 || self.status >= 400;
                            stackElement.statusCode = self.status;
                            extractHeaders(self, stackElement);
                            finishAjaxRequest(idx);
                        }
                    },
                    false,
                );
            }
            return proxied.apply(this, [method, url, ...rest]);
        };
    };

    const patchEventSource = () => {
        if (!window.EventSource) {
            return;
        }
        const oldEventSource = window.EventSource;
        function patchedEventSource(url, options) {
            const es = new oldEventSource(url, options);
            if (shouldTrack(url.toString())) {
                const stackElement = {
                    error: false,
                    url: url.toString(),
                    method: 'GET',
                    type: 'event-stream',
                    start: new Date(),
                };
                const idx = pushRequest(stackElement);
                addEventListener(es, 'error', () => {
                    stackElement.error = true;
                    finishAjaxRequest(idx);
                });
                addEventListener(es, 'open', () => {
                    stackElement.statusCode = 200;
                    stackElement.toolbarReplaceFinished = false;
                    stackElement.toolbarReplace = true;
                });
                addEventListener(es, 'symfony:debug:started', (event) => {
                    const items = event.data.split('\n');
                    stackElement.profile = items[0];
                    stackElement.profilerUrl = items[1];
                });
                addEventListener(es, 'symfony:debug:error', (event) => {
                    stackElement.error = true;
                    stackElement.statusCode = event.data;
                    finishAjaxRequest(idx);
                });
                addEventListener(es, 'symfony:debug:finished', () => {
                    stackElement.duration = new Date() - stackElement.start;
                    stackElement.toolbarReplaceFinished = false;
                    stackElement.toolbarReplace = true;
                    finishAjaxRequest(idx);
                });
            }
            return es;
        }
        Object.defineProperties(patchedEventSource, Object.getOwnPropertyDescriptors(oldEventSource));
        window.EventSource = patchedEventSource;
    };

    const wireToolbarAjaxUi = (token) => {
        renderAjaxRequests();
        const clearButton = document.querySelector('[data-ui-profiler-wdt-ajax-clear]');
        if (clearButton) {
            addEventListener(clearButton, 'click', () => {
                requestStack.length = 0;
                renderAjaxRequests();
                successStreak = 4;
                const list = document.querySelector('[data-ui-profiler-wdt-ajax-list]');
                if (list) {
                    list.innerHTML = '';
                }
            });
        }
        const ajaxBlock = document.querySelector('[data-ui-profiler-wdt-item][data-ui-profiler-collector="ajax"]');
        if (ajaxBlock) {
            addEventListener(ajaxBlock, 'mouseenter', () => {
                const elem = document.querySelector('[data-ui-profiler-collector="ajax"] [data-ui-profiler-wdt-panel]');
                if (elem) {
                    elem.scrollTop = elem.scrollHeight;
                }
            });
            const icon = document.querySelector('[data-ui-profiler-collector="ajax"] [data-ui-profiler-wdt-trigger]');
            if (icon) {
                addEventListener(icon, 'click', (event) => {
                    event.preventDefault();
                    ajaxBlock.toggleAttribute('data-ui-hover');
                });
            }
        }
        const dumpInfo = document.querySelector('[data-ui-profiler-collector="dump"] [data-ui-profiler-wdt-panel]');
        if (null !== dumpInfo) {
            addEventListener(dumpInfo, 'sfbeforedumpcollapse', () => {
                dumpInfo.style.minHeight = `${dumpInfo.getBoundingClientRect().height}px`;
            });
            addEventListener(dumpInfo, 'mouseleave', () => {
                dumpInfo.style.minHeight = '';
            });
        }
        void token;
    };

    return {
        install: () => {
            patchEventSource();
            patchFetch();
            patchXhr();
        },
        wireToolbarAjaxUi,
        renderAjaxRequests,
    };
}

function hasClass(el, cssClass) {
    if ('classList' in document.documentElement) {
        return el.classList.contains(cssClass);
    }
    return !!el.className.match(new RegExp(`\\b${cssClass}\\b`));
}

function removeClass(el, cssClass) {
    if ('classList' in document.documentElement) {
        el.classList.remove(cssClass);
    } else {
        el.className = el.className.replace(new RegExp(`\\b${cssClass}\\b`), ' ');
    }
}

function addClass(el, cssClass) {
    if ('classList' in document.documentElement) {
        el.classList.add(cssClass);
    } else if (!hasClass(el, cssClass)) {
        el.className += ` ${cssClass}`;
    }
}

function addEventListener(element, eventName, callback) {
    if (!element) {
        return;
    }
    if ('addEventListener' in element) {
        element.addEventListener(eventName, callback, false);
    } else if ('attachEvent' in element) {
        element.attachEvent(`on${eventName}`, callback);
    }
}

function request(url, onSuccess, onError, payload, options, tries) {
    const parsed = new URL(url, window.location.origin);
    parsed.searchParams.set('XDEBUG_IGNORE', '1');
    url = parsed.toString();

    const xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    options = options || {};
    options.retry = options.retry || false;
    tries = tries || 1;
    const delay = tries < 5 ? (tries - 0.5) * 250 : 1000;

    xhr.open(options.method || 'GET', url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function () {
        if (4 !== xhr.readyState) {
            return null;
        }

        if (404 === xhr.status && options.retry && !options.stop) {
            setTimeout(() => {
                if (options.stop) {
                    return;
                }
                request(url, onSuccess, onError, payload, options, tries + 1);
            }, delay);
            return null;
        }

        if (200 === xhr.status) {
            (onSuccess || noop)(xhr);
        } else {
            (onError || noop)(xhr);
        }
    };

    if (options.onSend) {
        options.onSend(tries);
    }

    xhr.send(payload || '');
}

export class UiProfilerWdt {
    static boot(config) {
        const runtime = new UiProfilerWdt(config);
        runtime.loadToolbar(config.token);
        return runtime;
    }

    constructor(config) {
        this.config = config;
        this.profilerStorageKey = 'symfony/profiler/';
        this.ajax = createAjaxRuntime(config, {
            onToolbarReplace: (token, newToken) => this.loadToolbar(token, newToken),
        });
        this.ajax.install();
    }

    getPreference(name) {
        if (!window.localStorage) {
            return null;
        }
        return localStorage.getItem(this.profilerStorageKey + name);
    }

    setPreference(name, value) {
        if (!window.localStorage) {
            return null;
        }
        return localStorage.setItem(this.profilerStorageKey + name, value);
    }

    getSfwdt(token) {
        return document.getElementById(`sfwdt${token}`);
    }

    showToolbar(token) {
        const sfwdt = this.getSfwdt(token);
        const root = document.getElementById(`sfTB-${token}`);
        const toggleButton = document.querySelector(`#sfToolbarToggleButton-${token}`);
        if (!sfwdt || !sfwdt.parentNode) {
            return;
        }

        if ('closed' === this.getPreference('toolbar/displayState')) {
            if (root) {
                root.setAttribute('data-ui-profiler-wdt-state', 'closed');
            }
            if (toggleButton) {
                toggleButton.setAttribute('aria-expanded', 'false');
            }
        } else {
            if (root) {
                root.setAttribute('data-ui-profiler-wdt-state', 'open');
            }
            if (toggleButton) {
                toggleButton.setAttribute('aria-expanded', 'true');
            }
        }
    }

    hideToolbar(token) {
        const sfwdt = this.getSfwdt(token);
        const root = document.getElementById(`sfTB-${token}`);
        const toggleButton = document.querySelector(`#sfToolbarToggleButton-${token}`);
        if (!sfwdt || !sfwdt.parentNode) {
            return;
        }
        if (root) {
            root.setAttribute('data-ui-profiler-wdt-state', 'closed');
        }
        if (toggleButton) {
            toggleButton.setAttribute('aria-expanded', 'false');
        }
    }

    initToolbar(token) {
        this.showToolbar(token);
        const toggleButton = document.querySelector(`#sfToolbarToggleButton-${token}`);
        if (!toggleButton || '1' === toggleButton.dataset.uiProfilerWdtBound) {
            return;
        }

        toggleButton.dataset.uiProfilerWdtBound = '1';
        addEventListener(toggleButton, 'click', (event) => {
            event.preventDefault();
            const newState = 'closed' === this.getPreference('toolbar/displayState') ? 'opened' : 'closed';
            this.setPreference('toolbar/displayState', newState);
            'opened' === newState ? this.showToolbar(token) : this.hideToolbar(token);
        });
    }

    positionToolbarBlocks(el) {
        const toolbarBlocks = [...el.querySelectorAll('[data-ui-profiler-wdt-item]')];
        for (const block of toolbarBlocks) {
            block.onmouseover = function () {
                const toolbarInfo = this.querySelector('[data-ui-profiler-wdt-panel]');
                if (!toolbarInfo) {
                    return;
                }
                const pageWidth = document.body.clientWidth;
                const elementWidth = toolbarInfo.offsetWidth;
                const leftValue = elementWidth + this.offsetLeft - pageWidth;
                const rightValue = elementWidth + (pageWidth - this.offsetLeft) - pageWidth;
                toolbarInfo.style.right = '';
                toolbarInfo.style.left = '';
                if (elementWidth > pageWidth) {
                    toolbarInfo.style.left = '0';
                } else if (leftValue > 0 && rightValue > 0) {
                    toolbarInfo.style.right = `${rightValue * -1}px`;
                } else if (leftValue < 0) {
                    toolbarInfo.style.left = '0';
                } else {
                    toolbarInfo.style.right = '0px';
                }
            };
        }
    }

    load(selector, url, onSuccess, onError, options) {
        const el = document.getElementById(selector);
        if (el && el.getAttribute('data-sfurl') !== url) {
            request(
                url,
                (xhr) => {
                    el.style.visibility = 'hidden';
                    hydrateToolbar(el, xhr.responseText);
                    el.setAttribute('data-sfurl', url);
                    removeClass(el, 'loading');
                    (onSuccess || noop)(xhr, el);
                    el.style.visibility = '';
                },
                (xhr) => (onError || noop)(xhr, el),
                '',
                options,
            );
        }
        return this;
    }

    renderErrorToolbar(token, newToken) {
        const sfwdt = this.getSfwdt(token);
        if (!sfwdt) {
            return;
        }
        sfwdt.innerHTML = `
            <div data-ui-profiler-wdt class="ui-profiler-wdt notranslate">
                <div data-ui-profiler-wdt-icon><svg width="26" height="28" xmlns="http://www.w3.org/2000/svg" version="1.1" x="0px" y="0px" viewBox="0 0 26 28" enable-background="new 0 0 26 28" xml:space="preserve"><path fill="currentColor" d="M13 0C5.8 0 0 5.8 0 13c0 7.2 5.8 13 13 13c7.2 0 13-5.8 13-13C26 5.8 20.2 0 13 0z M20 7.5 c-0.6 0-1-0.3-1-0.9c0-0.2 0-0.4 0.2-0.6c0.1-0.3 0.2-0.3 0.2-0.4c0-0.3-0.5-0.4-0.7-0.4c-2 0.1-2.5 2.7-2.9 4.8l-0.2 1.1 c1.1 0.2 1.9 0 2.4-0.3c0.6-0.4-0.2-0.8-0.1-1.3C18 9.2 18.4 9 18.7 8.9c0.5 0 0.8 0.5 0.8 1c0 0.8-1.1 2-3.3 1.9 c-0.3 0-0.5 0-0.7-0.1L15 14.1c-0.4 1.7-0.9 4.1-2.6 6.2c-1.5 1.8-3.1 2.1-3.8 2.1c-1.3 0-2.1-0.6-2.2-1.6c0-0.9 0.8-1.4 1.3-1.4 c0.7 0 1.2 0.5 1.2 1.1c0 0.5-0.2 0.6-0.4 0.7c-0.1 0.1-0.3 0.2-0.3 0.4c0 0.1 0.1 0.3 0.4 0.3c0.5 0 0.9-0.3 1.2-0.5 c1.3-1 1.7-2.9 2.4-6.2l0.1-0.8c0.2-1.1 0.5-2.3 0.8-3.5c-0.9-0.7-1.4-1.5-2.6-1.8c-0.8-0.2-1.3 0-1.7 0.4C8.4 10 8.6 10.7 9 11.1 l0.7 0.7c0.8 0.9 1.3 1.7 1.1 2.7c-0.3 1.6-2.1 2.8-4.3 2.1c-1.9-0.6-2.2-1.9-2-2.7c0.2-0.6 0.7-0.8 1.2-0.6 c0.5 0.2 0.7 0.8 0.6 1.3c0 0.1 0 0.1-0.1 0.3C6 15 5.9 15.2 5.9 15.3c-0.1 0.4 0.4 0.7 0.8 0.8c0.8 0.3 1.7-0.2 1.9-0.9 c0.2-0.6-0.2-1.1-0.4-1.2l-0.8-0.9c-0.4-0.4-1.2-1.5-0.8-2.8c0.2-0.5 0.5-1 0.9-1.4c1-0.7 2-0.8 3-0.6c1.3 0.4 1.9 1.2 2.8 1.9 c0.5-1.3 1.1-2.6 2-3.8c0.9-1 2-1.7 3.3-1.8C20 4.8 21 5.4 21 6.3C21 6.7 20.8 7.5 20 7.5z"/></svg></div>
                An error occurred while loading the web debug toolbar. <a href="${this.config.profilerHomeUrl}${newToken}">Open the web profiler.</a>
            </div>
        `;
        const root = sfwdt.parentElement;
        if (root) {
            root.classList.add('ui-profiler-wdt--error');
        }
    }

    loadToolbar(token, newToken) {
        this.showToolbar(token);

        const triesCounter = document.getElementById(`sfLoadCounter-${token}`);
        const options = {
            retry: true,
            onSend: (count) => {
                if (3 === count) {
                    this.showToolbar(token);
                }
                if (triesCounter) {
                    triesCounter.textContent = String(count);
                }
            },
        };

        const cancelButton = document.getElementById(`sfLoadCancel-${token}`);
        if (cancelButton) {
            addEventListener(cancelButton, 'click', (event) => {
                event.preventDefault();
                options.stop = true;
                this.hideToolbar(token);
            });
        }

        newToken = newToken || token;
        const wdtUrl = this.config.wdtUrl.replace(/xxxxxx/, newToken);

        this.load(
            `sfwdt${token}`,
            wdtUrl,
            (xhr, el) => {
                const toolbarContent = document.getElementById(`sfToolbarMainContent-${newToken}`);
                if (!toolbarContent) {
                    return;
                }

                if (toolbarContent.parentElement) {
                    toolbarContent.parentElement.id = `sfwdt${newToken}`;
                }

                el.style.display = -1 !== xhr.responseText.indexOf('data-ui-profiler-wdt') ? 'block' : 'none';
                if ('none' === el.style.display) {
                    return;
                }

                this.initToolbar(newToken);
                this.positionToolbarBlocks(el);
                this.ajax.wireToolbarAjaxUi(newToken);
            },
            (xhr) => {
                if (0 !== xhr.status && !options.stop) {
                    this.renderErrorToolbar(token, newToken);
                }
            },
            options,
        );

        return this;
    }
}
