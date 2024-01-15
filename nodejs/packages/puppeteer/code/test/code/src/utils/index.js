const fs = require('fs')
const https = require('https')
exports.downloadImage = function(url, destination) {
    return new Promise((resolve, reject) => {
        const writeStream = fs.createWriteStream(destination)
        https.get(url, resp => {
            resp.pipe(writeStream)
            writeStream.on('finish', () => {
                console.info('finish: ')
                writeStream.close(() => { resolve(true) })
            }).on('error', (error) =>{
                console.info('error: ')
                fs.unlink(destination, () => {})
                reject(error.message)
            })

        })
    })
}