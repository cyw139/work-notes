# 3、How to fill form that is inside an iframe?
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