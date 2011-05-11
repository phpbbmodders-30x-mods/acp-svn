<?php
/**
*
* @package phpbbmodders_site
* @version $Id: acp_svn.php 5 2011-05-09 19:08:32Z tumba25 $
* @copyright (c) 2007, 2008, 2011 phpbbmodders
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * This thing is mosly the work of Igor Wiedler.
 * I have done some modifications but most of the credit should go to him.
 */

/**
* @package acp
* @todo fix encoding of result
*/
class acp_svn
{
	public $u_action;
	protected $actions = array('update', 'status', 'info', 'list', 'diff', 'log');

	public function main($id, $mode)
	{
		global $user, $template, $auth, $db;
		global $phpbb_admin_path, $phpbb_root_path, $phpEx, $config;

		if (!$auth->acl_get('a_manage_svn'))
		{
			trigger_error('NOT_AUTHORISED');
		}

		include($phpbb_root_path . 'includes/functions_svn.' . $phpEx);

		$user->add_lang('mods/svn');

		$this->tpl_name = 'acp_svn';
		$this->page_title = 'SVN_MANAGEMENT';

		$form_key = 'acp_svn';
		add_form_key($form_key);

		// buttons
		$submit	= isset($_POST['submit']) ? true : false;
		$cancel	= request_var('cancel', false);

		// options
		$path							= utf8_normalize_nfc(request_var('path', '', true));
		$revision					= request_var('revision', 0);
		$action						= request_var('action', '');
		$verbose					= request_var('verbose', false);
		$purge_cache			= request_var('purge_cache', false);
		$refresh_theme		= request_var('refresh_theme', false);
		$preview					= request_var('preview', false);
		$preview_confirm	= request_var('preview_confirm', false);
		$limit						= request_var('limit', 10);
		$ignore_externals	= request_var('ignore_externals', false);

		// set some to true by default
		if (!$submit && !$preview && !$cancel)
		{
			//$preview = true;
			$purge_cache = true;
		}

		$s_hidden_fields = $error = array();

		// make sure $action is valid, do not allow checkouts (for now)
		if (!in_array($action, $this->actions, true))
		{
			$error[] = $user->lang['INVALID_MODE'];
		}

		// use exceptions here to easily jump out of the block
		try
		{
			if (!$submit)
			{
				throw new Exception('submit');
			}

			if (!check_form_key($form_key))
			{
				$error[] = $user->lang['FORM_INVALID'];
			}

			if (sizeof($error))
			{
				$template->assign_vars(array(
					'S_ERROR'	=> true,
					'ERROR_MSG'	=> implode($error),
				));

				throw new Exception('error');
			}

			global $svn_settings;

			$svn = new svn_commands($svn_settings['local_copy_path'], $svn_settings['local_bin_path'], $svn_settings['local_config_path'], $svn_settings['svn_repository'], $svn_settings['svn_username'], $svn_settings['svn_password']);

			$svn->set_global_option('non-interactive');

			if ($ignore_externals && $action === 'update' && (!$preview || $preview_confirm))
			{
				$svn->set_global_option('ignore-externals');
			}

			// get a preview
			if ($action === 'update' && $preview && !$preview_confirm)
			{
				$s_hidden_fields = array_merge($s_hidden_fields, array(
					'preview_confirm'	=> 1,

					'path'				=> $path,
					'revision'			=> $revision,
					'action'			=> $action,
					'verbose'			=> $verbose,
					'purge_cache'		=> $purge_cache,
					'refresh_theme'		=> $refresh_theme,
					'preview'			=> $preview,
					'limit'				=> $limit,
					'ignore_externals'	=> $ignore_externals,
				));

				$result = $svn->svn_diff(htmlspecialchars_decode($path), $config['svn_revision'], false, true, true);
				if (sizeof($result))
				{
					$template->assign_var('PREVIEW', implode('<br />', self::parse_diff(array_map('htmlspecialchars', $result))));

					$submit = false;

					throw new Exception('preview');
				}
			}

			switch ($action)
			{
				case 'update':
					$result = $svn->svn_update(htmlspecialchars_decode($path), $revision);
					break;
				case 'status':
					$result = $svn->svn_status(htmlspecialchars_decode($path), false, false, $verbose);
					break;
				case 'info':
					$result = $svn->svn_info(htmlspecialchars_decode($path), $revision);
					break;
				case 'list':
					$result = $svn->svn_list(htmlspecialchars_decode($path), $revision, false, $verbose);
					break;
				case 'proplist':
					$result = $svn->svn_proplist(htmlspecialchars_decode($path), $revision, false, $verbose);
					break;
			    case 'diff':
					$result = $svn->svn_diff(htmlspecialchars_decode($path), $revision ? $revision : $config['svn_revision'], false, true, true);
					$result = implode("\n", self::parse_diff(array_map('htmlspecialchars', $result)));
					break;
				case 'log':
					$result = $svn->svn_log(htmlspecialchars_decode($path), $revision, false, $verbose, false, $limit);
					break;
			}

			if (is_string($result))
			{
				$output = $result;
			}
			else if (is_array($result))
			{
				// use \n here because we're using <pre>
				$output = implode("\n", array_map('htmlspecialchars', $result));
			}
			else
			{
				throw new Exception('invalid_result');
			}

			// only purge cache on update
			if ($action === 'update')
			{
			    if (preg_match('#Updated to revision (\d+).#', $output, $matches))
			    {
					set_config('svn_revision', (int) $matches[1]);
					add_log('admin', 'LOG_SVN_UPDATED', "/$path", (int) $matches[1]);
				}

				if ($purge_cache)
				{
					global $auth, $cache;

					$cache->purge();
					$auth->acl_clear_prefetch();
					cache_moderators();
				}
				else
				{
					global $cache;

					$cache->destroy('_latest_svn');
				}

				if ($refresh_theme)
				{
					// Refresh the theme on css changes.
					$style_id = (int) $config['default_style'];

					$sql = 'SELECT *
						FROM ' . STYLES_THEME_TABLE . "
						WHERE theme_id = $style_id";
					$result = $db->sql_query($sql);
					$theme_row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if (!$theme_row)
					{
						trigger_error($user->lang['NO_THEME'] . adm_back_link($this->u_action), E_USER_WARNING);
					}

					if ($theme_row['theme_storedb'] && file_exists("{$phpbb_root_path}styles/{$theme_row['theme_path']}/theme/stylesheet.css"))
					{
						// Save CSS contents
						$sql_ary = array(
							'theme_mtime'	=> (int) filemtime("{$phpbb_root_path}styles/{$theme_row['theme_path']}/theme/stylesheet.css"),
							'theme_data'	=> $this->db_theme_data($theme_row)
						);

						$sql = 'UPDATE ' . STYLES_THEME_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
							WHERE theme_id = $style_id";
						$db->sql_query($sql);

						$cache->destroy('sql', STYLES_THEME_TABLE);

						add_log('admin', 'LOG_THEME_REFRESHED', $theme_row['theme_name']);
					}
				}
			}

			$template->assign_var('OUTPUT', $output);
		}
		catch (Exception $e)
		{
			switch ($e->getMessage())
			{
				case 'submit':
				case 'error':
				case 'preview':
					break;
				case 'invalid_result':
					trigger_error("In {$e->getFile()} on line {$e->getLine()}, there was no valid result.", E_USER_ERROR);
					break;
			}
		}

		$template->assign_vars(array(
			'REVISION'			=> $revision,
			'LIMIT'				=> $limit,
			'ACTION_SELECT'		=> form_select($this->actions, $action, 'SVN_MODE_', true),
			'S_VERBOSE'			=> $verbose ? true : false,
			'S_PURGE_CACHE'		=> $purge_cache ? true : false,
			'S_PREVIEW'			=> $preview ? true : false,
			'S_IGNORE_EXTERNALS'=> $ignore_externals ? true : false,

			'U_ACTION'			=> $this->u_action,

			// revision of installation
			'SVN_REVISION'		=> $config['svn_revision'],

			'S_SUBMIT'			=> $submit,
			'S_HIDDEN_FIELDS'	=> sizeof($s_hidden_fields) ? build_hidden_fields($s_hidden_fields) : '',
		));
	}

	/**
	 * parse a unified diff for display
	 *
	 * @param array $diff implode \n for actual result, we use the array returned by svn's exec, because we like it
	 * @return string parsed diff
	 */
	protected static function parse_diff($diff)
	{
		$in_file = $in_prop = false;
		for ($i = 0, $size = sizeof($diff); $i < $size; $i++)
		{
			if ($in_file)
			{
				if ($diff[$i] === '')
				{
					// empty line
				}
				else if ($diff[$i][0] === '+')
				{
					self::_add_class($diff[$i], 'add');
				}
				else if ($diff[$i][0] === '-')
				{
					self::_add_class($diff[$i], 'del');
				}
				else if ($diff[$i] === '\ No newline at end of file')
				{
					self::_add_class($diff[$i], 'index');
				}
				else if (preg_match('#^@@#', $diff[$i]))
				{
					self::_add_class($diff[$i], 'index');
				}
			}

			if ($in_prop)
			{
				if ($diff[$i] === '')
				{
					// empty line
				}
				if (preg_match('#^Name:#', $diff[$i]))
				{
					self::_add_class($diff[$i], 'index');
				}
				else if (preg_match('#^   (\+|-)#', $diff[$i], $matches))
				{
					self::_add_class($diff[$i], ($matches[1] === '+') ? 'add' : 'del');
				}
			}

			if (preg_match('#^Index:#', $diff[$i]))
			{
				$in_file = $in_prop = false;

				// next 5 lines are grey
				self::_add_class($diff[$i++], 'index');
				self::_add_class($diff[$i++], 'index');
				self::_add_class($diff[$i++], 'index');
				self::_add_class($diff[$i++], 'index');
				self::_add_class($diff[$i], 'index');

				// we're now in the file
				$in_file = true;
			}
			else if (preg_match('#^Property changes on:#', $diff[$i]))
			{
				$in_file = $in_prop = false;

				// next 2 lines are grey
				self::_add_class($diff[$i++], 'index');
				self::_add_class($diff[$i], 'index');

				$in_prop = true;
			}
		}

		return $diff;
	}

	/**
	 * add a css class to a string
	 */
	protected static function _add_class(&$string, $class)
	{
		if (is_array($class))
		{
			$class = implode(' ', $class);
		}

		$string = '<span' . ($class !== '' ? " class=\"$class\"" : '') . '>' . $string . '</span>';
	}

	/**
	* From acp_styles.php
	* Returns a string containing the value that should be used for the theme_data column in the theme database table.
	* Includes contents of files loaded via @import
	*
	* @param array $theme_row is an associative array containing the theme's current database entry
	* @param mixed $stylesheet can either be the new content for the stylesheet or false to load from the standard file
	* @param string $root_path should only be used in case you want to use a different root path than "{$phpbb_root_path}styles/{$theme_row['theme_path']}"
	*
	* @return string Stylesheet data for theme_data column in the theme table
	*/
	function db_theme_data($theme_row, $stylesheet = false, $root_path = '')
	{
		global $phpbb_root_path;

		if (!$root_path)
		{
			$root_path = $phpbb_root_path . 'styles/' . $theme_row['theme_path'];
		}

		if (!$stylesheet)
		{
			$stylesheet = '';
			if (file_exists($root_path . '/theme/stylesheet.css'))
			{
				$stylesheet = file_get_contents($root_path . '/theme/stylesheet.css');
			}
		}

		// Match CSS imports
		$matches = array();
		preg_match_all('/@import url\(["\'](.*)["\']\);/i', $stylesheet, $matches);

		if (sizeof($matches))
		{
			foreach ($matches[0] as $idx => $match)
			{
				$stylesheet = str_replace($match, $this->load_css_file($theme_row['theme_path'], $matches[1][$idx]), $stylesheet);
			}
		}

		// adjust paths
		return str_replace('./', 'styles/' . $theme_row['theme_path'] . '/theme/', $stylesheet);
	}

	/**
	* From acp_styles.php
	*
	* Load css file contents
	*/
	function load_css_file($path, $filename)
	{
		global $phpbb_root_path;

		$file = "{$phpbb_root_path}styles/$path/theme/$filename";

		if (file_exists($file) && ($content = file_get_contents($file)))
		{
			$content = trim($content);
		}
		else
		{
			$content = '';
		}
		if (defined('DEBUG'))
		{
			$content = "/* BEGIN @include $filename */ \n $content \n /* END @include $filename */ \n";
		}

		return $content;
	}
}