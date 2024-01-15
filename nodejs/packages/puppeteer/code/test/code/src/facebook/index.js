const {site_www_facebook_com} = require("./site_www_facebook_com");
const {profileList, profileOpen, profileBatchClose} = require("../utils/ixb/profile");


// FB分组ID[7282]-带广告de浏览器列表
// 异常分组[7284]
// @todo 首次打开失败，尝试N次重新加载
profileList({
    _body: { group_id: 7282, limit: 5 },
}).then(async function(resp){
    const {total, data: list} = resp.data
    console.info(total, list)
    const profile_ids = []
    for(const item of list) {
        profile_ids.push(item.profile_id)
        if (item.profile_id !== 520) {
            continue
        }
        const options = {
            _body: {
                profile_id: item.profile_id
            },
            toggle_group_id: 7284,
            profile_id: item.profile_id,
            group_id: item.group_id,
        }
        await profileOpen(async function(browser, {
            profile_id,
            group_id,
            toggle_group_id
        }) {
            await site_www_facebook_com.getProfile(browser, {
                profile_id,
                group_id,
                toggle_group_id
            })
            // await site_www_facebook_com.test(browser)
        }, options)
    }
    console.info(profile_ids)
    // await profileBatchClose(profile_ids)
})
