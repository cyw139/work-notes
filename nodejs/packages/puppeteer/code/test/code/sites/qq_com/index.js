const path = require("path");
const dayjs = require('dayjs')
const fs = require('fs')
exports.site_qq_com = {
    async initNewPage(browser, _options = {}) {
        const defaultOptions = {
            url: 'https://www.qq.com/',
            selector: 'ul.nav-main>li.nav-item>a:nth-child(1)',
        }
        const options = {...defaultOptions, ..._options}
        const { selector, url } = options
        const page = await browser.newPage()

        await page.goto(url)
        await page.content()
        return page
    },
    async screenShotIndex(browser, _options = {}) {
        const page = await this.initNewPage(browser, {
            selector: 'ul.nav-main>li.nav-item>a:nth-child(1)',
        })

        const daytimeString = dayjs().format('YYYY-MM-DD_HH_mm_ss')
        await page.hover(selector)
        let times = 15
        const mouseWheel = setInterval(() => {
            if (times-- > 0) {
                console.info(times)
                page.mouse.wheel({deltaY: 300})
            } else {
                const dir = path.join(__dirname, './screenShotIndex/')
                if (!fs.existsSync(dir)) {
                    fs.mkdirSync(dir)
                }
                page.screenshot({
                    fullPage: true,
                    path: dir + daytimeString+ '.png'
                }).then(() => {
                    page.close()
                });
            }

        }, 1000)
    },
    async addScriptTagExample( browser, _options = {}) {
        const page = await this.initNewPage(browser, {
            selector: '',
        })
        // 三种方式：url、本地路径、内容
        // 1、url
        await page.addScriptTag({type: 'module', url: 'https://client.crisp.chat/l.js' })
        // 2、本地路径：位置3
        const filePath = path.join(__dirname, './assets/scripts/test.js')
        console.info(filePath)
        await page.addScriptTag({ path: filePath })
        // 3、内容：位置2
        await page.addScriptTag({ content: "document.querySelector('.tit>a').innerHTML='直接js脚本'"})

        // 4、options 额外参数:
        // Sets the type of the script. Use `module` in order to load an ES2015 module.
        // Sets the id of the script.
        // { type: 'module', id: 'module' }

        return page
    },
    async addStyleTagExample( browser, _options = {}) {
        const page = await this.initNewPage(browser, {
            selector: '',
        })
        // 三种方式：url、本地路径、内容
        // 1、url
        await page.addStyleTag({ url: 'https://pptr.dev/assets/css/styles.c5c7e4fc.css' })
        // 2、本地路径：位置3
        const filePath = path.join(__dirname, './assets/styles/test.css')
        console.info(filePath)
        await page.addStyleTag({ path: filePath })
        // 3、内容：位置2
        await page.addStyleTag({ content: "h1.top-logo{ background-color: red;}"})

    },
    /**
     * 【案例一】www.qq.com
     * [
     *   <ref *1> Frame {
     *     _url: 'https://v.qq.com/thumbplayer-offline-log.html?max_age=3600',
     *     _detached: false,
     *     _loaderId: '441AFC08D95BACD228C2C2A1324C1ED3',
     *     _lifecycleEvents: Set(3) { 'init', 'DOMContentLoaded', 'load' },
     *     _frameManager: FrameManager {
     *       eventsMap: [Map],
     *       emitter: [Object],
     *       _frames: [Map],
     *       _contextIdToContext: [Map],
     *       _isolatedWorlds: [Set],
     *       _client: [CDPSession],
     *       _page: [Page],
     *       _networkManager: [NetworkManager],
     *       _timeoutSettings: [TimeoutSettings],
     *       _mainFrame: [Frame]
     *     },
     *     _parentFrame: Frame {
     *       _url: 'https://www.qq.com/',
     *       _detached: false,
     *       _loaderId: 'DDDDC1421282952C04D2B6EE41492BBE',
     *       _lifecycleEvents: [Set],
     *       _frameManager: [FrameManager],
     *       _parentFrame: null,
     *       _id: 'BC5F69BE63CCA9DC3C8944A98091210B',
     *       _mainWorld: [DOMWorld],
     *       _secondaryWorld: [DOMWorld],
     *       _childFrames: [Set],
     *       _name: undefined
     *     },
     *     _id: 'F27560F9DEE53E8CA645FA91BDEA6297',
     *     _mainWorld: DOMWorld {
     *       _documentPromise: null,
     *       _contextPromise: [Promise],
     *       _contextResolveCallback: null,
     *       _detached: false,
     *       _waitTasks: Set(0) {},
     *       _boundFunctions: Map(0) {},
     *       _ctxBindings: Set(0) {},
     *       _settingUpBinding: null,
     *       _frameManager: [FrameManager],
     *       _frame: [Circular *1],
     *       _timeoutSettings: [TimeoutSettings]
     *     },
     *     _secondaryWorld: DOMWorld {
     *       _documentPromise: null,
     *       _contextPromise: [Promise],
     *       _contextResolveCallback: null,
     *       _detached: false,
     *       _waitTasks: Set(0) {},
     *       _boundFunctions: Map(0) {},
     *       _ctxBindings: Set(0) {},
     *       _settingUpBinding: null,
     *       _frameManager: [FrameManager],
     *       _frame: [Circular *1],
     *       _timeoutSettings: [TimeoutSettings]
     *     },
     *     _childFrames: Set(0) {},
     *     _name: ''
     *   }
     * ]
     * 【案例二】 film.qq.com
     * frame.childFrames:  [
     *   <ref *1> Frame {
     *     _url: 'https://video.qq.com/getcookie/1.0.6/cookie.html?v=1066',
     *     _detached: false,
     *     _loaderId: '6F0EDCB10ED5D09FBD47E26034707A9E',
     *     _lifecycleEvents: Set(3) { 'init', 'load', 'DOMContentLoaded' },
     *     _frameManager: FrameManager {
     *       eventsMap: [Map],
     *       emitter: [Object],
     *       _frames: [Map],
     *       _contextIdToContext: [Map],
     *       _isolatedWorlds: [Set],
     *       _client: [CDPSession],
     *       _page: [Page],
     *       _networkManager: [NetworkManager],
     *       _timeoutSettings: [TimeoutSettings],
     *       _mainFrame: [Frame]
     *     },
     *     _parentFrame: Frame {
     *       _url: 'https://film.qq.com/',
     *       _detached: false,
     *       _loaderId: 'E90A41777818C33C89A634EEC0237CE4',
     *       _lifecycleEvents: [Set],
     *       _frameManager: [FrameManager],
     *       _parentFrame: null,
     *       _id: '42F2AAF9831231F9D2478D7BD6B6FABA',
     *       _mainWorld: [DOMWorld],
     *       _secondaryWorld: [DOMWorld],
     *       _childFrames: [Set],
     *       _name: undefined
     *     },
     *     _id: '383288B9E3EDF3146A16F870F4353DC2',
     *     _mainWorld: DOMWorld {
     *       _documentPromise: null,
     *       _contextPromise: [Promise],
     *       _contextResolveCallback: null,
     *       _detached: false,
     *       _waitTasks: Set(0) {},
     *       _boundFunctions: Map(0) {},
     *       _ctxBindings: Set(0) {},
     *       _settingUpBinding: null,
     *       _frameManager: [FrameManager],
     *       _frame: [Circular *1],
     *       _timeoutSettings: [TimeoutSettings]
     *     },
     *     _secondaryWorld: DOMWorld {
     *       _documentPromise: null,
     *       _contextPromise: [Promise],
     *       _contextResolveCallback: null,
     *       _detached: false,
     *       _waitTasks: Set(0) {},
     *       _boundFunctions: Map(0) {},
     *       _ctxBindings: Set(0) {},
     *       _settingUpBinding: null,
     *       _frameManager: [FrameManager],
     *       _frame: [Circular *1],
     *       _timeoutSettings: [TimeoutSettings]
     *     },
     *     _childFrames: Set(0) {},
     *     _name: 'sync-cookie-iframe'
     *   }
     * ]
     *
     * 就是页面上iframe的数量
     * @param browser
     * @param _options
     * @returns {Promise<void>}
     */
    async showChildFramesOfFrameExample(browser, _options = {}) {
        // const page = await this.initNewPage(browser, {
        //     url: 'https://film.qq.com',
        //     selector: '',
        // })
        const page = await this.addScriptTagExample(browser)
        // iframe处理方案
        // https://stackoverflow.com/questions/46529201/puppeteer-how-to-fill-form-that-is-inside-an-iframe
        await page.waitForSelector('iframe')
        // const iframeSelector = await page.$('iframe#film_qq_com')
        // console.info('iframeSelector: ', iframeSelector)
        // const iframe = await iframeSelector.contentFrame()
        // console.info('iframeSelector.contentFrame(): ', iframe)
        // await iframe.type('.channel_more', 'channel_more', { delay: 100 })
        // 页面的hover影响到frame里的操作
        await page.hover('.nav-main>.nav-item:nth-child(1)')
        for (const frame of page.frames()) {
            if (frame.name() === 'film_qq_com') {
                await frame.content()
                // console.info('film_qq_com_hover: ', frame.$('#keywords'))
                await frame.focus('#keywords')
                // await frame.click('#keywords', { offset: { x: 50, y: 10} })
                frame.hover('.channel_more')
            }
            console.info('frame.childFrames: ' , frame.childFrames())
            console.info('frame.parentFrame: ' , frame.parentFrame())
            console.info('frame.name: ' , frame.name())
            console.info('frame.url: ' , frame.url())
            console.info('frame.title: ' , frame.title())
            // console.info('frame.page: ' , frame.page()) // v5.5不存在
            // dumpFrameTree(frame, '')
        }

        // function dumpFrameTree(frame, indent = ' ') {
        //     console.info(indent + frame.url);
        //     for (let child in frame.childFrames) {
        //         dumpFrameTree(child, indent + '  ');
        //     }
        // }

    },
    async frameFocusAndHoverAndClickExample(browser, _options={}) {

    }
}