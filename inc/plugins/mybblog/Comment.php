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
	static protected $user = true;
	// Our default sql options
	static protected $default_options = array(
		"order_dir"	=> "asc", // Oldest first
	);

	public function validate($hard=true)
	{
		global $lang;

		// Only test this when saving
		if($hard === true && (empty($this->data['aid']) || Article::getByID($this->data['aid']) === false))
			$this->errors[] = $lang->mybblog_invalid_article;

		if(empty($this->data['content']))
		    $this->errors[] = $lang->mybblog_comment_no_content;

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
		return Article::getByID($this->data['aid']);
	}
}