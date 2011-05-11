<?php
/**
*
* AutoMOD [English]
*
* @package language
* @version $Id: info_acp_svn.php 5 2011-05-09 19:08:32Z tumba25 $
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
/**
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'acl_a_manage_svn'    => array('lang' => 'Can manage SVN', 'cat' => 'misc'),
));

?>
