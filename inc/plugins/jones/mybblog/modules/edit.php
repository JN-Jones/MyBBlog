<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_Edit extends JB_Module_Base
{
	private $article;

	function start()
	{
		global $mybb, $lang, $plugins;

		if(!mybblog_can("write"))
			error_no_permission();

		$article = JB_MyBBlog_Article::getByID($mybb->get_input("id", 1));
		if($article === false)
			error($lang->mybblog_invalid_article);

		$plugins->run_hooks("mybblog_edit_start", $article);

		add_breadcrumb(e($article->title), "mybblog.php?action=view&id={$article->id}");
		add_breadcrumb($lang->edit, "mybblog.php?action=edit&id={$article->id}");

		$this->article = $article;
	}

	function post()
	{
		global $mybb, $lang, $errors, $plugins;

		$this->article->title = $mybb->get_input('title');
		$this->article->content = $mybb->get_input('article');

		// This line explodes our tags, trims them and removes empty tags
		$tags = array_filter(array_map("trim", explode(",", $mybb->get_input('tags'))));

		$error = array();

		if(count($tags) == 0)
			$error[] = $lang->mybblog_article_no_tags;

		$plugins->run_hooks("mybblog_edit_pre_validate", $this->article);

		if($this->article->validate() && empty($error))
		{
			foreach($tags as $tag)
			{
				$tag = $this->article->createTag($tag);
				if(!$tag->validate(false))
					// We can override "old" errors here as it's useless to show the same error multiple times
					$errors = $tag->getInlineErrors();
			}

			if(empty($errors))
			{
				$plugins->run_hooks("mybblog_edit_save", $this->article);

				// We need to delete all tags first as we don't know whether they have changed or not
				$this->article->deleteTags();
				// This shouldn't fail as we're validating everything above but you never know...
				if($this->article->saveWithChilds())
					redirect("mybblog.php?action=view&id={$this->article->id}", $lang->mybblog_article_written);
				else
					$error[] = $lang->mybblog_article_unknown;
			}
		}
		else
			$error = array_merge($error, $this->article->getErrors());

		if(!empty($error))
			$errors = inline_error($error);

		return $this->get();
	}

	function get()
	{
		global $lang, $templates, $mybb, $theme, $plugins;

		if(!trim($mybb->get_input('title')) && !trim($mybb->get_input('article')) && !trim($mybb->get_input('tags')))
		{
			$mybb->input['title'] = $this->article->title;
			$mybb->input['article'] = $this->article->content;
			$tags = $this->article->getTags();
			$comma = "";
			foreach($tags as $tag)
			{
				$mybb->input['tags'] .= $comma.$tag->tag;
				$comma = ", ";
			}
		}

		$mybb->input['title'] = e($mybb->input['title']);
		$mybb->input['tags'] = e($mybb->input['tags']);

		$plugins->run_hooks("mybblog_edit_get");

		$codebuttons = build_mycode_inserter();
		$id = $this->article->id;
		$title = $lang->mybblog_edit_article;
		return eval($templates->render("mybblog_write"));
	}
}