# 4、无法获取跨域iframe内容解决
- [ ] puppeteer访问的页面存在跨域iframe时，会存在无法获取iframe内容的问题。解决方法，puppeteer加上启动参数
```javascript
const browser = await puppeteer.launch({
  args: [
      '--disable-web-security',
      '--disable-features=IsolateOrigins,site-per-process', // 很关键...
    ]
 })
````

无法获取 iframe的解决办法
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
        // 参考：https://peter.sh/experiments/chromium-command-line-switches/
        `--disable-notifications`, 
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