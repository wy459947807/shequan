//格式话日期
template.helper('emotionFormat', function(msg) {

    for(index in dataInfo.emotion){
        regExp = new RegExp("\\["+dataInfo.emotion[index]+"\\]", "g");
        msg = msg.replace(regExp, '<img src="images/emotion/'+index+'.gif" title="'+dataInfo.emotion[index]+'"/>');
    }
    return msg;
    
});
/*
function replaceAll(FindText, RepText) {
    regExp = new RegExp(FindText, "g");
    return this.replace(regExp, RepText);
}*/
