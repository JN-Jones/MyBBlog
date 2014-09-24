<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

// This file is used for getting MyBBlog up (or down)

function mybblog_up()
{
	global $db;

	// Template Group
	$templateset = array(
		"prefix"	=> "mybblog",
		"title"		=> "MyBBlog"
	);
	$db->insert_query("templategroups", $templateset);

	// Templates
	require_once MYBB_ROOT."inc/plugins/mybblog/resources/templates.php";
	if(!empty($mybblog_templates))
	{
		foreach($mybblog_templates as $template)
		{
			$template['sid'] = "-2"; // Master Theme
			$template['template'] = $db->escape_string($template['template']);
			$db->insert_query("templates", $template);
		}
	}


	// Settings Group
	$group = array(
		"title" 		=> "MyBBlog Settings",
		"name"			=> "mybblog",
		"description"	=> "Settings for MyBBlog",
		"isdefault"		=> "0"
	);
	$gid = $db->insert_query("settinggroups", $group);

	// Settings
	require_once MYBB_ROOT."inc/plugins/mybblog/resources/settings.php";
	if(!empty($mybblog_settings))
	{
		foreach($mybblog_settings as $disporder => $setting)
		{
			$setting['disporder'] = $disporder;
			$setting['gid'] = $gid;
			$db->insert_query("settings", $setting);
		}
	}
	rebuild_settings();

	// Tables (taken from mybb's installer)
	require_once MYBB_ROOT."inc/plugins/mybblog/resources/tables.php";
	if(!empty($mybblog_tables))
	{
		foreach($mybblog_tables as $table)
		{
			$table = preg_replace('#mybb_(\S+?)([\s\.,\(]|$)#', TABLE_PREFIX.'\\1\\2', $table);
			$table = preg_replace('#;$#', $db->build_create_table_collation().";", $table);
			preg_match('#CREATE TABLE (\S+)(\s?|\(?)\(#i', $table, $match);
			if($match[1])
			{
				$db->drop_table($match[1], false, false);
			}
			$db->query($table);
		}
	}	
}

function mybblog_down()
{
	global $db;

	// Template Group
	$db->delete_query("templategroups", "prefix='mybblog'");

	// Templates
	require_once MYBB_ROOT."inc/plugins/mybblog/resources/templates.php";
	if(!empty($mybblog_templates))
	{
		foreach($mybblog_templates as $template)
		{
			$db->delete_query("templates", "title='{$template['title']}'");
		}
	}

	// Settings Group and Settings
	$query = $db->simple_select("settinggroups", "gid", "name='mybblog'");
	$gid = $db->fetch_field($query, "gid");
	$db->delete_query("settinggroups", "gid='{$gid}'");
	$db->delete_query("settings", "gid='{$gid}'");
	rebuild_settings();

	// Tables (taken from mybb's installer)
	require_once MYBB_ROOT."inc/plugins/mybblog/resources/tables.php";
	if(!empty($mybblog_tables))
	{
		foreach($mybblog_tables as $table)
		{
			$table = preg_replace('#mybb_(\S+?)([\s\.,\(]|$)#', TABLE_PREFIX.'\\1\\2', $table);
			$table = preg_replace('#;$#', $db->build_create_table_collation().";", $table);
			preg_match('#CREATE TABLE (\S+)(\s?|\(?)\(#i', $table, $match);
			if($match[1])
			{
				$db->drop_table($match[1], false, false);
			}
		}
	}
}

function mybblog_is_up()
{
	global $db;

	return $db->table_exists("mybblog_articles");
}