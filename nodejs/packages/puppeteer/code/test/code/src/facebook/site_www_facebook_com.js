const path = require("path");
exports.site_www_facebook_com = {
    getProfile: async function( browser, _options = {}) {
        const defaultOptions = {
            url: 'https://www.facebook.com/',
            selectors: {
                '/': [
                    {selector: 'a[href*="https://www.facebook.com/profile.php?id="]'}
                ],
                '/profile.php': [
                    {name: 'photos', selector: ''},
                    {name: 'about_contact_info_mobile', selector: ''},
                    {name: 'about_contact_info_email', selector: ''},
                    {name: 'about_basic_info_gender', selector: ''},
                    {name: 'about_basic_info_birthday', selector: ''},
                    {name: 'about_basic_info_birth_year', selector: ''},
                    {name: 'about_categories', selector: ''},
                    {name: 'about_contact_info_mobile', selector: ''},
                    {name: 'about_contact_info_mobile', selector: ''},
                    {name: 'about_contact_info_mobile', selector: ''},
                ]
            }
        }
        const options = {...defaultOptions, ..._options}
        const { selectors, url } = options
        const pages = await browser.pages()
        const page = pages[0]
        await page.setViewport({
            width: 1200,
            height: 800,
            deviceScaleFactor: 1,
        })
        //
        await page.goto(url)
        await page.content()

        // 1、点击进入Profile
        const home_selector = selectors['/'][0].selector
        // await page.hover(home_selector)
        setTimeout(async function() {
            await page.click(home_selector)
            // 2、获取Profile信息：Profile、Pilot、Studied、Live、From
        }, 3000)

    }
}