exports.profile_default_config = {
    url: 'https://www.facebook.com/',
    rules: [
        {
            type: 'pageClick',
            name: 'profile',
            status: 'enable',
            selector: 'li a[href*="https://www.facebook.com/profile.php?id="]',
            timeout: 3000,
            navigationOptions: { waitUntil: 'domcontentloaded' },
            ops: [
                {
                    type: 'mouseMove',
                    name: 'contact_and_basic_info-mouseMove-up',
                    options: { deltaY: -300 },
                    time: 1,
                },
                {
                    type: 'mouseMove',
                    name: 'contact_and_basic_info-mouseMove-down',
                    options: { deltaY: 300 },
                    time: 1,
                },
                {
                    type: 'getFieldInfo',
                    name: 'contact_and_basic_info-fb_account_name-field',
                    fieldName: 'fb_account_name',
                    selector: 'div[class="x9f619 x1n2onr6 x1ja2u2z x78zum5 xdt5ytf x2lah0s x193iq5w x1cy8zhl xexx8yu"] h1[class="x1heor9g x1qlqyl8 x1pd3egz x1a2a7pz"]'
                },

            ]
        },
        {
            type: 'pageClick',
            name: 'about_overview',
            status: 'disabled',
            selector: 'a[href$="sk=about"]',
            timeout: 3000,
            navigationOptions: { waitUntil: 'domcontentloaded' },
        },
        {   type: 'pageClick',
            status: 'disabled',
            name: 'about_contact_and_basic_info',
            selector: 'a[href$="sk=about_contact_and_basic_info"]',
            timeout: 3000,
            navigationOptions: { waitUntil: 'domcontentloaded' },
            ops: [
                {
                    type: 'getFieldsInfo',
                    name: 'contact_and_basic_info-detail-01-fields',
                    fieldToInnerTexts: {
                        'Mobile': 'mobile',
                        'Email': 'email',
                        'Gender': 'gender',
                        'Birth date': 'birth_date',
                        'Birth year': 'birth_year',
                    },
                    selector_value: 'div[class="x9f619 x1n2onr6 x1ja2u2z x78zum5 x2lah0s x1nhvcw1 x1qjc9v5 xozqiw3 x1q0g3np xexx8yu xykv574 xbmpl8g x4cne27 xifccgj"] span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1lliihq x1s928wv xhkezso x1gmr53x x1cpjm7i x1fgarty x1943h6x xudqn12 x3x7a5m x6prxxf xvq8zen xo1l8bm xzsf02u x1yc453h"]',
                    selector_name: 'div[class="x9f619 x1n2onr6 x1ja2u2z x78zum5 x2lah0s x1nhvcw1 x1qjc9v5 xozqiw3 x1q0g3np xexx8yu xykv574 xbmpl8g x4cne27 xifccgj"] span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1pg5gke xvq8zen xo1l8bm xi81zsa x1yc453h"]'
                },
                {
                    type: 'getFieldInfo',
                    name: 'contact_and_basic_info-detail-02-fields',
                    fieldName: 'categories',
                    selector: 'div[class="xyamay9 xqmdsaz x1gan7if x1swvt13"] div[class="x9f619 x1n2onr6 x1ja2u2z x78zum5 x1nhvcw1 x1qjc9v5 xozqiw3 x1q0g3np xexx8yu xykv574 xbmpl8g x4cne27 xifccgj xs83m0k"] div[class="xzsf02u x6prxxf xvq8zen x126k92a"]'
                },
            ],
        },
        {
            type: 'pageClick',
            status: 'disabled',
            name: 'about_place',
            selector: 'a[href$="sk=about_places"]',
            timeout: 3000,
            navigationOptions: { waitUntil: 'domcontentloaded' },
            ops: [
                {
                    type: 'getFieldsInfo',
                    name: 'place_lived-fields',
                    fieldToInnerTexts: {
                        'Current city': 'current_city',
                        'Hometown': 'hometown',
                    },
                    selector_value: 'div[class="x9f619 x1n2onr6 x1ja2u2z x78zum5 x1nhvcw1 x1qjc9v5 xozqiw3 x1q0g3np xexx8yu xykv574 xbmpl8g x4cne27 xifccgj xs83m0k"] span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1lliihq x1s928wv xhkezso x1gmr53x x1cpjm7i x1fgarty x1943h6x xudqn12 x3x7a5m x6prxxf xvq8zen xo1l8bm xzsf02u"]',
                    selector_name: 'div[class="x9f619 x1n2onr6 x1ja2u2z x78zum5 x1nhvcw1 x1qjc9v5 xozqiw3 x1q0g3np xexx8yu xykv574 xbmpl8g x4cne27 xifccgj xs83m0k"] span[class="xi81zsa x1nxh6w3 x1sibtaa"]'
                },
            ],
        },
        {
            type: 'pageClick',
            status: 'disabled',
            name: 'about_work_and_education',
            selector: 'a[href$="sk=about_work_and_education"]',
            timeout: 3000,
            navigationOptions: { waitUntil: 'domcontentloaded' },
            ops: [
                {
                    type: 'getFieldsInfo',
                    name: 'about_work_and_education-fields',
                    fieldToInnerTexts: {
                        'Work': 'work_company',
                        'College': 'college',
                        'High school': 'high_school',
                    },
                    selector_value: 'div[class="xyamay9 xqmdsaz x1gan7if x1swvt13"] span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1lliihq x1s928wv xhkezso x1gmr53x x1cpjm7i x1fgarty x1943h6x xudqn12 x3x7a5m x6prxxf xvq8zen xo1l8bm xzsf02u"]',
                    selector_name: 'div[class="xyamay9 xqmdsaz x1gan7if x1swvt13"] span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1lliihq x1s928wv xhkezso x1gmr53x x1cpjm7i x1fgarty x1943h6x xudqn12 x676frb x1lkfr7t x1lbecb7 x1s688f xzsf02u"]'
                },
                {
                    type: 'getFieldsInfo',
                    name: 'about_work_and_education-work_city_or_town-fields',
                    fieldToInnerTexts: {
                        'Work': 'work_city_or_town',
                    },
                    selector_value: 'div[class="xyamay9 xqmdsaz x1gan7if x1swvt13"] span[class="xi81zsa x1nxh6w3 x1sibtaa"]',
                    selector_name: 'div[class="xyamay9 xqmdsaz x1gan7if x1swvt13"] span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1lliihq x1s928wv xhkezso x1gmr53x x1cpjm7i x1fgarty x1943h6x xudqn12 x676frb x1lkfr7t x1lbecb7 x1s688f xzsf02u"]'
                },
            ],
        },
        {
            type: 'pageClick',
            status: 'enable',
            name: 'your_photos',
            selector: 'a[href$="sk=photos"]',
            timeout: 3000,
            navigationOptions: { waitUntil: 'domcontentloaded' },
            ops: [
                {
                    type: 'pageClickAndGetPhotosURL',
                    status: 'enable',
                    fieldName: 'your_photos_original_url',
                    name: 'your_photos-download',
                    selector: 'div[class="x1e56ztr"] a[href*="photo.php"]',
                    photo_selector: 'div[class="x6s0dn4 x78zum5 xdt5ytf xl56j7k x1n2onr6"] > img',
                    timeout: 3000,
                    navigationOptions: { waitUntil: 'domcontentloaded' },
                },


            ],
        },
    ]
}