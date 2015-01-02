<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_Edit_comment extends JB_Module_Base
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

		$plugins->run_hooks("mybblog_edit_comment_start", $comment);

		add_breadcrumb(e($comment->getArticle()->title), "mybblog.php?action=view&id={$comment->getArticle()->id}");
		add_breadcrumb($lang->mybblog_article_comments, "mybblog.php?action=view&id={$comment->getArticle()->id}");
		add_breadcrumb($lang->edit, "mybblog.php?action=edit_comment&id={$comment->id}");

		$this->comment = $comment;
	}

	function post()
	{
		global $mybb, $lang, $errors, $plugins;

		$plugins->run_hooks("mybblog_edit_comment_pre_save", $this->comment);

		$this->comment->content = $mybb->get_input("comment");

		if($this->comment->save())
		{
			$plugins->run_hooks("mybblog_edit_comment_save", $this->comment);
			redirect("mybblog.php?action=view&id={$this->comment->getArticle()->id}", $lang->mybblog_comment_saved);
		}
		else
		{
			$errors = $this->comment->getInlineErrors();
			return $this->get();
		}
	}

	function get()
	{
		global $templates, $mybb, $lang, $theme, $plugins;

		if(!trim($mybb->get_input('comment')))
			$mybb->input['comment'] = $this->comment->content;

		$plugins->run_hooks("mybblog_edit_comment_get", $this->comment);

		$codebuttons = build_mycode_inserter();
		$id = $this->comment->id;
		$title = $lang->mybblog_edit_comment;
		return eval($templates->render("mybblog_comment_form"));
	}
}