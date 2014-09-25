<?php
define("IN_MYBB", 1);
define("THIS_SCRIPT", "mybblog.php");

$templatelist = "";

require("global.php");

if(!defined("MYBBLOG_LOADED")) {
	$lang->load("mybblog");
	error($lang->mybblog_deactivated);
}

mybblog_set_up();

add_breadcrumb($lang->mybblog, "mybblog.php");

if(!mybblog_can("view"))
	error_no_permission();

// Generate our write bar
$write = "";
if(mybblog_can("write") && $mybb->input['action'] != "write")
    $write = eval($templates->render("mybblog_write_bar"));

$content = $errors = $mod_link = "";

$mybb->input['action'] = $mybb->get_input('action');

if($mybb->input['action'] == "delete_comment")
{
	$comment = Comment::getByID($mybb->get_input("id", 1));
	if($comment === false)
	    error($lang->mybblog_invalid_comment);

    if(!mybblog_can("write") && !(mybblog_can("comment") && $comment->uid == $mybb->user['uid']))
	    error_no_permission();

	add_breadcrumb($comment->getArticle()->title, "mybblog.php?action=view&id={$comment->getArticle()->id}");
	add_breadcrumb($lang->mybblog_article_comments, "mybblog.php?action=view&id={$comment->getArticle()->id}");
	add_breadcrumb($lang->delete, "mybblog.php?action=delete_comment&id={$comment->id}");

	if($mybb->request_method == "post")
	{
		// Verify incoming POST request
		verify_post_check($mybb->get_input('my_post_key'));

	    $comment->delete();
	    redirect("mybblog.php?action=view&id={$comment->getArticle()->id}", $lang->mybblog_deleted);
	}
	else
	{
		$title = $lang->sprintf($lang->mybblog_comment_delete, $comment->getArticle()->title);
		$id = $comment->id;
		$content = eval($templates->render("mybblog_article_delete"));
	}
}
if($mybb->input['action'] == "delete")
{
	if(!mybblog_can("write"))
	    error_no_permission();

	$article = Article::getByID($mybb->get_input("id", 1));
	if($article === false)
	    error($lang->mybblog_invalid_article);

	add_breadcrumb($article->title, "mybblog.php?action=view&id={$article->id}");
	add_breadcrumb($lang->delete, "mybblog.php?action=delete&id={$article->id}");

	if($mybb->request_method == "post")
	{
		// Verify incoming POST request
		verify_post_check($mybb->get_input('my_post_key'));

	    $article->deleteWithChilds();
	    redirect("mybblog.php", $lang->mybblog_deleted);
	}
	else
	{
		$title = $lang->sprintf($lang->mybblog_article_delete, $article->title);
		$id = $article->id;
		$content = eval($templates->render("mybblog_article_delete"));
	}
}
if($mybb->input['action'] == "tag")
{
	add_breadcrumb($lang->sprintf($lang->mybblog_tags, $mybb->get_input("tag")), "mybblog.php?action=tag&tag={$mybb->get_input('tag')}");

	$articles = Article::getByTag($mybb->get_input("tag"));
	unset($mybb->input['action']);
}
if($mybb->input['action'] == "comment" && $mybb->request_method == "post")
{
	// Verify incoming POST request
	verify_post_check($mybb->get_input('my_post_key'));

	$article = Article::getByID($mybb->get_input("id", 1));
	if($article === false)
	    error($lang->mybblog_invalid_article);

	$comment = $article->createComment($mybb->get_input("comment"));

	if($comment->save())
	    redirect("mybblog.php?action=view&id={$article->id}", $lang->mybblog_comment_saved);
    else
    {
		$mybb->input['action'] = "view";
		$errors = $comment->getInlineErrors();
	}
}
if($mybb->input['action'] == "view")
{
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
		$content .= eval($templates->render("mybblog_comment_form"));
	}
}
if($mybb->input['action'] == "write")
{
	if(!mybblog_can("write"))
	    error_no_permission();

	add_breadcrumb($lang->mybblog_write, "mybblog.php?action=write");

    if($mybb->request_method == "post")
	{
		// Verify incoming POST request
		verify_post_check($mybb->get_input('my_post_key'));
	
		$array = array(
			"title"		=> $mybb->get_input('title'),
			"content"	=> $mybb->get_input('article')
		);
		$article = Article::create($array);

		// This line explodes our tags, trims them and removes empty tags
		$tags = array_filter(array_map("trim", explode(",", $mybb->get_input('tags'))));

		$error = array();

		if(count($tags) == 0)
		    $error[] = $lang->mybblog_article_no_tags;

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
	}

	$codebuttons = build_mycode_inserter();
	$content = eval($templates->render("mybblog_write"));
}

if(empty($mybb->input['action']))
{
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
}

if(!empty($content))
{
	$mybblog = eval($templates->render("mybblog"));
	output_page($mybblog);
}