const axios = require('axios');

const api_host = 'http://127.0.0.1'
const api_port = '53200'

/**
 * ixbRequest
 * @param action
 * @param body
 * @returns {Promise<*>}
 */
async function ixbRequest(action='', body ={}) {
    const url = api_host+':'+api_port+'/api/v2/'+action;
    try {
        const response = await axios.post(url, JSON.stringify(body));
        if (response.data.error.code === 0) {
            return response.data;
        } else {
            const customError = new Error(response.data.error.message);
            customError.code = response.data.error.code
            throw customError
        }
    } catch (error) {
        throw error;
    }
}

module.exports = { ixbRequest };