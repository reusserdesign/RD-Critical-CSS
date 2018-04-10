<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * RD Critical CSS
 *
 * @package		ExpressionEngine
 * @category	Plugin
 * @author		Jarrod D Nix, Reusser Design
 * @license		https://opensource.org/licenses/MIT The MIT License (MIT)
 */

class Rd_critical_css
{
	public $loadCSS = '<script>!function(a){"use strict";var b=function(b,c,d){function e(a){return h.body?a():void setTimeout(function(){e(a)})}function f(){i.addEventListener&&i.removeEventListener("load",f),i.media=d||"all"}var g,h=a.document,i=h.createElement("link");if(c)g=c;else{var j=(h.body||h.getElementsByTagName("head")[0]).childNodes;g=j[j.length-1]}var k=h.styleSheets;i.rel="stylesheet",i.href=b,i.media="only x",e(function(){g.parentNode.insertBefore(i,c?g:g.nextSibling)});var l=function(a){for(var b=i.href,c=k.length;c--;)if(k[c].href===b)return a();setTimeout(function(){l(a)})};return i.addEventListener&&i.addEventListener("load",f),i.onloadcssdefined=l,l(f),i};"undefined"!=typeof exports?exports.loadCSS=b:a.loadCSS=b}("undefined"!=typeof global?global:this);!function(a){if(a.loadCSS){var b=loadCSS.relpreload={};if(b.support=function(){try{return a.document.createElement("link").relList.supports("preload")}catch(b){return!1}},b.poly=function(){for(var b=a.document.getElementsByTagName("link"),c=0;c<b.length;c++){var d=b[c];"preload"===d.rel&&"style"===d.getAttribute("as")&&(a.loadCSS(d.href,d,d.getAttribute("media")),d.rel=null)}},!b.support()){b.poly();var c=a.setInterval(b.poly,300);a.addEventListener&&a.addEventListener("load",function(){b.poly(),a.clearInterval(c)}),a.attachEvent&&a.attachEvent("onload",function(){a.clearInterval(c)})}}}(this);</script>';

	public $return_data  = "";

	public function __construct()
	{

		if (version_compare(APP_VER, '3', '>='))
		{

			// Get critical css file
			$critical = ee()->TMPL->fetch_param('critical') ? ee()->TMPL->fetch_param('critical') : FALSE;
			if(!$critical)
			{
				$this->return_data = 'Critical CSS file required!';
				return;
			}

			// Get external font file(s)
			$externals = ee()->TMPL->fetch_param('external_fonts') ? ee()->TMPL->fetch_param('external_fonts') : FALSE;
			if($externals)
			{
				if(stristr($externals, "|") !== FALSE)
				{
					$externals = explode("|", $externals);
				}else
				{
					$externals = array($externals);
				}
			}

			// Create array of stylesheets
			$styles = ee()->TMPL->fetch_param('styles') ? ee()->TMPL->fetch_param('styles') : FALSE;
			if(!$styles){
				$this->return_data = 'Stylesheet file required!';
				return;
			}
			if(stristr($styles, "|") !== FALSE)
			{
				$styles = explode("|", $styles);
			}else
			{
				$styles = array($styles);
			}

			if($critical && file_exists($_SERVER['DOCUMENT_ROOT'].$critical) && ($criticalTime = filemtime($_SERVER['DOCUMENT_ROOT'].$critical)) !== FALSE && (!isset($_COOKIE["cssEmbedded"]) || $_COOKIE["cssEmbedded"] < $criticalTime))
			{

				// Set cookie to use cached stylesheets
				setcookie("cssEmbedded", time(), time()+60*60*24*365, "/");

				// Get contents of critical css file
				$criticalContents = file_get_contents($_SERVER['DOCUMENT_ROOT'].$critical);

				// Remove any source map comments, i.e. /*# sourceMappingURL=critical.css.map */
				$criticalContents = preg_replace("(\n\/\*\#.*\*\/)", "", $criticalContents);

				$this->return_data = "<style>";

				// Embed external fonts if available
				if($externals != false)
				{
					foreach($externals as $external)
					{
						$this->return_data .= file_get_contents($external);
					}
				}

				// Embed contents of critical css file
				$this->return_data .= $criticalContents;

				$this->return_data .= "</style>";

				// Create preload `<link>` elements
				if($externals != false)
				{
					foreach($externals as $external)
					{
						$this->return_data .= '<link href="'.$external.'" as="style" onload="this.rel=\'stylesheet\'" rel="preload" />';
					}
				}
				foreach($styles as $stylesheet)
				{
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$stylesheet) && ($styleTime = filemtime($_SERVER['DOCUMENT_ROOT'].$stylesheet)) !== FALSE)
					{
						$this->return_data .= '<link href="'.$stylesheet.'?'.$styleTime.'" as="style" onload="this.rel=\'stylesheet\'" rel="preload" />';
					}
				}

				// Create `<noscript>` fallbacks
				$this->return_data .= '<noscript>';
				if($externals != false)
				{
					foreach($externals as $external)
					{
						$this->return_data .= '<link href="'.$external.'" rel="stylesheet" />';
					}
				}
				foreach($styles as $stylesheet)
				{
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$stylesheet) && ($styleTime = filemtime($_SERVER['DOCUMENT_ROOT'].$stylesheet)) !== FALSE)
					{
						$this->return_data .= '<link href="'.$stylesheet.'?'.$styleTime.'" rel="stylesheet" />';
					}
				}
				$this->return_data .= '</noscript>';

				// Add loadCSS function as preload fallback
				$this->return_data .= $this->loadCSS;

			}else
			{

				// Create standard `<link>` elements since stylesheets are cached
				$this->return_data = "";

				if($externals != false)
				{
					foreach($externals as $external)
					{
						$this->return_data .= "<link href='".$external."' rel='stylesheet' />";
					}
				}

				foreach($styles as $stylesheet)
				{
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$stylesheet) && ($styleTime = filemtime($_SERVER['DOCUMENT_ROOT'].$stylesheet)) !== FALSE)
					{
						$this->return_data .= '<link href="'.$stylesheet.'?'.$styleTime.'" rel="stylesheet" />';
					}
				}

			}

		}

	}

}

/* End of file pi.rd_critical_css.php */
/* Location: ./system/user/addons/rd_critical_css/pi.rd_critical_css.php */