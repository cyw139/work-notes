const {ixbRequest} = require('./ixbRequest');

async function main() {
    const action = 'profile-create'
    const body = {
        "site_id": 21,
        "site_url": "http://baidu.com/",
        "color": "#CC9966",
        "name": "test11",
        "note": "",
        "group_id": 1,
        "username": "",
        "password": "",
        "cookie": "",
        "proxy_config": {
            "proxy_mode": 2,
            "proxy_type": "socks5",
            "proxy_ip": "192.168.3.252",
            "proxy_port": "8080"
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
