<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_Comment extends JB_Module_Base
{
	function post()
	{
		global $mybb, $lang, $errors, $plugins;

		$article = JB_MyBBlog_Article::getByID($mybb->get_input("id", 1));
		if($article === false)
			error($lang->mybblog_invalid_article);

		$plugins->run_hooks("mybblog_comment_start", $article);
		
		$comment = $article->createComment($mybb->get_input("comment"));
		
		if($comment->save())
		{
			$plugins->run_hooks("mybblog_comment_save", $comment);

			// Trigger the Alert for the article write
			$extra = array(
				"link" => "mybblog.php?action=view&id={$article->id}",
				"lang_data" => $article->title
			);
			JB_Alerts::trigger("mybblog", "new_comment", $article->uid, $extra);

			redirect("mybblog.php?action=view&id={$article->id}", $lang->mybblog_comment_saved);
		}
		else
		{
			$errors = $comment->getInlineErrors();
			$this->loader->loadModule("view", "get");
		}
	}

	function get() {}
}