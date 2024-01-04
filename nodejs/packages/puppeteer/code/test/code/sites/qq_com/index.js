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
    }
}