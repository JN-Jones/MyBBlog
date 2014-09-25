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

$content = $errors = "";

$mybb->input['action'] = $mybb->get_input('action');

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

	$content = eval($templates->render("mybblog_articles"));

	if($article->numberComments() > 0)
	{
		foreach($article->getComments() as $comment)
		{
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