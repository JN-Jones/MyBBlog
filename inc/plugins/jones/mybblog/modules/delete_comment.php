<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_Delete_comment extends JB_Module_Base
{
	private $comment;

	function start()
	{
		global $mybb, $lang, $plugins;

		$comment = JB_MyBBlog_Comment::getByID($mybb->get_input("id", 1));
		if($comment === false)
			error($lang->mybblog_invalid_comment);

		if(!mybblog_can("write") && !(mybblog_can("comment") && $comment->uid == $mybb->user['uid']))
			error_no_permission();

		$plugins->run_hooks("mybblog_delete_comment_start", $comment);

		$comment->getArticle()->title = e($comment->getArticle()->title);

		add_breadcrumb($comment->getArticle()->title, "mybblog.php?action=view&id={$comment->getArticle()->id}");
		add_breadcrumb($lang->mybblog_article_comments, "mybblog.php?action=view&id={$comment->getArticle()->id}");
		add_breadcrumb($lang->delete, "mybblog.php?action=delete_comment&id={$comment->id}");

		$this->comment = $comment;
	}

	function post()
	{
		global $lang;

		$this->comment->delete();
		redirect("mybblog.php?action=view&id={$this->comment->getArticle()->id}", $lang->mybblog_deleted);
	}

	function get()
	{
		global $lang, $templates, $theme, $mybb;

		$title = $lang->sprintf($lang->mybblog_comment_delete, $this->comment->getArticle()->title);
		$id = $this->comment->id;
		return eval($templates->render("mybblog_article_delete"));
	}
}