<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Loader.php 1572 2009-11-06 06:35:10Z vipsoft $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * Piwik auto loader
 *
 * @package Piwik
 */
class Piwik_Loader
{
	// our class search path; current directory is intentionally excluded
	protected static $dirs = array( '/core/', '/libs/', '/plugins/' );

	/**
	 * Get class file name
	 *
	 * @param string $class Class name
	 * @return string Class file name
	 */
	protected static function getClassFileName($class)
	{
		$class = str_replace('_', '/', $class);

		if($class == 'Piwik')
		{
			return $class;
		}

		if(substr($class, 0, 6) == 'Piwik/')
		{
			return substr($class, 6);
		}

		return $class;
	}

	/**
	 * Load class by name
	 *
	 * @param string $class Class name
	 * @throws exception if class cannot be loaded
	 */
	public static function autoload($class)
	{
		$classPath = self::getClassFileName($class);
		while(!empty($classPath))
		{
			// auto-discover class location
			for($i = 0; $i < count(self::$dirs); $i++)
			{
				$path = PIWIK_INCLUDE_PATH . self::$dirs[$i] . $classPath . '.php';
				if(file_exists($path))
				{
					require_once $path; // prefixed by PIWIK_INCLUDE_PATH
					if(class_exists($class, false) || interface_exists($class, false))
					{
						return;
					}
				}
			}

			// truncate to find file with multiple class definitions
			$lastSlash = strrpos($classPath, '/');
			$classPath = ($lastSlash === false) ? '' : substr($classPath, 0, $lastSlash);
		}
		throw new Exception("$class could not be autoloaded.");
	}
}

// Note: only one __autoload per PHP instance
if(function_exists('spl_autoload_register'))
{
	// use the SPL autoload stack
	spl_autoload_register(array('Piwik_Loader', 'autoload'));

	// preserve any existing __autoload
	if(function_exists('__autoload'))
	{
		spl_auto_register('__autoload');
	}
}
else
{
	function __autoload($class)
	{
		Piwik_Loader::autoload($class);
	}
}
