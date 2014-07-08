{%extends file='layout.tpl'%}
{%block name="page-css"%}
<link rel="stylesheet" type="text/css" href="http://101.69.182.26:8080/css/marking-step.css" />
{%/block%}
{%block name="content"%}
	<!-- 请把自己的代码放在这里 -->
	<div class="wizard">
		<img src="http://101.69.182.26:8080/images/zuoye-step3.jpg" />
	</div>
	
	<div class="task-list">
		<div style="padding-bottom:30px;">【完成人数】28/30</div>
		<label>按题目统计</label>
		<table  width="726" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th>题目名称</th>
				<th>题目类型</th>
				<th>难度系数</th>
				<th>正确率</th>
				<th>平均得分</th>
			</tr>
			{%foreach key=key item=item from=$exercises%}
			<tr>
				<td>第{%$key+1%}题</td>
				<td>{%$item->ExerciseType%}</td>
				<td>{%$item->exerciseLevel%}</td>
				<td>{%$item->RatioCorrect%}%</td>
				<td>{%$item->average%}</td>
			</tr>
			{%/foreach%}
		</table>
		<label style="margin-top:20px;">按学生统计</label>
		<table  width="726" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th>学生姓名</th>
				<th>提交时间</th>
				<th>正确率</th>
				<th>具体情况</th>
			</tr>
			{%foreach key=key item=item from=$students%}
			<tr>
				<td>{%$item->name%}</td>
				<td>{%$item->FinishTime%}</td>
				<td>{%$item->Bratio%}</td>
				<td>查看详情</td>
			</tr>
			{%/foreach%}
		</table>
	</div>
	<div class="btn-container">
		<button class="return">&nbsp;&nbsp;返回&nbsp;&nbsp;</button>
	</div>
{%/block%}