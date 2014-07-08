<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta name="description" content="优秀教育网">
<meta name="keywords" content="优秀教育网">
<title>{%block name="title"%}优秀教育网{%/block%}</title>
{%block name="commmon-css-js"%}
<link rel="stylesheet" type="text/css" href="http://101.69.182.26:8080/css/style.css" />
<script type="text/javascript" src="http://101.69.182.26:8080/js/jquery-1.11.1.min.js"></script>
{%/block%}
{%block name="page-css"%}
{%/block%}
</head>
<body >
	<div class="wrapper">
	<!-- HEADER BEGIN -->
	{%block name="header"%}
	{%include file='header.tpl'%}
	{%/block%}
	<!-- HEADER END -->
	<!-- NAV BEGIN -->
	{%block name="nav"%}
	{%include file='nav.tpl'%}
	{%/block%}
	<!-- NAV END -->
	<div id="contWrapper" class="content">
	<!-- SIDEBAR BEGIN -->
	{%block name="sidebar"%}
	{%include file='sidebar.tpl'%}
	{%/block%}
	<!-- SIDEBAR END -->
	
		<div class="main-container">
			<!-- CONTENT BEGIN -->
			{%block name="content"%}
			<!-- 请把自己的代码放在这里 -->
			{%/block%}
			<!-- CONTENT END -->
		</div>
	
	</div>
	<!-- FOOTER BEGIN -->
	{%block name="footer"%}
	{%include file='footer.tpl'%}
	{%/block%}
	<!-- FOOTER END -->
	
</div>
{%block name="page-js"%}
{%/block%}
</body>
</html>