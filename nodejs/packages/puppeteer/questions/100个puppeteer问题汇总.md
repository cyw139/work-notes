# 100个puppeteer问题汇总
## 5、无法获取 iframe的解决办法
- 问题描述：puppeteer 无法获取到指定的 iframe。
  使用 puppeteer 的 page 获取页面 iframe 的时候，有时候可能获取不到自己想要的iframe，但是页面中其他 iframe 能获取到。
- 解决方案：
  给 args 添加 --disable-features=site-per-process
```javascript
const newBrowser = async () => {
  init = true;
  let findChromePath = await findChrome({});
  let executablePath = findChromePath.executablePath;

  browser = await puppeteer.launch({
    executablePath,
    headless: false,
    devtools: false, // F12打开控制台
    args: [
      `--disable-extensions-except=/Users/mac/project/dev/puppeteer/extend`, // 不屏蔽这个插件 mac
      // `--disable-extensions-except=C:/Users/Administrator/Desktop/rechargenew`, // 不屏蔽这个插件 window
      `--window-size=${width},${height}`, // 窗口大小
      '--disable-features=site-per-process', // 添加这个
      `–disable-gpu`
    ],
    defaultViewport: { width: width, height: height } // 页面大小
  });

  page = await browser.newPage();

  newhtml()
}

```
- 原因分析：
- [ ] site-per-process
> 目前 Chromium 默认的进程模型叫做 process-per-site-instance (还有其他的进程模型如 process-per-site 和 process-per-tab)[3]。这个进程模型基本上就是为每个页面创建一个进程，但是还是存在不同的网站用同一个进程的情况，如 iframes 和父页面，同一个标签页里的页面跳转，以及标签页过多的时候等。Site isolation 引入了一个新的策略叫做 site-per-process。这个策略更为严格，只要是不同的网站，不管你是在新的标签页打开，还是在同一个标签页跳转，还是嵌在 iframes 里，统统都要换一个新的进程。这里主要的工作量是把 iframes 给拿出来放到不同的进程里(所谓的 OOPIF, out of process iframe)。
> 
> 使用同一个协议，同一个注册域名 (所谓的 eTLD+1) 的网址都属于同一个网站，这比同源策略里的 same origin 要宽泛一些，不同的子域名，不同的端口都算同一个网站。
> 
> 说明我当前的 Chromium中 是默认开启 site-per-process 的，必须手动禁用它。具体的解释还需要翻翻 Chromeium 开源的文档
## 4、无法获取跨域iframe内容解决
- [ ] puppeteer访问的页面存在跨域iframe时，会存在无法获取iframe内容的问题。解决方法，puppeteer加上启动参数
```javascript
const browser = await puppeteer.launch({
  args: [
      '--disable-web-security',
      '--disable-features=IsolateOrigins,site-per-process', // 很关键...
    ]
 })
````
## 3、How to fill form that is inside an iframe?
[来源](https://stackoverflow.com/questions/46529201/puppeteer-how-to-fill-form-that-is-inside-an-iframe)
```javascript
console.log('waiting for iframe with form to be ready.');
await page.waitForSelector('iframe');
console.log('iframe is ready. Loading iframe content');

const elementHandle = await page.$(
    'iframe[src="https://example.com"]',
);
const frame = await elementHandle.contentFrame();

console.log('filling form in iframe');
await frame.type('#Name', 'Bob', { delay: 100 });
```
## 2、如何修改chrome 的 viewport？
### 2.1、方案一
命令行参考：[List of Chromium Command Line Switches](https://peter.sh/experiments/chromium-command-line-switches/)
### 2.2、方案二
```javascript
await page.setViewport({
    width: 1200,
    height: 800,
    deviceScaleFactor: 1,
})
```
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