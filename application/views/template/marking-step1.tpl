{%extends file='layout.tpl'%}
{%block name="page-css"%}
<link rel="stylesheet" type="text/css" href="../css/marking-step.css" />
{%/block%}
{%block name="content"%}
	<!-- 请把自己的代码放在这里 -->
	<div class="wizard">
		<img src="../images/zuoye-step1.jpg" />
	</div>
	<div class="select">
		<label>选择年级</label>
		<select id="grade">
			<option value="2012">一年级</option>
		</select>
		&nbsp;&nbsp;&nbsp;
		<label>选择班级</label>
		<select id="class">
			<option value="一班">一班</option>
			<option value="二班">二班</option>
			<option value="三班">三班</option>
			<option value="四班">四班</option>
		</select>
	</div>
	<div id="task-container" class="task-list">
		
	</div>
	<div class="btn-container">
		<button class="return">&nbsp;&nbsp;返回&nbsp;&nbsp;</button>
		<button class="next">&nbsp;&nbsp;下一步&nbsp;&nbsp;</button>
	</div>
	<script id="task-content" type="text/html">
		<label>待批阅任务</label>
		<table  width="726" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th>任务名称</th>
				<th>题目数量</th>
				<th>发布时间</th>
				<th>完成人数/总人数</th>
				<th>批阅</th>
			</tr>
			<% for(var i=0;i<data.length;i++){ tmp=data[i]; console.log(tmp); %>
			<tr>
				<td><%=tmp.ExerciseName%></td>
				<td><%=tmp.ExerciseNumber%></td>
				<td><%=tmp.PublishTime%></td>
				<td><%=tmp.finishCount%>/<%=tmp.allCount%></td>
				<% if(tmp.finishCount){ %>
				<td><a href="/ExerciseRecordJudge/judge/<%=tmp.EAID%>">继续批阅</a></td>
				<% }else{ %>
				<td><a href="/ExerciseRecordJudge/judge/<%=tmp.EAID%>">开始批阅</a></td>
				<% } %>
			</tr>
			<% } %>
		</table>
	</script>
{%/block%}
{%block name="page-js"%}
<script type="text/javascript" src="../js/baiduTemplate.js"></script>
<script type="text/javascript" src="../js/marking-step1.js"></script>
{%/block%}