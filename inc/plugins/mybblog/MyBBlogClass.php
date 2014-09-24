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
	// Whether we use timestamps which needs to be touched
	static protected $timestamps = false;
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
		// -1 is our "not existant"
		if(empty($data['id']))
		    $data['id'] = -1;

		$this->data = $data;
	}

	// Get all objects
	public static function getAll($where='', $options=array())
	{
		global $db;

		$entries = array();
		
		$query = $db->simple_select(static::$table, "*", $where, $options);
		while ($e = $db->fetch_array($query))
			$entries[$e['id']] = static::create($e);

		// Merge our current entries to the cache
		static::$cache = array_merge(static::$cache, $entries);
			
		return $entries;
	}

	public static function getNumber($where='')
	{
		return count(static::getAll($where));
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

	// Saves the current object
	// TODO: Save the child elements
	public function save()
	{
		global $db;

		// Escape everything
		$data = array_map(array($db, 'escape_string'), $this->data);		

		// Not existant -> insert
		if($this->data['id'] == -1)
		{
			if(static::$timestamps)
			    $this->data['dateline'] = TIME_NOW;
		    $this->data['id'] = $db->insert_query(static::table, $data);
		}
		// exists -> update
		else
			$db->update_query(static::table, $data, "id='{$this->data['id']}'");
	}

	// Delete the current object
	public function delete()
	{
		global $db;

		$id = $this->data['id'];

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