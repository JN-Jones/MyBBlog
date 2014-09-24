<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

abstract class MyBBlogClass
{
	// Cache our objects
	static protected $cache = array();
	// The "real" data
	protected $data = array();
	// The table we're operating on
	static protected $table;

	// Should return a new object with the $data
	protected static function create($data)
	{
		return new static($data);
	}

	// Save our data
	public function __construct($data)
	{
		$this->data = $data;
	}

	// Get's the object with that ID
	public static function getByID($id)
	{
		global $db;

		if(isset(static::$cache[$id]))
		    return static::$cache[$id];

		$query = $db->simple_select(static::$table, "*", "id='{$id}'");
		$article = $db->fetch_array($query);
		$class = static::create($article);

	    static::$cache[$id] = $class;

		return $class;

	}

	// Delete's an object by ID
	public static function deleteById($id)
	{
		global $db;

		if(isset(static::$cache[$id]))
		    unset(static::$cache[$id]);

		$db->delete_query(static::$table, "id='{$id}'");

	}

	// Magic PHP methods to use our $data array
	public function __get($key)
	{
		return $this->data[$key];
	}

	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	public function __unset($key)
	{
		unset($this->data[$key]);
	}
}