const ws = new WeakSet()
// ws.add(1)
ws.add(Symbol('test'))
const a = {'name': 'sam'}
ws.add(a).add(a)
console.log(ws, ws.has(a))