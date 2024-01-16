const fs = require('fs')
const https = require('https')

exports.downloadImage = async function(page, url, destination) {
    // @todo 下载失败异常；原因可能是下载配置
    // Client network socket disconnected before secure TLS connection was established
    // return new Promise((resolve, reject) => {
    //     const writeStream = fs.createWriteStream(destination)
    //     https.get(url, resp => {
    //         resp.pipe(writeStream)
    //         writeStream.on('finish', () => {
    //             console.info('finish: ')
    //             writeStream.close(() => { resolve(true) })
    //         }).on('error', (error) =>{
    //             console.info('error: ')
    //             fs.unlink(destination, () => {})
    //             reject(error.message)
    //         })
    //
    //     })
    // })

    // await page.goto(url)
    const newPage = await page.browser().newPage()
    newPage.once('response', async response => {
        const responseUrl = response.url()
        console.info('downloading: ', response.request().resourceType(), responseUrl === url,  responseUrl, url)
        if (responseUrl === url) {
            response.buffer().then(file => {
                console.info('downloading file: ')
                const writeStream = fs.createWriteStream(destination)
                writeStream.write(file);
                newPage.close()
            })
        }
    })
    await newPage.goto(url)
}