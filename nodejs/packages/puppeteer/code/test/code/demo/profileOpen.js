const puppeteer = require('puppeteer-core');
const {ixbRequest} = require('./ixbRequest');

async function main() {
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

        /**your business code**/
        try {
            const browser = await puppeteer.connect({
                browserWSEndpoint: response_body.data.ws
            });
            const page = await browser.newPage();
            await page.goto('https://www.ixbrowser.com');
            await page.waitForTimeout(5000);
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
