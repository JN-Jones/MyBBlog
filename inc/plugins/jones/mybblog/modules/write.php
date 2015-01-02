<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_Write extends JB_Module_Base
{
	function start()
	{
		global $plugins;

		if(!mybblog_can("write"))
			error_no_permission();

		$plugins->run_hooks("mybblog_write_start");
	}

	function post()
	{
		global $mybb, $lang, $errors, $plugins;

		$array = array(
			"title"		=> $mybb->get_input('title'),
			"content"	=> $mybb->get_input('article')
		);
		$article = JB_MyBBlog_Article::create($array);

		// This line explodes our tags, trims them and removes empty tags
		$tags = array_filter(array_map("trim", explode(",", $mybb->get_input('tags'))));

		$error = array();

		if(count($tags) == 0)
			$error[] = $lang->mybblog_article_no_tags;

		$plugins->run_hooks("mybblog_write_pre_validate", $article);

		if($article->validate() && empty($error))
		{
			foreach($tags as $tag)
			{
				$tag = $article->createTag($tag);
				if(!$tag->validate(false))
					// We can override "old" errors here as it's useless to show the same error multiple times
					$errors = $tag->getInlineErrors();
			}

			if(empty($errors))
			{
				$plugins->run_hooks("mybblog_write_save", $this->article);

				// This shouldn't fail as we're validating everything above but you never know...
				if($article->saveWithChilds())
					redirect("mybblog.php?action=view&id={$article->id}", $lang->mybblog_article_written);
				else
					$error[] = $lang->mybblog_article_unknown;
			}
		}
		else
			$error = array_merge($error, $article->getErrors());

		if(!empty($error))
			$errors = inline_error($error);

		return $this->get();
	}

	function get()
	{
		global $lang, $templates, $mybb, $theme, $plugins;

		add_breadcrumb($lang->mybblog_write, "mybblog.php?action=write");

		$plugins->run_hooks("mybblog_write_get");

		$mybb->input['title'] = e($mybb->input['title']);
		$mybb->input['tags'] = e($mybb->input['tags']);

		$codebuttons = build_mycode_inserter();
		$id = "";
		$title = $lang->mybblog_new_article;
		return eval($templates->render("mybblog_write"));
	}
}