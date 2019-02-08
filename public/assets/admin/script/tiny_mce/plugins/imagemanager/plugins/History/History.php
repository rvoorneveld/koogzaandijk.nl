<?php
/**
 * HistoryCookiePlugin.php
 *
 * @package HistoryCookiePlugin
 * @author Moxiecode
 * @copyright Copyright � 2007, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class handles MCImageManager HistoryCookiePlugin stuff.
 *
 * @package HistoryCookiePlugin
 */
class Moxiecode_HistoryPlugin extends Moxiecode_ManagerPlugin {
	/**#@+
	 * @access public
	 */
	var $_maxhistory = 10;

	/**
	 * ..
	 */
	public function __construct() {
	}

	public function onInit(&$man) {
		$man->registerFileSystem('history', 'Moxiecode_HistoryFile');

		return true;
	}

	public function onInsertFile(&$man, &$file) {
		$path = $file->getAbsolutePath();
		$type = $man->getType();
		$maxhistory = isset($config["history.max"]) ? $config["history.max"] : $this->_maxhistory;
		$cookievalue = $this->getCookieData($type);

		$patharray = array();
		$patharray = explode(",", $cookievalue);
		if (count($patharray) > 0) {

			for($i=0;$i<count($patharray);$i++) {
				if ($patharray[$i] == $path) {
					array_splice($patharray, $i, 1);
					break;
				}
			}

			array_unshift($patharray, $path);

			if (count($patharray) > $maxhistory)
				array_pop($patharray);

		} else
			$patharray[] = $path;

		$cookievalue = implode(",", $patharray);

		$this->setCookieData($type, $cookievalue);
		return true;
	}

	public function getCookieData($type) {
		if (isset($_COOKIE["MCManagerHistoryCookie_". $type]))
			return $_COOKIE["MCManagerHistoryCookie_". $type];
		else
			return "";
	}

	public function setCookieData($type, $val) {
		setcookie("MCManagerHistoryCookie_". $type, $val, time()+(3600*24*30)); // 30 days
	}

	public function onClearHistory(&$man) {
		setcookie ("MCManagerHistoryCookie_". $man->getType(), "", time() - 3600); // 1 hour ago
		return true;
	}
}

class Moxiecode_HistoryFile extends Moxiecode_BaseFileImpl {
	public function __construct(&$manager, $absolute_path, $child_name = "", $type = MC_IS_FILE) {
		$absolute_path = str_replace('favorite://', '', $absolute_path);
		Moxiecode_BaseFileImpl::Moxiecode_BaseFileImpl($manager, $absolute_path, $child_name, $type);
	}

	public function canRead() {
		return true;
	}

	public function canWrite() {
		return false;
	}

	public function exists() {
		return true;
	}

	public function isDirectory() {
		return true;
	}

	public function isFile() {
		return false;
	}

	public function getParent() {
		return null;
	}

	public function &getParentFile() {
		return null;
	}

	/**
	 * Returns an array of File instances.
	 *
	 * @return Array array of File instances.
	 */
	public function &listFiles() {
		$files = $this->listFilesFiltered(new Moxiecode_DummyFileFilter());
		return $files;
	}

	/**
	 * Returns an array of MCE_File instances based on the specified filter instance.
	 *
	 * @param MCE_FileFilter &$filter MCE_FileFilter instance to filter files by.
	 * @return Array array of MCE_File instances based on the specified filter instance.
	 */
	public function &listFilesFiltered(&$filter) {
		$files = array();
		$man = $this->_manager;

		$type = $man->getType();

		$cookievalue = $this->_getCookieData($type);

		$patharray = array();
		if (IndexOf($cookievalue, ",") != -1)
			$patharray = explode(",", $cookievalue);
		else if ($cookievalue != "")
			$patharray[] = $cookievalue;

		foreach ($patharray as $path) {
			if (!$man->verifyPath($path))
				continue;

			$file = $man->getFile($path);

			if (!$file->exists()) {
				$this->_removeFavorite($man, $path);
				continue;
			}

			if ($man->verifyFile($file) < 0)
				continue;

			if ($filter->accept($file) == BASIC_FILEFILTER_ACCEPTED)
				$files[] = $file;
		}

		return $files;
	}

	public function _getCookieData($type) {
		if (isset($_COOKIE["MCManagerHistoryCookie_". $type]))
			return $_COOKIE["MCManagerHistoryCookie_". $type];
		else
			return "";
	}

	public function _removeFavorite(&$man, $path=array()) {
		$type = $man->getType();

		$cookievalue = $this->_getCookieData($type);

		$patharray = array();
		$patharray = explode(",", $cookievalue);

		$break = false;
		if (count($patharray) > 0) {
			for($i=0;$i<count($patharray);$i++) {
				if (is_array($path)) {
					if (in_array($patharray[$i], $path))
						$break = true;

				} else {
					if ($patharray[$i] == $path)
						$break = true;
				}
				
				if ($break) {
					array_splice($patharray, $i, 1);
					break;
				}
			}
		}

		$cookievalue = implode(",", $patharray);

		$this->_setCookieData($type, $cookievalue);
		return true;
	}

	public function _setCookieData($type, $val) {
		setcookie("MCManagerHistoryCookie_". $type, $val, time()+(3600*24*30)); // 30 days
	}
}

// Add plugin to MCManager
$man->registerPlugin("history", new Moxiecode_HistoryPlugin());
