<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Helpers
{
	static private $parser = null;
	static private $parser_options = array(
		"allow_html"		=> 0,
		"allow_mycode"		=> 1,
		"allow_smilies"		=> 1,
		"allow_imgcode"		=> 1,
		"allow_videocode"	=> 1,
		"filter_badwords"	=> 1
	);

	// Cache our parser class and the options
	public static function parse($message)
	{
   		if(static::$parser == null)
		{
			require_once MYBB_ROOT."inc/class_parser.php";
			static::$parser = new postParser;
		}

		return static::$parser->parse_message($message, static::$parser_options);
	}

	// Shorten the line a bit
	public static function formatDate($date)
	{
		return my_date('relative', $date);
	}

	// Helper to create a preview
	public static function preview($message, $length = 100, $append = "...", $parse = true)
	{
		// Do we still need to parse our message?
		if($parse)
		    $message = static::parse($message);

		// If it's short enough: return it
		if(strlen($message) <= $length)
		    return $message;

		// Shorten the message and append what should be appended
		return my_substr($message, 0, $length-strlen($append)).$append;
	}

	// Create a simple text with (avatar) Username
	public static function formatUser($user, $avatar = true, $formatName = false)
	{
		global $lang;

		if(!is_array($user))
		    $user = get_user($user);

		$name = $user['username'];
		if(empty($name))
		    $name = $lang->guest;
		if($formatName)
		    $name = format_name($name, $user['usergroup'], $user['displaygroup']);

		if($avatar)
		{
			$favatar = format_avatar($user['avatar'], $user['avatardimensions'], "17x17");
			$name = "<img src=\"{$favatar['image']}\" {$favatar['width_height']} valign=\"middle\" /> {$name}";
		}

		return build_profile_link($name, $user['uid']);
	}

	// Loads and runs our module
	public static function loadModule($module, $method="")
	{
		global $mybb, $templates, $lang, $headerinclude, $header, $errors, $write, $footer;

		// Empty is index
		if(empty($module))
		    $module = "index";

		// Unknown module - blank page
		if(!file_exists(MYBBLOG_PATH."/modules/{$module}.php"))
		    return;

		if($method != "get" && $method != "post")
		    $method = $mybb->request_method;

		// Require our nice module classes
		require_once MYBBLOG_PATH."/modules/{$module}.php";

		// And activate them
		$classname = "Module_".ucfirst($module);
		$mc = new $classname();

		// Let's figure out what to do
		// Something we need to do for post and get?
		if(method_exists($mc, "start"))
		    $mc->start();

		// If we have a post method and we're posting -> run it
		if($method == "post" && method_exists($mc, "post"))
		{
			// First we need to verify our post key
			verify_post_check($mybb->get_input('my_post_key'));

			$content = $mc->post();
		}
		// Either we don't have a post method or we're not posting
		else
			$content = $mc->get();

		// Do we need to cleanup something?
		if(method_exists($mc, "finish"))
		    $mc->finish();

		if(!empty($content))
		{
			$mybblog = eval($templates->render("mybblog"));
			output_page($mybblog);
		}
	}
}