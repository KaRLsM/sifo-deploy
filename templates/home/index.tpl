<!DOCTYPE html>
<html dir="ltr" lang="es-ES">
<head>
	<title>SIFO Deploy Control | Utilidades GIT hipervitaminadas</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language"  content="es_ES" />
	<link rel="stylesheet" type="text/css" href="css/deploy.css" media="screen" />
</head>
<body>
<div id="container">
	<div id="content">
		<header>
			<h1><strong>SIFO deploy control</strong> v0.3</h1>
			<nav>
				<ul>
					<li><a title="Deploy options" href="#deploy">Deploy</a></li>
					<li><a title="Database options" href="#database">Databases</a></li>
				</ul>
			</nav>
		</header>
		{foreach from=$groups item=projects key=group}
		<div id="group_{$group}" class="util_box">
			<h2>{$group}</h2>
			{foreach from=$projects key=project_id item=project}
			<form method="post" action="">
			<input type="hidden" name="group" value="{$group}" />
			<input type="hidden" name="project_id" value="{$project_id}" />
			<div class="repo_box">
				<div class="repo_title clearfix">
					<h3 class="repo_name">{$project.name}</h3>
					<p>Rev.: <abbr title="{$project.revision|trim}">{$project.revision|truncate:10}</abbr></p>
				</div>
				<div class="repo_head clearfix">
					<div class="repo_head_left">
						<p>Atomic update: <strong>{if $project.atomic}yes{else}no{/if}</strong></p>
						<p>Server URL: <strong>{$project.working_copy}</strong></p>
					</div>
					<div class="repo_head_right">
						<input type="submit" name="update_head" class="ok_btn" value="Update to Head" />
						<br />
						<input type="submit" name="update_revision" class="ok_btn update_rev" value="Update to Rev." />
						<input required type="text" name="revision" value="" size="2" class="input_text" />
					</div>
				</div>
				{if $project.log}
				<div class="repo_content">
					<div class="log_box">
						<pre>{$project.log}</pre>
					</div>
				</div>
				{/if}
			</div>
			</form>
			{/foreach}
		</div>
		{/foreach}
		<h2 id="database">Databases</h2>
		<div id="db01" class="util_box">
			<form method="post" action="">
			<div class="repo_head clearfix">
				<div class="repo_head_left">
					<p>musicjumble Pre-production</p>
					<p><strong>SQL Dump</strong>:</p>
				</div>
				<div class="repo_head_right">
					<input type="submit" name="sql_dump" class="ok_btn" value="Download SQL Dump" />
				</div>
			</div>
			<div class="repo_content">
				<div class="log_box">
					<pre>1 - Download the latest database dump.
2 - Put the gzipped dump file "mj_dump.sql.gz" in the /libs/utils folder.
3 - Execute the script: <a href="http://utils.vm/database-import">http://utils.vm/database-import</a>
4 - Enjoy! :)</pre>
				</div>
			</div>
			</form>
		</div>

	</div>
</div>
</body>
</html>
