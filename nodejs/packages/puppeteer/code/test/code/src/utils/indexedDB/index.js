exports.indexedDB = {}
/**
 * 打开数据库
 * @param {object} dbName 数据库的名字
 * @param {string} storeName 仓库名称
 * @param {string} version 数据库的版本
 * @return {object} 该函数会返回一个数据库实例
 */
exports.indexedDB.openDB = function (dbName, version = 1) {
    return new Promise((resolve, reject) => {
        // try {
        //
        // }catch (error) {
        //     console.info('打开indexedDB错误：', error.code, error.message)
        // }
        //  兼容浏览器
        var indexedDB =
            window.indexedDB ||
            window.mozIndexedDB ||
            window.webkitIndexedDB ||
            window.msIndexedDB;
        let db;
        // 打开数据库，若没有则会创建
        const request = indexedDB.open(dbName, version);
        // 数据库打开成功回调
        request.onsuccess = function (event) {
            db = event.target.result; // 数据库对象
            console.info("数据库打开成功");
            resolve(db);
        };
        // 数据库打开失败的回调
        request.onerror = function (event) {
            console.info("数据库打开报错");
        };
        // 数据库有更新时候的回调
        request.onupgradeneeded = function (event) {
            // 数据库创建或升级的时候会触发
            console.info("onupgradeneeded");
            db = event.target.result; // 数据库对象
            var objectStore;
            // 创建存储库
            objectStore = db.createObjectStore("signalChat", {
                keyPath: "sequenceId", // 这是主键
                // autoIncrement: true // 实现自增
            });
            // 创建索引，在后面查询数据的时候可以根据索引查
            objectStore.createIndex("link", "link", { unique: false });
            objectStore.createIndex("sequenceId", "sequenceId", { unique: false });
            objectStore.createIndex("messageType", "messageType", {
                unique: false,
            });
        };
    });
}
/**
 * 新增数据
 * @param {object} db 数据库实例
 * @param {string} storeName 仓库名称
 * @param {string} data 数据
 */
exports.indexedDB.addData = function(db, storeName, data) {
    return new Promise((resolve, reject) => {
        const request = db
            .transaction([storeName], "readwrite") // 事务对象 指定表格名称和操作模式（"只读"或"读写"）
            .objectStore(storeName) // 仓库对象
            .add(data);

        request.onsuccess = function (event) {
            console.info("数据写入成功");
            resolve(true)
        };

        request.onerror = function (event) {
            console.info("数据写入失败: ", event.target.errorCode);
            reject(false)
        };
    })

}
/**
 * 通过主键读取数据
 * @param {object} db 数据库实例
 * @param {string} storeName 仓库名称
 * @param {string} key 主键值
 */
exports.indexedDB.getDataByKey = function(db, storeName, key) {
    return new Promise((resolve, reject) => {
        var transaction = db.transaction([storeName]); // 事务
        var objectStore = transaction.objectStore(storeName); // 仓库对象
        var request = objectStore.get(key); // 通过主键获取数据

        request.onerror = function (event) {
            console.info("事务失败");
        };

        request.onsuccess = function (event) {
            console.info("主键查询结果: ", request.result);
            resolve(request.result);
        };
    });
}

/**
 * 通过游标读取数据
 * @param {object} db 数据库实例
 * @param {string} storeName 仓库名称
 */
exports.indexedDB.cursorGetData = function (db, storeName) {
    let list = [];
    var store = db
        .transaction(storeName, "readwrite") // 事务
        .objectStore(storeName); // 仓库对象
    var request = store.openCursor(); // 指针对象
    // 游标开启成功，逐行读数据
    request.onsuccess = function (e) {
        var cursor = e.target.result;
        if (cursor) {
            // 必须要检查
            list.push(cursor.value);
            cursor.continue(); // 遍历了存储对象中的所有内容
        } else {
            console.info("游标读取的数据：", list);
        }
    };
}

/**
 * 通过索引读取数据
 * @param {object} db 数据库实例
 * @param {string} storeName 仓库名称
 * @param {string} indexName 索引名称
 * @param {string} indexValue 索引值
 */
exports.indexedDB.getDataByIndex = function(db, storeName, indexName, indexValue) {
    var store = db.transaction(storeName, "readwrite").objectStore(storeName);
    var request = store.index(indexName).get(indexValue);
    request.onerror = function () {
        console.info("事务失败");
    };
    request.onsuccess = function (e) {
        var result = e.target.result;
        console.info("索引查询结果：", result);
    };
}

/**
 * 通过索引和游标查询记录
 * @param {object} db 数据库实例
 * @param {string} storeName 仓库名称
 * @param {string} indexName 索引名称
 * @param {string} indexValue 索引值
 */
exports.indexedDB.cursorGetDataByIndex = function(db, storeName, indexName, indexValue) {
    let list = [];
    var store = db.transaction(storeName, "readwrite").objectStore(storeName); // 仓库对象
    var request = store
        .index(indexName) // 索引对象
        .openCursor(IDBKeyRange.only(indexValue)); // 指针对象
    request.onsuccess = function (e) {
        var cursor = e.target.result;
        if (cursor) {
            // 必须要检查
            list.push(cursor.value);
            cursor.continue(); // 遍历了存储对象中的所有内容
        } else {
            console.info("游标索引查询结果：", list);
        }
    };
    request.onerror = function (e) {};
}

/**
 * 通过索引和游标分页查询记录
 * @param {object} db 数据库实例
 * @param {string} storeName 仓库名称
 * @param {string} indexName 索引名称
 * @param {string} indexValue 索引值
 * @param {number} page 页码
 * @param {number} pageSize 查询条数
 */
exports.indexedDB.cursorGetDataByIndexAndPage = function(
    db,
    storeName,
    indexName,
    indexValue,
    page,
    pageSize
) {
    let list = [];
    let counter = 0; // 计数器
    let advanced = true; // 是否跳过多少条查询
    var store = db.transaction(storeName, "readwrite").objectStore(storeName); // 仓库对象
    var request = store
        .index(indexName) // 索引对象
        .openCursor(IDBKeyRange.only(indexValue)); // 指针对象
    request.onsuccess = function (e) {
        var cursor = e.target.result;
        if (page > 1 && advanced) {
            advanced = false;
            cursor.advance((page - 1) * pageSize); // 跳过多少条
            return;
        }
        if (cursor) {
            // 必须要检查
            list.push(cursor.value);
            counter++;
            if (counter < pageSize) {
                cursor.continue(); // 遍历了存储对象中的所有内容
            } else {
                cursor = null;
                console.info("分页查询结果", list);
            }
        } else {
            console.info("分页查询结果", list);
        }
    };
    request.onerror = function (e) {};
}

/**
 * 更新数据
 * @param {object} db 数据库实例
 * @param {string} storeName 仓库名称
 * @param {object} data 数据
 */
exports.indexedDB.updateDB = function (db, storeName, data) {
    var request = db
        .transaction([storeName], "readwrite") // 事务对象
        .objectStore(storeName) // 仓库对象
        .put(data);

    request.onsuccess = function () {
        console.info("数据更新成功");
    };

    request.onerror = function () {
        console.info("数据更新失败");
    };
}

/**
 * 通过主键删除数据
 * @param {object} db 数据库实例
 * @param {string} storeName 仓库名称
 * @param {object} id 主键值
 */
exports.indexedDB.deleteDB = function(db, storeName, id) {
    var request = db
        .transaction([storeName], "readwrite")
        .objectStore(storeName)
        .delete(id);

    request.onsuccess = function () {
        console.info("数据删除成功");
    };

    request.onerror = function () {
        console.info("数据删除失败");
    };
}

/**
 * 通过索引和游标删除指定的数据
 * @param {object} db 数据库实例
 * @param {string} storeName 仓库名称
 * @param {string} indexName 索引名
 * @param {object} indexValue 索引值
 */
exports.indexedDB.cursorDelete = function (db, storeName, indexName, indexValue) {
    var store = db.transaction(storeName, "readwrite").objectStore(storeName);
    var request = store
        .index(indexName) // 索引对象
        .openCursor(IDBKeyRange.only(indexValue)); // 指针对象
    request.onsuccess = function (e) {
        var cursor = e.target.result;
        var deleteRequest;
        if (cursor) {
            deleteRequest = cursor.delete(); // 请求删除当前项
            deleteRequest.onerror = function () {
                console.info("游标删除该记录失败");
            };
            deleteRequest.onsuccess = function () {
                console.info("游标删除该记录成功");
            };
            cursor.continue();
        }
    };
    request.onerror = function (e) {};
}

/**
 * 关闭数据库
 * @param {object} db 数据库实例
 */
exports.indexedDB.closeDB = function (db) {
    db.close();
    console.info("数据库已关闭");
}

/**
 * 删除数据库
 * @param {object} dbName 数据库名称
 */
exports.indexedDB.deleteDBAll = function (dbName) {
    console.info(dbName);
    let deleteRequest = window.indexedDB.deleteDatabase(dbName);
    deleteRequest.onerror = function (event) {
        console.info("删除失败");
    };
    deleteRequest.onsuccess = function (event) {
        console.info("删除成功");
    };
}