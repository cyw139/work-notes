# 100个puppeteer问题汇总
## 1、代码运行后，浏览器没有运行起来
### 1.1、代码
```javascript
import puppeteer from 'puppeteer';

(async () => {
    // Launch the browser and open a new blank page
    const browser = await puppeteer.launch({executablePath: 'D:\\FreeBrower\\Brower\\resources\\chrome\\115.0.5735.45\\chrome.exe'});
    const page = await browser.newPage();

    // Navigate the page to a URL
    await page.goto('https://developer.chrome.com/');

    // Set screen size
    await page.setViewport({width: 1080, height: 1024});

    // Type into search box
    await page.type('.search-box__input', 'automate beyond recorder');

    // Wait and click on first result
    const searchResultSelector = '.search-box__link';
    await page.waitForSelector(searchResultSelector);
    await page.click(searchResultSelector);

    // Locate the full title with a unique string
    const textSelector = await page.waitForSelector(
        'text/Customize and automate'
    );
    const fullTitle = await textSelector?.evaluate(el => el.textContent);

    // Print the full title
    console.log('The title of this blog post is "%s".', fullTitle);

    await browser.close();
})();
```
### 1.2、现象
执行到 await puppeteer.launch 会卡住
### 1.3、解决
每个puppeteer 版本都对应一个浏览器版本。根据官网：
[Chromium Support | Puppeteer (pptr.dev)](https://pptr.dev/chromium-support)
电脑上的chrome 为低版本的就要安装对应版本的puppeteer。
- [ ] [blog.csdn.net-旧版chrome](https://blog.csdn.net/cy5849203/article/details/130855429)
- [ ] [win版-chrome](https://chromiumdash.appspot.com/releases?platform=Windows)
- [ ] [chromium projects](https://www.chromium.org/getting-involved/download-chromium/)
- [ ] [chromium 历史版本](https://github.com/vikyd/note/blob/master/chrome_offline_download.md#chrome-%E7%A6%BB%E7%BA%BF%E5%8C%85---%E5%8E%86%E5%8F%B2%E7%89%88%E6%9C%AC%E5%AE%98%E6%96%B9)
- [ ] [知乎下载chrome老版本](https://zhuanlan.zhihu.com/p/339042765)