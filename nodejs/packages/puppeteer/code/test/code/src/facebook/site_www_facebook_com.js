const path = require("path");
const fs = require("fs");
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
            await this[operationMethod](page, rule)
        }
    },
    pageDataOperate: async function(page, rule) {
        const operationMethod = rule.type
        if (operationMethod in this && typeof this[operationMethod] === 'function') {
            await this[operationMethod](page, rule)
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
        // const selector = 'span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1lliihq x1s928wv xhkezso x1gmr53x x1cpjm7i x1fgarty x1943h6x xudqn12 x3x7a5m x6prxxf xvq8zen xo1l8bm xzsf02u x1yc453h"]'
        const selector = 'span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1pg5gke xvq8zen xo1l8bm xi81zsa x1yc453h"] > div'
        console.info(selector)

        const pageItems = await page.$$(selector)
        console.info('pageItems: ', pageItems)
        for(const index in pageItems) {
            const pageItem = pageItems[index]
            console.info('html: ', pageItem.toString())
            const result = await page.evaluate(els => {
                return { innerHTML: els.innerHTML}
            }, pageItem)
            console.info('result: ', result)
            // const browserInnerText = this.tools().clearAllHtmlTag(pageItem.parentElement.nextSibling)
            // console.info(browserInnerText)
        }

        // const pageItems1 = await page.evaluate((selector_name, selector_value) => {
        //     const selector = 'span[class="x193iq5w xeuugli x13faqbe x1vvkbs x1xmvt09 x1lliihq x1s928wv xhkezso x1gmr53x x1cpjm7i x1fgarty x1943h6x xudqn12 x3x7a5m x6prxxf xvq8zen xo1l8bm xzsf02u x1yc453h"]'
        //     const elements = document.querySelectorAll(selector)
        //     // console.info(typeof elements)
        //     // let elements_type = []
        //     // for(const element of elements) {
        //     //     elements_type.push(element.parentElement)
        //     // }
        //     // const elements_value = Array.from(elements).map(element => {
        //     //     return element.innerHTML
        //     // })
        //     // console.info('type and value: ', elements)
        //     return Object.keys(elements).map(key => {
        //         const element = elements[key]
        //         return element.parentNode
        //     })
        // })
        // console.info('pageItems1: ', pageItems1)


    },
    getFieldInnerHTML: async function(page, selector) {
        const pageItems = await page.$$(selector)
        console.info('pageItems: ', pageItems)
        const results = []
        for(const index in pageItems) {
            const pageItem = pageItems[index]
            console.info('html: ', pageItem.toString())
            const result = await page.evaluate(els => {
                return els.innerHTML
            }, pageItem)

            results.push(this.tools().clearAllHtmlTag(result))
            // results.push(result)
        }
        console.info('results: ', results)
        return results
    },
    getFieldsInfo: async function(page, rule) {
        const {
            name = '',
            selector_name = '',
            selector_value = '',
            fieldToInnerTexts = {}
        } = rule
        console.info('selector: ', selector_name, selector_value)
        const pageItemNames = await this.getFieldInnerHTML(page, selector_name)
        const pageItemValues = await this.getFieldInnerHTML(page, selector_value)
        if (pageItemValues.length !== pageItemNames.length) {
            console.info('异常[' + name + ']获取的字段记录数不同')
        }
        const data = {}
        const InnerTexts = new Set(Object.keys(fieldToInnerTexts))
        pageItemNames.forEach((itemName, index) => {
            if (InnerTexts.has(itemName)) {
                data[fieldToInnerTexts[itemName]] = pageItemValues[index]
            }
        })
        console.info(data)
        return data
    },
    mouseMove: async function(page, rule) {
        const { name= '', options = {}, times = 1} = rule
        console.info(name)
        // for(let i = 0; i<times; i++) {
        //     await page.mouse.wheel(options)
        // }
        let i = times
        const mouseWheel = setInterval(() => {
            if (i-- > 0) {
                console.info(times)
                page.mouse.wheel(options)
            }

        }, 1000)

    },
    pageFocus: async function(page, rule) {
        const { name= '', selector = ''} = rule
        console.info(name)
        page.focus(selector)
    },
    pageClick: async function(page, rule) {
        const { name= '', selector = '', navigationOptions = {}} = rule
        const defaultOptions = {
            waitUntil: 'networkidle0'
        }
        const _navigationOptions = { ...defaultOptions, ...navigationOptions}
        console.info(name)
        console.info(page.url())
        await page.waitForSelector(selector)
        await Promise.all([
            page.waitForNavigation(_navigationOptions),
            page.click(selector)
        ]);
        // await page.content()
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
                    ops: [
                        {
                            type: 'mouseMove',
                            name: 'contact_and_basic_info-mouseMove',
                            options: { deltaY: 300 },
                            time: 1,
                        },
                    ]
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
                    navigationOptions: { waitUntil: 'domcontentloaded' },
                    ops: [
                        {
                            type: 'getFieldsInfo',
                            name: 'contact_and_basic_info-fields',
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
            deviceScaleFactor: 3,
        })

        await page.goto(url, { waitUntil: 'domcontentloaded' })
        const oThis = this
        for (let i = 0; i < rules.length; i++) {
            const rule = rules[i]
            if (rule.status === 'enable') {
                await this.done(async function(resolve) {
                    await oThis.pageOperate(page, rule)
                    if ('ops' in rule && Array.isArray(rule.ops) && rule.ops.length > 0) {
                        for(const ops_rule of rule.ops) {
                            await oThis.done(async function(resolve) {
                                await oThis.pageDataOperate(page, ops_rule)
                                await resolve(true)
                            }, 3000)
                        }
                    }
                    await resolve(true)
                })
            }

        }
    }
}