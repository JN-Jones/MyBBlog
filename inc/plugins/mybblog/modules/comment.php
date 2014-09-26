<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_Comment
{
	function post()
	{
		global $mybb, $lang, $errors;

		$article = Article::getByID($mybb->get_input("id", 1));
		if($article === false)
		    error($lang->mybblog_invalid_article);
		
		$comment = $article->createComment($mybb->get_input("comment"));
		
		if($comment->save())
		    redirect("mybblog.php?action=view&id={$article->id}", $lang->mybblog_comment_saved);
		else
		{
			$errors = $comment->getInlineErrors();
			Helpers::loadModule("view", "get");
		}
	}
}