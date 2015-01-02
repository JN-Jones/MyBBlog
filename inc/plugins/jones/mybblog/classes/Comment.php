<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class JB_MyBBLog_Comment extends JB_MyBBLog_Class
{
	static protected $table = "mybblog_comments";
	static protected $cache = array();
	static protected $timestamps = true;
	static protected $user = true;
	// Our default sql options
	static protected $default_options = array(
		"order_dir"	=> "asc", // Oldest first
	);

	public function validate($hard=true)
	{
		global $lang;

		// Only test this when saving
		if($hard === true && (empty($this->data['aid']) || JB_MyBBlog_Article::getByID($this->data['aid']) === false))
			$this->errors[] = $lang->mybblog_invalid_article;

		if(!isset($this->data['content']) || !trim($this->data['content']))
			$this->errors[] = $lang->mybblog_comment_no_content;

		static::runHook("validate", $this);

		if(!empty($this->errors))
			return false;

		return true;
	}

	public static function getByArticle($id)
	{
		return static::getAll("aid='{$id}'");
	}

	public function getArticle()
	{
		return JB_MyBBlog_Article::getByID($this->data['aid']);
	}
}