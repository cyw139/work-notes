exports.site_macat_vip = {
    moveResourceToBaiduYunPan: async function(browser, options={}) {
        const defaultOptions = {
            url: 'https://www.qq.com/',
            selector: 'ul.nav-main>li.nav-item>a:nth-child(1)',
        }

        const page = await browser.newPage()
        const navigationPromise = page.waitForNavigation()

        await page.goto('https://www.macat.vip/')

        await page.setViewport({ width: 1920, height: 919 })

        await page.waitForSelector('a.login-btn')
        await page.click('a.login-btn')

        await page.waitForSelector('input[type=email]')
        await page.click('input[type=email]')
        await page.type('input[type=email]', 'chenyiwei.sam@163.com')

        await page.click('input[type=password]')
        await page.type('input[type=password]', '123456..')

        await page.click('button.go-login')


        // await navigationPromise
        //
        // await page.waitForSelector('#menu-menu-1 > .menu-item > .sub-menu > .menu-item:nth-child(1) > .sub-menu > .menu-item:nth-child(1) > a')
        // await page.click('#menu-menu-1 > .menu-item > .sub-menu > .menu-item:nth-child(1) > .sub-menu > .menu-item:nth-child(1) > a')
        //
        // await navigationPromise
        //
        // await page.waitForSelector('#post-36457 > .entry-media > .placeholder > a > .ls-is-cached')
        // await page.click('#post-36457 > .entry-media > .placeholder > a > .ls-is-cached')
        //
        // await navigationPromise
        //
        // await page.waitForSelector('.theiaStickySidebar > #secondary > #ripro_v2_shop_down-7 > .btn-group:nth-child(3) > .btn')
        // await page.click('.theiaStickySidebar > #secondary > #ripro_v2_shop_down-7 > .btn-group:nth-child(3) > .btn')
        //
        // await navigationPromise
        //
        // await page.waitForSelector('#submitBtn')
        // await page.click('#submitBtn')
        //
        // await page.waitForSelector('.bar > .x-button-box > .tools-share-save-hb > .g-button-right > .text')
        // await page.click('.bar > .x-button-box > .tools-share-save-hb > .g-button-right > .text')
        //
        // await navigationPromise
        //
        // await page.waitForSelector('.treeview > .treeview- > .treeview-node-hover > .treeview-node-handler > .treeview-txt')
        // await page.click('.treeview > .treeview- > .treeview-node-hover > .treeview-node-handler > .treeview-txt')
        //
        // await page.waitForSelector('.treeview > .treeview- > .treeview-node-hover > .treeview-node-handler > .treeview-txt')
        // await page.click('.treeview > .treeview- > .treeview-node-hover > .treeview-node-handler > .treeview-txt')
        //
        // await page.waitForSelector('.vsc-initialized > #fileTreeDialog > .dialog-footer > .g-button-blue-large')
        // await page.click('.vsc-initialized > #fileTreeDialog > .dialog-footer > .g-button-blue-large')
        //
        // await page.waitForSelector('.vsc-initialized > #emptyDialogId > .after-trans-dialog > .fx-icon-close')
        // await page.click('.vsc-initialized > #emptyDialogId > .after-trans-dialog > .fx-icon-close')
        //
        // await browser.close()
    }
}