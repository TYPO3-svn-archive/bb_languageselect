<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Bernhard Berger <bernhard.berger@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'BB Language Select' for the 'bb_languageselect' extension.
 *
 * @author	Bernhard Berger <bernhard.berger@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_bblanguageselect
 */
class tx_bblanguageselect_pi1 extends tslib_pibase {
	var $prefixId = 'tx_bblanguageselect_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_bblanguageselect_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'bb_languageselect';	// The extension key.
	var $pi_checkCHash = TRUE;

	/**
	 * At the moment just an Alias for showFlags
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	nothing
	 */
	function main($content,$conf)	{
		return; // just please do nothing :)
	}


	/**
	 * showFlags returns the HTML-Output for the LanguageMenu
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	string		flagMenu
	 */
	function showFlags($content, $conf){

		## Getting the translated pagetitles
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
										'*',
										'pages_language_overlay',
										'pid='.intval($GLOBALS['TSFE']->id).$GLOBALS['TSFE']->sys_page->enableFields('pages_language_overlay'),
										'sys_language_uid'
									);

		$langArr = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$langArr[$row['sys_language_uid']] = $row['title'];
		}


		## Choosing the Iconset by config
		switch($conf['iconset']) {
			case 'famfamfam':
				$flag_path = 'famfamfam';
				$size['x'] = 16;
				$size['y'] = 11;
				break;
			case 'jacorre':
				$flag_path = 'jacorre';
				$size['x'] = 27;
				$size['y'] = 17;
				break;
			case 't3':
				$flag_path = 't3flags';
				$size['x'] = 20;
				$size['y'] = 12;
				break;
			default:
				$flag_path = 'famfamfam';
				$size['x'] = 16;
				$size['y'] = 11;
				break;
		}

		## The little red indicator for the active language
		if ($conf['pointerImg']) {
			$pointerImg = $conf['pointerImg'];
		} else {
			$pointerImg = t3lib_extMgm::siteRelPath('cms').'tslib/media/icons_misc/content_client.gif';
		}
		
		$pointer = '<img src="'.$pointerImg.'" align="bottom" alt="" />';
		
		$flags = array();
		$countries = array();
		$countries = $this->getCountries($conf);

		foreach($countries as $cn => $sys_uid){
			$flag_file = t3lib_extMgm::siteRelPath('bb_languageselect').'flags/'.$flag_path.'/'.$cn . ($langArr[$sys_uid] || $sys_uid == 0 || $conf['noDisable'] ? '' : '_d').'.gif';

			if (file_exists($flag_file)) { ## to prevent from displaying broken images
				$flags[] = ($GLOBALS['TSFE']->sys_language_uid == $sys_uid ? $pointer: '').
				'<a href="'.htmlspecialchars(
								$this->pi_getPageLink(
										$GLOBALS['TSFE']->id,
										'', array('L' => $sys_uid)
								)
							).
				'"><img src="'.$flag_file.'" width="'.$size['x'].'" height="'.$size['y'].'" border="0" alt="" /></a>&nbsp;';
				}
			}


		$content = implode('',$flags);
		return $this->pi_wrapInBaseClass($content);

	}


	/**
	*	Returns an Array of countries, filtered from the config.
	*
	* 	@param	array	$conf			plugin-config
	* 	@return	array	$countries		an array just filled with the ISO-3166 country codes
	*
	* */
	function getCountries($conf){
		foreach($conf as $k => $v){
			if(strpos($k, 'cn_') === false) {
				// do nothing
			} else {
				$countries[strtolower(str_replace('cn_','', $k))] = $v;
			}
		}
		return $countries;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bb_languageselect/pi1/class.tx_bblanguageselect_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bb_languageselect/pi1/class.tx_bblanguageselect_pi1.php']);
}

?>