const {ixbRequest} = require('./ixbRequest');

async function main() {
    const action = 'profile-clear-cache'
    const body = {
        "profile_id":["161","162"]
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
