<?php

class JB_MyBBlog_Version_V11 extends JB_Version_Base
{
	static function execute()
	{
		// Remove old  directory
		if(is_dir(MYBB_ROOT."inc/plugins/mybblog"))
			jb_remove_recursive(MYBB_ROOT."inc/plugins/mybblog");
	}
}