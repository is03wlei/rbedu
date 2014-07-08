{%extends file='layout.tpl'%}
{%block name="page-css"%}
<link rel="stylesheet" type="text/css" href="../css/jqtree.css" />
<link rel="stylesheet" type="text/css" href="../css/material.css" />
<link rel="stylesheet" type="text/css" href="../css/publish.css" />
<style>
.sub-content{
    border-top: 1px dashed #ddd;
}
.file-detail{
    margin-top: 30px;
    border: 1px solid black;
    border-radius: 3px;
}
.file-content p{
    display: inline-block;
    width: 640px;
    margin-left: 10px;
    vertical-align: middle;
}
.file-content img{
    vertical-align: middle;
    border:none;
    width: 55px;
    height: 64px;
}
.file-head{
    border-bottom: 1px solid black;
    background: #fcc;
    line-height: 34px;
    height: 34px;
    position: relative;
}
.file-title{
    width: 80%;
    text-indent: 20px;
    display:inline-block;
}
.file-action{
    width: 19%;
    display: inline-block;
    text-align: center;
    color: red;
    cursor: pointer;
    border-left: 1px solid black;
    position:absolute;
    right: 0;
    top: 0;
}
.file-aciton-txt{
    vertical-align: middle;
}
.file-action:hover{
    background-color: #eaa;
}
.added .file-action{
         background: #ec8;
}
.file-content{
    padding: 30px;
}
.icon {
    display: inline-block;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    text-align: center;
    vertical-align: middle;
    line-height: 16px;
    margin-right: 5px;
    color: #fcc;
    background-color: red;
    font-style: normal;
    font-weight: bold;
}
.pager{
    text-align: center;
    margin-top: 10px;
}
.btn{
    display: inline-block;
    padding: 10px;
    background: #F7F7F7;
    margin: 0 5px;
    border: 1px solid #eee;
    border-radius: 4px;
    cursor: pointer;
}
.btn.active{
    background: #FFA7A7;
}
.btn.disabled{
    background: #e7e7e7;
    color: #aaa;
}
.types {
    padding-bottom: 60px;
}
.types span{
    margin-left: 20px;
}
</style>
<script src="../js/tree.jquery.js"></script>
<script src="../js/selects.js"></script>
{%/block%}
{%block name="content"%}	
    <!-- 请把自己的代码放在这里 -->
    <div class="sub-nav">
        <div class="steps steps-2"></div>
        <div class="types">
            <h6>资料类型</h6>
            <span onclick="location.href='/MaterialAssignControl/show_resource_list?type=video'"><label>视频<input type="radio" name="cate" value="video" {%if $resourcetype=="video"%}checked="checked"{%/if%}/></label></span>
            <span onclick="location.href='/MaterialAssignControl/show_resource_list?type=doc'"><label>文档<input type="radio" name="cate" value="doc"  {%if $resourcetype=="doc"%}checked="checked"{%/if%}/></label></span>
        </div>
    </div>
    <div class="sub-content">
        {%foreach from=$itemlist name=datails item=res%}
        <div class="file-detail{%if $res.IsSelectItem%} added{%/if%}" data-id="{%$res.ItemId%}" data-type="{%$res.SourceType%}">
            <div class="file-head">
                <div class="file-title">资料名称：{%$res.Title%}</div>
                <div class="file-action"><i class="icon">+</i><span class="file-action-txt">{%if $res.IsSelectItem == 0%}加入任务{%else%}已加入{%/if%}</span></div>
            </div>
            <div class="file-content">
                <img src="/images/detail-icon-{%$res.SourceType%}.png" >
                <p>{%$res.Description%}</p>
            </div>
        </div>    
        {%/foreach%}
        <div class="pager">
            <span class="btn{%if $currentpage == 0%} disabled"{%else%}" onclick="gopage({%$currentpage-1%})"{%/if%}>&lt; 上一页</span>
            {%section name="loop" loop=$sumpage %}
                <span class="btn{%if $currentpage == $smarty.section.loop.index%} active{%/if%}" onclick="gopage({%$smarty.section.loop.index%})">{%$smarty.section.loop.index + 1%}</span>
            {%/section%}
            <span class="btn{%if $currentpage+1 == $sumpage%} disabled"{%else%}" onclick="gopage({%$currentpage+1%})"{%/if%}>下一页 &gt;</span>
            <script>
                function gopage(pagenum){
                    var h = location.href;
                    if(h.indexOf("pagenumber=") == -1){
                            if(h.indexOf('?')==-1){
                                location.href = h+"?pagenumber="+pagenum;
                            }else{
                                location.href = h+"&pagenumber="+pagenum;
                            }
                    }else{
                        location.href = h.replace(/pagenumber=[\d]*/, "pagenumber="+pagenum); 
                    }
                }
                $('body').on('click', '.file-action',function(e){
                    var t = $(e.currentTarget).closest('.file-detail');
                    var id = t.attr('data-id');
                    var type = t.attr('data-type');
                    var isadd = !t.hasClass('added');
                    $.ajax({
                        url: "material_selection",
                        method: "post",
                        dataType: "json",
                        data: {
                            itemid: id,
                            resourcetype: type,
                            isadd: isadd
                        },
                        success: function(data){
                            if(data.status != 0){
                                alert("出错啦");
                                return;
                            }
                            if(isadd){
                                t.addClass('added');
                                t.find(".file-action-txt").html("已加入");
                            } else {
                                t.removeClass('added');
                                t.find(".file-action-txt").html("加入任务");
                            }
                        },
                        error: function(){
                            alert("请求失败，请稍后再试");
                        }
                    })
                });
            </script>    
        </div>
    </div>
    <div class="sub-foot">
        <button class="return" onclick="location.href='show_teacher_group'">返回</button>
        <button class="next" onclick="location.href='publish_info_confirm'">下一步</button>
    </div>
{%/block%}
