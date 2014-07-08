{%extends file='layout.tpl'%}
{%block name="page-css"%}
<link rel="stylesheet" type="text/css" href="../css/jqtree.css" />
<link rel="stylesheet" type="text/css" href="../css/material.css" />
<link rel="stylesheet" type="text/css" href="../css/publish.css" />
<script src="../js/tree.jquery.js"></script>
<script src="../js/selects.js"></script>
<style>

</style>
{%/block%}
{%block name="content"%}	
    <!-- 请把自己的代码放在这里 -->
    <form method="post" action="show_resource_list">
    <div class="sub-nav">
        <div class="steps"></div>
        <div class="targets">
            <span>年级选择<select class="sel sel-a"></select></span>
            <span>班级选择<select class="sel sel-b"></select></span>
            <span>分组选择<select class="sel sel-c" name="groupid"></select></span>
            <script>new Selects({%$grouptree%}, [$('.sel-a'),$('.sel-b'),$('.sel-c')]);</script>
        </div>
    </div>
    <div class="sub-content">
        <div class="tech">
            <h6>知识点结构</h6>
            <input type="hidden" name="kid"/>
            <div id="tree"></div>
        </div>
        <div class="detail"></div>
    </div>
    <div class="sub-foot">
        <button class="return">返回</button>
        <button type="submit" class="next">下一步</button>
    </div>
    </form>
    <script>
        var knowlageData = {%$knowledgelist%};
    </script>
    <script>
        var teacherId = null;
        $('.sel-c').change(function(e){
            teacherId = e.currentTarget.value;
            $.ajax({
                url: '/MaterialAssignControl/show_student_list?teacherGroupId='+teacherId, 
                method: "post",
                success: function (html) {
                    $('.detail').html(html);
                }
            });                
        });
        
        $('.sel-a').change(function(e){
            var grade = e.currentTarget.value;
            tree = $('#tree').tree({
                data:knowlageData[grade],
                openedIcon:"/images/f-icon.png",
                closedIcon:"/images/f-icon.png"
            });
        });
        var tree = $('#tree').tree({
            data:knowlageData[0],
            openedIcon:"/images/f-icon.png",
            closedIcon:"/images/f-icon.png"
        });
        $('.sel-c').change();
        function nextStep(e) {
            treeNode =  $('#tree').tree("getSelectedNode");
            if(!treeNode || !treeNode.id){
                alert("请选择知识点");
                e.preventDefault();
            }
            $('[name=kid]').val(treeNode.id);
        }
        $('form').submit(nextStep);
    </script>
{%/block%}
