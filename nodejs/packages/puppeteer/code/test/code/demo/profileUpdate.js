const {ixbRequest} = require('./ixbRequest');

async function main() {
    const action = 'profile-update'
    const body = {
        "profile_id": 4187,
        "user_agent": {
            "ua_type": 1,
            "platform": "Windows",
            "ua_info": "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.5726.2 Safari/537.36"
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
