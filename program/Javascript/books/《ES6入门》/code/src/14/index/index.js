let set12 = new Set([1,2,3])
set12 = new Set([...set12].map(item => item * 2)) // [2,4,6]
console.log(set12)
set12 = new Set(Array.from(set12, item => item * 2)) // [2,4,6]
console.log(set12)
