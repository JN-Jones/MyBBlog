<?php
define("IN_MYBB", 1);
define("THIS_SCRIPT", "mybblog.php");

$templatelist = "";

require("global.php");

if(!defined("MYBBLOG_LOADED")) {
	$lang->load("mybblog");
	error($lang->mybblog_deactivated);
}

mybblog_set_up();

add_breadcrumb($lang->mybblog, "mybblog.php");

if(!mybblog_can("view"))
	error_no_permission();


if(!$mybb->input['action'])
{
	$write = "";
	if(mybblog_can("write"))
	    $write = eval($templates->render("mybblog_write"));

	if(Article::getNumber() == 0)
		$articles = eval($templates->render("mybblog_articles_none"));
	else
	{
		$arts = Article::getAll();
		foreach($arts as $article)
		{
			$posted = $lang->sprintf($lang->mybblog_posted, Helpers::formatDate($article->dateline));
			$preview = Helpers::preview($article->content);
			$comments = $lang->sprintf($lang->mybblog_comments, $article->numberComments());
			$tags = $lang->sprintf($lang->mybblog_tags, implode(", ", $article->getTags()));

			$articles .= eval($templates->render("mybblog_articles"));
		}
	}

	$mybblog = eval($templates->render("mybblog"));
	output_page($mybblog);
}