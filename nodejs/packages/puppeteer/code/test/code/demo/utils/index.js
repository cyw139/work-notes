const browser = {
    defaultMethodConfig: [
        { name: 'browserContexts', op: ['show']},
        { name: 'createIncognitoBrowserContext', op: ['show']},
        { name: 'defaultBrowserContext', op: ['show']},
        { name: 'isConnected', op: ['show']},
        { name: 'newPage', op: ['show']},
        { name: 'pages', op: ['show']},
        { name: 'process', op: ['show']},
        { name: 'target', op: ['show']},
        { name: 'targets', op: ['show']},
        { name: 'userAgent', op: ['show']},
        { name: 'version', op: ['show']},
        { name: 'disconnect', op: ['show']},
        { name: 'isConnected', op: ['show']},
        { name: 'wsEndpoint', op: ['show']},
        // { name: 'waitForTarget', op: []},
        // { name: 'close', op: []},
    ],

    show: function(_params = {}, _methodConfig = []) {
        console.log('==browser==')
        const methodConfigs = [...this.defaultMethodConfig, ..._methodConfig]
        console.log(methodConfigs)
        const { browser } = _params
        methodConfigs.forEach(methodConfig => {
            const method = methodConfig.name
            console.info('browser.' + method + '()')
            if (methodConfig.op.some(item => item === 'show')) {
                console.info(browser[method]())
            } else if (methodConfig.op.some(item => item === 'done')) {
                browser[method]()
            }

        });
    }
}
const browserContext = {
    defaultMethodConfig: [
        { name: 'browser', op: ['show']},
        { name: 'clearPermissionOverrides', op: ['show']},
        { name: 'isIncognito', op: ['show']},
        { name: 'newPage', op: ['show']},
        { name: 'overridePermissions', op: []},
        { name: 'pages', op: ['show']},
        { name: 'targets', op: ['show']},
        { name: 'waitForTarget', op: []},
        { name: 'close', op: []},
    ],
    show: function(_params = {}, _methodConfig = []) {
        console.log('==browserContext==')
        console.log(this.defaultMethodConfig)
        const methodConfigs = [...this.defaultMethodConfig, ..._methodConfig]
        console.log(methodConfigs)
        const { browserContexts } = _params
        Array.from(browserContexts).forEach(browserContext => {
            methodConfigs.forEach(methodConfig => {
                const method = methodConfig.name
                methodConfig.op.forEach(op => {
                    console.info('browserContext.' + method + '()')
                    if (op === 'show') {
                        console.info(browserContext[method]())
                    } else if (op === 'done') {
                        browserContext[method]()
                    } else if (op === '') {

                    }
                })
            });
        })
    }
}
const page = {
    defaultMethodConfig: [
        /**
         * Runs `document.querySelector` within the page. If no element matches the
         * selector, the return value resolves to `null`.
         *
         * @remarks
         * Shortcut for {@link Frame.$ | Page.mainFrame().$(selector) }.
         *
         * @param selector - A
         * {@link https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Selectors | selector}
         * to query page for.
         */
        { name: '$', op: ['show']},
        { name: '$$', op: ['show']},
        { name: '$$eval', op: ['show']},
        { name: '$eval', op: ['show']},
        { name: '$x', op: ['show']},
        { name: 'addScriptTag', op: ['show']},
        { name: 'addStyleTag', op: ['show']},
        { name: 'authenticate', op: ['show']},
        { name: 'eval', op: ['show']},
        { name: 'bringToFront', op: ['show']},
        // Get the browser the page belongs to.
        // @todo browser 和 browser context 区别？
        { name: 'browser', op: ['show']},
        // Get the browser context that the page belongs to.
        { name: 'browserContext', op: ['show']},
        { name: 'click', op: ['show']},
        { name: 'close', op: ['show']},
        // The full HTML contents of the page, including the DOCTYPE.
        { name: 'content', op: ['show']},
        { name: 'cookies', op: ['show']},
        { name: 'createCDPSession', op: ['show']},
        { name: 'createPDFStream', op: ['show']},
        { name: 'deleteCookie', op: ['show']},
        { name: 'emulate', op: ['show']},
        { name: 'emulateCPUThrottling', op: ['show']},
        { name: 'emulateIdleState', op: ['show']},
        { name: 'emulateMediaFeatures', op: ['show']},
        { name: 'emulateMediaType', op: ['show']},
        { name: 'emulateNetworkConditions', op: ['show']},
        { name: 'emulateTimezone', op: ['show']},
        { name: 'emulateVisionDeficiency', op: ['show']},
        { name: 'evaluate', op: ['show']},
        { name: 'evaluateHandle', op: ['show']},
        { name: 'evaluateOnNewDocument', op: ['show']},
        { name: 'exposeFunction', op: ['show']},
        { name: 'focus', op: ['show']},
        { name: 'frames', op: ['show']},
        { name: 'getDefaultTimeout', op: ['show']},
        { name: 'goBack', op: ['show']},
        { name: 'goForward', op: ['show']},
        { name: 'goto', op: ['show']},
        // This method fetches an element with selector, scrolls it into view if needed,
        // and then uses Page.mouse to hover over the center of the element.
        // If there's no element matching selector, the method throws an error.
        { name: 'hover', op: ['show']},
        { name: 'isClosed', op: ['show']},
        { name: 'isDragInterceptionEnabled', op: ['show']},
        { name: 'isJavaScriptEnabled', op: ['show']},
        { name: 'isServiceWorkerBypassed', op: ['show']},
        { name: 'locator', op: ['show']},
        // The page's main frame.
        { name: 'mainFrame', op: ['show']},
        { name: 'metrics', op: ['show']},
        { name: 'pdf', op: ['show']},
        { name: 'queryObjects', op: ['show']},
        { name: 'reload', op: ['show']},
        { name: 'removeExposedFunction', op: ['show']},
        { name: 'removeScriptToEvaluateOnNewDocument', op: ['show']},
        { name: 'screencast', op: ['show']},
        { name: 'screenshot', op: ['show']},
        { name: 'select', op: ['show']},
        { name: 'setBypassCSP', op: ['show']},
        { name: 'setBypassServiceWorker', op: ['show']},
        { name: 'setCacheEnabled', op: ['show']},
        { name: 'setContent', op: ['show']},
        { name: 'setCookie', op: ['show']},
        { name: 'setDefaultNavigationTimeout', op: ['show']},
        { name: 'setDefaultTimeout', op: ['show']},
        { name: 'setDragInterception', op: ['show']},
        { name: 'setExtraHTTPHeaders', op: ['show']},
        { name: 'setGeolocation', op: ['show']},
        { name: 'setJavaScriptEnabled', op: ['show']},
        { name: 'setOfflineMode', op: ['show']},
        { name: 'setRequestInterception', op: ['show']},
        { name: 'setUserAgent', op: ['show']},
        { name: 'setViewport', op: ['show']},
        { name: 'tap', op: ['show']},
        { name: 'target', op: ['show']},
        { name: 'title', op: ['show']},
        { name: 'type', op: ['show']},
        { name: 'url', op: ['show']},
        { name: 'viewport', op: ['show']},
        { name: 'waitForDevicePrompt', op: ['show']},
        { name: 'waitForFileChooser', op: ['show']},
        { name: 'waitForFrame', op: ['show']},
        { name: 'waitForFunction', op: ['show']},
        { name: 'waitForNavigation', op: ['show']},
        { name: 'waitForNetworkIdle', op: ['show']},
        { name: 'waitForRequest', op: ['show']},
        { name: 'waitForResponse', op: ['show']},
        { name: 'waitForSelector', op: ['show']},
        { name: 'waitForTimeout', op: ['show']},
        { name: 'waitForXPath', op: ['show']},
        { name: 'workers', op: ['show']},
    ],
    show: function(_params = {}, _methodConfig = []) {
        console.log('==browserContext==')
        console.log(this.defaultMethodConfig)
        const methodConfigs = [...this.defaultMethodConfig, ..._methodConfig]
        console.log(methodConfigs)
        const { browserContexts } = _params
        Array.from(browserContexts).forEach(browserContext => {
            methodConfigs.forEach(methodConfig => {
                const method = methodConfig.name
                methodConfig.op.forEach(op => {
                    console.info('browserContext.' + method + '()')
                    if (op === 'show') {
                        console.info(browserContext[method]())
                    } else if (op === 'done') {
                        browserContext[method]()
                    } else if (op === '') {

                    }
                })
            });
        })
    }
}
const frame = {
    defaultMethodConfig: [
        // Queries the frame for an element matching the given selector.
        /**
         * This method queries the frame for the given selector.
         *
         * @param selector - a selector to query for.
         * @returns A promise which resolves to an `ElementHandle` pointing at the
         * element, or `null` if it was not found.
         */
        { name: '$', op: ['show']},
        // Queries the frame for all elements matching the given selector.
        /**
         * This runs `document.querySelectorAll` in the frame and returns the result.
         *
         * @param selector - a selector to search for
         * @returns An array of element handles pointing to the found frame elements.
         */
        { name: '$$', op: ['show']},
        /**
         * @remarks
         *
         * This method runs `Array.from(document.querySelectorAll(selector))` within
         * the frame and passes it as the first argument to `pageFunction`.
         *
         * If `pageFunction` returns a Promise, then `frame.$$eval` would wait for
         * the promise to resolve and return its value.
         *
         * @example
         *
         * ```js
         * const divsCounts = await frame.$$eval('div', divs => divs.length);
         * ```
         *
         * @param selector - the selector to query for
         * @param pageFunction - the function to be evaluated in the frame's context
         * @param args - additional arguments to pass to `pageFuncton`
         */
        { name: '$$eval', op: ['show']},
        /**
         * @remarks
         *
         * This method runs `document.querySelector` within
         * the frame and passes it as the first argument to `pageFunction`.
         *
         * If `pageFunction` returns a Promise, then `frame.$eval` would wait for
         * the promise to resolve and return its value.
         *
         * @example
         *
         * ```js
         * const searchValue = await frame.$eval('#search', el => el.value);
         * ```
         *
         * @param selector - the selector to query for
         * @param pageFunction - the function to be evaluated in the frame's context
         * @param args - additional arguments to pass to `pageFuncton`
         */
        { name: '$eval', op: ['show']},
        /**
         * This method evaluates the given XPath expression and returns the results.
         *
         * @param expression - the XPath expression to evaluate.
         */
        { name: '$x', op: ['show']},
        /**
         * Adds a `<script>` tag into the page with the desired url or content.
         *
         * @param options - configure the script to add to the page.
         *
         * @returns a promise that resolves to the added tag when the script's
         * `onload` event fires or when the script content was injected into the
         * frame.
         */
        { name: 'addScriptTag', op: ['show']},
        { name: 'addStyleTag', op: ['show']},
        { name: 'childFrames', op: ['show']},
        { name: 'click', op: ['show']},
        { name: 'content', op: ['show']},
        { name: 'evaluate', op: ['show']},
        { name: 'evaluateHandle', op: ['show']},
        { name: 'focus', op: ['show']},
        { name: 'goto', op: ['show']},
        { name: 'hover', op: ['show']},
        { name: 'isDetached', op: ['show']},
        { name: 'isOOPFrame', op: ['show']},
        { name: 'locator', op: ['show']},
        { name: 'name', op: ['show']},
        { name: 'page', op: ['show']},
        { name: 'parentFrame', op: ['show']},
        { name: 'select', op: ['show']},
        { name: 'setContent', op: ['show']},
        { name: 'tap', op: ['show']},
        { name: 'title', op: ['show']},
        { name: 'type', op: ['show']},
        { name: 'url', op: ['show']},
        { name: 'waitForFunction', op: ['show']},
        { name: 'waitForNavigation', op: ['show']},
        { name: 'waitForSelector', op: ['show']},
        { name: 'waitForTimeout', op: ['show']},
        { name: 'waitForXPath', op: ['show']},
    ]
}
exports.my_puppeteer = {
    browser,
    browserContext,
    page,
    frame,
}