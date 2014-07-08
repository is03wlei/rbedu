function Selects(data, doms){
    function buildhtml(data, dom, depth){
        var html = "";
        for(var key in data){
            html += "<option value='"+key+"'>";
            if(depth == 2){
                html += data[key];
            } else if(depth == 0) {
                html += "一年级";
            }else{
                html += key;
            }
            html += "<opion>";
        }
        dom.html(html);
    }
    buildhtml(data, doms[0], 0);
    for(var i = 0; i < doms.length; i++){
        (function(i){
            doms[i].on('change', function(){
                var d = data;
                for(var j = 0; j <= i; j++){
                    d = d[doms[j].val()] || d[parseInt(doms[j].val())]; 
                    if(!d){
                        break;
                    }
                }
                if(i+1 < doms.length && d) {
                    buildhtml(d, $(doms[j]), j);
                    doms[j].trigger("change");
                }
            });
        })(i);
    }
    doms[0].trigger("change");
}
