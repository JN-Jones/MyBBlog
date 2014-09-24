<?php
define("IN_MYBB", 1);
define("THIS_SCRIPT", "mybblog.php");

$templatelist = "";

require("global.php");

if(!defined("MYBBLOG_LOADED")) {
	$lang->load("mybblog");
	error($lang->mybblog_deactivated);
}

mybblog_set_up();

add_breadcrumb($lang->mybblog, "mybblog.php");

if(!mybblog_can("view"))
	error_no_permission();

echo "MyBBlog loaded";