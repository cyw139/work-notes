const path = require("path");
const dayjs = require('dayjs')
const fs = require('fs')
exports.site_qq_com = {
    screenShotIndex: async function(browser, _options = {}) {
        const defaultOptions = {
            url: 'https://www.qq.com/',
            selector: 'ul.nav-main>li.nav-item>a:nth-child(1)',
        }
        const options = {...defaultOptions, ..._options}
        const { selector, url } = options
        const page = await browser.newPage()

        await page.goto(url)
        await page.content()
        const daytimeString = dayjs().format('YYYY-MM-DD_HH_mm_ss')
        await page.hover(selector)
        let times = 15
        const mouseWheel = setInterval(() => {
            if (times-- > 0) {
                console.info(times)
                page.mouse.wheel({deltaY: 300})
            } else {
                const dir = path.join(__dirname, './screenShotIndex/')
                if (!fs.existsSync(dir)) {
                    fs.mkdirSync(dir)
                }
                page.screenshot({
                    fullPage: true,
                    path: dir + daytimeString+ '.png'
                }).then(() => {
                    page.close()
                });
            }

        }, 1000)
    },
    addScriptTagExample: async function( browser, _options = {}) {
        const defaultOptions = {
            url: 'https://www.qq.com/',
            selector: '',
        }
        const options = {...defaultOptions, ..._options}
        const { selector, url } = options
        const page = await browser.newPage()

        await page.goto(url)
        await page.content()
        // 三种方式：url、本地路径、内容
        // 1、url 跨越怎么解决？未实验
        await page.addScriptTag({type: 'module', url: 'https://client.crisp.chat/l.js' })
        // 2、本地路径：位置3
        const filePath = path.join(__dirname, './assets/scripts/test.js')
        console.info(filePath)
        await page.addScriptTag({ path: filePath })
        // 3、内容：位置2
        await page.addScriptTag({ content: "document.querySelector('.tit>a').innerHTML='直接js脚本'"})

        // 4、options 额外参数:
        // Sets the type of the script. Use `module` in order to load an ES2015 module.
        // Sets the id of the script.
        // { type: 'module', id: 'module' }

    }
}