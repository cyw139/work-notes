const path = require("path");
exports.site_facebook_com = {
    getProfile: async function( browser, _options = {}) {
        const defaultOptions = {
            url: 'https://www.facebook.com/',
            selector: 'a[href*="https://www.facebook.com/profile.php?id="]',
        }
        const options = {...defaultOptions, ..._options}
        const { selector, url } = options
        const page = await browser.newPage()

        await page.goto(url)
        await page.content()

        // 1、点击进入Profile

        // 2、获取Profile信息：Profile、Pilot、Studied、Live、From


    }
}