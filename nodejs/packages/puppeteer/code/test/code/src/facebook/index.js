const {site_www_facebook_com} = require("./site_www_facebook_com");
const {profileList, profileOpen} = require("../utils/ixb/profile");

// 带广告的浏览器
profileList({_body: { group_id: 7282, limit: 5 }}).then(async function(resp){
    const {total, data: list} = resp.data
    console.info(total, list)
    for(const item of list) {
        const options = {
            _body: {
                profile_id: item.profile_id
            }
        }
        await profileOpen(async function(profile_id, browser) {
            await site_www_facebook_com.getProfile(profile_id, browser)
            // await site_www_facebook_com.test(browser)
        }, options)
    }
})
