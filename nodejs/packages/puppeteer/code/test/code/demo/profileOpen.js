const puppeteer = require('puppeteer-core');
const {ixbRequest} = require('./ixbRequest');
const {my_puppeteer} = require("./utils");
const path = require('path')
const {site_qq_com} = require("../sites/qq_com");
const {site_macat_vip} = require("../sites/macat_vip");
// const fs = require('fs');
// const dayjs = require('dayjs')
// const logger = require('pino')()

async function main() {
    let logs = []
    const action = 'profile-open'
    const body = {
        "profile_id": 3,
        "args": [
            "--disable-extension-welcome-page"
        ],
        "load_extensions": false,
        "load_profile_info_page": false,
        "cookies_backup": false,
        "cookie": ""
    }

    try {
        const response_body = await ixbRequest(action, body);
        console.info(response_body)

        /**your business code**/
        try {
            const browser = await puppeteer.connect({
                browserWSEndpoint: response_body.data.ws
            });

            // qq.com 首页截图
            // site_qq_com.screenShotIndex(browser)
            site_macat_vip.moveResourceToBaiduYunPan(browser)

            // await page.waitForTimeout(1000);

            // my_puppeteer.browser.show({ browser, page})
            // my_puppeteer.browserContext.show({ browserContexts: browser.browserContexts() })


            // await browser.close();
        } catch (err) {
            console.log(err.message);
        }
        /** end **/

    } catch (error) {
        console.error(error.code);
        console.error(error.message);
    }
}

main();
