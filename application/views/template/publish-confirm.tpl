{%extends file='layout.tpl'%}
{%block name="page-css"%}
<link rel="stylesheet" type="text/css" href="../css/jqtree.css" />
<link rel="stylesheet" type="text/css" href="../css/material.css" />
<link rel="stylesheet" type="text/css" href="../css/publish.css" />
<script src="../js/tree.jquery.js"></script>
<script src="../js/selects.js"></script>
{%/block%}
{%block name="content"%}	
    <!-- 请把自己的代码放在这里 -->
    <div class="sub-nav">
        <div class="steps steps-3"></div>
    </div>
    <div class="sub-content">
        <div class="tline">[知识点] : {%$KnowledgeName%}</div>
        <div class="tline">[学生组] : {%$GroupName%}</div>
        <div class="tline">[发布内容] : </div>
            <table>
                <thead><tr><th>题型</th><th>数量</th></tr></thead>
                <tr><td>视频</td><td>{%$VideoCount%}</td></tr>
                <tr><td>文档</td><td>{%$DocumentCount%}</td></tr>
            </table>
    </div>
    <div class="sub-foot">
        <button class="return" onclick="location.href='show_resource_list'">返回</button>
        <button class="next" onclick="location.href='publish_material_assignment'">发布任务</button>
    </div>
{%/block%}
