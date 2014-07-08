<div class="left-menu">
	<div class="jiaoxue">
		<h2 class="list-title">教学管理</h2>
		<ul>
			<li>作业发布</li>
			<li class="{%if $sitebar eq 'marking'%}current{%/if%}" onclick="location.href='/ExerciseRecordJudge'">作业批阅</li>
			<li>课件发布</li>
		</ul>
	</div>
	<div class="ziliao">
		<h2 class="list-title">资料管理</h2>
		<ul>
			<li class="{%if $sitebar eq 'exercise'%}current{%/if%}" onclick="location.href='/ExerciseEditControl/show_knowledge_list'">习题管理</li>
			<li class="{%if $sitebar eq 'material'%}current{%/if%}" onclick="location.href='/material'">课件管理</li>
		</ul>
	</div>
	<div class="kehou">
		<h2 class="list-title">课后诊断</h2>
		<ul>
			<li>作业诊断</li>
		</ul>            	
	</div>
	<div class="banji">
		<h2 class="list-title">班级管理</h2>
		<ul>
			<li>学生管理</li>
		</ul>            	
	</div>
</div>