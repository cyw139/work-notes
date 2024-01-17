const path = require("path");
const fs = require("fs");
const {profileUpdate, profileClose, profileBatchClose} = require("../utils/ixb/profile");
const {downloadImage, clearAllHtmlTag, clearNBSP, tools} = require("../utils");
const {base_download_path} = require("../config");
const {indexedDB} = require("../utils/indexedDB");
const {profile_default_config} = require("./config/profile");
const site_name = 'www_facebook_com'
const site_config = {
    base_download_image_path: base_download_path + 'sites/' + site_name + '/images/',
}
exports.site_www_facebook_com = {
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
        let data = ''
        if (operationMethod in this && typeof this[operationMethod] === 'function') {
            return await this[operationMethod](page, rule)
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
            // const browserInnerText = clearAllHtmlTag(pageItem.parentElement.nextSibling)
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
    getFieldsInnerHTML: async function(page, selector) {
        const pageItems = await page.$$(selector)
        console.info('pageItems: ', pageItems)
        const results = []
        for(const index in pageItems) {
            const pageItem = pageItems[index]
            console.info('html: ', pageItem.toString())
            const result = await page.evaluate(els => {
                return els.innerHTML
            }, pageItem)

            results.push(clearNBSP(clearAllHtmlTag(result)))
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
        const pageItemNames = await this.getFieldsInnerHTML(page, selector_name)
        const pageItemValues = await this.getFieldsInnerHTML(page, selector_value)
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
    getFieldInnerHTML: async function(page, selector) {
        const pageItem = await page.$(selector)
        console.info('pageItem: ', pageItem)
        if (pageItem === null || pageItem === undefined || !pageItem) {
            return ''
        }
        console.info('html: ', pageItem.toString())
        const innerHTML = await page.evaluate(els => {
            return els.innerHTML
        }, pageItem)
        const result = clearNBSP(clearAllHtmlTag(innerHTML))
        console.info('result: ', result)
        return result
    },
    getFieldInfo: async function(page, rule) {
        const {
            name = '',
            fieldName = '',
            selector = '',
        } = rule
        console.info('name: ', name)
        console.info('selector: ', selector)
        const pageItemValue = await this.getFieldInnerHTML(page, selector)
        const data = { [fieldName]: pageItemValue}
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
        return {}
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
    pageClickAndPhotosDownload: async function(page, rule) {
        return new Promise(async (resolve, reject) => {
            const {
                name= '',
                selector = '',
                fieldName = '',
                photo_selector = '',
                navigationOptions = {}
            } = rule
            const defaultOptions = {
                waitUntil: 'networkidle0'
            }
            const _navigationOptions = { ...defaultOptions, ...navigationOptions}
            console.info(name)
            console.info(page.url())
            console.log('selector: ', selector)
            try {
                await page.waitForSelector(selector)
                const pageItems = await page.$$(selector)
                const fileNames = []
                for(const index in pageItems) {
                    const pageItem = pageItems[index]
                    await Promise.all([
                        page.waitForNavigation(_navigationOptions),
                        pageItem.click(this)
                    ]);
                    console.log('photo_selector: ', photo_selector)
                    await page.waitForSelector(photo_selector)
                    const imageHref = await page.evaluate((selector) => {
                        const element = document.querySelector(selector)
                        return element ? element.getAttribute('src') : ''
                    }, photo_selector)
                    console.log('imageHref: ', imageHref)
                    let fileName = imageHref.substring(0, imageHref.indexOf('?')).split('/').pop()
                    console.info('fileName: ', fileName)
                    await downloadImage(page, imageHref,  site_config.base_download_image_path + fileName )
                    console.info('focus: ', photo_selector)
                    await page.focus(photo_selector)
                    console.info('Escape: ')
                    await page.keyboard.press('Escape')
                    fileNames.push(fileName)
                }
                resolve({ [fieldName]: fileNames.join(',')})
            }catch( err) {
                // 情况一：selector 选择器不存在时，出现等待异常
                console.info('err: ', err.code, err.message)
                resolve( { [fieldName]: ''})
            }
        })
    },
    pageClickAndGetPhotosURL: async function(page, rule) {
        return new Promise(async (resolve, reject) => {
            const {
                name= '',
                selector = '',
                fieldName = '',
                photo_selector = '',
                navigationOptions = {}
            } = rule
            const defaultOptions = {
                waitUntil: 'networkidle0'
            }
            const _navigationOptions = { ...defaultOptions, ...navigationOptions}
            console.info(name)
            console.info(page.url())
            console.log('selector: ', selector)
            try {
                await page.waitForSelector(selector)
                const pageItems = await page.$$(selector)
                const imageHrefs = []
                for(const index in pageItems) {
                    const pageItem = pageItems[index]
                    await Promise.all([
                        page.waitForNavigation(_navigationOptions),
                        pageItem.click(this)
                    ]);
                    console.log('photo_selector: ', photo_selector)
                    await page.waitForSelector(photo_selector)
                    const imageHref = await page.evaluate((selector) => {
                        const element = document.querySelector(selector)
                        return element ? element.getAttribute('src') : ''
                    }, photo_selector)
                    console.log('imageHref: ', imageHref)
                    console.info('focus: ', photo_selector)
                    await page.focus(photo_selector)
                    console.info('Escape: ')
                    await page.keyboard.press('Escape')
                    imageHrefs.push(imageHref)
                }
                resolve({ [fieldName]: imageHrefs.join('###')})
            }catch( err) {
                // 情况一：selector 选择器不存在时，出现等待异常
                console.info('err: ', err.code, err.message)
                resolve( { [fieldName]: ''})
            }
        })
    },
    getUrlFieldInfo: function(page, rule) {
        const {
            name= '',
            regRule = '',
            regPosition = '',
            fieldName = '',
        } = rule
        console.info(name)
        const url = page.url()
        const regResult = regRule.exec(url)

        const result =  regResult === null || (!regResult[regPosition]) ? '' :regResult[regPosition]
        console.info({ [fieldName]: result})
        return { [fieldName]: result}
    },
    getProfile: function( browser, _options) {
        try {
            return new Promise(async (resolve, reject) => {
                const options = {...profile_default_config, ..._options}
                const { rules,
                    url,
                    profile_id,
                    group_id,
                    toggle_group_id } = options

                // const pages = await browser.pages()
                // page = pages[0]
                // for(const index in pages) {
                //     const p = pages[index]
                //     await p.close()
                // }
                const page = await browser.newPage()
                await page.setViewport( {
                    width: 1200,
                    height: 800,
                    deviceScaleFactor: 3,
                })
                // @todo 失败的话，需要再尝试一次
                await page.goto(url, { waitUntil: 'domcontentloaded' })
                //登录失效，出现登录
                const isVisibleLoginButton = await page.$('div[class="_6ltg"] a[class="_42ft _4jy0 _6lti _4jy6 _4jy2 selected _51sy"]')
                console.info('isVisibleLoginButton: ', isVisibleLoginButton)
                let data = {ixb_profile_id: profile_id}
                if (isVisibleLoginButton) {
                    console.info(`profile_id[${profile_id}]未登录`)
                    profileUpdate({ _body: {profile_id, group_id: toggle_group_id } }).then(async resp => {
                        if (resp.error.code === 0) {
                            await tools.setTimeout(10000)
                            profileBatchClose([profile_id]).then(resp => {
                                console.info('profileBatchClose-success: ' + profile_id)
                                reject('profileBatchClose-success: ' + profile_id)
                            }).catch(err => {
                                console.info('profileBatchClose-failure: ' + profile_id)
                                reject('profileBatchClose-failure: ' + profile_id)
                            })
                        }
                    })
                } else {
                    const oThis = this
                    for (let i = 0; i < rules.length; i++) {
                        const rule = rules[i]
                        if (rule.status === 'enable') {
                            await this.done(async function(_resolve) {
                                await oThis.pageOperate(page, rule)
                                if ('ops' in rule && Array.isArray(rule.ops) && rule.ops.length > 0) {
                                    for(const ops_rule of rule.ops) {
                                        await oThis.done(async function(_resolve) {
                                            const filedData = await oThis.pageDataOperate(page, ops_rule)
                                            data = {...data, ...filedData}
                                            await _resolve(true)
                                        }, 3000)
                                    }
                                }
                                await _resolve(true)
                            })
                        }

                    }
                    console.info(`[${profile_id}]-getAllFieldsData: `, data)
                }

                // @todo 写入indexedDB
                // const db = await indexedDB.openDB(site_name)
                // await indexedDB.addData(db, 'profile', data)
                resolve(data)
            })
        }catch(error) {
            console.info('facebook-getProfile-error: ', error.code, error.message)
            // @todo Error: net::ERR_SOCKS_CONNECTION_FAILED
        }

    }
}