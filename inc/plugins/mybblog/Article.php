<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Article extends MyBBlogClass
{
	static protected $table = "mybblog_articles";
	static protected $timestamps = true;
	private $comment_cache = array();
	private $tags_cache = array();

	// Functions to interact with our comments
	public function hasComments()
	{
		return ($this->numberComments() > 0);
	}

	public function numberComments()
	{
		if(empty($this->comment_cache))
		    $this->getComments();

		return count($this->comment_cache);
	}

	public function getComments()
	{
		if(empty($this->comment_cache))
			$this->comment_cache = Comment::getByArticle($this->data['id']);

	    return $this->comment_cache;
	}

	// Functions to interact with our tags
	public function hasTags()
	{
		return ($this->numberTags() > 0);
	}

	public function numberTags()
	{
		if(empty($this->tags_cache))
		    $this->getTags();

		return count($this->tags_cache);
	}

	public function getTags()
	{
		if(empty($this->tags_cache))
			$this->tags_cache = Tag::getByArticle($this->data['id']);

	    return $this->tags_cache;
	}
}