# 100个puppeteer问题汇总
## 8、[How can I download images on a page using puppeteer?](https://stackoverflow.com/questions/52542149/how-can-i-download-images-on-a-page-using-puppeteer)
## 7、[How do you click on an element with text in Puppeteer?](https://stackoverflow.com/questions/47407791/how-do-you-click-on-an-element-with-text-in-puppeteer)
## 6、[如何确保页面完全加载，然后在scrape呢？](https://juejin.cn/post/6965000868030595103)
```javascript
await page.goto(url, { waitUntil: 'load' });
await page.goto(url, { waitUntil: 'domcontentloaded' });
await page.goto(url, { waitUntil: 'networkidle0' });
await page.goto(url, { waitUntil: 'networkidle2' });

```
[puppeteer怎么等到某个元素出现在页面中才执行程序？](https://blog.csdn.net/m0_58201165/article/details/128804967)
[stackoverflow 方案](https://stackoverflow.com/questions/52497252/puppeteer-wait-until-page-is-completely-loaded)
## 5、


