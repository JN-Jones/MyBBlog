<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Article extends MyBBlogClass
{
	static protected $table = "mybblog_articles";
	static protected $cache = array();
	static protected $timestamps = true;
	static protected $user = true;
	private $comment_cache = array();
	private $tags_cache = array();
	private $new_comment_cache = array();
	private $new_tags_cache = array();

	public function validate($hard=true)
	{
		global $lang;

		if(!isset($this->data['title']) || !trim($this->data['title']))
			$this->errors[] = $lang->mybblog_article_no_title;

		if(!isset($this->data['content']) || !trim($this->data['content']))
			$this->errors[] = $lang->mybblog_article_no_content;

		static::runHook("validate", $this);

		if(!empty($this->errors))
			return false;

		return true;
	}

	public function saveWithChilds()
	{
		// First: save us to get our ID
		if(!$this->save())
			return false;

		// Next: comments
		foreach($this->comment_cache as $comment)
		{
			// Make sure the connection is correct
			$comment->data['aid'] = $this->data['id'];
			if(!$comment->save())
				return false;
		}
		foreach($this->new_comment_cache as $comment)
		{
			// Make sure the connection is correct
			$comment->data['aid'] = $this->data['id'];
			if(!$comment->save())
				return false;
		}

		// Last: tags
		foreach($this->tags_cache as $tag)
		{
			// Make sure the connection is correct
			$tag->data['aid'] = $this->data['id'];
			if(!$tag->save())
				return false;
		}
		foreach($this->new_tags_cache as $tag)
		{
			// Make sure the connection is correct
			$tag->data['aid'] = $this->data['id'];
			if(!$tag->save())
				return false;
		}

		static::runHook("saveWithChilds", $this);

		// Still here? Lucky guy
		return true;
	}

	public function deleteWithChilds()
	{
		// Get all comments and delete them
		$cs = $this->getComments();
		foreach($cs as $c)
			$c->delete();

		// Same for tags
		$this->deleteTags();

		static::runHook("deleteWithChilds", $this);

		// And bye :(
		$this->delete();
	}

	public function deleteTags()
	{
		$ts = $this->getTags();
		foreach($ts as $t)
			$t->delete();
	}

	public static function getByTag($tag)
	{
		global $db;

		static::runHook("getByTag", $tag);
		if(!trim($tag))
			return;

		$tag = $db->escape_string($tag);
		$tags = Tag::getAll("tag='{$tag}'");
		$articles = array();
		foreach($tags as $t)
		{
			$a = $t->getArticle();
			if($a === false)
				// The attached article doesn't exist so delete this tag too (should've done automatically but some guys love to work directly on the database)
				$t->delete();
			else
				$articles[] = $a;
		}

		if(empty($articles))
			// This tag isn't used anywhere
			return false;

		return $articles;
	}

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

	public function createComment($data)
	{
		if(!is_array($data))
			$data = array("content" => $data);

		$data['aid'] = $this->data['id'];
		$comment = Comment::create($data);
		$this->new_comment_cache[] = $comment;
		return $comment;
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

	public function createTag($data)
	{
		if(!is_array($data))
			$data = array("tag" => $data);

		$data['aid'] = $this->data['id'];
		$tag = Tag::create($data);
		$this->new_tags_cache[] = $tag;
		return $tag;
	}
}