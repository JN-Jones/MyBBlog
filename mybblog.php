<?php
define("IN_MYBB", 1);
define("THIS_SCRIPT", "mybblog.php");

$templatelist = "";

require("global.php");

if(!function_exists("mybblog_set_up")) {
	$lang->load("mybblog");
	error($lang->mybblog_deactivated);
}

mybblog_set_up();

add_breadcrumb($lang->mybblog);

// Permissions aren't added yet
//if(!wiki_is_allowed("can_view"))
//    error_no_permission();