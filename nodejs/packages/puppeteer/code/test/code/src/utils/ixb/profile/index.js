const puppeteer = require('puppeteer-core');
const {ixbRequest} = require('../ixbRequest');

exports.profileOpen = async function profileOpen(callback, options = {}) {
    let logs = []
    const { _body = { }} = options
    const action = 'profile-open'
    const body = {...{
        "profile_id": 0, // 直连
        "args": [
            "--disable-extension-welcome-page"
        ],
        "load_extensions": false,
        "load_profile_info_page": false,
        "cookies_backup": false,
        "cookie": ""
    }, ..._body}

    try {
        const response_body = await ixbRequest(action, body);
        console.info(response_body)

        /**your business code**/
        try {
            const browser = await puppeteer.connect({
                browserWSEndpoint: response_body.data.ws
            });

            callback(body.profile_id, browser)

        } catch (err) {
            console.log(err.message);
        }
        /** end **/

    } catch (error) {
        console.error(error.code);
        console.error(error.message);
    }
}

exports.profileList = async function profileList(options = {}) {
    let logs = []
    const { _body = {} } = options
    const action = 'profile-list'
    const body = {...{
        "page": 1, //页数  默认：1
        "limit": 10, //每页返回的数量  默认：10
        "group_id": 0, //分组id
        "name": "" //窗口名称
    }, ..._body}

    console.info(body)

    try {
        const response_body = await ixbRequest(action, body);
        console.info(response_body)

        return response_body;
    } catch (error) {
        console.error(error.code);
        console.error(error.message);
    }
}
