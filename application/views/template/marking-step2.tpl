{%extends file='layout.tpl'%}
{%block name="page-css"%}
<link rel="stylesheet" type="text/css" href="http://101.69.182.26:8080/css/marking-step.css" />
{%/block%}
{%block name="content"%}
	<!-- 请把自己的代码放在这里 -->
	<div class="wizard">
		<img src="http://101.69.182.26:8080/images/zuoye-step2.jpg" />
	</div>
	
	<div class="task-list ">
	<div class="user-list">
		<input type="hidden" id="eaid" value="{%$eaid%}" />
		<label>待批阅任务</label>
		<table  width="135" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th>学生姓名</th>
			</tr>
			{%foreach key=key item=item from=$students%}
			<tr>
				<td data-id="{%$item['SID']%}">{%$item['StudentName']%}</td>
			</tr>
			{%/foreach%}
		</table>
	</div>
	<form class="exercise-form">
	<div class="task-info">
		请选择同学
	</div>
	</form>	
	<div class="clearfix"></div>
		
	</div>
	<div class="btn-container">
		<button class="return">&nbsp;&nbsp;返回&nbsp;&nbsp;</button>
		<button class="next">&nbsp;&nbsp;完成&nbsp;&nbsp;</button>
	</div>
	
	<div class="clearfix"></div>
	<script id="ex-item" type="text/html">
		<label class="textcenter">学生姓名：<%=name%></label>
		<div class="task-detail">
		<div class="task-header">
		
		第<%=num%>题
		</div>
		<div class="task-desc">
		<span class="tip">&nbsp;题目&nbsp;</span>
		<img src="<%=data.ExerciseTitle%>" width="470px;"/>
		</div>
		<div class="task-desc">
		<span class="tip">&nbsp;内容&nbsp;</span>
		<img src="<%=data.ExerciseContent%>" width="470px;"/>
		</div>
		<div class="task-desc">
		<span class="tip">&nbsp;答案&nbsp;</span>
		<img src="<%=data.AnswerContentPicture%>" width="470px;"/>
		</div>
		<div class="task-desc">
		<div class="textright">【学生答案】</div>
		<img src="<%=data.StudentAnswerPicture%>" width="470px;" alt="没有作答"/>
		</div>
		<div class="task-desc">
		<div class="textright">【学生疑问】</div>
		</div>
		<div class="task-desc">
		<div class="textright">【教师批阅】</div>
		<div class="line-height-40">总&nbsp;&nbsp;&nbsp;&nbsp;分：&nbsp;0分
		</div>
		<div class="line-height-40">是否正确：
			<input type="radio" name="right" value="1" checked>正确
			<input type="radio" name="right" value="2">错误	
		</div>
		<div class="line-height-40">
			给分情况：
			<input style="width:100px;" id="score" type="text" name="score" placeholder="分数">分
			&nbsp;&nbsp;<span id="score-error" style="color:red"></span>
		</div>
		<div class="line-height-40">教师评语：
			<input style="width:358px;" type="text" id="comment" placeholder="请输入教师评语" name="comment">
		</div>
		
		</div>
		</div>
		<div class="textcenter" style="padding-top:20px;"><a class="next-exercise" href="javascript:void(0);">下一题</a></div>
	</script>
{%/block%}
{%block name="page-js"%}
<script type="text/javascript" src="http://101.69.182.26:8080/js/baiduTemplate.js"></script>
<script type="text/javascript" src="http://101.69.182.26:8080/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="http://101.69.182.26:8080/js/marking-step2.js"></script>
{%/block%}