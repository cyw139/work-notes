# 03_Browser
Browser represents a browser instance that is either:
- [ ] connected to via `Puppeteer.connect()` or - launched by `PuppeteerNode.launch()`.
- [ ] Browser [emits](https://pptr.dev/api/puppeteer.eventemitter) various events which are documented in the [BrowserEvent](https://pptr.dev/api/puppeteer.browserevent) enum.
- [ ] The constructor for this class is marked as internal. Third-party code should not call the constructor directly or create subclasses that extend the Browser class.
```javascript
export declare abstract class Browser extends EventEmitter<BrowserEvents>
class Browser {
    // Whether Puppeteer is connected to this browser.
    private connected = false; // readonly boolean, 

    // Gets a list of open browser contexts.
    // In a newly-created browser, this will return a single instance of BrowserContext.
    abstract browserContexts(): BrowserContext[];
    // Closes this browser and all associated pages.
    abstract close(): Promise<void>;
}
```
### 1.案例1: Using a Browser to create a Page:
```javascript
import puppeteer from 'puppeteer';

const browser = await puppeteer.launch();
const page = await browser.newPage();
await page.goto('https://example.com');
await browser.close();
```
### 2.案例2：Disconnecting from and reconnecting to a Browser:
```javascript
import puppeteer from 'puppeteer';

const browser = await puppeteer.launch();
// Store the endpoint to be able to reconnect to the browser.
const browserWSEndpoint = browser.wsEndpoint();
// Disconnect puppeteer from the browser.
await browser.disconnect();

// Use the endpoint to reestablish a connection
const browser2 = await puppeteer.connect({browserWSEndpoint});
// Close the browser.
await browser2.close();
```
## 03_01_Browser.browserContexts
Gets a list of open browser contexts.

In a newly-created browser, this will return a single instance of BrowserContext.
## 03_02_Browser.close
Closes this browser and all associated pages.
## 03_03_Browser.createlncognitoBrowserContext
## 03_04_Browser.defaultBrowserContext
## 03_05_Browser.disconnect
## 03_06_Browser.isConnected
## 03_07_Browser.newPage
## 03_08_Browser.pages
## 03_09_Browser.process
## 03_10_Browser.target
## 03_11_Browser.targets
## 03_12_Browser.userAgent
## 03_13_Browser.version
## 03_14_Browser.waitForTarget
## 03_15_Browser.wsEndpoint