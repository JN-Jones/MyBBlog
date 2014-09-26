<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_View
{
	function get()
	{
		global $mybb, $lang, $templates, $theme;

		$article = Article::getByID($mybb->get_input("id", 1));
		if($article === false)
		    error($lang->mybblog_invalid_article);
	
		add_breadcrumb($article->title, "mybblog.php?action=view&id={$article->id}");
	
		$posted = $lang->sprintf($lang->mybblog_posted, Helpers::formatDate($article->dateline), Helpers::formatUser($article->uid));
		$preview = Helpers::parse($article->content);
		$comments = $lang->sprintf($lang->mybblog_comments, $article->numberComments());
		$tags = $lang->sprintf($lang->mybblog_tags, implode(", ", $article->getTags()));
	
		if(mybblog_can("write"))
		    $mod_link = "<a href=\"mybblog.php?action=edit&id={$article->id}\">{$lang->edit}</a> | <a href=\"mybblog.php?action=delete&id={$article->id}\">{$lang->delete}</a>";
	
		$content = eval($templates->render("mybblog_articles"));
	
		if($article->numberComments() > 0)
		{
			foreach($article->getComments() as $comment)
			{
				if(mybblog_can("write") || (mybblog_can("comment") && $comment->uid == $mybb->user['uid']))
				    $mod_link = "<a href=\"mybblog.php?action=edit_comment&id={$comment->id}\">{$lang->edit}</a> | <a href=\"mybblog.php?action=delete_comment&id={$comment->id}\">{$lang->delete}</a>";
	
				$posted = $lang->sprintf($lang->mybblog_posted, Helpers::formatDate($comment->dateline), Helpers::formatUser($comment->uid));
				$parsed = Helpers::parse($comment->content);
				$my_comments .= eval($templates->render("mybblog_comment"));
			}
			$content .= eval($templates->render("mybblog_comments"));
		}
	
		if(mybblog_can("comment"))
		{
			$codebuttons = build_mycode_inserter();
			$id = $article->id;
			$title = $lang->mybblog_new_comment;
			// We need to fake this here a bit
			$mybb->input['action'] = "comment";
			$content .= eval($templates->render("mybblog_comment_form"));
			$mybb->input['action'] = "view";
		}

		return $content;
	}
}