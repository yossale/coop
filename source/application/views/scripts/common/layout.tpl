<html>
<head>

<title>{$coop.coop_name|escape:"html"|stripslashes}</title>
<link rel="stylesheet" href="{$css_path}/admin.css" />
<script src="{$js_path}/jquery/jquery_latest.js"></script>
<script src="{$js_path}/jquery/validation/jquery.validate.min.js"></script>
<script src="{$js_path}/jquery/infra.js"></script>
</head> 
<body>

<div id="canvas">
	<div id="header">
		<div id="title_and_logged">
			<div id="title">
				<h1>{$coop.coop_name|escape:"html"|stripslashes}</h1>
			</div>
			<div id="logged_box">
				<a>מחובר בתור </a>
				<b>{$loggedUserName}</b><a> - </a>
				<a href="{$public_path}/index/logout">התנתק</a>
			</div>
		</div>
{if $userAccess != "USER"}
		<div id="menu">
			<ul>
			{foreach from=$menu key=link item=title}
				<li>{if $link == $controller}<b>{$title}</b>{else}<a href="{$public_path}/{$link}">{$title}</a>{/if}</li>
			{/foreach}
			</ul>
		</div>
	{/if}
		{if $submenu != null}
		<div id="submenu">
			<ul>
			{foreach from=$submenu key=link item=title}
				<li>{if $link == $action}<b>{$title}</b>{else}<a href="{$public_path}/{$controller}/{$link}">{$title}</a>{/if}</li>
			{/foreach}
			</ul>
		</div>
		{/if}
	</div>
	<div id="main_without_left_panel">
		{include file=$tpl_file}
	</div>
	<br class="clear_both" />
	<div id="bottom">
		<hr />
		{$coop.coop_name|escape:"html"|stripslashes}
	</div>
</div>

</body>
</html>