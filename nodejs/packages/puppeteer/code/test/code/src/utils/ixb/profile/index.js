const puppeteer = require('puppeteer-core');
const {ixbRequest} = require('../ixbRequest');

exports.profileOpen = async function profileOpen(callback, {
    _body = { },
    toggle_group_id,
    profile_id,
    group_id,
}) {
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

            callback(browser, { group_id, toggle_group_id, profile_id})

        } catch (err) {
            console.log(err.message);
        }
        /** end **/

    } catch (error) {
        // console.error(error.code);
        // console.error(error.message);
        console.error('profileOpen: ', `[${error.code}]`, error.message);
    }
}

exports.profileList = async function({ _body = {} }) {
    const action = 'profile-list'
    const body = {...{
        "page": 1, //页数  默认：1
        "limit": 10, //每页返回的数量  默认：10
        "group_id": 0, //分组id
        "name": "" //窗口名称
    }, ..._body}

    console.info('profileList: ', body)

    try {
        const response_body = await ixbRequest(action, body);
        console.info(response_body)

        return response_body;
    } catch (error) {
        console.error('profileList: ', `[${error.code}]`, error.message);
        // console.error(error.code);
        // console.error(error.message);
    }
}

exports.profileUpdate = async function({ _body = { }}) {
    const action = 'profile-update'
    const body = {...{
            "profile_id": 0,
            "group_id": 0,
        }, ..._body}

    try {
        const response_body = await ixbRequest(action, body);
        console.info(response_body)

        return response_body

    } catch (error) {
        console.error('profileUpdate: ', `[${error.code}]`, error.message);
        // console.error(error.code);
        // console.error(error.message);
    }
}
exports.profileBatchClose = async function(profile_ids) {
    const action = 'profile-close'
    const body = {
            "profile_id": profile_ids,
        }

    try {
        const response_body = await ixbRequest(action, body);
        console.info(response_body)

        return response_body

    } catch (error) {
        console.error('profileBatchClose: ', error.code, error.message);
        // console.error('profileBatchClose: ' + error.message);
    }
}