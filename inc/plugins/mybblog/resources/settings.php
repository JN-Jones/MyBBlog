<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

// Creates an array of our settings

$mybblog_settings[] = array(
	"name"			=> "mybblog_can_view",
	"title"			=> "Which groups can read articles?",
	"description"	=> "",
	"optionscode"	=> "groupselect",
	"value"			=> "-1"
);

$mybblog_settings[] = array(
	"name"			=> "mybblog_can_comment",
	"title"			=> "Which groups can comment on articles?",
	"description"	=> "",
	"optionscode"	=> "groupselect",
	"value"			=> "2,3,4,6"
);

$mybblog_settings[] = array(
	"name"			=> "mybblog_can_write",
	"title"			=> "Which groups can write articles?",
	"description"	=> "",
	"optionscode"	=> "groupselect",
	"value"			=> "3,4,6"
);