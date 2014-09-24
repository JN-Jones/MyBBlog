<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

// Creates an array of our templates
$mybblog_templates[] = array(
	"title"		=> "mybblog",
	"template"	=> '<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->mybblog}</title>
{$headerinclude}
</head>
<body>
{$header}

{$write}
{$articles}

<br class="clear" />
{$footer}
</body>
</html>'
);

$mybblog_templates[] = array(
	"title"		=> "mybblog_articles",
	"template"	=> '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
	<td class="thead"><strong><a href="mybblog.php?action=view&id={$article->id}">{$article->title}</a></strong></td>
</tr>
<tr>
	<td class="tcat"><span class="smalltext">{$posted}</span></td>
</tr>
<tr>
	<td class="trow1">{$preview}</td>
</tr>
<tr>
	<td class="tcat">{$tags}<span style="float: right;">{$comments}</span></td>
</tr>
</table>
<br />'
);

$mybblog_templates[] = array(
	"title"		=> "mybblog_articles_none",
	"template"	=> '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
	<td class="trow1">{$lang->mybblog_articles_none}</td>
</tr>
</table>'
);

$mybblog_templates[] = array(
	"title"		=> "mybblog_write",
	"template"	=> '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
	<td class="trow1" style="text-align: right;">{$lang->mybblog_write}</td>
</tr>
</table>
<br />'
);