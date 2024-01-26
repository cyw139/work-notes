// A、对同一个键多次赋值，后面的值将覆盖前面的值。
const map4 = new Map()
map4.set(1, 'aaa').set(1, 'bbb')
console.log(map4.get(1)) // bbb
// B、如果读取一个未知的键，则返回undefined。
console.log(map4.get('asdfsa')) // undefined
// C、只有对同一个对象的引用，Map 结构才将其视为同一个键。
// Map 的键实际上是跟内存地址绑定的，只要内存地址不一样，就视为两个键
const key4 = ['aa']
map4.set(['aa'], 1)
console.log(map4.get(['aa'])) // undefined
map4.set(key4, 2)
console.log(map4.get(key4)) // 2
// D、如果 Map 的键是一个简单类型的值（数字、字符串、布尔值），
// 则只要两个值严格相等，Map 将其视为一个键，比如0和-0就是一个键，
// 布尔值true和字符串true则是两个不同的键。
// 另外，undefined和null也是两个不同的键。虽然NaN不严格相等于自身，
// 但 Map 将其视为同一个键。
map4.set(-0, 123)
console.log(map4.get(+0)) // 123
map4.set(true, 1)
map4.set('true', 2)
console.log(map4.get(true), map4.get('true')) // 1,2
map4.set(undefined, 3)
map4.set(null, 4)
console.log(map4.get(undefined), map4.get(null)) // 3, 4
map4.set(NaN, 5)
map4.set(NaN, 6)
console.log(map4.get(NaN)) // 6