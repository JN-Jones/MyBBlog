<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_Delete
{
	private $article;

	function start()
	{
		global $mybb, $lang, $plugins;

		if(!mybblog_can("write"))
			error_no_permission();

		$article = Article::getByID($mybb->get_input("id", 1));
		if($article === false)
			error($lang->mybblog_invalid_article);

		$plugins->run_hooks("mybblog_delete_start", $article);

		add_breadcrumb($article->title, "mybblog.php?action=view&id={$article->id}");
		add_breadcrumb($lang->delete, "mybblog.php?action=delete&id={$article->id}");

		$this->article = $article;
	}

	function post()
	{
		global $lang;

		$this->article->deleteWithChilds();
		redirect("mybblog.php", $lang->mybblog_deleted);
	}

	function get()
	{
		global $lang, $templates, $theme, $mybb;

		$title = $lang->sprintf($lang->mybblog_article_delete, $this->article->title);
		$id = $this->article->id;
		return eval($templates->render("mybblog_article_delete"));
	}
}