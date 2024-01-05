const path = require("path");
exports.site_facebook_com = {
    signup: async function( browser, _options = {}) {
        const defaultOptions = {
            url: 'https://www.facebook.com/',
            selector: '',
        }
        const options = {...defaultOptions, ..._options}
        const { selector, url } = options
        const page = await browser.newPage()

        await page.goto(url)
        await page.content()

    }
}