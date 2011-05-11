<?php
/**
*
* @package umil
* @version $Id
* @copyright (c) 2010 phpbbmodders.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
	{
		trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
	}

/*
* The language file which will be included when installing
* Language entries that should exist in the language file for UMIL (replace $mod_name with the mod's name you set to $mod_name above)
* $mod_name
* 'INSTALL_' . $mod_name
* 'INSTALL_' . $mod_name . '_CONFIRM'
* 'UPDATE_' . $mod_name
* 'UPDATE_' . $mod_name . '_CONFIRM'
* 'UNINSTALL_' . $mod_name
* 'UNINSTALL_' . $mod_name . '_CONFIRM'
*/
$language_file = 'mods/svn';

// The name of the mod to be displayed during installation.
$mod_name = 'INSTALL_SVN';

/*
* The name of the config variable which will hold the currently installed version
* You do not need to set this yourself, UMIL will handle setting and updating the version itself.
*/
$version_config_name = 'svn_manage';

/**
 * Add permissions.
 */

/**
 * Add the module.
 */
$versions = array(
	// Version 1.0.0
	'1.0.0'	=> array(

		 // Add module.
		'module_add'	=> array(
            array('acp', 'ACP_GENERAL_TASKS', array(
					'module_basename'	=> 'acp_svn',
					'module_langname'	=> 'ACP_SVN_MANAGE',
					'module_mode'		=> 'acp_svn',
					'module_auth'		=> 'acl_a_manage_svn',
				),
			),
		),

		// Add permission
		'permission_add' => array(
			array('a_manage_svn', true),
		),
		'cache_purge'	=> (''),
	),
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);
?>