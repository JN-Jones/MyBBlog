<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Tag extends MyBBlogClass
{
	static protected $table = "mybblog_tags";
	static protected $cache = array();
	// Our default sql options
	static protected $default_options = array(
		"order_by"	=> "tag",
	);

	public function validate($hard=true)
	{
		global $lang;

		// Only test this when saving
		if($hard === true && (empty($this->data['aid']) || Article::getByID($this->data['aid']) === false))
			$this->errors[] = $lang->mybblog_invalid_article;

		if(!isset($this->data['tag']) || !trim($this->data['tag']))
		    $this->errors[] = $lang->mybblog_tag_no_tag;

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

	public function __toString()
	{
		return "<a href=\"mybblog.php?action=tag&tag={$this->data['tag']}\">{$this->data['tag']}</a>";
	}
}