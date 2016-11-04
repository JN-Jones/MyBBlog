<?php

class JB_MyBBlog_WIO_Handler extends JB_WIO_Base
{
	protected static $handle = array(
		"mybblog"	=> array(
			"comment"			=> "showArticle",
			"delete"			=> "delete",
			"delete_comment"	=> "delete_comment",
			"edit"				=> "showArticle",
			"edit_comment"		=> "edit_comment",
			"index"				=> "index",
			"tag"				=> "tag",
			"view"				=> "showArticle",
			"write"				=> "writing"
		)
	);

	public static function init()
	{
		global $lang;
		$lang->load("mybblog");
	}

	public static function getParamsFor($file, $action="")
	{
		global $parameters;
		// Never used at the same time
		if(isset($parameters['id']))
			return (int)$parameters['id'];
		if(isset($parameters['tag']))
			return $parameters['tag'];
	}

	public static function buildShowArticle($id, $action="")
	{
		global $lang;
		$l = "mybblog_{$action}";
		return $lang->sprintf($lang->$l, "mybblog.php?action=view&id={$id}", static::getArticleName($id));
	}

	public static function buildTag($tag, $action="")
	{
		global $lang;
		return $lang->sprintf($lang->mybblog_tag, "mybblog.php?action=tag&tag=".urlencode($tag), e($tag));
	}

	private static function getArticleName($id)
	{
		return e(JB_MyBBlog_Article::getById($id)->title);
	}
}
