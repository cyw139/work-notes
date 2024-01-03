const {ixbRequest} = require('./ixbRequest');

async function main() {
    const action = 'profile-update-proxy-for-custom-proxy'
    const body = {
        "profile_id": 4187,
        "proxy_info": {
            "proxy_mode":2,
            "proxy_type": "socks5",
            "proxy_ip": "127.0.0.1",
            "proxy_port": "51095",
            "proxy_user": "",
            "proxy_password": ""
        }
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
