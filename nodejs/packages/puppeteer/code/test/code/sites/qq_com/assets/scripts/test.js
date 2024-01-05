
const titleSelectors = document.querySelectorAll('.tit');
Array.from(titleSelectors).forEach((selector, index) => {
    if (index === 2) {
        selector.innerHTML = '内部js文件引入'
    }
})
createIframe('film_qq_com', 'https://film.qq.com')
function createIframe(id,url,width = 800,height = 800,onLoadCallback = () => {},timeOut = 1000,timeOutCallback = () => {}){
    var timeOutVar = setTimeout(function(){
        clearTimeout(timeOutVar);
        timeOutCallback.apply(this, arguments);
        return ;
    }, timeOut);
    var iframe = document.createElement("iframe");
    iframe.id=id;
    iframe.width=width;
    iframe.height=height;
    iframe.src=url;
    if (iframe.attachEvent){
        iframe.attachEvent("onload", function(){
            clearTimeout(timeOutVar);
            onLoadCallback.apply(this, arguments);
        });
    } else {
        iframe.onload = function(){
            clearTimeout(timeOutVar);
            onLoadCallback.apply(this, arguments);
        };
    }
    document.body.before(iframe);
    return iframe;
}
