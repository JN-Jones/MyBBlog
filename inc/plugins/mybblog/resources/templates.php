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

{$errors}
{$write}
{$content}

<br class="clear" />
{$footer}
</body>
</html>'
);

$mybblog_templates[] = array(
	"title"		=> "mybblog_article_delete",
	"template"	=> '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
	<td class="thead"><strong>{$title}</strong></td>
</tr>
<tr>
	<td class="trow1">{$lang->mybblog_delete_confirm}</td>
</tr>
</table>
<br />
<form action="mybblog.php" method="post">
	<input type="hidden" name="action" value="{$mybb->input[\'action\']}" />
	<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
	<input type="hidden" name="id" value="{$id}" />
	<div align="center"><input type="submit" class="button" value="{$lang->delete}" /></div>
</form>'
);

$mybblog_templates[] = array(
	"title"		=> "mybblog_articles",
	"template"	=> '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
	<td class="thead"><strong><a href="mybblog.php?action=view&id={$article->id}">{$article->title}</a></strong></td>
</tr>
<tr>
	<td class="tcat"><span class="smalltext">{$posted}</span><span class="smalltext" style="float: right">{$mod_link}</span></td>
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
	"title"		=> "mybblog_comment",
	"template"	=> '<tr>
	<td class="tcat"><span class="smalltext">{$posted}</span><span class="smalltext" style="float: right">{$mod_link}</span></td>
</tr>
<tr>
	<td class="trow1">{$parsed}</td>
</tr>'
);

$mybblog_templates[] = array(
	"title"		=> "mybblog_comment_form",
	"template"	=> '<form action="mybblog.php?action=comment" method="post">
	<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
  <input type="hidden" name="id" value="{$article->id}" />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
	<td class="thead"><strong>{$lang->mybblog_new_comment}</strong></td>
</tr>
<tr>
	<td class="trow1"><textarea name="comment" id="message" rows="20" cols="70">{$mybb->input[\'comment\']}</textarea>
		{$codebuttons}</td>
</tr>
</table>
<br />
<div align="center"><input type="submit" class="button" value="{$lang->mybblog_comment_submit}" /></div>
</form>'
);

$mybblog_templates[] = array(
	"title"		=> "mybblog_comments",
	"template"	=> '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
  <tr>
    <td class="thead">{$lang->mybblog_article_comments}</td>
  </tr>
{$my_comments}
</table>
<br />'
);

$mybblog_templates[] = array(
	"title"		=> "mybblog_write",
	"template"	=> '<form action="mybblog.php?action=write" method="post">
	<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
	<td class="thead" colspan="2"><strong>{$lang->mybblog_new_article}</strong></td>
</tr>
<tr>
	<td class="trow1">{$lang->mybblog_title}:</td>
	<td class="trow1"><input type="text" class="textbox" name="title" value="{$mybb->input[\'title\']}" size="50" />
</tr>
<tr>
	<td class="trow2">{$lang->mybblog_article_tags}:<br /><span class="smalltext">{$lang->mybblog_tags_desc}</span></td>
	<td class="trow2"><input type="text" class="textbox" name="tags" value="{$mybb->input[\'tags\']}" size="50" />
</tr>
<tr>
	<td class="trow2">{$lang->mybblog_article}:</td>
	<td class="trow2"><textarea name="article" id="message" rows="20" cols="70">{$mybb->input[\'article\']}</textarea>
		{$codebuttons}</td>
</tr>
</table>
<br />
<div align="center"><input type="submit" class="button" value="{$lang->mybblog_article_submit}" /></div>
</form>'
);

$mybblog_templates[] = array(
	"title"		=> "mybblog_write_bar",
	"template"	=> '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
	<td class="trow1" style="text-align: right;"><a href="mybblog.php?action=write">{$lang->mybblog_write}</a></td>
</tr>
</table>
<br />'
);