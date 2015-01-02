<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_Index extends JB_Module_Base
{
	public $post = false;

	function get()
	{
		global $lang, $templates, $theme, $articles, $plugins, $mybb;

		$plugins->run_hooks("mybblog_index_start");

		if(!isset($articles))
			$articles = JB_MyBBlog_Article::getAll();
	
		if(count($articles) == 0 || $articles === false)
			$content = eval($templates->render("mybblog_articles_none"));
		else
		{
			foreach($articles as $article)
			{
				$article->title = e($article->title);
				$posted = $lang->sprintf($lang->mybblog_posted, JB_Helpers::formatDate($article->dateline), JB_Helpers::formatUser($article->uid));
				$preview = JB_Helpers::preview($article->content, $mybb->settings['mybblog_preview_length'], "...", $mybb->settings['mybblog_preview_parse']);
				$comments = $lang->sprintf($lang->mybblog_comments, $article->numberComments());
				$tags = $lang->sprintf($lang->mybblog_tags, implode(", ", $article->getTags()));

				$plugins->run_hooks("mybblog_index_format", $article);
	
				$content .= eval($templates->render("mybblog_articles"));
			}
		}

		return $content;
	}
}