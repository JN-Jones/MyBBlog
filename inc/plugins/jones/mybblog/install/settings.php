<?php

$settingsgroup = array(
	"title" 		=> "MyBBlog Settings",
	"description"	=> "Settings for MyBBlog",
);

$settings[] = array(
	"name"			=> "mybblog_can_view",
	"title"			=> "Which groups can read articles?",
	"description"	=> "",
	"optionscode"	=> "groupselect",
	"value"			=> "-1"
);

$settings[] = array(
	"name"			=> "mybblog_can_comment",
	"title"			=> "Which groups can comment on articles?",
	"description"	=> "",
	"optionscode"	=> "groupselect",
	"value"			=> "2,3,4,6"
);

$settings[] = array(
	"name"			=> "mybblog_can_write",
	"title"			=> "Which groups can write articles?",
	"description"	=> "",
	"optionscode"	=> "groupselect",
	"value"			=> "3,4,6"
);

$settings[] = array(
	"name"			=> "mybblog_preview_length",
	"title"			=> "How many characters should be shown for the preview?",
	"description"	=> "",
	"optionscode"	=> "numeric",
	"value"			=> "100"
);

$settings[] = array(
	"name"			=> "mybblog_preview_parse",
	"title"			=> "Should the preview be parsed or not?",
	"description"	=> "",
	"optionscode"	=> "yesno",
	"value"			=> "1"
);