const path = require("path");
exports.site_www_facebook_com = {
    tools: function() {
      return {
          clearAllHtmlTag: function(str){
            return str.replace(/<[^>]+>/g,"");
        }
      }
    },
    done: async function(callback, intervalTime= 2000) {
        return new Promise((resolve, reject) => {
            try {
                setTimeout(callback, intervalTime, resolve)
            } catch (e) {
                reject(e)
            }
            
        })
    },
    pageOperate: async function(page, rule) {
        const operationMethod = rule.type
        if (operationMethod in this && typeof this[operationMethod] === 'function') {
            await this.pageClick(page, rule)
        }
    },
    test: async function(browser) {
        const page = await browser.newPage()
        await page.setViewport({
            width: 1200,
            height: 800,
            deviceScaleFactor: 1,
        })
        await page.goto('https://www.facebook.com/profile.php?id=61554420952445&sk=about_contact_and_basic_info', {
            waitUntil: 'domcontentloaded'
        })
        const selector = 'span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1lliihq x1s928wv xhkezso x1gmr53x x1cpjm7i x1fgarty x1943h6x xudqn12 x3x7a5m x6prxxf xvq8zen xo1l8bm xzsf02u x1yc453h"]'
        console.info(selector)
        const pageItems = await page.$$(selector)
        console.info('pageItems: ', pageItems)
        for(const index in pageItems) {
            const pageItem = pageItems[index]
            console.info('html: ', pageItem.toElement('span'))
            // const browserInnerText = this.tools().clearAllHtmlTag(pageItem.parentElement.nextSibling)
            // console.info(browserInnerText)
        }


    },
    getFieldsInfo: function(page, rule) {
        const { name = '', selector = '', fieldsMap = {} } = rule
        console.info(name)
        for(const field of fieldsMap) {
            const innerText = fieldsMap[field]
            const pageItems = page.$$eval(selector)
            for(const pageItem in pageItems) {
                this.tools().clearAllHtmlTag(pageItem.parentElement.nextSibling)
            }
        }
    },
    pageClick: async function(page, rule) {
        const { name= '', selector = ''} = rule
        console.info(name)
        console.info(page.url())
        await page.waitForSelector(selector)
        await Promise.all([
            page.click(selector),
            page.waitForNavigation({ waitUntil: 'domcontentloaded' })
        ]);
        console.info(page.url())
    },
    getProfile: async function( browser, _options = {}) {
        const defaultOptions = {
            url: 'https://www.facebook.com/',
            rules: [
                {
                    type: 'pageClick',
                    name: 'profile',
                    status: 'enable',
                    selector: 'a[href*="https://www.facebook.com/profile.php?id="]',
                    timeout: 3000,
                },
                {
                    type: 'pageClick',
                    name: 'about_overview',
                    status: 'enable',
                    selector: 'a[href$="sk=about"]',
                    timeout: 3000,
                },
                {   type: 'pageClick',
                    status: 'enable',
                    name: 'about_contact_and_basic_info',
                    selector: 'a[href$="sk=about_contact_and_basic_info"]',
                    timeout: 3000,
                    ops: [
                        {
                            type: 'getFieldsInfo',
                            name: 'contact_and_basic_info-fields',
                            fieldsMap: {
                                mobile: 'Mobile',
                                email: 'Email',
                                gender: 'Gender',
                                birthday: 'Birth Day',
                                birth_year: 'Birth Year'
                            },
                            selector: 'span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1lliihq x1s928wv xhkezso x1gmr53x x1cpjm7i x1fgarty x1943h6x xudqn12 x3x7a5m x6prxxf xvq8zen xo1l8bm xzsf02u x1yc453h"]'
                        }
                    ],
                },
                {
                    type: 'pageClick',
                    status: 'disabled',
                    name: 'about_work_and_education',
                    selector: 'a[href$="sk=about_work_and_education"]',
                    timeout: 3000,
                },
            ]
        }
        const options = {...defaultOptions, ..._options}
        const { rules, url } = options
        const pages = await browser.pages()
        const page = pages[0]
        await page.setViewport({
            width: 1200,
            height: 800,
            deviceScaleFactor: 1,
        })
        //
        await page.goto(url, { waitUntil: 'domcontentloaded' })
        const oThis = this
        for (let i = 0; i < rules.length; i++) {
            const rule = rules[i]
            if (rule.status === 'enable') {
                await this.done(async function(resolve) {
                    await oThis.pageOperate(page, rule)
                    if ('ops' in rule && Array.isArray(rule.ops) && rule.ops.length > 0) {
                        rule.ops.forEach(function() {

                        })
                    }
                    resolve(true)
                })
            }

        }
    }
}