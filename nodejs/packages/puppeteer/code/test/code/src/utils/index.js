const fs = require('fs')
const https = require('https')
const axios = require('axios')

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

    // return new Promise(async (resolve, reject) => {
    //     const newPage = await page.browser().newPage()
    //     newPage.on('response', async response => {
    //         const responseUrl = response.url()
    //         console.info('downloading: ', response.request().resourceType(), responseUrl === url,  responseUrl, url)
    //         if (responseUrl === url) {
    //             response.buffer().then(file => {
    //                 console.info('downloading file: ', destination)
    //                 const writeStream = fs.createWriteStream(destination)
    //                 const result = writeStream.write(file)
    //                 console.info('download result: ' , result)
    //                 if (result) {
    //                     resolve(true)
    //                 }
    //             })
    //         }
    //     })
    //     await newPage.goto(url, { waitUntil: 'networkidle0'})
    //     await newPage.close()
    // })

    return new Promise(async (resolve, reject) => {
        try {
            console.info('savePath: ', destination)
            const writer = fs.createWriteStream(destination)
            const response = await axios({
                url,
                method: 'GET',
                responseType: 'stream'
            })
            await response.data.pipe(writer)
            writer.on('finish', resolve)
            writer.on('error', reject)
        }catch(error) {
            console.info('error: ', error.code, error.message)
            reject( error.code + ': '+  error.message)
        }
    })
}