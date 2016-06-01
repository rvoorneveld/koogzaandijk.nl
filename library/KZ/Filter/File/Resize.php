<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Rename.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class KZ_Filter_File_Resize implements Zend_Filter_Interface
{
	/**
	 * Internal array of array(source, target, overwrite)
	 */
	protected $_width = null;
	protected $_height = null;
	protected $_keepRatio = true;
	protected $_keepSmaller = true;
	protected $_directory = null;
	protected $_adapter = 'Zend_Filter_File_Resize_Adapter_Gd';

	/**
	 * Create a new resize filter with the given options
	 *
	 * @param Zend_Config|array $options Some options. You may specify: width,
	 * height, keepRatio, keepSmaller (do not resize image if it is smaller than
	 * expected), directory (save thumbnail to another directory),
	 * adapter (the name or an instance of the desired adapter)
	 * @return Skoch_Filter_File_Resize An instance of this filter
	 */
	public function __construct($options = array())
	{
		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		} elseif (!is_array($options)) {
			require_once 'Zend/Filter/Exception.php';
			throw new Zend_Filter_Exception('Invalid options argument provided to filter');
		}

		if (!isset($options['width']) && !isset($options['height'])) {
			require_once 'Zend/Filter/Exception.php';
			throw new Zend_Filter_Exception('At least one of width or height must be defined');
		}

		if (isset($options['width'])) {
			$this->_width = $options['width'];
		}
		if (isset($options['height'])) {
			$this->_height = $options['height'];
		}
		if (isset($options['keepRatio'])) {
			$this->_keepRatio = $options['keepRatio'];
		}
		if (isset($options['keepSmaller'])) {
			$this->_keepSmaller = $options['keepSmaller'];
		}
		if (isset($options['directory'])) {
			$this->_directory = $options['directory'];
		}
		if (isset($options['adapter'])) {
			if ($options['adapter'] instanceof KZ_Filter_File_Resize_Adapter_Abstract) {
				$this->_adapter = $options['adapter'];
			} else {
				$name = $options['adapter'];
				if (substr($name, 0, 33) != 'KZ_Filter_File_Resize_Adapter_') {
					$name = 'KZ_Filter_File_Resize_Adapter_' . ucfirst(strtolower($name));
				}
				$this->_adapter = $name;
			}
		}

		$this->_prepareAdapter();
	}

	/**
	 * Instantiate the adapter if it is not already an instance
	 *
	 * @return void
	 */
	protected function _prepareAdapter()
	{
		if ($this->_adapter instanceof KZ_Filter_File_Resize_Adapter_Abstract) {
			return;
		} else {
			$this->_adapter = new $this->_adapter();
		}
	}

	/**
	 * Defined by Zend_Filter_Interface
	 *
	 * Resizes the file $value according to the defined settings
	 *
	 * @param  string $value Full path of file to change
	 * @return string The filename which has been set, or false when there were errors
	 */
	public function filter($value)
	{
		if ($this->_directory) {
			$target = $this->_directory . '/' . basename($value);
		} else {
			$target = $value;
		}

		return $this->_adapter->resize($this->_width, $this->_height,
			$this->_keepRatio, $value, $target, $this->_keepSmaller);
	}
}
