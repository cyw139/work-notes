
const titleSelectors = document.querySelectorAll('.tit');
Array.from(titleSelectors).forEach((selector, index) => {
    if (index === 2) {
        selector.innerHTML = '内部js文件引入'
    }
})

