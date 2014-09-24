<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Comment extends MyBBlogClass
{
	static protected $table = "mybblog_comments";
	static protected $timestamps = true;

	public static function getByArticle($id)
	{
		return static::getAll("aid='{$id}'");
	}

	public function getArticle()
	{
		return Article::getByID($this->data['aid']);
	}
}