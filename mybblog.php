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

// Generate our write bar
$write = "";
if(mybblog_can("write") && $mybb->input['action'] != "write")
	$write = eval($templates->render("mybblog_write_bar"));

$content = $errors = $mod_link = "";

$plugins->run_hooks("mybblog_start");

Helpers::loadModule($mybb->get_input('action'));