const fs = require('fs')
const path = require('path')
const basePath = path.join(__dirname, 'src/assets')
exports.asyncExample = function() {
    const readFile = function (fileName) {
        return new Promise(function(resolve, reject) {
            fs.readFile(fileName, function(error, data) {
                if (error) return reject(error)
                resolve(data)
            } )
        })
    }

    return {
        // 方法一
        generatorReadFile: function* () {
            const f1 = yield readFile(basePath + '/test01.txt')
            const f2 = yield readFile(basePath + '/test02.txt')
            console.log(f1.toString())
            console.log(f2.toString())
        },
        asyncReadFile: async function() {
            const f1 = await readFile(basePath + '/test01.txt')
            const f2 = await readFile(basePath + '/test02.txt')
            console.log(f1.toString())
            console.log(f2.toString())
        }

    }
}