<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

// Creates an array of our tables
$mybblog_tables[] = "CREATE TABLE mybb_mybblog_articles (
	id int unsigned NOT NULL default '0',
	title varchar(200) NOT NULL default '',
	dateline int unsigned NOT NULL default '0',
	content text NOT NULL,
	PRIMARY KEY (id)
) ENGINE=MyISAM;";

$mybblog_tables[] = "CREATE TABLE mybb_mybblog_tags (
	aid int unsigned NOT NULL default '0',
	tag varchar(200) NOT NULL default '',
) ENGINE=MyISAM;";