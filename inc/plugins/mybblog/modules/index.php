<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_Index
{
	function get()
	{
		global $lang, $templates, $theme, $articles;

		if(!isset($articles))
		    $articles = Article::getAll();
	
		if(count($articles) == 0 || $articles === false)
			$content = eval($templates->render("mybblog_articles_none"));
		else
		{
			foreach($articles as $article)
			{
				$posted = $lang->sprintf($lang->mybblog_posted, Helpers::formatDate($article->dateline), Helpers::formatUser($article->uid));
				$preview = Helpers::preview($article->content);
				$comments = $lang->sprintf($lang->mybblog_comments, $article->numberComments());
				$tags = $lang->sprintf($lang->mybblog_tags, implode(", ", $article->getTags()));
	
				$content .= eval($templates->render("mybblog_articles"));
			}
		}

		return $content;
	}
}