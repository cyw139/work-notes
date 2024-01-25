// 案例三： Map构造函数可接受的参数：Array/Set/Map 对象
const array3 = [['id_01', {'name': 'sam'}], ['id_02', {'name': 'George'}]]
const set3 = new Set(array3)
const map3 = new Map(set3)
const map3_1 = new Map(array3)
const map3_2 = new Map(map3_1)
console.info(array3,set3,map3,map3_1, map3_2)