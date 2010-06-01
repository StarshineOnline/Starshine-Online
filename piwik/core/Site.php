<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Site.php 2023 2010-03-31 08:47:57Z matt $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * 
 * @package Piwik
 */
class Piwik_Site
{
	protected $id = null;
	
	protected static $infoSites = array();

	function __construct($idsite)
	{
		$this->id = $idsite;
		if(!isset(self::$infoSites[$this->id]))
		{
			self::$infoSites[$this->id] = Piwik_SitesManager_API::getInstance()->getSiteFromId($idsite);
		}
	}
	
	function __toString()
	{
		return "site id=".$this->getId().",
				 name=".$this->getName() .",
				 url = ". $this->getMainUrl() .",
				 IPs excluded = ".$this->getExcludedIps().",
				 timezone = ".$this->getTimezone().",
				 currency = ".$this->getCurrency().",
				 creation date = ".$this->getCreationDate();
	}
	
	function getName()
	{
		return self::$infoSites[$this->id]['name'];
	}
	
	function getMainUrl()
	{
		return self::$infoSites[$this->id]['main_url'];
	}
	
	function getId()
	{
		return $this->id;
	}
	
	function getCreationDate()
	{
		$date = self::$infoSites[$this->id]['ts_created'];
		return Piwik_Date::factory($date);
	}

	function getTimezone()
	{
		return self::$infoSites[$this->id]['timezone'];
	}
	
	function getCurrency()
	{
		return self::$infoSites[$this->id]['currency'];
	}
	
	function getExcludedIps()
	{
		return self::$infoSites[$this->id]['excluded_ips'];
	}
	
	function getExcludedQueryParameters()
	{
		return self::$infoSites[$this->id]['excluded_parameters'];
	}
	
	/**
	 * @param string comma separated idSite list
	 * @return array of valid integer
	 */
	static public function getIdSitesFromIdSitesString( $string )
	{
		$ids = explode(',', $string);
		$validIds = array();
		foreach($ids as $id)
		{
			$id = trim($id);
			$validIds[] = $id;
		}
		return $validIds;
	}
	
	static public function clearCache()
	{
		self::$infoSites = array();
	}
}
