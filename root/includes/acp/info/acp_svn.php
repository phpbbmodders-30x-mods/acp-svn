<?php
/**
*
* @package acp
* @version $Id: acp_svn.php 5 2011-05-09 19:08:32Z tumba25 $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_svn_info
{
	function module()
	{
		return array(
			'filename' => 'acp_svn',
			'title' => 'ACP_SVN_MANAGEMENT',
			'version' => '1.0.0',
			'modes' => array(
				'main' => array('title' => 'ACP_SVN_MANAGE', 'auth' => 'acl_a_manage_svn', 'cat' => array('ACP_GENERAL_TASKS')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>