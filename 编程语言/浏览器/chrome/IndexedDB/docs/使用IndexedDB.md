# 使用 IndexedDB
- [ ] `IndexedDB 是一种在用户浏览器内持久化存储数据的方法`。它可以让你创建具有丰富查询能力的 Web 应用，而无需考虑网络可用性，因此你的应用在在线和离线时都可以正常运行。
## 1、关于本文档
- [ ] 本篇教程将指导你如何使用 IndexedDB 的异步 API。如果你对 IndexedDB 还不熟悉，你应该首先阅读文章：[IndexedDB 的关键特性和基本术语 (en-US)](https://developer.mozilla.org/en-US/docs/Web/API/IndexedDB_API/Basic_Terminology)。
- [ ] 有关 IndexedDB API 的参考手册，请参见 [IndexedDB API](https://developer.mozilla.org/zh-CN/docs/Web/API/IndexedDB_API) 这篇文章及其子页面。文章内容包括 IndexedDB 使用的对象类型，以及异步 API（同步 API 已从规范中删除）的方法。
## 2、基本模式
- IndexedDB 鼓励使用的基本模式如下所示：
```shell
# 1. 打开数据库。
# 2. 在数据库中创建一个对象存储（object store）。
# 3. 启动事务，并发送一个请求来执行一些数据库操作，如添加或获取数据等。
# 4. 通过监听正确类型的 DOM 事件以等待操作完成。
# 5. 对结果进行一些操作（可以在 request 对象中找到）
```
## 3、生成和构建一个对象存储
### 3.1、打开数据库
```javascript
// 1. 打开数据库
const request = window.indexedDB.open('www_facebook_com', 1)
// 2. 生成处理器
request.onerror = event => {
    console.error("为什么不允许我的 web 应用使用 IndexedDB！");
}
request.onsuccess = event => {
    db = event.target.result;
}
```
- [ ] open 请求不会立即打开数据库或者开始一个事务。
- ① 对 open() 函数的调用会返回一个我们可以作为事件来处理的包含结果（result，如果成功的话）或者错误值的 [IDBOpenDBRequest](https://developer.mozilla.org/en-US/docs/Web/API/IDBOpenDBRequest) (en-US) 对象。
- ② 在 IndexedDB 中的大部分异步方法做的都是同样的事情 —— 返回一个包含结果或错误的 [IDBRequest](https://developer.mozilla.org/zh-CN/docs/Web/API/IDBRequest) 对象。
- open 函数的结果是一个 `IDBDatabase` 对象的实例。
- [ ] open 方法的二个参数是数据库的版本号。数据库的版本决定了数据库模式（schema），即数据库的对象存储（object store）以及存储结构。
- ① 如果数据库不存在，`open` 操作会创建该数据库，然后触发 `onupgradeneeded` 事件。你需要在该事件的处理器中创建数据库模式。
- ② 如果数据库已经存在，但你指定了一个更高的数据库版本，会直接触发 `onupgradeneeded` 事件，允许你在处理器中更新数据库模式。
- 我们在后面的[创建或更新数据库的版本](https://developer.mozilla.org/zh-CN/docs/Web/API/IndexedDB_API/Using_IndexedDB#%E5%88%9B%E5%BB%BA%E6%88%96%E6%9B%B4%E6%96%B0%E6%95%B0%E6%8D%AE%E5%BA%93%E7%9A%84%E7%89%88%E6%9C%AC) 和 [IDBFactory.open](https://developer.mozilla.org/zh-CN/docs/Web/API/IDBFactory/open) 参考页中会提到更多有关这方面的内容。
> 警告： 版本号是一个 unsigned long long 数字，这意味着它可以是一个特别大的数字，也意味着不能使用浮点数，否则它将会被转换成不超过它的最近整数，这可能导致事务无法启动，upgradeneeded 事件也不会被触发。例如，不要使用 2.4 作为版本号：const request = indexedDB.open("MyTestDatabase", 2.4); // 不要这么做，因为版本会被取整为 2

### 3.2、生成处理器
- [ ] IndexedDB API 以满足尽可能地减少对错误处理的需求而设计，所以你可能不会看到有很多的错误事件（至少，不会在你已经习惯了这些 API 之后！）。然而在打开数据库的情况下，还是有一些会产生错误事件的常见情况。最有可能出现的问题是用户决定不允许你的 web 应用创建数据库。IndexedDB 的主要设计目标之一就是允许大量数据可以被存储以供离线使用。（要了解关于针对每个浏览器你可以有多少存储空间的更多内容，请参见[浏览器存储限制和清理标准页面的数据存储限制](https://developer.mozilla.org/zh-CN/docs/Web/API/Storage_API/Storage_quotas_and_eviction_criteria#%E6%95%B0%E6%8D%AE%E5%AD%98%E5%82%A8%E9%99%90%E5%88%B6)）。
- [ ] 显然，浏览器不希望允许某些广告网络或恶意网站来污染你的计算机，所以浏览器会在任意给定的 web 应用`首次尝试打开 IndexedDB 以存储数据时对用户进行提醒`。用户可以选择允许访问或者拒绝访问。
- 此外，`浏览器的隐私模式`（Firefox 的隐私浏览模式和 Chrome 的无痕模式，但截至 2021 年 5 月，Firefox 尚未实现此特性，所以你仍然无法在 Firefox 的隐私浏览中使用 IndexedDB）下，`IndexedDB 存储仅在内存中存在至隐私会话结束`。
- [ ] 现在，假设用户已经允许了你的要创建数据库的请求，同时你也已经收到了一个触发了 success 回调的 success 事件；然后呢？这里的请求（request）是通过调用 indexedDB.open() 产生的，所以 `request.result 是一个 IDBDatabase 的实例`，而且你肯定希望将其保存下来以供后续使用。你的代码看起来可能像这样：

### 3.3、错误处理
- [ ] 如上文所述，`错误事件遵循冒泡机制`。错误事件都是针对产生这些错误的请求的，然后事件冒泡到事务，然后最终到达数据库对象。如果你希望避免为所有的请求都增加错误处理程序，你可以仅对数据库对象添加错误处理器，像这样：
```javascript
db.onerror = (event) => {
  // 针对此数据库请求的所有错误的通用错误处理器！
  console.error(`数据库错误：${event.target.errorCode}`);
};
```
- [ ] 在打开数据库时常见的可能出现的错误之一是 `VER_ERR`。这表明存储在磁盘上的数据库的版本高于你试图打开的版本。这是一种必须要被错误处理器处理的一种出错情况。

### 3.4、创建或更新数据库的版本
- [ ] 当你创建一个新的数据库或者增加已存在的数据库的版本号（当打开数据库时，指定一个比之前更大的版本号），会触发 onupgradeneeded 事件，IDBVersionChangeEvent (en-US) 对象会作为参数传递给绑定在 request.result（例如示例中的 db）上的 onversionchange 事件处理器。在 upgradeneeded 事件的处理器中，你应该创建该数据库版本需要的对象存储（object store）：