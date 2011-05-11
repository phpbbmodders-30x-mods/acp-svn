<?php
/**
*
* @package svn
* @version $Id: config_svn.php 9 2011-05-09 21:59:59Z tumba25 $
* @copyright (c) 2008, 2011 phpbbmodders
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
 * Config for svn.
 * Make sure to edit these with your own info.
 *
 * You might want to set this file to be ignored to.
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * SVN settings.
 * local_copy_path = the path to where to local repo is on your server.
 * local_bin_path = usually "/usr/local/bin/" but can be left empty if svn is in the path (try to type 'svn' in a cli)
 * local_config_path = where to find your local settings.
 * svn_repository = url to the external repo. This is where svn will update from.
 *
 * svn_username = the username you have given your forum to access the repo
 * svn_password = the password you have given your forum to access the repo
 */

$svn_settings = array(
	'local_copy_path'	=> '/home/user/www/site_dir/',
	'local_bin_path'	=> '',
	'local_config_path'	=> '/home/user/.subversion',
	'svn_repository'	=> 'https://svn-host.tld/svn/repo/',

	'svn_username'		=> '',
	'svn_password'		=> '',
);

?>