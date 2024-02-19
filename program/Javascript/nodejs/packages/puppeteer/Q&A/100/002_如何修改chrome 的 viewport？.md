# 2、如何修改chrome 的 viewport？
## 2.1、方案一
命令行参考：[List of Chromium Command Line Switches](https://peter.sh/experiments/chromium-command-line-switches/)
[命令行参考](https://kapeli.com/cheat_sheets/Chromium_Command_Line_Switches.docset/Contents/Resources/Documents/index)：
## 2.2、方案二
```javascript
await page.setViewport({
    width: 1200,
    height: 800,
    deviceScaleFactor: 1,
})
```