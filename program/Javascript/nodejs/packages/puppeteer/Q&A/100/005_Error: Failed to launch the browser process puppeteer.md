# Error: Failed to launch the browser process puppeteer
[stackoverflow](https://stackoverflow.com/questions/59979188/error-failed-to-launch-the-browser-process-puppeteer)

I had the same issue, I tried everything which is listed from the [Puppeteer guide](https://github.com/puppeteer/puppeteer/blob/master/docs/troubleshooting.md#recommended-enable-user-namespace-cloning), none of them worked for me.

What works for me was to download chromium manually `sudo apt-get install chromium-browser`.

And then, tell Puppeteer where chromium is located :
```javascript
const browser = await puppeteer.launch({
    executablePath: '/usr/bin/chromium-browser'
})
```
Hope this will help someone :)
```shell
# Works on Mac OS X too. 
brew install chromium && which chromium
```