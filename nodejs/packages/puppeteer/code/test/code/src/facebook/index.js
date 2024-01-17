const {site_www_facebook_com} = require("./site_www_facebook_com");
const {profileList, profileOpen, profileBatchClose} = require("../utils/ixb/profile");
const axios = require("axios");


// FB分组ID[7282]-带广告de浏览器列表
// 异常分组[7284]
// @todo 打开失败，尝试N次重新加载
// @todo 打开浏览器后，如何固定在顶部显示
profileList({
    _body: { group_id: 7282, limit: 200},
}).then(async function(resp){
    const {total, data: list} = resp.data
    console.info(total, list)
    const profile_ids = []
    for(const index in  list) {
        const item = list[index]
        profile_ids.push(item.profile_id)
        // if (item.profile_id > 492) {
        //     continue
        // }
        // if (![521].some(id => id === item.profile_id)) {
        //     continue
        // }
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
            const data = await site_www_facebook_com.getProfile(browser, {
                profile_id,
                group_id,
                toggle_group_id
            })
            console.info('one profile data: ', data)
            // const data = {
            //     "ixb_profile_id": 521,
            //     "fb_account_id": "61554420952445",
            //     "fb_account_name": "Mia Bell 3",
            //     "mobile": "+254 773 392143",
            //     "email": "azgjupub@mailkv.com",
            //     "gender": "Female",
            //     "birth_date": "August 30",
            //     "birth_year": "1989",
            //     "categories": "Digital creator · Blogger",
            //     "current_city": "Pretty Prairie, Kansas",
            //     "hometown": "Pretty Prairie, Kansas",
            //     "work_company": "Pilot at Pretty Prairie Tees",
            //     "college": "Studied at Harvard University Press",
            //     "high_school": "Went to Harvard University, Cambridge, Mass,",
            //     "work_city_or_town": "Pretty Prairie, Kansas",
            //     "your_photos_original_url": "https://scontent.fphl1-1.fna.fbcdn.net/v/t39.30808-6/405363926_122093975678147365_4038158411878019842_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=783fdb&_nc_ohc=UXsBy26YsZsAX-9HG4q&_nc_ht=scontent.fphl1-1.fna&oh=00_AfBl8ESj3NT1Up_E-vO1GErj3UwbdJY6BYAwBB3cAcy1Ow&oe=65AD1032###https://scontent.fphl1-1.fna.fbcdn.net/v/t39.30808-6/406534866_122093975534147365_854721331364887105_n.jpg?_nc_cat=106&ccb=1-7&_nc_sid=efb6e6&_nc_ohc=RKdl4aj7rnMAX_S7puQ&_nc_ht=scontent.fphl1-1.fna&oh=00_AfAo3BJ8_u8gB1VHzXDy-wgOmwx0B1rQbR6w8ZfZ0WkdzQ&oe=65AC668B"
            // }
            if (data) {
                axios({
                    url: 'http://fb-auto.local/api/profile',
                    method: 'post',
                    headers: { 'Accept': 'application/x.fb-auto.v1.1+json'},
                    data
                }).then(resp => {
                    console.info('remote-response: ', resp)
                    if (resp.data.code === 0) {
                        browser.close()
                    }
                }).catch(error => {
                    console.info('error: ', error)
                    browser.close()
                })
            }

        }, options)

    }
    console.info(profile_ids)
    // await profileBatchClose(profile_ids)
})
