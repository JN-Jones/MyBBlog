<?php
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

define("MYBBLOG_LOADED", true);
define("MYBBLOG_PATH", MYBB_ROOT."inc/plugins/mybblog");

function mybblog_info()
{
	return array(
		"name"			=> "MyBBlog",
		"description"	=> "Adds a simple blog to your forum",
		"website"		=> "http://jonesboard.de/",
		"author"		=> "Jones",
		"authorsite"	=> "http://jonesboard.de/",
		"version"		=> "1.0",
		"compatibility" => "18*"
	);
}

function mybblog_install()
{
	require_once MYBBLOG_PATH."/resources/plugin.php";
	mybblog_up();
}

function mybblog_is_installed()
{
	require_once MYBBLOG_PATH."/resources/plugin.php";
	return mybblog_is_up();
}

function mybblog_uninstall()
{
	require_once MYBBLOG_PATH."/resources/plugin.php";
	mybblog_down();
}

function mybblog_activate() {}

function mybblog_deactivate() {}

// This will load all necessary files and set up a few things for us
function mybblog_set_up()
{
	global $lang;

	// Load our language vars
	$lang->load("mybblog");

	// require our custom classes
	require_once MYBBLOG_PATH."/classes/MyBBlogClass.php";
	require_once MYBBLOG_PATH."/classes/Article.php";
	require_once MYBBLOG_PATH."/classes/Comment.php";
	require_once MYBBLOG_PATH."/classes/Tag.php";

	require_once MYBBLOG_PATH."/Helpers.php";
}

// Permissions check
function mybblog_can($perm, $user = false)
{
	global $mybb;

	$perm = "mybblog_can_".$perm;

	// The setting doesn't exist
	if(!isset($mybb->settings[$perm]))
	    return false;

	// -1 => all
	if($mybb->settings[$perm] == -1)
	    return true;

	// empty => none
	if(empty($mybb->settings[$perm]))
	    return false;

	// Still here? Check for is_member. $user can be simply passed as we'll use the same as mybb itself does
	return is_member($mybb->settings[$perm], $user);
}