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
	// Whether we need to save the user id or not
	static protected $user = false;
	// The table we're operating on
	static protected $table;
	// An array of errors which the validation produced
	protected $errors = array();
	// Our default sql options
	static protected $default_options = array();

	// Should return a new object with the $data
	public static function create($data)
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

		$options = array_merge(static::getDefaultOptions(), $options);

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

		$class = false;

		$query = $db->simple_select(static::$table, "*", "id='{$id}'");
		if($db->num_rows($query) == 1)
		{
			$article = $db->fetch_array($query);
			$class = static::create($article);
		}

	    static::$cache[$id] = $class;

		return $class;

	}

	public abstract function validate($hard=true);

	// Saves the current object
	public function save()
	{
		global $db, $mybb;

		// First: Validate
		if(!$this->validate(true))
		    return false;

		// Escape everything
		$data = array_map(array($db, 'escape_string'), $this->data);

		// Not existant -> insert
		if($this->data['id'] == -1)
		{
			if(static::$timestamps)
			    $this->data['dateline'] = $data['dateline'] = TIME_NOW;
			if(static::$user && empty($this->data['uid']))
			    $this->data['uid'] = $data['uid'] = $mybb->user['uid'];
			unset($data['id']);
		    $this->data['id'] = $db->insert_query(static::$table, $data);
		}
		// exists -> update
		else
			$db->update_query(static::table, $data, "id='{$this->data['id']}'");

		return true;
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

	// Get the default SQL options
	private function getDefaultOptions()
	{
		$order_dir = "desc";
		$order_by = "id";
		if(static::$timestamps)
		    $order_by = "dateline";

		$options = array("order_by" => $order_by, "order_dir" => $order_dir);

		if(!empty(static::$default_options))
		    $options = array_merge($options, static::$default_options);

		return $options;
	}

	// Error functions
	public function getErrors()
	{
		return $this->errors;
	}
	public function getInlineErrors()
	{
		return inline_error($this->errors);
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