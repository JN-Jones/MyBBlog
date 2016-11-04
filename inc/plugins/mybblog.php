<?php
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

define("MYBBLOG_LOADED", true);

// Test whether core is installed and if so get it up
defined("JB_CORE_INSTALLED") or require_once MYBB_ROOT."inc/plugins/jones/core/include.php";

function mybblog_info()
{
	$info = array(
		"name"			=> "MyBBlog",
		"description"	=> "Adds a simple blog to your forum",
		"website"		=> "http://jonesboard.de/",
		"author"		=> "Jones",
		"authorsite"	=> "http://jonesboard.de/",
		"version"		=> "1.1.1",
		"compatibility" => "18*",
		"codename"		=> "mybblog"
	);

	if(JB_CORE_INSTALLED === true)
	    return JB_CORE::i()->getInfo($info);

	return $info;
}

function mybblog_install()
{
	jb_install_plugin("mybblog");
}

function mybblog_is_installed()
{
	global $db;

	return $db->table_exists("mybblog_articles");
}

function mybblog_uninstall()
{
	JB_Core::i()->uninstall("mybblog");
}

function mybblog_activate()
{
	JB_Core::i()->activate("mybblog");
}

function mybblog_deactivate()
{
	JB_Core::i()->deactivate("mybblog");
}

// This will load all necessary files and set up a few things for us
function mybblog_set_up()
{
	global $lang, $plugins;

	// Load our language vars
	$lang->load("mybblog");

	$plugins->run_hooks("mybblog_set_up");
}

// Permissions check
function mybblog_can($perm, $user = false)
{
	global $mybb, $plugins;

	$arg = array("perm" => &$perm, "user" => &$user);
	$plugins->run_hooks("mybblog_can", $arg);

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
