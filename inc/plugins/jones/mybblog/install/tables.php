<?php

$tables[] = "CREATE TABLE mybb_mybblog_articles (
	id int unsigned NOT NULL auto_increment,
	uid int unsigned NOT NULL default '0',
	title varchar(200) NOT NULL default '',
	content text NOT NULL,
	dateline int unsigned NOT NULL default '0',
	PRIMARY KEY (id)
) ENGINE=MyISAM;";

$tables[] = "CREATE TABLE mybb_mybblog_tags (
	id int unsigned NOT NULL auto_increment,
	aid int unsigned NOT NULL default '0',
	tag varchar(200) NOT NULL default '',
	PRIMARY KEY (id)
) ENGINE=MyISAM;";

$tables[] = "CREATE TABLE mybb_mybblog_comments (
	id int unsigned NOT NULL auto_increment,
	aid int unsigned NOT NULL default '0',
	uid int unsigned NOT NULL default '0',
	content text NOT NULL,
	dateline int unsigned NOT NULL default '0',
	PRIMARY KEY (id)
) ENGINE=MyISAM;";