const {ixbRequest} = require('./ixbRequest');

async function main() {
    const action = 'proxy-list'
    const body = {
        "page":1,
        "limit": 10,
        "type":0,
        "proxy_ip":"",
        "tag_id": ""
    }

    try {
        const response_body = await ixbRequest(action, body);

        /**your business code**/
        console.log(response_body)
        /** end **/

    } catch (error) {
        console.error(error.code);
        console.error(error.message);
    }
}

main();
