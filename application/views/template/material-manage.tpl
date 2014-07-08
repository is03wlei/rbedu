{%extends file='layout.tpl'%}
{%block name="page-css"%}
<link rel="stylesheet" type="text/css" href="../css/jqtree.css" />
<link rel="stylesheet" type="text/css" href="../css/material.css" />
<link rel="stylesheet" type="text/css" href="../css/webuploader.css" />
{%/block%}
{%block name="content"%}
	<!-- 请把自己的代码放在这里 -->
	<form id="material-info" method="POST" action="">
	<div class="material-type">
		<label>资料类型</label><br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" value="0" name="materialType" checked="checked" />视频资料
		<input type="radio" value="1" name="materialType"  />文档资料
	</div>
	<div class="material-type">
		<label>资料难度</label><br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" value="0" name="level" checked="checked" />容易
		<input type="radio" value="1" name="level"  />一般
		<input type="radio" value="2" name="level"  />中等
		<input type="radio" value="3" name="level"  />困难
	</div>
	<div class="material-info">
		<div class="knowledge">
			<label>知识点结构</label>
			<input type="hidden" name="kid"/>
			<div id="file-error-tree" class="help-block" style="display:none;">
					 请选择知识点
			</div>
			<div id="tree"></div>
		</div>
		<div class="material">
			<span>资料名称：</span><input id="title" style="width:200px;" type="text" name="title" /><br /><br />
			<span style="vertical-align: top">资料描述：</span><textarea id="description" style="width:350px;height:150px;"  name="description" ></textarea><br /><br />
			<div id="uploader" class="wu-example">
				<!--用来存放文件信息-->
				<div id="thelist" class="uploader-list"></div>
				<div class="btns">
					<div id="picker">选择文件</div>
				</div>
			</div>
            <div id="file-error" class="help-block" style="display:none;">
                     请选择文件
            </div>
		</div>
		<div class="clearfix"></div>
	</div>
	</form>
	<div class="btn-container">
		<button id="submit-return" class="return">&nbsp;&nbsp;提交并返回&nbsp;&nbsp;</button>
		<button id="submit-continue" class="next">&nbsp;&nbsp;继续新增&nbsp;&nbsp;</button>
	</div>
{%/block%}
{%block name="page-js"%}
<script>
	var knowlageData = {%$knowledgelist%};
	console.log(knowlageData);
</script>
<script type="text/javascript" src="../js/webuploader.min.js"></script>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<script type="text/javascript" src="../js/tree.jquery.js"></script>
<script type="text/javascript" src="../js/material-manage.js"></script>

{%/block%}
