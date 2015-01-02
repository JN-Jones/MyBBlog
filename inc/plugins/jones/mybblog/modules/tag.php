<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

class Module_Tag extends JB_Module_Base
{
	public $post = false;

	function get()
	{
		global $lang, $mybb, $articles, $plugins;

		$plugins->run_hooks("mybblog_tag");

		add_breadcrumb($lang->sprintf($lang->mybblog_tags, e($mybb->get_input("tag"))), "mybblog.php?action=tag&tag={$mybb->get_input('tag')}");
	
		$articles = JB_MyBBlog_Article::getByTag($mybb->get_input("tag"));

		// The index module can show all articles so we only need to load them while the index module does the rest
		$this->loader->loadModule("index");
	}
}