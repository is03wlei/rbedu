{%extends file="layout.tpl"%}
{%block name="page-css"%}
  <link rel="stylesheet" type="text/css" href="../css/homework.css" />
  <link rel="stylesheet" type="text/css" href="../css/jqtree.css" />
{%/block%}
{%block name="page-js"%}
  <script type="text/javascript" src="../js/baiduTemplate.js"></script>
  <script type="text/javascript" src="../js/homework.js"></script>
  <script type="text/javascript" src="../js/page.js"></script>  
  <script type="text/javascript" src="../js/tree.jquery.js"></script>
    <script type="text/javascript">
        var knowlageData = {%$knowledgelist%};
    </script>
{%/block%}
{%block name="content"%}
    <div id="main_wrapper" class="right-content">

    </div>
    <textarea id='students_page' style="display:none">
       <div class="step">
         <img alt="步骤" src="../images/buzhou01.png" />
       </div>
       <div class="select-cont">
           <label class="grade-lbl">年级选择</label>
           <select name="grade" id="grade_select">
                <option value="2014">一年级</option>
           </select>
           <label class="class-lbl">班级选择</label>
           <select name="class"  id="class_select">
                <option value="1">一班</option>
                <option value="2">二班</option>
                <option value="3">三班</option>
                <option value="4">四班</option>                                                                         
           </select>
           <label class="group-lbl" >分组选择</label>
           <select name="group" id="group_select">
                <option value="1">一组</option>
                <option value="2">二组</option>
                <option value="3">三组</option>
           </select>               
       </div>
       <div class="main-cont">
          <div class="struct-tree">
            <h2>知识点结构</h2>
            <input type="hidden" name="kid"/>
            <div id="tree"></div>            
          </div>
          <div class="std-list">
            <h2>选中学生名单</h2>
            <div id="stu_wrapper">
            </div>
            <p class="std-bottom"><a href="#">查看完整名单</a></p>
          </div>           
       </div>
       <form class="pub-form" name="form1" id="form1">
           <input type="button" value="返回" class="back"/>
           <input type="button" value="下一步" class="next"/>
       </form>      
    </textarea>
    <textarea id='students_table' style="display:none"> 
         <table  class="std-table" border="1">        
        <tr class="table-head">
          <th colspan="2">学生姓名</th>
        </tr>
        <#for(var i=0;i<list.length;i=i+2){#>
           <tr> 
              <td><#-list[i]?list[i].StudentName:''#></td>
              <td><#-list[i+1]?list[i+1].StudentName:''#></td>
           </tr>
        <#}#>
     </table>   
     </textarea>  
    <textarea id='homework_list' style="display:none">
      <div class="step">
         <img alt="步骤" src="../images/buzhou02.png" />
       </div>
       <div class="select-cont">
         <div class="ex-cont">
           <h2>题目类型</h2>
           <form class="exercise-form" name="exercise_form" id="exercise_form">
              <input type="radio" name="ex-type" value="sc" />单选
              <input type="radio" name="ex-type" value="mc" />多选
              <input type="radio" name="ex-type" value="jq" />判断
              <input type="radio" name="ex-type" value="bq" />填空
              <input type="radio" name="ex-type" value="sq" />问答                     
           </form>
         </div>
         <div class="level-cont">
           <h2>题目难度</h2>
           <form class="level-form" name="level_form"  id="level_form">
              <input type="radio" name="level-type" value="1" />容易
              <input type="radio" name="level-type" value="2" />一般
              <input type="radio" name="level-type" value="3" />中等
              <input type="radio" name="level-type" value="4" />困难                      
           </form>
         </div>             
       </div>
       <div class="hmwork-cont" >
        <#for(var i=0;i<worklist.length;i++){#>
          <table class="hmwork-table" id="work-table-<#-i#>" border="1" data-index="<#-i#>" >
            <tr>
              <th class="work-title" >第<#-i+1#>题</th>
              <th class="add-work">
                  <span><em  title="加入作业" data-type="add" data-eid="<#-worklist[i].EID#>"  data-extype="<#-worklist[i].ExerciseType#>"></em><i>加入作业</i></span>
              </th>
            </tr>
            <tr>
              <td colspan="2">
                 <p>
                   <span class="d-title">问题</span>
                   <img src="<#-worklist[i].ExerciseTitle#>" alt="问题描述" />
                 </p>
                 <p>
                   <span class="d-title">描述</span>
                   <img src="<#-worklist[i].ExerciseContent#>" alt="问题描述" />
                 </p>
                 <p class="ans-cont">
                    <span class="d-title">答案</span>
                    <img class="ex-content" src="<#-worklist[i].AnswerContentPicture#>" alt="问题描述" id="ans-pic-<#-i#>" />
                 </p>                 
                 <p class="ans"  id="ans-btn-<#-i#>" data-index="<#-i#>" >查看详细答案</p>
              </td>
            </tr>               
          </table>
        <#}#>                  
       </div>
       <div class="page" id="homework-page">
        分页组件
       </div>
       <form class="pub-form" name="form2" id="form2">
           <input type="button" value="返回" class="back"/>
           <input type="button" value="下一步" class="next"/>
       </form>       
    </textarea> 
    <textarea id="pub_list" style="display:none">
       <div class="step">
         <img alt="步骤" src="../images/buzhou03.png" />
       </div>
      <dl class="hm-list"> 
        <dt>[知识点]: </dt> 
        <dd><#-pubdata.kname#></dd>
        <dt>[学生组]: </dt> 
        <dd><#-pubdata.gname#></dd> 
        <dt>[发布内容]: </dt> 
        <dd>
          <table class="pub-table" border="1">
              <tr >
                 <th >题型</th>
                 <th >数量</th>
              </tr>
              <tr>
                <td><#-hmwork_data.sc.name#>题</td>
                <td><#-hmwork_data.sc.num#></td>
              </tr>
              <tr>
                <td><#-hmwork_data.mc.name#>题</td>
                <td><#-hmwork_data.mc.num#></td>
              </tr>
              <tr>
                <td><#-hmwork_data.jq.name#>题</td>
                <td><#-hmwork_data.jq.num#></td>
              </tr>
              <tr>
                <td><#-hmwork_data.bq.name#>题</td>
                <td><#-hmwork_data.bq.num#></td>
              </tr> 
              <tr>
                <td><#-hmwork_data.sq.name#>题</td>
                <td><#-hmwork_data.sq.num#></td>
              </tr>                                                        
          </table>
        </dd> 
      </dl> 
    <form class="pub-form" name="form3" id="form3">
         <input type="button" value="上一步" class="back"/>
         <input type="button" value="发布作业" class="next"/>
     </form>         
    </textarea>
{%/block%}
