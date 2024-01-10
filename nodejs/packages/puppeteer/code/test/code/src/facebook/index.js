const puppeteer = require('puppeteer-core');
const {ixbRequest} = require('../utils/ixbRequest');
const {site_www_facebook_com} = require("./site_www_facebook_com");

async function main() {
    let logs = []
    const action = 'profile-open'
    const body = {
        "profile_id": 521,
        "args": [
            "--disable-extension-welcome-page",
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

            await site_www_facebook_com.getProfile(browser)
            // await site_www_facebook_com.test(browser)

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
