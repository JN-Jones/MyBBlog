<?php

// Disallow direct access to this file for security reasons
if(!defined("MYBBLOG_LOADED"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure MYBBLOG_LOADED is defined.");
}

abstract class JB_MyBBLog_Class extends JB_Classes_StorableObject
{
	/**
	 * Doing some magic to generate nice hooks
	 * {@inheritdoc}
	 */
	public function runHook($name, &$arguments="")
	{
		global $plugins;

		$class = strtolower(get_called_class());
		$name = "mybblog_{$class}_{$name}";
		return $plugins->run_hooks($name, $arguments);
	}
}
