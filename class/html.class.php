<?php
//======================================================================//
//== About html.class.php
//==--------------------------------------------------------------------//
//== This file is part of puppets library.
//== Licensed under the GPL version 2.0 license.
//== See LICENSE file or
//== http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
//==--------------------------------------------------------------------//
//== Contributor : Erwan LE LOSTEC
//== Contributor : Patrick PERONNY
//== Contributor : Olivier Toussaint
//======================================================================//

class html
{/**
  * @class html
  * @brief HTML related manipulation class.
  * Provides different methods to output HTML.
  **/
	public $doctype;					/*!< doctype the website */
  	public $author;						/*!< define the author of the website */
	public $category;					/*!< define the category of the website */
	public $content_langage;			/*!< define the langage(s) to use */
	public $content_type;				/*!< define the charactter table to use */
	public $content_script_type;		/*!< allow the javascript event manager */
	public $copyright;					/*!< define the copyrght of the website */
	public $base;
	public $description; 				/*!< describe the website */
  	public $favicon;					/*!< define the favorite icon */
	public $generator;					/*!< define the program used to create of the website */
	public $googlebot;					/*!< allow/deny caching by google bots (archive|noarchive) */
	public $identifier_url;				/*!< define the domain of the page */
	public $keywords; 					/*!< principal keywords of the website per langage */
	public $publisher;					/*!< define the name of the publisher */
	public $robots;						/*!< allow/deny the indexation by bots (all|none|index|noindex|follow|nofollow) */
	public $title;						/*!< the title of the page */
	public $viewport;					/*!< precise the attribut of viewport (ex: width=200px) */
	public $google_site_verification;	/*!< google verification meta tag */
	public $bing_site_verification;		/*!< bing verification meta tag */

	public $css;						/*!< links to css files */
	public $scripts;					/*!< links to javascript files */

	public $css_fix;					/*!< links to internet explorer css files */
	public $script_fix;					/*!< links to internet explorer javascript hack */

	//-- ANCIENNE VARIABLE NE PLUS UTILISER | GARDER POUR COMPATIBILITE
	public $script_inline;				/*!< insert a javascript code in head */
	public $script_header;				/*!< insert a javascript code in head */
	public $fix_ie6css;					/*!< links to internet explorer 6 css files */
	public $fix_ie6script;				/*!< links to internet explorer 6 javascript hack */
	public $fix_ie7css;					/*!< links to internet explorer 7 css files */
	public $fix_ie7script;				/*!< links to internet explorer 7 javascript hack */

  	public function __construct($doctype = null)
  	{/** @function __construct()
	  * Create a new html object.
	  *
	  * @return	nothing
	  **/
		$this->doctype					= $doctype;
  		$this->author					= null;
		$this->category					= null;
		$this->content_langage 			= array("fr");
  		$this->content_type				= "utf-8";
  		$this->content_script_type		= null;
		$this->copyright				= null;
		$this->base						= null;
		$this->description				= null;
  		$this->favicon					= null;
  		$this->generator				= null;
  		$this->googlebot				= null;
		$this->identifier_url			= null;
		$this->keywords					= array();
		$this->publisher				= null;
		$this->robots					= null;
		$this->title					= null;
		$this->viewport					= null;
		$this->google_site_verification	= null;
		$this->bing_site_verification	= null;
		$this->css						= array();
		$this->scripts					= array();
		$this->css_fix					= array();
		$this->script_fix				= array();
		
		//-- ANCIENNE VARIABLE NE PLUS UTILISER | GARDER POUR COMPATIBILITE
		$this->script_inline			= array();
		$this->script_header			= array();
		$this->fix_ie6css				= null;
		$this->fix_ie6script			= null;
		$this->fix_ie7css				= null;
		$this->fix_ie7script			= null;
		
   	}
   	public function header()
   	{/** @function header()
	  * Echo the begining of a html page.
	  *
	  * @return	nothing
	  **/
   		switch($this->doctype)
		{
			case "html5":	echo "<!DOCTYPE html>\n".
								 "<html lang='".implode(", ", $this->content_langage)."'>\n".
								 "  <head>\n".
								 "    <meta http-equiv='content-type' content='text/html; charset=".$this->content_type."' />\n";
							break;

			case "xhtml11":	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>\n".
								 "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='".implode(", ", $this->content_langage)."'>\n".
							   	 " <head>\n".
								 "  <meta http-equiv='content-type' content='text/html; charset=".$this->content_type."' />\n".
								 "  <meta http-equiv='content-language' content='".implode(", ", $this->content_langage)."'/>\n".
								 "  <meta name='language' content='".implode(", ", $this->content_langage)."R' />\n";
							break;

			default:		echo "<?xml version='1.1' encoding='".$this->content_type."'?>\n".
								 "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>\n".
								 "<html xmlns='http://www.w3.org/1999/xhtml'>\n".
								 " <head>\n".
								 "  <meta http-equiv='content-type' content='text/html; charset=".$this->content_type."' />\n".
								 "  <meta http-equiv='content-language' content='".implode(", ", $this->content_langage)."'/>\n".
								 "  <meta name='language' content='".implode(", ", $this->content_langage)."' />\n";
							break;
		}
		if($this->dublin_core === true) 
		{
			echo "  <link rel='schema.dc' href='http://purl.org/dc/elements/1.1/' />\n";
			echo "  <link rel='schema.dcterms' href='http://purl.org/dc/terms/' />\n";
			echo "  <meta name='dc.language' scheme='DCTERMS.RFC1766' content=".implode(", ", $this->content_langage)."' />\n";
			echo "  <meta name='dc.type' scheme='DCTERMS.DCMIType' content='InteractiveResource' />\n";
			echo "  <meta name='dc.format' content='text/html' />\n";
			
			//-- tmp
			echo "  <link rel='dc.relation' href='http://www.maqprint.pro' />\n";
		};	
		if(!empty($this->base))			
		{ 
			echo "  <base href='http://".$this->base."'/>\n"; 				
			if($this->dublin_core === true) { echo "  <link rel='dc.source' href='http://".$this->base."' />\n"; };
		};
		if(!empty($this->copyright))					
		{ 
			echo "  <meta name='copyright' content='".htmlentities($this->copyright, ENT_QUOTES, $this->content_type)."'/>\n";
			if($this->dublin_core === true) { echo "  <meta name='dc.rights' content='".htmlentities($this->copyright, ENT_QUOTES, $this->content_type)."' />\n"; };
		};
		if(!empty($this->generator))					
		{ 
			echo "  <meta name='generator' content='".htmlentities($this->generator, ENT_QUOTES, $this->content_type)."'/>\n";				
			if($this->dublin_core === true) 
			{
				echo "  <meta name='dc.creator' content='".htmlentities($this->generator, ENT_QUOTES, $this->content_type)."' />\n";
				echo "  <meta name='dc.contributor' content='".htmlentities($this->generator, ENT_QUOTES, $this->content_type)."' />\n";
			}
		};
		if(!empty($this->identifier_url))				
		{ 
			echo "  <meta name='identifier-url' content='".$this->identifier_url."'/>\n";														
			if($this->dublin_core === true) { echo "  <meta name='dc.identifier' scheme='DCTERMS.URI' content='".$this->identifier_url."' />\n"; };
		};
		if(!empty($this->description))					
		{ 
			echo "  <meta name='description' content='".htmlentities($this->description, ENT_QUOTES, $this->content_type)."'/>\n";			
			if($this->dublin_core === true) { echo "  <meta name='dc.description' lang='".$this->content_langage."' content='".htmlentities($this->description, ENT_QUOTES, $this->content_type)."' />\n"; };
		};
		if(!empty($this->keywords))					
		{ 
			if(is_array($this->keywords)) { $keywords = implode(", ", $this->keywords); } else { $keywords = $this->keywords; };
			
			echo "  <meta name='keywords' content='".htmlentities($keywords, ENT_QUOTES, $this->content_type)."'/>\n";	
			if($this->dublin_core === true) { echo "  <meta name='dc.subject' lang='".$this->content_langage."' content='".htmlentities(implode("; ", $this->keywords), ENT_QUOTES, $this->content_type)."' />\n"; };
		};
		if(!empty($this->publisher))					
		{ 
			echo "  <meta name='publisher' content='".htmlentities($this->publisher, ENT_QUOTES, $this->content_type)."'/>\n"; 																
			if($this->dublin_core === true) { echo "  <meta name='dc.publisher' content='".htmlentities($this->publisher, ENT_QUOTES, $this->content_type)."' />\n"; };
		};
		if(!empty($this->content_script_type))				{ echo "  <meta http-equiv='content-script-type' content='text/javascript' />\n"; 													};
		if(!empty($this->robots))							{ echo "  <meta name='robots' content='".$this->robots."'/>\n"; 																	};
		if(!empty($this->googlebot))						{ echo "  <meta name='googlebot' content='".$this->googlebot."'/>\n";																};
		if(!empty($this->viewport))							{ echo "  <meta name='viewport' content='".$this->viewport."' />\n"; 																};
		if(!empty($this->google_site_verification))			{ echo "  <meta name='google-site-verification' content='".$this->google_site_verification."' />\n";								};
		if(!empty($this->bing_site_verification))			{ echo "  <meta name='msvalidate.01' content='".$this->bing_site_verification."' />\n";												};
		if(!empty($this->geolocalisation))
		{//-- geolocation
			if(!empty($this->geolocalisation["region"])) 	{ echo "  <meta name='geo.region' content='".$this->geolocalisation["region"]."' />\n"; 											};
			if(!empty($this->geolocalisation["placename"])) { echo "  <meta name='geo.placename' content='".$this->geolocalisation["placename"]."' />\n"; 										};
			if(!empty($this->geolocalisation["position"])) 	{ echo "  <meta name='geo.position' content='".$this->geolocalisation["position"]."' />\n"; 										};
			if(!empty($this->geolocalisation["ICBM"])) 		{ echo "  <meta name='ICBM' content='".$this->geolocalisation["ICBM"]."' />\n"; 													};
			
			if($this->dublin_core === true) {  echo "  <meta name='dc.coverage' content='".$this->geolocalisation["region"].", ".$this->geolocalisation["placename"].", ".$this->geolocalisation["position"].", ".$this->geolocalisation["ICBM"]."' />\n"; };
		}
		if(!empty($this->favicon)) 							{ echo "  <link href='".$this->favicon."' type='image/png' rel='icon'/>\n"; 														};
		if(!empty($this->title))						
		{ 
			echo "  <title>".$this->title."</title>\n"; 
			if($this->dublin_core === true) { echo "  <meta name='dc.title' lang='".$this->content_langage."' content='".$this->title."' />\n"; 												};
		};
		
		foreach($this->css as $css)							{ echo "  <link href='".$css."' rel='stylesheet' type='text/css' />\n";																};
		
		foreach($this->scripts as $scripts)
		{
			if(!is_array($scripts))	{ echo "  <script type='text/javascript' src='".$scripts."'></script>\n"; } /*-- for retro compatibility --*/
			else
			{
				if(($scripts['position'] == "header") || (empty($scripts['position'])))
				{
					if(($scripts['async'] === true) && ($this->doctype == "html5")) { echo "  <script type='text/javascript' src='".$scripts['file']."' async='async'>".$scripts['content']."</script>\n";																	}
					elseif($scripts['async'] === true)  							{ echo "  <script type='text/javascript'>document.write(unescape(\"%3Cscript src='".$scripts['file']."' type='text/javascript'%3E".$scripts['content']."%3C/script%3E\"));</script>\n"; }
					else 															{ echo "  <script type='text/javascript' src='".$scripts['file']."'>".$scripts['content']."</script>\n"; 																				};
				}
			}
		}
		
		foreach($this->css_fix as $css_fix)
		{
			echo "  <!--[if ".$css_fix['target']."]><link href='".$css_fix['file']."' rel='stylesheet' type='text/css' /><![endif]-->\n";
		};

		foreach($this->script_fix as $script_fix)
		{
			if(($script_fix['position'] == "header") || (empty($script_fix['position'])))
			{
				echo "  <!--[if ".$script_fix['target']."]><script type='text/javascript' src='".$script_fix['file']."'></script><![endif]-->\n";
			};
		};
		
		//-- Garder la compatibilité avec les anciennes versions.
		if(count($this->script_header) > 0) { $this->script_inline = $this->script_header;	};
		if(count($this->script_inline) > 0)	{ echo "  <script type='text/javascript'>\n//<![CDATA[\n".implode(";", $this->script_inline)."\n// ]]>\n</script>\n"; 				};

		if(!empty($this->fix_ie7css))		{ $this->css_fix[] = array("target" => "lte IE 7", "file" => $this->fix_ie7css);	}
	    if(!empty($this->fix_ie6css))		{ $this->css_fix[] = array("target" => "lte IE 6", "file" => $this->fix_ie6css); 	}
		
		echo " </head>\n";
		flush();
		echo " <body>\n";
   	}
	public function footer()
	{/** @function footer()
	  * Echo the end of html file.
	  *
	  * @return	nothing
	  **/
		foreach($this->scripts as $scripts)
		{
			if($scripts['position'] == "footer")	
			{
				if(($scripts['async'] === true) && ($this->doctype == "html5"))	{ echo "  <script type='text/javascript' src='".$scripts['file']."' async='async'>".$scripts['content']."</script>\n";																	}
				elseif($scripts['async'] === true)								{ echo "  <script type='text/javascript'>document.write(unescape(\"%3Cscript src='".$scripts['file']."' type='text/javascript'%3E".$scripts['content']."%3C/script%3E\"));</script>\n";	}
				else															{ echo "  <script type='text/javascript' src='".$scripts['file']."'>".$scripts['content']."</script>\n";																				};
			}
		};
		foreach($this->script_fix as $script_fix)
		{
			if($script_fix['position'] == "footer")
			{
				echo "  <!--[if ".$script_fix['target']."]><script type='text/javascript' src='".$script_fix['file']."'></script><![endif]-->\n";
			};
		};
		echo "\n</body>\n".
			 "</html>";
	}
	public static function clean($string)
	{/** @function clean()
	  * Strip all html tag in the $string
	  *
	  * @param	string	<string>	the string with html tags to remove
	  *
	  * @return	<string>
	  **/
		return strip_tags($string);
	}
	public static function encode($string, $charset = "UTF-8", $keep_brs = true)
	{/** @function clean()
	  * Strip all html tag in the $string
	  *
	  * @param	string	<string>	the string with html tags to remove
	  *
	  * @return	<string>
	  **/  	
		if($keep_brs === false)	{ return htmlentities($string, ENT_QUOTES, $charset);	}
		else					{ return preg_replace("/&lt;(br\s{0,}\/?)&gt;/i", "<br/>", htmlentities($string, ENT_QUOTES, $charset)); };
	}
	
}
?>