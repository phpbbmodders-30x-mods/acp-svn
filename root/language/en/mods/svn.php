<?php
/**
*
* @package phpbbmodders_site
* @version $Id: svn.php 5 2011-05-09 19:08:32Z tumba25 $
* @copyright (c) 2007 phpbbmodders
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// SVN
$lang = array_merge($lang, array(
	'FULL_RESULT'					=> 'Full result',
	'FULL_RESULT_EXPLAIN'	=> 'If this is not ticked, you will only get the last line from the result.',

	'IGNORE_EXTERNALS'	=> 'Ignore externals',
	'INVALID_MODE'			=> 'An invalid mode was specified.',

	'LIMIT'	=> 'Limit',

	'NO_SVN_MANAGE'	=> 'You are not authorised to manage the svn.',

	'OUTPUT'	=> 'Output',

	'PATH'						=> 'Path',
	'PREVIEW_CHANGES'	=> 'Preview changes',

	'REFRESH_THEME'					=> 'Refresh theme',
	'REFRESH_THEME_EXPLAIN'	=> 'Only needed for css changes.',
	'REVISION'							=> 'Revision',
	'REVISION_EXPLAIN' 			=> 'This is for reverting to earlier revision. Leave at zero for standard updates.',

	'SVN_CURRENT_REVISION'		=> 'Current revision',
	'SVN_MANAGEMENT'					=> 'Subversion management',
	'SVN_MANAGEMENT_EXPLAIN'	=> 'Here you can update the website to the latest svn revision. You can also only update a specific path or to a specific revision.',
	'SVN_MODE_DIFF'						=> 'Diff',
	'SVN_MODE_INFO'						=> 'Info',
	'SVN_MODE_LIST'						=> 'List',
	'SVN_MODE_LOG'						=> 'Log',
	'SVN_MODE_PROPLIST'				=> 'Proplist',
	'SVN_MODE_STATUS'					=> 'Status',
	'SVN_MODE_UPDATE'					=> 'Update',

	'VERBOSE'	=> 'Verbose',
));

?>