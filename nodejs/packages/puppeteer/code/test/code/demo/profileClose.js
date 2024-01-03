const {ixbRequest} = require('./ixbRequest');

async function main() {
    const action = 'profile-close'
    const body = {
        "profile_id": 1442
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
