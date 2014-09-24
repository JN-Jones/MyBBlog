<?php
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

define("MYBBLOG_LOADED", true);

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
	require_once MYBB_ROOT."inc/plugins/mybblog/resources/plugin.php";
	mybblog_up();
}

function mybblog_is_installed()
{
	require_once MYBB_ROOT."inc/plugins/mybblog/resources/plugin.php";
	return mybblog_is_up();
}

function mybblog_uninstall()
{
	require_once MYBB_ROOT."inc/plugins/mybblog/resources/plugin.php";
	mybblog_down();
}

function mybblog_activate() {}

function mybblog_deactivate() {}

function mybblog_set_up()
{
	global $lang;

	// Load our language vars
	$lang->load("mybblog");

	// require our custom classes
	require_once MYBB_ROOT."inc/plugins/mybblog/MyBBlogClass.php";
	require_once MYBB_ROOT."inc/plugins/mybblog/Article.php";
	require_once MYBB_ROOT."inc/plugins/mybblog/Tag.php";
}